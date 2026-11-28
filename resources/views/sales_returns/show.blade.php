@extends('Layouts.master')

@section('title', $return->return_no)

@section('content')
  @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  <div class="page-head">
    <div>
      <h1>{{ $return->return_no }}</h1>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb small mb-0">
          <li class="breadcrumb-item"><a href="{{ route('sales-returns.index') }}">Sales returns</a></li>
          <li class="breadcrumb-item active" aria-current="page">{{ $return->return_no }}</li>
        </ol>
      </nav>
    </div>
    <a class="btn btn-light" href="{{ route('sales-returns.index') }}"><i class="bi bi-arrow-left"></i> Back</a>
  </div>

  <div class="card-x mb-3">
    <div class="card-b">
      <div class="row g-3">
        <div class="col-md-3"><div class="text-muted small">Order</div>
          @if ($return->order)
            <a href="{{ route('orders.show', $return->order) }}">{{ $return->order->order_no }}</a>
          @else — @endif
        </div>
        <div class="col-md-3"><div class="text-muted small">Date</div>{{ $return->created_at->format('d M Y, h:i A') }}</div>
        <div class="col-md-3"><div class="text-muted small">Reason</div>{{ \App\Models\SalesReturn::REASONS[$return->reason] ?? $return->reason }}</div>
        <div class="col-md-3"><div class="text-muted small">Entered by</div>{{ $return->user?->name ?? '—' }}</div>
        <div class="col-md-3"><div class="text-muted small">Refunded via</div>{{ \App\Models\Order::PAYMENT_METHODS[$return->refund_method] ?? $return->refund_method }}</div>
        <div class="col-md-3"><div class="text-muted small">Stock</div>{{ $return->restock ? 'Ingredients returned to stock' : 'Not restocked (loss)' }}</div>
        @if ($return->note)
          <div class="col-md-6"><div class="text-muted small">Note</div>{{ $return->note }}</div>
        @endif
      </div>
    </div>
  </div>

  <div class="card-x">
    <div class="table-responsive">
      <table class="table table-x align-middle mb-0">
        <thead>
          <tr>
            <th scope="col">Item</th>
            <th scope="col" class="text-end">Qty</th>
            <th scope="col" class="text-end">Price</th>
            <th scope="col" class="text-end">Total</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($return->items as $item)
            <tr>
              <td>{{ $item->item_name }}</td>
              <td class="text-end">{{ $item->quantity }}</td>
              <td class="text-end">৳{{ number_format($item->unit_price, 2) }}</td>
              <td class="text-end">৳{{ number_format($item->line_total, 2) }}</td>
            </tr>
          @endforeach
        </tbody>
        <tfoot>
          <tr><td colspan="3" class="text-end">Items</td><td class="text-end">৳{{ number_format($return->subtotal, 2) }}</td></tr>
          <tr><td colspan="3" class="text-end">Discount share</td><td class="text-end">− ৳{{ number_format($return->discount_amount, 2) }}</td></tr>
          <tr><td colspan="3" class="text-end">VAT</td><td class="text-end">৳{{ number_format($return->vat_amount, 2) }}</td></tr>
          <tr><th colspan="3" class="text-end">Refund</th><th class="text-end">৳{{ number_format($return->refund_amount, 2) }}</th></tr>
        </tfoot>
      </table>
    </div>
  </div>
@endsection
