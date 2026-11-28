<?php

namespace App\Http\Controllers;

use App\Http\Requests\SalesReturnRequest;
use App\Models\Ingredient;
use App\Models\Order;
use App\Models\SalesReturn;
use App\Models\SalesReturnItem;
use DomainException;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SalesReturnController extends Controller
{
    public function index(Request $request)
    {
        $query = SalesReturn::with('order')
            ->when($request->filled('search'), function ($q) use ($request): void {
                $term = '%'.$request->input('search').'%';
                $q->where(fn ($w) => $w->where('return_no', 'like', $term)
                    ->orWhereHas('order', fn ($o) => $o->where('order_no', 'like', $term)));
            })
            ->when($request->filled('reason'), fn ($q) => $q->where('reason', $request->input('reason')))
            ->when($request->filled('from'), fn ($q) => $q->whereDate('created_at', '>=', $request->input('from')))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('created_at', '<=', $request->input('to')));

        $totalRefund = (float) (clone $query)->sum('refund_amount');
        $returns     = $query->orderByDesc('id')->paginate(10)->withQueryString();

        return view('sales_returns.index', compact('returns', 'totalRefund'));
    }

    /** ধাপ ১: অর্ডার নম্বর লেখা। ধাপ ২ (?order=ID): আইটেম ও পরিমাণ */
    public function create(Request $request)
    {
        if ($request->filled('order_no')) {
            $order = Order::where('order_no', trim($request->input('order_no')))->first();

            if (! $order) {
                return back()->withInput()->with('error', 'No order found with that number.');
            }
            if ($order->status !== 'completed') {
                return back()->withInput()->with('error', 'Only completed orders can be returned. This order is "'.Order::STATUSES[$order->status].'".');
            }

            return redirect()->route('sales-returns.create', ['order' => $order->id]);
        }

        $order = null;
        $lines = collect();

        if ($request->filled('order')) {
            $order = Order::with('table')->findOrFail($request->input('order'));
            $lines = $this->lines($order);
        }

        return view('sales_returns.create', compact('order', 'lines'));
    }

    public function store(SalesReturnRequest $request)
    {
        try {
            $return = DB::transaction(function () use ($request) {
                $order = Order::whereKey($request->input('order_id'))->lockForUpdate()->firstOrFail();

                if ($order->status !== 'completed') {
                    throw ValidationException::withMessages(['items' => 'Only completed orders can be returned.']);
                }

                $lines = $this->lines($order);
                $rows  = [];

                foreach ($request->input('items', []) as $orderItemId => $qty) {
                    $qty = (int) $qty;
                    if ($qty <= 0) {
                        continue;
                    }

                    $line = $lines->get((int) $orderItemId);
                    if (! $line) {
                        throw ValidationException::withMessages(['items' => 'An item in the form is not part of this order.']);
                    }
                    if ($qty > $line['remaining']) {
                        throw ValidationException::withMessages(['items' => 'You can return at most '.$line['remaining'].' of "'.$line['item']->item_name.'".']);
                    }

                    $rows[] = ['line' => $line, 'qty' => $qty];
                }

                if (empty($rows)) {
                    throw ValidationException::withMessages(['items' => 'Enter a return quantity for at least one item.']);
                }

                // ফেরত অংশের টাকার হিসাব
                $subtotal = round(collect($rows)->sum(fn ($r) => $r['qty'] * (float) $r['line']['item']->unit_price), 2);

                $prev = SalesReturn::where('order_id', $order->id);
                $fullyReturned = $lines->sum('remaining') === collect($rows)->sum('qty');

                if ($fullyReturned) {
                    // শেষ ফেরতে অর্ডারের বাকি ছাড় ও VAT পুরোটা ধরা হয়, যাতে পয়সার গরমিল না হয়
                    $discount = round((float) $order->discount_amount - (float) (clone $prev)->sum('discount_amount'), 2);
                    $vat      = round((float) $order->vat_amount - (float) (clone $prev)->sum('vat_amount'), 2);
                } else {
                    $ratio    = (float) $order->subtotal > 0 ? $subtotal / (float) $order->subtotal : 0;
                    $discount = round((float) $order->discount_amount * $ratio, 2);
                    $vat      = round(($subtotal - $discount) * (float) $order->vat_rate / 100, 2);
                }

                $refund = round($subtotal - $discount + $vat, 2);

                $restock = (bool) $request->boolean('restock');
                $back    = [];     // ingredient_id => পরিমাণ
                $items   = [];

                foreach ($rows as $r) {
                    $item = $r['line']['item'];
                    $restocked = null;

                    if ($restock) {
                        $restocked = [];
                        foreach ($item->consumption ?? [] as $c) {
                            $amount = round((float) $c['quantity'] * $r['qty'] / max((int) $item->quantity, 1), 3);
                            $restocked[] = ['ingredient_id' => $c['ingredient_id'], 'quantity' => $amount];
                            $back[$c['ingredient_id']] = ($back[$c['ingredient_id']] ?? 0) + $amount;
                        }
                    }

                    $items[] = [
                        'order_item_id' => $item->id,
                        'item_name'     => $item->item_name,
                        'quantity'      => $r['qty'],
                        'unit_price'    => $item->unit_price,
                        'unit_cost'     => $item->unit_cost,
                        'line_total'    => round($r['qty'] * (float) $item->unit_price, 2),
                        'restocked'     => $restocked,
                    ];
                }

                if ($back) {
                    $ingredients = Ingredient::withTrashed()->whereIn('id', array_keys($back))->lockForUpdate()->get();
                    foreach ($ingredients as $ing) {
                        $ing->update(['stock_quantity' => round((float) $ing->stock_quantity + $back[$ing->id], 3)]);
                    }
                }

                $return = SalesReturn::create([
                    'order_id'        => $order->id,
                    'subtotal'        => $subtotal,
                    'discount_amount' => $discount,
                    'vat_amount'      => $vat,
                    'refund_amount'   => $refund,
                    'refund_method'   => $request->input('refund_method'),
                    'reason'          => $request->input('reason'),
                    'restock'         => $restock,
                    'note'            => $request->input('note'),
                    'user_id'         => auth()->id(),
                ]);
                $return->update(['return_no' => 'SRT-'.str_pad((string) $return->id, 6, '0', STR_PAD_LEFT)]);
                $return->items()->createMany($items);

                return $return;
            });
        } catch (DomainException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->route('sales-returns.show', $return)->with('success', 'Return saved. Refund ৳'.number_format($return->refund_amount, 2).'.');
    }

    public function show(SalesReturn $salesReturn)
    {
        $salesReturn->load(['order', 'items', 'user']);

        return view('sales_returns.show', ['return' => $salesReturn]);
    }

    public function destroy(SalesReturn $salesReturn)
    {
        try {
            DB::transaction(function () use ($salesReturn): void {
                $salesReturn->load('items');

                // স্টকে ফেরত গিয়ে থাকলে আবার কমাতে হবে
                $take = [];
                foreach ($salesReturn->items as $item) {
                    foreach ($item->restocked ?? [] as $c) {
                        $take[$c['ingredient_id']] = ($take[$c['ingredient_id']] ?? 0) + (float) $c['quantity'];
                    }
                }

                if ($take) {
                    $ingredients = Ingredient::withTrashed()->whereIn('id', array_keys($take))->lockForUpdate()->get();
                    foreach ($ingredients as $ing) {
                        if ((float) $ing->stock_quantity < $take[$ing->id] - 0.0005) {
                            throw new DomainException('Cannot delete: "'.$ing->name.'" stock was already used after this return was restocked.');
                        }
                    }
                    foreach ($ingredients as $ing) {
                        $ing->update(['stock_quantity' => round((float) $ing->stock_quantity - $take[$ing->id], 3)]);
                    }
                }

                $salesReturn->delete();
            });
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('sales-returns.index')->with('success', 'Return deleted.');
    }

    /** অর্ডারের প্রতিটি আইটেমে কত বিক্রি, কত ফেরত, কত ফেরতযোগ্য */
    private function lines(Order $order): Collection
    {
        $returned = SalesReturnItem::whereHas('salesReturn', fn ($q) => $q->where('order_id', $order->id))
            ->selectRaw('order_item_id, SUM(quantity) as qty')
            ->groupBy('order_item_id')
            ->pluck('qty', 'order_item_id');

        return $order->items()->get()->keyBy('id')->map(function ($item, $id) use ($returned) {
            $done = (int) ($returned[$id] ?? 0);

            return [
                'item'      => $item,
                'returned'  => $done,
                'remaining' => max((int) $item->quantity - $done, 0),
            ];
        });
    }
}
