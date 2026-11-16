<?php

namespace App\Http\Controllers;

use App\Http\Requests\MenuItemRequest;
use App\Models\Category;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MenuItemController extends Controller
{
    public function index(Request $request)
    {
        $items = MenuItem::with('category')
            ->when($request->filled('search'), fn ($q) => $q->where('name', 'like', '%' . $request->search . '%'))
            ->when($request->filled('category'), fn ($q) => $q->where('category_id', $request->category))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        $categories = Category::orderBy('name')->get();

        return view('menu_items.index', compact('items', 'categories'));
    }

    public function create()
    {
        return view('menu_items.create', [
            'menuItem'   => new MenuItem(['is_active' => true]),
            'categories' => Category::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function store(MenuItemRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('menu-items', 'public');
        }

        MenuItem::create($data);

        return redirect()->route('menu-items.index')->with('success', 'Menu item created.');
    }

    public function edit(MenuItem $menuItem)
    {
        return view('menu_items.edit', [
            'menuItem'   => $menuItem,
            // active ক্যাটাগরি + এই আইটেমের বর্তমান ক্যাটাগরি (inactive হলেও)
            'categories' => Category::where('is_active', true)
                                ->orWhere('id', $menuItem->category_id)
                                ->orderBy('name')->get(),
        ]);
    }

    public function update(MenuItemRequest $request, MenuItem $menuItem)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            if ($menuItem->image) {
                Storage::disk('public')->delete($menuItem->image);
            }
            $data['image'] = $request->file('image')->store('menu-items', 'public');
        } else {
            unset($data['image']); // নতুন ছবি না দিলে পুরনোটা থাকবে
        }

        $menuItem->update($data);

        return redirect()->route('menu-items.index')->with('success', 'Menu item updated.');
    }

    public function destroy(MenuItem $menuItem)
    {
        // পরে orders / recipes বানালে এখানে চেক বসাবে: অর্ডারে ব্যবহার হলে delete আটকাবে।
        $menuItem->delete(); // soft delete: পুরনো অর্ডারের ডেটা ঠিক থাকে

        return redirect()->route('menu-items.index')->with('success', 'Menu item deleted.');
    }
}
