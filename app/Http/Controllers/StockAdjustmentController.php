<?php

namespace App\Http\Controllers;

use App\Http\Requests\StockAdjustmentRequest;
use App\Models\Ingredient;
use App\Models\StockAdjustment;
use App\Services\StockAdjustmentService;
use DomainException;
use Illuminate\Http\Request;

class StockAdjustmentController extends Controller
{
    public function __construct(private StockAdjustmentService $service)
    {
    }

    public function index(Request $request)
    {
        $query = StockAdjustment::with(['ingredient', 'user'])
            ->when($request->filled('ingredient_id'), fn ($q) => $q->where('ingredient_id', $request->input('ingredient_id')))
            ->when($request->filled('direction'), fn ($q) => $q->where('direction', $request->input('direction')))
            ->when($request->filled('reason'), fn ($q) => $q->where('reason', $request->input('reason')))
            ->when($request->filled('from'), fn ($q) => $q->whereDate('created_at', '>=', $request->input('from')))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('created_at', '<=', $request->input('to')));

        // কমানো স্টকের মোট ক্ষতি (quantity × তখনকার গড় খরচ)
        $lossValue = (float) (clone $query)->where('direction', 'out')
            ->where('reason', '!=', 'count_correction')
            ->sum(\Illuminate\Support\Facades\DB::raw('quantity * unit_cost'));

        $adjustments = $query->orderByDesc('id')->paginate(15)->withQueryString();

        return view('stock_adjustments.index', [
            'adjustments' => $adjustments,
            'lossValue'   => $lossValue,
            'ingredients' => Ingredient::withTrashed()->orderBy('name')->get(['id', 'name']),
            'allReasons'  => collect(StockAdjustment::REASONS['out'])->union(StockAdjustment::REASONS['in']),
        ]);
    }

    public function create()
    {
        return view('stock_adjustments.create', [
            'ingredients' => Ingredient::where('is_active', true)->orderBy('name')->get(),
            'reasons'     => StockAdjustment::REASONS,
        ]);
    }

    public function store(StockAdjustmentRequest $request)
    {
        try {
            $this->service->create($request->validated(), auth()->id());
        } catch (DomainException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->route('stock-adjustments.index')->with('success', 'Stock adjusted.');
    }
}
