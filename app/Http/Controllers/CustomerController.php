<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerRequest;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $customers = Customer::query()
            ->when($request->filled('search'), function ($query) use ($request): void {
                $term = '%'.$request->input('search').'%';
                $query->where(function ($q) use ($term): void {
                    $q->where('name', 'like', $term)->orWhere('phone', 'like', $term);
                });
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        return view('customers.create', ['customer' => new Customer(['is_active' => true])]);
    }

    public function store(CustomerRequest $request)
    {
        Customer::create($request->validated());

        return redirect()
            ->route('customers.index')
            ->with('success', 'Customer created successfully.');
    }

    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(CustomerRequest $request, Customer $customer)
    {
        $customer->update($request->validated());

        return redirect()
            ->route('customers.index')
            ->with('success', 'Customer updated successfully.');
    }

    public function destroy(Customer $customer)
    {
        // অন্য টেবিলে ব্যবহার হলে delete আটকাবে (টেবিলগুলো বানানোর পর নিজে থেকে চালু হবে)
        if ((Schema::hasTable('orders') && DB::table('orders')->where('customer_id', $customer->id)->exists())
            || (Schema::hasTable('reservations') && DB::table('reservations')->where('customer_id', $customer->id)->exists())) {
            return redirect()
                ->route('customers.index')
                ->with('error', 'This customer cannot be deleted because it has orders or it has reservations.');
        }

        $customer->delete();

        return redirect()
            ->route('customers.index')
            ->with('success', 'Customer deleted successfully.');
    }
}
