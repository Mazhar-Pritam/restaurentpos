@extends('Layouts.master')

@section('title', 'New sales return')

@section('content')
  @if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      {{ session('error') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  <div class="page-head">
    <div>
      <h1>New sales return</h1>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb small mb-0">
          <li class="breadcrumb-item"><a href="{{ route('sales-returns.index') }}">Sales returns</a></li>
          <li class="breadcrumb-item active" aria-current="page">New</li>
        </ol>
      </nav>
    </div>
  </div>

  @if (! $order)
    <div class="card-x">
      <div class="card-h">Step 1 · Find the order</div>
      <div class="card-b">
        <form method="GET" action="{{ route('sales-returns.create') }}">
          <label class="form-label" for="order_no">Order number</label>
          <input class="form-control" id="order_no" name="order_no" type="text" value="{{ old('order_no', request('order_no')) }}"
                 placeholder="ORD-000123" required autofocus>
          <div class="form-text">Only completed orders can be returned. The order number is printed on the receipt.</div>
          <button class="btn btn-accent mt-3" type="submit">Next <i class="bi bi-arrow-right"></i></button>
        </form>
      </div>
    </div>
  @else
    <div class="card-x">
      <div class="card-h">
        <span>Step 2 · {{ $order->order_no }} · total ৳{{ number_format($order->total, 2) }} · paid by {{ \App\Models\Order::PAYMENT_METHODS[$order->payment_method] ?? $order->payment_method }}</span>
        <a class="btn btn-sm btn-light" href="{{ route('sales-returns.create') }}">Change order</a>
      </div>
      <div class="card-b">
        @if ($errors->has('items'))
          <div class="alert alert-danger">{{ $errors->first('items') }}</div>
        @endif

        <form method="POST" action="{{ route('sales-returns.store') }}">
          @csrf
          <input type="hidden" name="order_id" value="{{ $order->id }}">

          <div class="table-responsive mb-3">
            <table class="table table-x align-middle mb-0">
              <thead>
                <tr>
                  <th scope="col">Item</th>
                  <th scope="col" class="text-end">Price</th>
                  <th scope="col" class="text-end">Sold</th>
                  <th scope="col" class="text-end">Already returned</th>
                  <th scope="col" style="width:140px">Return qty</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($lines as $id => $line)
                  <tr>
                    <td>{{ $line['item']->item_name }}</td>
                    <td class="text-end">৳{{ number_format($line['item']->unit_price, 2) }}</td>
                    <td class="text-end">{{ $line['item']->quantity }}</td>
                    <td class="text-end">{{ $line['returned'] }}</td>
                    <td>
                      <input class="form-control" type="number" step="1" min="0" max="{{ $line['remaining'] }}"
                             name="items[{{ $id }}]" value="{{ old('items.'.$id) }}" {{ $line['remaining'] <= 0 ? 'disabled' : '' }}>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>

          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label" for="reason">Reason</label>
              <select class="form-select @error('reason') is-invalid @enderror" id="reason" name="reason" required>
                @foreach (\App\Models\SalesReturn::REASONS as $value => $label)
                  <option value="{{ $value }}" @selected(old('reason') === $value)>{{ $label }}</option>
                @endforeach
              </select>
              @error('reason') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-4">
              <label class="form-label" for="refund_method">Refund via</label>
              <select class="form-select @error('refund_method') is-invalid @enderror" id="refund_method" name="refund_method" required>
                @foreach (\App\Models\Order::PAYMENT_METHODS as $value => $label)
                  <option value="{{ $value }}" @selected(old('refund_method', $order->payment_method) === $value)>{{ $label }}</option>
                @endforeach
              </select>
              @error('refund_method') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-4">
              <label class="form-label" for="note">Note</label>
              <input class="form-control @error('note') is-invalid @enderror" id="note" name="note" type="text" maxlength="255" value="{{ old('note') }}">
              @error('note') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="restock" name="restock" value="1" @checked(old('restock'))>
                <label class="form-check-label" for="restock">Put ingredients back into stock</label>
              </div>
              <div class="form-text">Leave this unticked for cooked food (it cannot be reused, so the ingredient cost stays as a loss). Tick it only if the food was never prepared or is reusable.</div>
            </div>
          </div>

          <div class="form-text mt-3">The refund is calculated automatically: the item price, minus its share of the order discount, plus VAT at the rate used on the original order.</div>

          <div class="d-flex gap-2 mt-4">
            <button class="btn btn-accent" type="submit"><i class="bi bi-check-lg"></i> Save return</button>
            <a class="btn btn-light" href="{{ route('sales-returns.index') }}">Cancel</a>
          </div>
        </form>
      </div>
    </div>
  @endif
@endsection
