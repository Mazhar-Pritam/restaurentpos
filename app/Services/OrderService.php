<?php

namespace App\Services;

use App\Models\Ingredient;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\RestaurantTable;
use App\Models\Setting;
use DomainException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * অর্ডারের সব হিসাব সার্ভারে হয় (ব্রাউজারের টোটাল বিশ্বাস করা হয় না):
 *   সাবটোটাল − ডিসকাউন্ট = করযোগ্য অঙ্ক;  VAT = করযোগ্য অঙ্ক × হার;  মোট = করযোগ্য অঙ্ক + VAT
 * অর্ডার সেভ হলে রেসিপি অনুযায়ী ইনগ্রেডিয়েন্টের স্টক কমে। void করলে ফেরত আসে।
 */
class OrderService
{
    public function create(array $data, ?int $userId): Order
    {
        return DB::transaction(function () use ($data, $userId) {
            $rows = collect($data['items']);

            $menuItems = MenuItem::with('recipes')
                ->whereIn('id', $rows->pluck('menu_item_id'))
                ->get()->keyBy('id');

            $ingredientIds = $menuItems->flatMap(fn ($m) => $m->recipes->pluck('ingredient_id'))->unique()->values();
            $ingredients   = Ingredient::withTrashed()->whereIn('id', $ingredientIds)->lockForUpdate()->get()->keyBy('id');

            $lines    = [];
            $need     = [];   // ingredient_id => মোট কতটুকু লাগবে
            $subtotal = 0.0;

            foreach ($rows as $row) {
                $menuItem = $menuItems[$row['menu_item_id']];
                $qty      = (int) $row['quantity'];
                $unitCost = 0.0;
                $consumed = [];

                foreach ($menuItem->recipes as $recipe) {
                    $perUnit = (float) $recipe->quantity;
                    $ing     = $ingredients[$recipe->ingredient_id];

                    $unitCost += $perUnit * (float) $ing->average_cost;
                    $consumed[] = ['ingredient_id' => $ing->id, 'quantity' => round($perUnit * $qty, 3)];
                    $need[$ing->id] = ($need[$ing->id] ?? 0) + $perUnit * $qty;
                }

                $lineTotal = round((float) $menuItem->price * $qty, 2);
                $subtotal += $lineTotal;

                $lines[] = [
                    'menu_item_id' => $menuItem->id,
                    'item_name'    => $menuItem->name,
                    'quantity'     => $qty,
                    'unit_price'   => (float) $menuItem->price,
                    'unit_cost'    => round($unitCost, 4),
                    'line_total'   => $lineTotal,
                    'consumption'  => $consumed,
                ];
            }

            $subtotal = round($subtotal, 2);

            // স্টক যথেষ্ট কিনা
            foreach ($need as $id => $amount) {
                $ing = $ingredients[$id];
                if ((float) $ing->stock_quantity < $amount - 0.0005) {
                    throw new DomainException(
                        'Not enough stock of "'.$ing->name.'" (available '.$this->qty($ing->stock_quantity).' '.$ing->unit
                        .', needed '.$this->qty($amount).' '.$ing->unit.').'
                    );
                }
            }

            // ডিসকাউন্ট
            $discountType   = $data['discount_type'] ?? null;
            $discountValue  = (float) ($data['discount_value'] ?? 0);
            $discountAmount = 0.0;

            if ($discountType === 'percent') {
                if ($discountValue > 100) {
                    throw ValidationException::withMessages(['discount_value' => 'Discount cannot be more than 100%.']);
                }
                $discountAmount = round($subtotal * $discountValue / 100, 2);
            } elseif ($discountType === 'flat') {
                if ($discountValue > $subtotal + 0.005) {
                    throw ValidationException::withMessages(['discount_value' => 'Discount cannot be more than the subtotal (৳'.number_format($subtotal, 2).').']);
                }
                $discountAmount = round($discountValue, 2);
            } else {
                $discountType  = null;
                $discountValue = 0;
            }

            $taxable   = round($subtotal - $discountAmount, 2);
            $vatRate   = (float) Setting::get('vat_rate', 0);
            $vatAmount = round($taxable * $vatRate / 100, 2);
            $total     = round($taxable + $vatAmount, 2);

            // স্টক কমানো (গড় খরচ একই থাকে)
            foreach ($need as $id => $amount) {
                $ing = $ingredients[$id];
                $ing->update(['stock_quantity' => max(0, round((float) $ing->stock_quantity - $amount, 3))]);
            }

            $order = Order::create([
                'type'            => $data['type'],
                'table_id'        => $data['type'] === 'dine_in' ? ($data['table_id'] ?? null) : null,
                'customer_id'     => $data['customer_id'] ?? null,
                'subtotal'        => $subtotal,
                'discount_type'   => $discountType,
                'discount_value'  => $discountValue,
                'discount_amount' => $discountAmount,
                'vat_rate'        => $vatRate,
                'vat_amount'      => $vatAmount,
                'total'           => $total,
                'payment_method'  => $data['payment_method'],
                'status'          => 'cooking',
                'note'            => $data['note'] ?? null,
                'user_id'         => $userId,
            ]);

            $order->update(['order_no' => 'ORD-'.str_pad((string) $order->id, 6, '0', STR_PAD_LEFT)]);
            $order->items()->createMany($lines);

            if ($order->table_id) {
                RestaurantTable::whereKey($order->table_id)->update(['status' => 'occupied']);
            }

            return $order;
        });
    }

    public function updateStatus(Order $order, string $status): void
    {
        DB::transaction(function () use ($order, $status): void {
            $order->update(['status' => $status]);
            if ($status === 'completed') {
                $this->releaseTable($order);
            }
        });
    }

    /** অর্ডার বাতিল: স্টক ফেরত, টেবিল খালি, অর্ডার soft delete */
    public function void(Order $order): void
    {
        DB::transaction(function () use ($order): void {
            $order->load('items');

            $back = [];
            foreach ($order->items as $item) {
                foreach ($item->consumption ?? [] as $c) {
                    $back[$c['ingredient_id']] = ($back[$c['ingredient_id']] ?? 0) + (float) $c['quantity'];
                }
            }

            if ($back) {
                $ingredients = Ingredient::withTrashed()->whereIn('id', array_keys($back))->lockForUpdate()->get();
                foreach ($ingredients as $ing) {
                    $ing->update(['stock_quantity' => round((float) $ing->stock_quantity + $back[$ing->id], 3)]);
                }
            }

            $order->delete();
            $this->releaseTable($order);
        });
    }

    // ওই টেবিলে আর কোনো চলমান অর্ডার না থাকলে টেবিল Available করা
    private function releaseTable(Order $order): void
    {
        if (! $order->table_id) {
            return;
        }

        $stillOpen = Order::where('table_id', $order->table_id)
            ->where('id', '!=', $order->id)
            ->where('status', '!=', 'completed')
            ->exists();

        if (! $stillOpen) {
            RestaurantTable::whereKey($order->table_id)->update(['status' => 'available']);
        }
    }

    private function qty($value): string
    {
        return rtrim(rtrim(number_format((float) $value, 3, '.', ''), '0'), '.');
    }
}
