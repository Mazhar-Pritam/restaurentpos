<?php

namespace App\Services;

use App\Models\Ingredient;
use App\Models\StockAdjustment;
use DomainException;
use Illuminate\Support\Facades\DB;

class StockAdjustmentService
{
    public function create(array $data, ?int $userId): StockAdjustment
    {
        return DB::transaction(function () use ($data, $userId) {
            $ingredient = Ingredient::whereKey($data['ingredient_id'])->lockForUpdate()->firstOrFail();

            $qty    = round((float) $data['quantity'], 3);
            $before = (float) $ingredient->stock_quantity;
            $after  = $data['direction'] === 'out' ? $before - $qty : $before + $qty;

            if ($after < -0.0005) {
                throw new DomainException(
                    'Only '.rtrim(rtrim(number_format($before, 3, '.', ''), '0'), '.').' '.$ingredient->unit
                    .' of "'.$ingredient->name.'" is in stock, so you cannot remove '.rtrim(rtrim(number_format($qty, 3, '.', ''), '0'), '.').'.'
                );
            }

            $after = max($after, 0);

            // গড় খরচ বদলায় না: কমলে/বাড়লে বর্তমান average_cost-এই ধরা হয়
            $ingredient->update(['stock_quantity' => $after]);

            return StockAdjustment::create([
                'ingredient_id' => $ingredient->id,
                'direction'     => $data['direction'],
                'quantity'      => $qty,
                'reason'        => $data['reason'],
                'unit_cost'     => $ingredient->average_cost,
                'stock_before'  => $before,
                'stock_after'   => $after,
                'note'          => $data['note'] ?? null,
                'user_id'       => $userId,
            ]);
        });
    }
}
