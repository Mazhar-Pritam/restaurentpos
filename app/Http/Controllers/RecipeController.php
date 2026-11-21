<?php

namespace App\Http\Controllers;

use App\Http\Requests\RecipeRequest;
use App\Models\Ingredient;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RecipeController extends Controller
{
    public function index(Request $request)
    {
        $items = MenuItem::with(['category', 'recipes.ingredient'])
            ->whereHas('recipes')
            ->when($request->filled('search'), function ($query) use ($request): void {
                $query->where('name', 'like', '%'.$request->input('search').'%');
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('recipes.index', compact('items'));
    }

    public function create()
    {
        return view('recipes.create', [
            'menuItem'   => null,
            // রেসিপি নেই এমন active মেনু আইটেম
            'menuItems'  => MenuItem::where('is_active', true)->doesntHave('recipes')->orderBy('name')->get(),
            'ingredients' => Ingredient::where('is_active', true)->orderBy('name')->get(),
            'rows'       => [['ingredient_id' => '', 'quantity' => '']],
        ]);
    }

    public function store(RecipeRequest $request)
    {
        DB::transaction(function () use ($request): void {
            $menuItem = MenuItem::findOrFail($request->input('menu_item_id'));
            $this->saveRows($menuItem, $request->validated('items'));
        });

        return redirect()
            ->route('recipes.index')
            ->with('success', 'Recipe created successfully.');
    }

    public function edit(MenuItem $menuItem)
    {
        $menuItem->load('recipes');
        $usedIds = $menuItem->recipes->pluck('ingredient_id');

        return view('recipes.edit', [
            'menuItem'    => $menuItem,
            'menuItems'   => collect(),
            // active ইনগ্রেডিয়েন্ট + এই রেসিপিতে আগে থেকে থাকা (inactive হলেও)
            'ingredients' => Ingredient::where('is_active', true)->orWhereIn('id', $usedIds)->orderBy('name')->get(),
            'rows'        => $menuItem->recipes->map(fn ($r) => [
                'ingredient_id' => $r->ingredient_id,
                'quantity'      => rtrim(rtrim(number_format((float) $r->quantity, 3, '.', ''), '0'), '.'),
            ])->values()->all(),
        ]);
    }

    public function update(RecipeRequest $request, MenuItem $menuItem)
    {
        DB::transaction(function () use ($request, $menuItem): void {
            $menuItem->recipes()->delete();
            $this->saveRows($menuItem, $request->validated('items'));
        });

        return redirect()
            ->route('recipes.index')
            ->with('success', 'Recipe updated successfully.');
    }

    public function destroy(MenuItem $menuItem)
    {
        // শুধু রেসিপি মোছে; মেনু আইটেম ও ইনগ্রেডিয়েন্ট থেকে যায়
        $menuItem->recipes()->delete();

        return redirect()
            ->route('recipes.index')
            ->with('success', 'Recipe deleted successfully.');
    }

    private function saveRows(MenuItem $menuItem, array $items): void
    {
        foreach ($items as $row) {
            $menuItem->recipes()->create([
                'ingredient_id' => $row['ingredient_id'],
                'quantity'      => $row['quantity'],
            ]);
        }
    }
}
