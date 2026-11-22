@extends('Layouts.master')

@section('title', 'Purchases')

@section('content')
  @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif
  @if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      {{ session('error') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  <div class="page-head">
    <div>
      <h1>Purchases</h1>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb small mb-0">
          <li class="breadcrumb-item">Purchasing</li>
          <li class="breadcrumb-item active" aria-current="page">Purchases</li>
        </ol>
      </nav>
    </div>
    @can('purchases.create')
      <a class="btn btn-accent" href="{{ route('purchases.create') }}">
        <i class="bi bi-plus-lg"></i> New purchase
      </a>
    @endcan
  </div>

  <div class="card-x">
    <div class="card-h">
      <span>All purchases</span>
      <form class="d-flex flex-wrap gap-2" method="GET" action="{{ route('purchases.index') }}">
        <select class="form-select" name="supplier" aria-label="Supplier">
          <option value="">All suppliers</option>
          @foreach ($suppliers as $supplier)
            <option value="{{ $supplier->id }}" @selected((string) request('supplier') === (string) $supplier->id)>{{ $supplier->name }}</option>
          @endforeach
        </select>
        <input class="form-control" type="date" name="from" value="{{ request('from') }}" aria-label="From date">
        <input class="form-control" type="date" name="to" value="{{ request('to') }}" aria-label="To date">
        <input class="form-control" type="search" name="search" value="{{ request('search') }}"
               placeholder="Invoice or supplier" aria-label="Search">
        <button class="btn btn-light" type="submit"><i class="bi bi-search"></i> Search</button>
        @if (request()->hasAny(['search', 'supplier', 'from', 'to']))
          <a class="btn btn-light" href="{{ route('purchases.index') }}">Clear</a>
        @endif
      </form>
    </div>

    <div class="table-responsive">
      <table class="table table-x align-middle mb-0">
        <thead>
          <tr>
            <th scope="col">Invoice</th>
            <th scope="col">Date</th>
            <th scope="col">Supplier</th>
            <th scope="col">Total</th>
            <th scope="col">Paid</th>
            <th scope="col">Due</th>
            <th scope="col">Status</th>
            <th scope="col" class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($purchases as $purchase)
            <tr>
              <td>{{ $purchase->invoice_no }}</td>
              <td>{{ $purchase->purchase_date->format('d M Y') }}</td>
              <td>{{ $purchase->supplier?->name ?? '—' }}</td>
              <td>৳{{ number_format($purchase->total_amount, 2) }}</td>
              <td>৳{{ number_format($purchase->paid_amount, 2) }}</td>
              <td>৳{{ number_format($purchase->due, 2) }}</td>
              <td>
                @php($st = $purchase->payment_status)
                <span class="status {{ $st === 'Paid' ? 'paid' : ($st === 'Partial' ? 'cooking' : 'low') }}">{{ $st }}</span>
              </td>
              <td class="text-end">
                <a class="btn btn-sm btn-light" href="{{ route('purchases.show', $purchase) }}">
                  <i class="bi bi-printer"></i> View
                </a>
                @can('purchases.edit')
                  <a class="btn btn-sm btn-light" href="{{ route('purchases.edit', $purchase) }}">
                    <i class="bi bi-pencil"></i> Edit
                  </a>
                @endcan
                @can('purchases.delete')
                  <form class="d-inline" method="POST" action="{{ route('purchases.destroy', $purchase) }}"
                        onsubmit="return confirm('Delete this purchase? The stock it added will be removed.');">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-light text-danger" type="submit">
                      <i class="bi bi-trash"></i> Delete
                    </button>
                  </form>
                @endcan
              </td>
            </tr>
          @empty
            <tr><td colspan="8" class="text-center text-muted py-4">No purchases found.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if ($purchases->hasPages())
      <div class="card-b">{{ $purchases->links('pagination::bootstrap-5') }}</div>
    @endif
  </div>
@endsection
