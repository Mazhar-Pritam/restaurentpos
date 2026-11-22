<?php

namespace App\Http\Controllers;

use App\Http\Requests\RestaurantTableRequest;
use App\Models\RestaurantTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RestaurantTableController extends Controller
{
    public function index(Request $request)
    {
        $tables = RestaurantTable::query()
            ->when($request->filled('search'), function ($query) use ($request): void {
                $query->where('name', 'like', '%'.$request->input('search').'%');
            })
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->input('status')))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('tables.index', compact('tables'));
    }

    public function create()
    {
        return view('tables.create', [
            'table' => new RestaurantTable(['is_active' => true, 'capacity' => 2, 'status' => 'available']),
        ]);
    }

    public function store(RestaurantTableRequest $request)
    {
        RestaurantTable::create($request->validated());

        return redirect()
            ->route('tables.index')
            ->with('success', 'Table created successfully.');
    }

    public function edit(RestaurantTable $table)
    {
        return view('tables.edit', compact('table'));
    }

    public function update(RestaurantTableRequest $request, RestaurantTable $table)
    {
        $table->update($request->validated());

        return redirect()
            ->route('tables.index')
            ->with('success', 'Table updated successfully.');
    }

    public function destroy(RestaurantTable $table)
    {
        // অর্ডার বা রিজার্ভেশনে ব্যবহার হলে delete আটকাবে (ওই টেবিল বানানোর পর নিজে থেকে চালু হবে)
        if ((Schema::hasTable('orders') && DB::table('orders')->where('table_id', $table->id)->exists())
            || (Schema::hasTable('reservations') && DB::table('reservations')->where('table_id', $table->id)->exists())) {
            return redirect()
                ->route('tables.index')
                ->with('error', 'This table cannot be deleted because it has orders or reservations. Mark it Inactive instead.');
        }

        $table->delete();

        return redirect()
            ->route('tables.index')
            ->with('success', 'Table deleted successfully.');
    }
}
