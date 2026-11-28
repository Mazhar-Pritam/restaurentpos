@extends('Layouts.master')

@section('title', 'Purchase returns')

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
      <h1>Purchase returns</h1>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb small mb-0">
          <li class="breadcrumb-item">Purchasing</li>
          <li class="breadcrumb-item active" aria-current="page">Returns</li>
        </ol>
      </nav>
    </div>
    @can('purchase_returns.create')
      <a class="btn btn-accent" href="{{ route('purchase-returns.create') }}">
        <i class="bi bi-plus-lg"></i> New return
      </a>
    @endcan
  </div>

  <div class="card-x">
    <div class="card-h">
      <span>Returns to suppliers</span>
      <form class="d-flex flex-wrap gap-2" method="GET" action="{{ route('purchase-returns.index') }}">
        <select class="form-select" name="supplier" aria-label="Supplier">
          <option value="">All suppliers</option>
          @foreach ($suppliers as $supplier)
            <option value="{{ $supplier->id }}" @selected((string) request('supplier') === (string) $supplier->id)>{{ $supplier->name }}</option>
          @endforeach
        </select>
        <input class="form-control" type="date" name="from" value="{{ request('from') }}" aria-label="From date">
        <input class="form-control" type="date" name="to" value="{{ request('to') }}" aria-label="To date">
        <input class="form-control" type="search" name="search" value="{{ request('search') }}" placeholder="Return or invoice no" aria-label="Search">
        <button class="btn btn-light" type="submit"><i class="bi bi-search"></i> Search</button>
        @if (request()->hasAny(['search', 'supplier', 'from', 'to']))
          <a class="btn btn-light" href="{{ route('purchase-returns.index') }}">Reset</a>
        @endif
      </form>
    </div>

    <div class="table-responsive">
      <table class="table table-x align-middle mb-0">
        <thead>
          <tr>
            <th scope="col">Return no</th>
            <th scope="col">Date</th>
            <th scope="col">Invoice</th>
            <th scope="col">Supplier</th>
            <th scope="col" class="text-end">Value</th>
            <th scope="col" class="text-end">Cash refund</th>
            <th scope="col" class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($returns as $return)
            <tr>
              <td><a href="{{ route('purchase-returns.show', $return) }}">{{ $return->return_no }}</a></td>
              <td>{{ $return->return_date->format('d M Y') }}</td>
              <td>{{ $return->purchase?->invoice_no ?? '—' }}</td>
              <td>{{ $return->supplier?->name ?? '—' }}</td>
              <td class="text-end">৳{{ number_format($return->total_amount, 2) }}</td>
              <td class="text-end">৳{{ number_format($return->refund_amount, 2) }}</td>
              <td class="text-end">
                <a class="btn btn-sm btn-light" href="{{ route('purchase-returns.show', $return) }}"><i class="bi bi-eye"></i> View</a>
                @can('purchase_returns.delete')
                  <form class="d-inline" method="POST" action="{{ route('purchase-returns.destroy', $return) }}"
                        onsubmit="return confirm('Delete this return? Stock will be added back.');">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-light text-danger" type="submit"><i class="bi bi-trash"></i> Delete</button>
                  </form>
                @endcan
              </td>
            </tr>
          @empty
            <tr><td colspan="7" class="text-center text-muted py-4">No returns yet.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if ($returns->hasPages())
      <div class="card-b">{{ $returns->links('pagination::bootstrap-5') }}</div>
    @endif
  </div>
@endsection
