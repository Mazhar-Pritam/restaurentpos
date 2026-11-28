@extends('Layouts.master')

@section('title', 'Stock adjustments')

@section('content')
  @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  <div class="page-head">
    <div>
      <h1>Stock adjustments</h1>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb small mb-0">
          <li class="breadcrumb-item">Inventory</li>
          <li class="breadcrumb-item active" aria-current="page">Adjustments &amp; wastage</li>
        </ol>
      </nav>
    </div>
    @can('stock_adjustments.create')
      <a class="btn btn-accent" href="{{ route('stock-adjustments.create') }}">
        <i class="bi bi-plus-lg"></i> New adjustment
      </a>
    @endcan
  </div>

  <div class="card-x">
    <div class="card-h">
      <span>History · loss value ৳{{ number_format($lossValue, 2) }}</span>
      <form class="d-flex flex-wrap gap-2" method="GET" action="{{ route('stock-adjustments.index') }}">
        <select class="form-select" name="ingredient_id" aria-label="Ingredient">
          <option value="">All ingredients</option>
          @foreach ($ingredients as $ingredient)
            <option value="{{ $ingredient->id }}" @selected((string) request('ingredient_id') === (string) $ingredient->id)>{{ $ingredient->name }}</option>
          @endforeach
        </select>
        <select class="form-select" name="direction" aria-label="Type">
          <option value="">Add &amp; remove</option>
          <option value="out" @selected(request('direction') === 'out')>Removed</option>
          <option value="in" @selected(request('direction') === 'in')>Added</option>
        </select>
        <select class="form-select" name="reason" aria-label="Reason">
          <option value="">All reasons</option>
          @foreach ($allReasons as $key => $label)
            <option value="{{ $key }}" @selected(request('reason') === $key)>{{ $label }}</option>
          @endforeach
        </select>
        <input class="form-control" type="date" name="from" value="{{ request('from') }}" aria-label="From date">
        <input class="form-control" type="date" name="to" value="{{ request('to') }}" aria-label="To date">
        <button class="btn btn-light" type="submit"><i class="bi bi-search"></i> Filter</button>
        @if (request()->hasAny(['ingredient_id', 'direction', 'reason', 'from', 'to']))
          <a class="btn btn-light" href="{{ route('stock-adjustments.index') }}">Reset</a>
        @endif
      </form>
    </div>

    <div class="table-responsive">
      <table class="table table-x align-middle mb-0">
        <thead>
          <tr>
            <th scope="col">Date</th>
            <th scope="col">Ingredient</th>
            <th scope="col">Change</th>
            <th scope="col">Stock</th>
            <th scope="col">Reason</th>
            <th scope="col" class="text-end">Value</th>
            <th scope="col">By</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($adjustments as $row)
            <tr>
              <td>
                {{ $row->created_at->format('d M Y') }}
                <div class="small text-muted">{{ $row->created_at->format('h:i A') }}</div>
              </td>
              <td>{{ $row->ingredient?->name ?? '—' }}</td>
              <td class="{{ $row->direction === 'out' ? 'text-danger' : 'text-success' }}">
                {{ $row->direction === 'out' ? '−' : '+' }}{{ rtrim(rtrim(number_format($row->quantity, 3, '.', ''), '0'), '.') }} {{ $row->ingredient?->unit }}
              </td>
              <td class="small text-muted">
                {{ rtrim(rtrim(number_format($row->stock_before, 3, '.', ''), '0'), '.') }}
                → {{ rtrim(rtrim(number_format($row->stock_after, 3, '.', ''), '0'), '.') }}
              </td>
              <td>
                {{ \App\Models\StockAdjustment::reasonLabel($row->direction, $row->reason) }}
                @if ($row->note)
                  <div class="small text-muted">{{ $row->note }}</div>
                @endif
              </td>
              <td class="text-end">৳{{ number_format($row->quantity * $row->unit_cost, 2) }}</td>
              <td>{{ $row->user?->name ?? '—' }}</td>
            </tr>
          @empty
            <tr><td colspan="7" class="text-center text-muted py-4">No adjustments yet.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if ($adjustments->hasPages())
      <div class="card-b">{{ $adjustments->links('pagination::bootstrap-5') }}</div>
    @endif
  </div>
@endsection
