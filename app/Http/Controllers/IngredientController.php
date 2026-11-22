<?php

namespace App\Http\Controllers;

use App\Http\Requests\IngredientRequest;
use App\Models\Ingredient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class IngredientController extends Controller
{
    public function index(Request $request)
    {
        $ingredients = Ingredient::query()
            ->when($request->filled('search'), function ($query) use ($request): void {
                $query->where('name', 'like', '%'.$request->input('search').'%');
            })
            ->when($request->input('stock') === 'low', function ($query): void {
                $query->whereColumn('stock_quantity', '<=', 'reorder_level');
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('ingredients.index', compact('ingredients'));
    }

    public function create()
    {
        return view('ingredients.create', [
            'ingredient' => new Ingredient(['is_active' => true, 'stock_quantity' => 0, 'average_cost' => 0]),
        ]);
    }

    public function store(IngredientRequest $request)
    {
        $data = $request->validated();
        $data['stock_quantity'] = $data['stock_quantity'] ?? 0;
        $data['average_cost']   = $data['average_cost'] ?? 0;

        Ingredient::create($data);

        return redirect()
            ->route('ingredients.index')
            ->with('success', 'Ingredient created successfully.');
    }

    public function edit(Ingredient $ingredient)
    {
        return view('ingredients.edit', compact('ingredient'));
    }

    public function update(IngredientRequest $request, Ingredient $ingredient)
    {
        // স্টক ও গড় খরচ এখানে বদলানো যায় না; এগুলো purchase / stock adjustment থেকে বদলাবে
        $ingredient->update($request->safe()->except(['stock_quantity', 'average_cost']));

        return redirect()
            ->route('ingredients.index')
            ->with('success', 'Ingredient updated successfully.');
    }

   public function destroy(Ingredient $ingredient)
{
    // রেসিপি বা purchase এ ব্যবহার হলে delete আটকাবে
    $usedInRecipe = Schema::hasTable('recipes')
        && DB::table('recipes')->where('ingredient_id', $ingredient->id)->exists();

    $usedInPurchase = Schema::hasTable('purchase_items')
        && DB::table('purchase_items')
            ->join('purchases', 'purchases.id', '=', 'purchase_items.purchase_id')
            ->whereNull('purchases.deleted_at')
            ->where('purchase_items.ingredient_id', $ingredient->id)
            ->exists();

    if ($usedInRecipe || $usedInPurchase) {
        return redirect()
            ->route('ingredients.index')
            ->with('error', 'This ingredient cannot be deleted because it is used in a recipe or a purchase.');
    }

    $ingredient->delete();

    return redirect()
        ->route('ingredients.index')
        ->with('success', 'Ingredient deleted successfully.');
}
}   