<?php

namespace App\Services;

use App\Models\Ingredient;
use DomainException;

/**
 * Purchase এর কারণে ইনগ্রেডিয়েন্টের স্টক ও গড় খরচ (weighted average) বদলায়।
 * একটাই মেথড: আগের আইটেমগুলো উল্টে দিয়ে নতুন আইটেমগুলো যোগ করে।
 *   নতুন purchase  : apply([], $newItems)
 *   edit           : apply($oldItems, $newItems)
 *   delete         : apply($oldItems, [])
 * সবকিছু DB::transaction এর ভেতর থেকে ডাকতে হবে (lockForUpdate এর জন্য)।
 * আইটেম = ['ingredient_id' => .., 'quantity' => .., 'unit_cost' => ..]
 */
class PurchaseStockService
{
    public function apply(array $old, array $new): void
    {
        $ids = collect($old)->pluck('ingredient_id')
            ->merge(collect($new)->pluck('ingredient_id'))
            ->unique()->values();

        if ($ids->isEmpty()) {
            return;
        }

        $ingredients = Ingredient::withTrashed()->whereIn('id', $ids)->lockForUpdate()->get()->keyBy('id');

        // প্রতিটি ইনগ্রেডিয়েন্টের স্টক ও মোট মূল্য (stock × avg) মেমোরিতে হিসাব করি
        $state = [];
        foreach ($ingredients as $id => $ing) {
            $stock = (float) $ing->stock_quantity;
            $state[$id] = [
                'stock' => $stock,
                'value' => $stock * (float) $ing->average_cost,
                'avg'   => (float) $ing->average_cost,
            ];
        }

        foreach ($old as $row) {
            $id = $row['ingredient_id'];
            $state[$id]['stock'] -= (float) $row['quantity'];
            $state[$id]['value'] -= (float) $row['quantity'] * (float) $row['unit_cost'];
        }

        foreach ($new as $row) {
            $id = $row['ingredient_id'];
            $state[$id]['stock'] += (float) $row['quantity'];
            $state[$id]['value'] += (float) $row['quantity'] * (float) $row['unit_cost'];
        }

        foreach ($state as $id => $s) {
            if ($s['stock'] < -0.0005) {
                throw new DomainException(
                    'Not enough stock of "'.$ingredients[$id]->name.'". Part of this purchase was already used, '
                    .'so the stock would go below zero.'
                );
            }

            $stock = max(0, round($s['stock'], 3));
            $avg   = $stock > 0 ? max(0, $s['value'] / $stock) : $s['avg'];

            $ingredients[$id]->update([
                'stock_quantity' => $stock,
                'average_cost'   => round($avg, 4),
            ]);
        }
    }
}
