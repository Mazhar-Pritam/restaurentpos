<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExpenseRequest;
use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->has('from') ? $request->input('from') : now()->startOfMonth()->toDateString();

        $query = Expense::query()
            ->when($from, fn ($q) => $q->whereDate('expense_date', '>=', $from))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('expense_date', '<=', $request->input('to')))
            ->when($request->filled('category'), fn ($q) => $q->where('category', $request->input('category')))
            ->when($request->filled('payment_method'), fn ($q) => $q->where('payment_method', $request->input('payment_method')))
            ->when($request->filled('search'), function ($q) use ($request): void {
                $term = '%'.$request->input('search').'%';
                $q->where(fn ($w) => $w->where('paid_to', 'like', $term)->orWhere('note', 'like', $term));
            });

        $total    = (float) (clone $query)->sum('amount');
        $expenses = $query->orderByDesc('expense_date')->orderByDesc('id')->paginate(15)->withQueryString();

        return view('expenses.index', compact('expenses', 'total', 'from'));
    }

    public function create()
    {
        return view('expenses.create', ['expense' => new Expense(['expense_date' => now(), 'payment_method' => 'cash'])]);
    }

    public function store(ExpenseRequest $request)
    {
        Expense::create($request->validated() + ['user_id' => auth()->id()]);

        return redirect()->route('expenses.index')->with('success', 'Expense saved.');
    }

    public function edit(Expense $expense)
    {
        return view('expenses.edit', compact('expense'));
    }

    public function update(ExpenseRequest $request, Expense $expense)
    {
        $expense->update($request->validated());

        return redirect()->route('expenses.index')->with('success', 'Expense updated.');
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();

        return redirect()->route('expenses.index')->with('success', 'Expense deleted.');
    }
}
