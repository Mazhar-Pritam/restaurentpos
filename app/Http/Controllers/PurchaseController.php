<?php

namespace App\Http\Controllers;

use App\Http\Requests\PurchaseRequest;
use App\Models\Ingredient;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Services\PurchaseStockService;
use DomainException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PurchaseController extends Controller
{
    public function __construct(private PurchaseStockService $stock)
    {
    }

    public function index(Request $request)
    {
        $purchases = Purchase::with('supplier')
            ->when($request->filled('search'), function ($query) use ($request): void {
                $term = '%'.$request->input('search').'%';
                $query->where(function ($q) use ($term): void {
                    $q->where('invoice_no', 'like', $term)
                      ->orWhereHas('supplier', fn ($s) => $s->where('name', 'like', $term));
                });
            })
            ->when($request->filled('supplier'), fn ($q) => $q->where('supplier_id', $request->input('supplier')))
            ->when($request->filled('from'), fn ($q) => $q->whereDate('purchase_date', '>=', $request->input('from')))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('purchase_date', '<=', $request->input('to')))
            ->orderByDesc('purchase_date')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        $suppliers = Supplier::orderBy('name')->get();

        return view('purchases.index', compact('purchases', 'suppliers'));
    }

    public function create()
    {
        return view('purchases.create', [
            'purchase'    => null,
            'suppliers'   => Supplier::where('is_active', true)->orderBy('name')->get(),
            'ingredients' => Ingredient::where('is_active', true)->orderBy('name')->get(),
            'rows'        => [['ingredient_id' => '', 'quantity' => '', 'unit_cost' => '']],
        ]);
    }

    public function store(PurchaseRequest $request)
    {
        try {
            DB::transaction(function () use ($request): void {
                $items = $this->buildItems($request->validated('items'));
                $total = round(collect($items)->sum('line_total'), 2);
                $paid  = round((float) $request->input('paid_amount'), 2);
                $this->checkPaid($paid, $total);

                $purchase = Purchase::create([
                    'supplier_id'   => $request->input('supplier_id'),
                    'purchase_date' => $request->input('purchase_date'),
                    'total_amount'  => $total,
                    'paid_amount'   => $paid,
                    'note'          => $request->input('note'),
                    'user_id'       => auth()->id(),
                ]);

                $purchase->update(['invoice_no' => 'PUR-'.str_pad((string) $purchase->id, 5, '0', STR_PAD_LEFT)]);
                $purchase->items()->createMany($items);

                $this->stock->apply([], $items);
            });
        } catch (DomainException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->route('purchases.index')->with('success', 'Purchase saved. Stock updated.');
    }

    public function show(Purchase $purchase)
    {
        $purchase->load(['supplier', 'items.ingredient', 'user']);

        return view('purchases.show', compact('purchase'));
    }

    public function edit(Purchase $purchase)
    {
        if ($purchase->returns()->exists()) {
            return redirect()->route('purchases.index')
                ->with('error', 'This purchase has returns, so it can no longer be edited. Delete its returns first.');
        }

        $purchase->load('items');
        $usedIds = $purchase->items->pluck('ingredient_id');

        return view('purchases.edit', [
            'purchase'    => $purchase,
            'suppliers'   => Supplier::where('is_active', true)->orWhere('id', $purchase->supplier_id)->orderBy('name')->get(),
            'ingredients' => Ingredient::where('is_active', true)->orWhereIn('id', $usedIds)->orderBy('name')->get(),
            'rows'        => $purchase->items->map(fn ($i) => [
                'ingredient_id' => $i->ingredient_id,
                'quantity'      => $this->trim($i->quantity, 3),
                'unit_cost'     => $this->trim($i->unit_cost, 4),
            ])->values()->all(),
        ]);
    }

    public function update(PurchaseRequest $request, Purchase $purchase)
    {
        if ($purchase->returns()->exists()) {
            return redirect()->route('purchases.index')
                ->with('error', 'This purchase has returns, so it can no longer be edited. Delete its returns first.');
        }

        try {
            DB::transaction(function () use ($request, $purchase): void {
                $old   = $purchase->items()->get()->map(fn ($i) => [
                    'ingredient_id' => $i->ingredient_id,
                    'quantity'      => (float) $i->quantity,
                    'unit_cost'     => (float) $i->unit_cost,
                ])->all();

                $items = $this->buildItems($request->validated('items'));
                $total = round(collect($items)->sum('line_total'), 2);
                $paid  = round((float) $request->input('paid_amount'), 2);
                $this->checkPaid($paid, $total);

                $purchase->items()->delete();
                $purchase->items()->createMany($items);

                $purchase->update([
                    'supplier_id'   => $request->input('supplier_id'),
                    'purchase_date' => $request->input('purchase_date'),
                    'total_amount'  => $total,
                    'paid_amount'   => $paid,
                    'note'          => $request->input('note'),
                ]);

                $this->stock->apply($old, $items);
            });
        } catch (DomainException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->route('purchases.index')->with('success', 'Purchase updated. Stock adjusted.');
    }

    public function destroy(Purchase $purchase)
    {
        if ($purchase->returns()->exists()) {
            return redirect()->route('purchases.index')
                ->with('error', 'This purchase has returns, so it can no longer be deleted. Delete its returns first.');
        }

        try {
            DB::transaction(function () use ($purchase): void {
                $old = $purchase->items()->get()->map(fn ($i) => [
                    'ingredient_id' => $i->ingredient_id,
                    'quantity'      => (float) $i->quantity,
                    'unit_cost'     => (float) $i->unit_cost,
                ])->all();

                $this->stock->apply($old, []);   // স্টক ফেরত নেওয়া
                $purchase->delete();             // soft delete
            });
        } catch (DomainException $e) {
            return redirect()->route('purchases.index')->with('error', $e->getMessage());
        }

        return redirect()->route('purchases.index')->with('success', 'Purchase deleted. Stock reversed.');
    }

    /** ফর্মের সারিগুলো থেকে DB-র আইটেম বানায়; লাইন টোটাল সবসময় সার্ভারে হিসাব হয় */
    private function buildItems(array $rows): array
    {
        return collect($rows)->map(function ($row) {
            $qty  = round((float) $row['quantity'], 3);
            $cost = round((float) $row['unit_cost'], 4);

            return [
                'ingredient_id' => (int) $row['ingredient_id'],
                'quantity'      => $qty,
                'unit_cost'     => $cost,
                'line_total'    => round($qty * $cost, 2),
            ];
        })->values()->all();
    }

    private function checkPaid(float $paid, float $total): void
    {
        if ($paid > $total + 0.005) {
            throw ValidationException::withMessages([
                'paid_amount' => 'Paid amount cannot be more than the total (৳'.number_format($total, 2).').',
            ]);
        }
    }

    private function trim($value, int $decimals): string
    {
        return rtrim(rtrim(number_format((float) $value, $decimals, '.', ''), '0'), '.');
    }
}
