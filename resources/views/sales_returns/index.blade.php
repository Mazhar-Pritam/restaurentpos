@extends('Layouts.master')

@section('title', 'Sales returns')

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
      <h1>Sales returns</h1>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb small mb-0">
          <li class="breadcrumb-item">Orders</li>
          <li class="breadcrumb-item active" aria-current="page">Sales returns</li>
        </ol>
      </nav>
    </div>
    @can('sales_returns.create')
      <a class="btn btn-accent" href="{{ route('sales-returns.create') }}"><i class="bi bi-plus-lg"></i> New return</a>
    @endcan
  </div>

  <div class="card-x">
    <div class="card-h">
      <span>Total refunded ৳{{ number_format($totalRefund, 2) }}</span>
      <form class="d-flex flex-wrap gap-2" method="GET" action="{{ route('sales-returns.index') }}">
        <select class="form-select" name="reason" aria-label="Reason">
          <option value="">All reasons</option>
          @foreach (\App\Models\SalesReturn::REASONS as $value => $label)
            <option value="{{ $value }}" @selected(request('reason') === $value)>{{ $label }}</option>
          @endforeach
        </select>
        <input class="form-control" type="date" name="from" value="{{ request('from') }}" aria-label="From date">
        <input class="form-control" type="date" name="to" value="{{ request('to') }}" aria-label="To date">
        <input class="form-control" type="search" name="search" value="{{ request('search') }}" placeholder="Return or order no" aria-label="Search">
        <button class="btn btn-light" type="submit"><i class="bi bi-search"></i> Search</button>
        @if (request()->hasAny(['search', 'reason', 'from', 'to']))
          <a class="btn btn-light" href="{{ route('sales-returns.index') }}">Reset</a>
        @endif
      </form>
    </div>

    <div class="table-responsive">
      <table class="table table-x align-middle mb-0">
        <thead>
          <tr>
            <th scope="col">Return no</th>
            <th scope="col">Date</th>
            <th scope="col">Order</th>
            <th scope="col">Reason</th>
            <th scope="col">Refund via</th>
            <th scope="col" class="text-end">Refund</th>
            <th scope="col" class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($returns as $return)
            <tr>
              <td><a href="{{ route('sales-returns.show', $return) }}">{{ $return->return_no }}</a></td>
              <td>{{ $return->created_at->format('d M Y') }}<div class="small text-muted">{{ $return->created_at->format('h:i A') }}</div></td>
              <td>{{ $return->order?->order_no ?? '—' }}</td>
              <td>{{ \App\Models\SalesReturn::REASONS[$return->reason] ?? $return->reason }}</td>
              <td>{{ \App\Models\Order::PAYMENT_METHODS[$return->refund_method] ?? $return->refund_method }}</td>
              <td class="text-end">৳{{ number_format($return->refund_amount, 2) }}</td>
              <td class="text-end">
                <a class="btn btn-sm btn-light" href="{{ route('sales-returns.show', $return) }}"><i class="bi bi-eye"></i> View</a>
                @can('sales_returns.delete')
                  <form class="d-inline" method="POST" action="{{ route('sales-returns.destroy', $return) }}"
                        onsubmit="return confirm('Delete this return?');">
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
