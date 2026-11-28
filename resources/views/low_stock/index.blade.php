@extends('Layouts.master')

@section('title', 'Low stock')

@section('content')
  <div class="page-head">
    <div>
      <h1>Low stock</h1>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb small mb-0">
          <li class="breadcrumb-item">Inventory</li>
          <li class="breadcrumb-item active" aria-current="page">Low stock</li>
        </ol>
      </nav>
    </div>
    @can('purchases.create')
      <a class="btn btn-accent" href="{{ route('purchases.create') }}">
        <i class="bi bi-truck"></i> New purchase
      </a>
    @endcan
  </div>

  <div class="card-x">
    <div class="card-h">
      <span>Needs restocking · {{ $allCount }} item(s), {{ $outCount }} out of stock</span>
      <form class="d-flex flex-wrap gap-2" method="GET" action="{{ route('low-stock') }}">
        <select class="form-select" name="filter" aria-label="Filter">
          <option value="">Low and out of stock</option>
          <option value="out" @selected(request('filter') === 'out')>Out of stock only</option>
        </select>
        <input class="form-control" type="search" name="search" value="{{ request('search') }}" placeholder="Search ingredient" aria-label="Search">
        <button class="btn btn-light" type="submit"><i class="bi bi-search"></i> Search</button>
        @if (request()->hasAny(['search', 'filter']))
          <a class="btn btn-light" href="{{ route('low-stock') }}">Reset</a>
        @endif
      </form>
    </div>

    <div class="table-responsive">
      <table class="table table-x align-middle mb-0">
        <thead>
          <tr>
            <th scope="col">Ingredient</th>
            <th scope="col" class="text-end">In stock</th>
            <th scope="col" class="text-end">Reorder level</th>
            <th scope="col" class="text-end">Short by</th>
            <th scope="col">Status</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($items as $item)
            @php
              $short = max((float) $item->reorder_level - (float) $item->stock_quantity, 0);
              $fmt = fn ($n) => rtrim(rtrim(number_format((float) $n, 3, '.', ''), '0'), '.') ?: '0';
            @endphp
            <tr>
              <td>{{ $item->name }}</td>
              <td class="text-end">{{ $fmt($item->stock_quantity) }} {{ $item->unit }}</td>
              <td class="text-end">{{ $fmt($item->reorder_level) }} {{ $item->unit }}</td>
              <td class="text-end">{{ $short > 0 ? $fmt($short) . ' ' . $item->unit : '—' }}</td>
              <td>
                <span class="status low">{{ (float) $item->stock_quantity <= 0 ? 'Out of stock' : 'Low' }}</span>
              </td>
            </tr>
          @empty
            <tr><td colspan="5" class="text-center text-muted py-4">All ingredients are well stocked.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if ($items->hasPages())
      <div class="card-b">{{ $items->links('pagination::bootstrap-5') }}</div>
    @endif
  </div>
@endsection
