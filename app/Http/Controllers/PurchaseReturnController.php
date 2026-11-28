<?php

namespace App\Http\Controllers;

use App\Http\Requests\PurchaseReturnRequest;
use App\Models\Purchase;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnItem;
use App\Models\Supplier;
use App\Services\PurchaseStockService;
use DomainException;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PurchaseReturnController extends Controller
{
    public function __construct(private PurchaseStockService $stock)
    {
    }

    public function index(Request $request)
    {
        $returns = PurchaseReturn::with(['supplier', 'purchase'])
            ->when($request->filled('search'), function ($query) use ($request): void {
                $term = '%'.$request->input('search').'%';
                $query->where(function ($q) use ($term): void {
                    $q->where('return_no', 'like', $term)
                      ->orWhereHas('purchase', fn ($p) => $p->where('invoice_no', 'like', $term))
                      ->orWhereHas('supplier', fn ($s) => $s->where('name', 'like', $term));
                });
            })
            ->when($request->filled('supplier'), fn ($q) => $q->where('supplier_id', $request->input('supplier')))
            ->when($request->filled('from'), fn ($q) => $q->whereDate('return_date', '>=', $request->input('from')))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('return_date', '<=', $request->input('to')))
            ->orderByDesc('return_date')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('purchase_returns.index', [
            'returns'   => $returns,
            'suppliers' => Supplier::orderBy('name')->get(),
        ]);
    }

    /** ধাপ ১: ইনভয়েস বাছা। ধাপ ২ (?purchase=ID): ফেরতের পরিমাণ লেখা */
    public function create(Request $request)
    {
        $purchase  = null;
        $returnable = collect();

        if ($request->filled('purchase')) {
            $purchase = Purchase::with('supplier')->findOrFail($request->input('purchase'));
            $returnable = $this->returnable($purchase);
        }

        return view('purchase_returns.create', [
            'purchase'   => $purchase,
            'returnable' => $returnable,
            'purchases'  => $purchase ? collect() : Purchase::with('supplier')->orderByDesc('purchase_date')->orderByDesc('id')->limit(300)->get(),
        ]);
    }

    public function store(PurchaseReturnRequest $request)
    {
        try {
            $return = DB::transaction(function () use ($request) {
                $purchase   = Purchase::whereKey($request->input('purchase_id'))->lockForUpdate()->firstOrFail();
                $returnable = $this->returnable($purchase);

                if ($request->date('return_date')->lt($purchase->purchase_date)) {
                    throw ValidationException::withMessages(['return_date' => 'Return date cannot be before the purchase date.']);
                }

                $items = [];
                foreach ($request->input('items', []) as $ingredientId => $qty) {
                    $qty = round((float) $qty, 3);
                    if ($qty <= 0) {
                        continue;
                    }

                    $row = $returnable->get((int) $ingredientId);
                    if (! $row) {
                        throw ValidationException::withMessages(['items' => 'An ingredient in the form is not part of this purchase.']);
                    }
                    if ($qty > $row['remaining'] + 0.0005) {
                        throw ValidationException::withMessages([
                            'items' => 'You can return at most '.$this->trim($row['remaining']).' '.$row['ingredient']->unit.' of "'.$row['ingredient']->name.'".',
                        ]);
                    }

                    $cost    = round($row['unit_cost'], 4);
                    $items[] = [
                        'ingredient_id' => (int) $ingredientId,
                        'quantity'      => $qty,
                        'unit_cost'     => $cost,
                        'line_total'    => round($qty * $cost, 2),
                    ];
                }

                if (empty($items)) {
                    throw ValidationException::withMessages(['items' => 'Enter a return quantity for at least one item.']);
                }

                $total  = round(collect($items)->sum('line_total'), 2);
                $refund = round((float) $request->input('refund_amount', 0), 2);

                if ($refund > $total + 0.005) {
                    throw ValidationException::withMessages(['refund_amount' => 'Cash refund cannot be more than the returned value (৳'.number_format($total, 2).').']);
                }

                $alreadyRefunded = (float) PurchaseReturn::where('purchase_id', $purchase->id)->sum('refund_amount');
                if ($refund > (float) $purchase->paid_amount - $alreadyRefunded + 0.005) {
                    throw ValidationException::withMessages(['refund_amount' => 'Cash refund cannot be more than what was paid to the supplier on this invoice.']);
                }

                $return = PurchaseReturn::create([
                    'purchase_id'   => $purchase->id,
                    'supplier_id'   => $purchase->supplier_id,
                    'return_date'   => $request->input('return_date'),
                    'total_amount'  => $total,
                    'refund_amount' => $refund,
                    'note'          => $request->input('note'),
                    'user_id'       => auth()->id(),
                ]);
                $return->update(['return_no' => 'PRT-'.str_pad((string) $return->id, 5, '0', STR_PAD_LEFT)]);
                $return->items()->createMany($items);

                // স্টক কমে যায় (মূল দামে), যথেষ্ট স্টক না থাকলে DomainException
                $this->stock->apply($items, []);

                return $return;
            });
        } catch (DomainException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->route('purchase-returns.show', $return)->with('success', 'Return saved. Stock reduced.');
    }

    public function show(PurchaseReturn $purchaseReturn)
    {
        $purchaseReturn->load(['supplier', 'purchase', 'items.ingredient', 'user']);

        return view('purchase_returns.show', ['return' => $purchaseReturn]);
    }

    public function destroy(PurchaseReturn $purchaseReturn)
    {
        try {
            DB::transaction(function () use ($purchaseReturn): void {
                $items = $purchaseReturn->items()->get()->map(fn ($i) => [
                    'ingredient_id' => $i->ingredient_id,
                    'quantity'      => (float) $i->quantity,
                    'unit_cost'     => (float) $i->unit_cost,
                ])->all();

                $this->stock->apply([], $items);   // স্টক আবার যোগ
                $purchaseReturn->delete();
            });
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('purchase-returns.index')->with('success', 'Return deleted. Stock restored.');
    }

    /** ইনভয়েসের প্রতিটি ইনগ্রেডিয়েন্টে কতটা কেনা, কতটা ফেরত, কতটা ফেরতযোগ্য বাকি */
    private function returnable(Purchase $purchase): Collection
    {
        $purchased = $purchase->items()->with('ingredient')->get()
            ->groupBy('ingredient_id')
            ->map(function ($rows) {
                $qty = $rows->sum(fn ($r) => (float) $r->quantity);
                $val = $rows->sum(fn ($r) => (float) $r->quantity * (float) $r->unit_cost);

                return [
                    'ingredient' => $rows->first()->ingredient,
                    'purchased'  => $qty,
                    'unit_cost'  => $qty > 0 ? $val / $qty : 0,
                ];
            });

        $returned = PurchaseReturnItem::whereHas('purchaseReturn', fn ($q) => $q->where('purchase_id', $purchase->id))
            ->selectRaw('ingredient_id, SUM(quantity) as qty')
            ->groupBy('ingredient_id')
            ->pluck('qty', 'ingredient_id');

        return $purchased->map(function ($row, $id) use ($returned) {
            $done = (float) ($returned[$id] ?? 0);

            return $row + [
                'returned'  => $done,
                'remaining' => max(round($row['purchased'] - $done, 3), 0),
            ];
        });
    }

    private function trim($value): string
    {
        return rtrim(rtrim(number_format((float) $value, 3, '.', ''), '0'), '.') ?: '0';
    }
}
