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
          <li class="breadcrumb-item"><a href="{{ route('purchase-returns.index') }}">Purchase returns</a></li>
          <li class="breadcrumb-item active" aria-current="page">{{ $return->return_no }}</li>
        </ol>
      </nav>
    </div>
    <a class="btn btn-light" href="{{ route('purchase-returns.index') }}"><i class="bi bi-arrow-left"></i> Back</a>
  </div>

  <div class="card-x mb-3">
    <div class="card-b">
      <div class="row g-3">
        <div class="col-md-3"><div class="text-muted small">Supplier</div>{{ $return->supplier?->name }}</div>
        <div class="col-md-3"><div class="text-muted small">Invoice</div>
          @if ($return->purchase)
            <a href="{{ route('purchases.show', $return->purchase) }}">{{ $return->purchase->invoice_no }}</a>
          @else — @endif
        </div>
        <div class="col-md-3"><div class="text-muted small">Date</div>{{ $return->return_date->format('d M Y') }}</div>
        <div class="col-md-3"><div class="text-muted small">Entered by</div>{{ $return->user?->name ?? '—' }}</div>
        @if ($return->note)
          <div class="col-12"><div class="text-muted small">Note</div>{{ $return->note }}</div>
        @endif
      </div>
    </div>
  </div>

  <div class="card-x">
    <div class="table-responsive">
      <table class="table table-x align-middle mb-0">
        <thead>
          <tr>
            <th scope="col">Ingredient</th>
            <th scope="col" class="text-end">Quantity</th>
            <th scope="col" class="text-end">Unit cost</th>
            <th scope="col" class="text-end">Total</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($return->items as $item)
            <tr>
              <td>{{ $item->ingredient?->name }}</td>
              <td class="text-end">{{ rtrim(rtrim(number_format($item->quantity, 3, '.', ''), '0'), '.') }} {{ $item->ingredient?->unit }}</td>
              <td class="text-end">৳{{ number_format($item->unit_cost, 2) }}</td>
              <td class="text-end">৳{{ number_format($item->line_total, 2) }}</td>
            </tr>
          @endforeach
        </tbody>
        <tfoot>
          <tr><th colspan="3" class="text-end">Returned value</th><th class="text-end">৳{{ number_format($return->total_amount, 2) }}</th></tr>
          <tr><td colspan="3" class="text-end">Cash refund received</td><td class="text-end">৳{{ number_format($return->refund_amount, 2) }}</td></tr>
          <tr><td colspan="3" class="text-end">Adjusted against dues</td><td class="text-end">৳{{ number_format($return->total_amount - $return->refund_amount, 2) }}</td></tr>
        </tfoot>
      </table>
    </div>
  </div>
@endsection
