<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use Illuminate\Http\Request;

class LowStockController extends Controller
{
    /** স্টক ≤ reorder level (reorder level ০ হলে শুধু স্টক শেষ হলে ধরা হয়) */
    public function index(Request $request)
    {
        $base = Ingredient::where('is_active', true)
            ->where(function ($q) {
                $q->whereColumn('stock_quantity', '<=', 'reorder_level')
                  ->orWhere('stock_quantity', '<=', 0);
            });

        $outCount = (clone $base)->where('stock_quantity', '<=', 0)->count();
        $allCount = (clone $base)->count();

        $items = $base
            ->when($request->filled('search'), fn ($q) => $q->where('name', 'like', '%'.$request->input('search').'%'))
            ->when($request->input('filter') === 'out', fn ($q) => $q->where('stock_quantity', '<=', 0))
            ->orderByRaw('stock_quantity <= 0 desc')
            ->orderByRaw('(reorder_level - stock_quantity) desc')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('low_stock.index', compact('items', 'outCount', 'allCount'));
    }
}
