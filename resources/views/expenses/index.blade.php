@extends('Layouts.master')

@section('title', 'Expenses')

@section('content')
  @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  <div class="page-head">
    <div>
      <h1>Expenses</h1>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb small mb-0">
          <li class="breadcrumb-item">Money</li>
          <li class="breadcrumb-item active" aria-current="page">Expenses</li>
        </ol>
      </nav>
    </div>
    @can('expenses.create')
      <a class="btn btn-accent" href="{{ route('expenses.create') }}"><i class="bi bi-plus-lg"></i> New expense</a>
    @endcan
  </div>

  <div class="card-x">
    <div class="card-h">
      <span>Total ৳{{ number_format($total, 2) }}</span>
      <form class="d-flex flex-wrap gap-2" method="GET" action="{{ route('expenses.index') }}">
        <select class="form-select" name="category" aria-label="Category">
          <option value="">All categories</option>
          @foreach (\App\Models\Expense::CATEGORIES as $value => $label)
            <option value="{{ $value }}" @selected(request('category') === $value)>{{ $label }}</option>
          @endforeach
        </select>
        <select class="form-select" name="payment_method" aria-label="Paid by">
          <option value="">All methods</option>
          @foreach (\App\Models\Expense::PAYMENT_METHODS as $value => $label)
            <option value="{{ $value }}" @selected(request('payment_method') === $value)>{{ $label }}</option>
          @endforeach
        </select>
        <input class="form-control" type="date" name="from" value="{{ $from }}" aria-label="From date">
        <input class="form-control" type="date" name="to" value="{{ request('to') }}" aria-label="To date">
        <input class="form-control" type="search" name="search" value="{{ request('search') }}" placeholder="Paid to or note" aria-label="Search">
        <button class="btn btn-light" type="submit"><i class="bi bi-search"></i> Filter</button>
        <a class="btn btn-light" href="{{ route('expenses.index', ['from' => '']) }}">All dates</a>
      </form>
    </div>

    <div class="table-responsive">
      <table class="table table-x align-middle mb-0">
        <thead>
          <tr>
            <th scope="col">Date</th>
            <th scope="col">Category</th>
            <th scope="col">Paid to</th>
            <th scope="col">Paid by</th>
            <th scope="col" class="text-end">Amount</th>
            <th scope="col" class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($expenses as $expense)
            <tr>
              <td>{{ $expense->expense_date->format('d M Y') }}</td>
              <td>
                {{ \App\Models\Expense::CATEGORIES[$expense->category] ?? $expense->category }}
                @if ($expense->note)
                  <div class="small text-muted">{{ $expense->note }}</div>
                @endif
              </td>
              <td>{{ $expense->paid_to ?: '—' }}</td>
              <td>{{ \App\Models\Expense::PAYMENT_METHODS[$expense->payment_method] ?? $expense->payment_method }}</td>
              <td class="text-end">৳{{ number_format($expense->amount, 2) }}</td>
              <td class="text-end">
                @can('expenses.edit')
                  <a class="btn btn-sm btn-light" href="{{ route('expenses.edit', $expense) }}"><i class="bi bi-pencil"></i> Edit</a>
                @endcan
                @can('expenses.delete')
                  <form class="d-inline" method="POST" action="{{ route('expenses.destroy', $expense) }}" onsubmit="return confirm('Delete this expense?');">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-light text-danger" type="submit"><i class="bi bi-trash"></i> Delete</button>
                  </form>
                @endcan
              </td>
            </tr>
          @empty
            <tr><td colspan="6" class="text-center text-muted py-4">No expenses found.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if ($expenses->hasPages())
      <div class="card-b">{{ $expenses->links('pagination::bootstrap-5') }}</div>
    @endif
  </div>
@endsection
