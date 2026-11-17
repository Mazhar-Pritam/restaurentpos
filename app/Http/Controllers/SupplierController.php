<?php

namespace App\Http\Controllers;

use App\Http\Requests\SupplierRequest;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $suppliers = Supplier::query()
            ->when($request->filled('search'), function ($query) use ($request): void {
                $term = '%'.$request->input('search').'%';
                $query->where(function ($q) use ($term): void {
                    $q->where('name', 'like', $term)->orWhere('phone', 'like', $term);
                });
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('suppliers.create', ['supplier' => new Supplier(['is_active' => true])]);
    }

    public function store(SupplierRequest $request)
    {
        Supplier::create($request->validated());

        return redirect()
            ->route('suppliers.index')
            ->with('success', 'Supplier created successfully.');
    }

    public function edit(Supplier $supplier)
    {
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(SupplierRequest $request, Supplier $supplier)
    {
        $supplier->update($request->validated());

        return redirect()
            ->route('suppliers.index')
            ->with('success', 'Supplier updated successfully.');
    }

    public function destroy(Supplier $supplier)
    {
        // অন্য টেবিলে ব্যবহার হলে delete আটকাবে (টেবিলগুলো বানানোর পর নিজে থেকে চালু হবে)
        if ((Schema::hasTable('purchases') && DB::table('purchases')->where('supplier_id', $supplier->id)->exists())) {
            return redirect()
                ->route('suppliers.index')
                ->with('error', 'This supplier cannot be deleted because it has purchases.');
        }

        $supplier->delete();

        return redirect()
            ->route('suppliers.index')
            ->with('success', 'Supplier deleted successfully.');
    }
}
