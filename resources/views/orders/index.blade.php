@extends('Layouts.master')

@section('title', 'Orders')

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
      <h1>All orders</h1>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb small mb-0">
          <li class="breadcrumb-item">Orders</li>
          <li class="breadcrumb-item active" aria-current="page">All orders</li>
        </ol>
      </nav>
    </div>
    @can('orders.create')
      <a class="btn btn-accent" href="{{ route('pos') }}">
        <i class="bi bi-receipt"></i> New order
      </a>
    @endcan
  </div>

  <div class="card-x">
    <div class="card-h">
      <span>Orders · total ৳{{ number_format($totalSales, 2) }}</span>
      <form class="d-flex flex-wrap gap-2" method="GET" action="{{ route('orders.index') }}">
        <select class="form-select" name="type" aria-label="Type">
          <option value="">All types</option>
          @foreach (\App\Models\Order::TYPES as $value => $label)
            <option value="{{ $value }}" @selected(request('type') === $value)>{{ $label }}</option>
          @endforeach
        </select>
        <select class="form-select" name="status" aria-label="Status">
          <option value="">All statuses</option>
          @foreach (\App\Models\Order::STATUSES as $value => $label)
            <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
          @endforeach
        </select>
        <input class="form-control" type="date" name="from" value="{{ request('from') }}" aria-label="From date">
        <input class="form-control" type="date" name="to" value="{{ request('to') }}" aria-label="To date">
        <input class="form-control" type="search" name="search" value="{{ request('search') }}" placeholder="Order no" aria-label="Order no">
        <button class="btn btn-light" type="submit"><i class="bi bi-search"></i> Search</button>
        @if (request()->hasAny(['search', 'type', 'status', 'from', 'to']))
          <a class="btn btn-light" href="{{ route('orders.index') }}">Clear</a>
        @endif
      </form>
    </div>

    <div class="table-responsive">
      <table class="table table-x align-middle mb-0">
        <thead>
          <tr>
            <th scope="col">Order</th>
            <th scope="col">Time</th>
            <th scope="col">Type</th>
            <th scope="col">Table</th>
            <th scope="col">Customer</th>
            <th scope="col">Total</th>
            <th scope="col">Paid by</th>
            <th scope="col">Status</th>
            <th scope="col" class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($orders as $order)
            <tr>
              <td>{{ $order->order_no }}</td>
              <td>{{ $order->created_at->format('d M, h:i A') }}</td>
              <td>{{ \App\Models\Order::TYPES[$order->type] ?? $order->type }}</td>
              <td>{{ $order->table?->name ?? '·' }}</td>
              <td>{{ $order->customer?->name ?? 'Walk-in' }}</td>
              <td>৳{{ number_format($order->total, 2) }}</td>
              <td>{{ \App\Models\Order::PAYMENT_METHODS[$order->payment_method] ?? $order->payment_method }}</td>
              <td>
                <span class="status {{ $order->status === 'cooking' ? 'cooking' : ($order->status === 'ready' ? 'open' : 'paid') }}">
                  {{ \App\Models\Order::STATUSES[$order->status] ?? $order->status }}
                </span>
              </td>
              <td class="text-end">
                <a class="btn btn-sm btn-light" href="{{ route('orders.show', $order) }}">
                  <i class="bi bi-printer"></i> Receipt
                </a>
                @can('orders.update_status')
                  @if ($order->status === 'cooking')
                    <form class="d-inline" method="POST" action="{{ route('orders.status', $order) }}">
                      @csrf
                      @method('PATCH')
                      <input type="hidden" name="status" value="ready">
                      <button class="btn btn-sm btn-light" type="submit"><i class="bi bi-bell"></i> Ready</button>
                    </form>
                  @endif
                  @if ($order->status !== 'completed')
                    <form class="d-inline" method="POST" action="{{ route('orders.status', $order) }}">
                      @csrf
                      @method('PATCH')
                      <input type="hidden" name="status" value="completed">
                      <button class="btn btn-sm btn-light" type="submit"><i class="bi bi-check2-all"></i> Complete</button>
                    </form>
                  @endif
                @endcan
                @can('orders.delete')
                  <form class="d-inline" method="POST" action="{{ route('orders.destroy', $order) }}"
                        onsubmit="return confirm('Void this order? The ingredient stock will be restored.');">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-light text-danger" type="submit"><i class="bi bi-x-circle"></i> Void</button>
                  </form>
                @endcan
              </td>
            </tr>
          @empty
            <tr><td colspan="9" class="text-center text-muted py-4">No orders found.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if ($orders->hasPages())
      <div class="card-b">{{ $orders->links('pagination::bootstrap-5') }}</div>
    @endif
  </div>
@endsection
