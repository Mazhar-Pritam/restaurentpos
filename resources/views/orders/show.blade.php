<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Receipt {{ $order->order_no }} · {{ $settings['name'] }}</title>
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <style>
    body { font-family: 'Manrope', system-ui, sans-serif; background: #f4f2ee; color: #1b2233; }
    .receipt { width: 340px; max-width: 100%; margin: 20px auto; background: #fff; border-radius: 12px; padding: 22px; box-shadow: 0 1px 4px rgba(0,0,0,.08); font-size: 13px; }
    .receipt .brand { font-weight: 800; font-size: 1.15rem; text-align: center; }
    .receipt .brand i { color: #e8401c; }
    .receipt .center { text-align: center; }
    .receipt hr { border-top: 1px dashed #999; opacity: 1; margin: 10px 0; }
    .receipt table { width: 100%; }
    .receipt td, .receipt th { padding: 2px 0; vertical-align: top; }
    .receipt .r { text-align: right; white-space: nowrap; }
    .btn-accent { background: #e8401c; border-color: #e8401c; color: #fff; }
    .btn-accent:hover { background: #cf3515; border-color: #cf3515; color: #fff; }
    @media print {
      body { background: #fff; }
      .no-print { display: none !important; }
      .receipt { margin: 0 auto; box-shadow: none; border-radius: 0; padding: 0; width: 100%; }
    }
  </style>
</head>
<body>
  <div class="no-print" style="width:340px;max-width:100%;margin:16px auto 0">
    @if (session('success'))
      <div class="alert alert-success py-2 small mb-2">{{ session('success') }}</div>
    @endif
    <div class="d-flex justify-content-between gap-2">
      <a class="btn btn-light" href="{{ route('orders.index') }}"><i class="bi bi-arrow-left"></i> Orders</a>
      @can('orders.create')
        <a class="btn btn-light" href="{{ route('pos') }}"><i class="bi bi-plus-lg"></i> New order</a>
      @endcan
      <button class="btn btn-accent" type="button" onclick="window.print()"><i class="bi bi-printer"></i> Print</button>
    </div>
  </div>

  <div class="receipt">
    <div class="brand"><i class="bi bi-fire"></i> {{ $settings['name'] }}</div>
    @if ($settings['address']) <div class="center">{{ $settings['address'] }}</div> @endif
    @if ($settings['phone']) <div class="center">Tel: {{ $settings['phone'] }}</div> @endif
    <hr>

    <table>
      <tr><td>Order</td><td class="r fw-bold">{{ $order->order_no }}</td></tr>
      <tr><td>Date</td><td class="r">{{ $order->created_at->format('d M Y, h:i A') }}</td></tr>
      <tr><td>Type</td><td class="r">{{ \App\Models\Order::TYPES[$order->type] ?? $order->type }}{{ $order->table ? ' · ' . $order->table->name : '' }}</td></tr>
      @if ($order->customer)
        <tr><td>Customer</td><td class="r">{{ $order->customer->name }}</td></tr>
      @endif
      @if ($order->user)
        <tr><td>Cashier</td><td class="r">{{ $order->user->name }}</td></tr>
      @endif
    </table>
    <hr>

    <table>
      <thead>
        <tr><th>Item</th><th class="r">Qty</th><th class="r">Price</th><th class="r">Total</th></tr>
      </thead>
      <tbody>
        @foreach ($order->items as $item)
          <tr>
            <td>{{ $item->item_name }}</td>
            <td class="r">{{ $item->quantity }}</td>
            <td class="r">{{ number_format($item->unit_price, 2) }}</td>
            <td class="r">{{ number_format($item->line_total, 2) }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
    <hr>

    <table>
      <tr><td>Subtotal</td><td class="r">৳{{ number_format($order->subtotal, 2) }}</td></tr>
      @if ((float) $order->discount_amount > 0)
        <tr>
          <td>Discount{{ $order->discount_type === 'percent' ? ' (' . rtrim(rtrim(number_format((float) $order->discount_value, 2), '0'), '.') . '%)' : '' }}</td>
          <td class="r">−৳{{ number_format($order->discount_amount, 2) }}</td>
        </tr>
      @endif
      <tr><td>VAT ({{ rtrim(rtrim(number_format((float) $order->vat_rate, 2), '0'), '.') }}%)</td><td class="r">৳{{ number_format($order->vat_amount, 2) }}</td></tr>
      <tr class="fw-bold" style="font-size:15px"><td>TOTAL</td><td class="r">৳{{ number_format($order->total, 2) }}</td></tr>
      <tr><td>Paid by</td><td class="r">{{ \App\Models\Order::PAYMENT_METHODS[$order->payment_method] ?? $order->payment_method }}</td></tr>
    </table>

    @if ($order->note)
      <hr>
      <div>Note: {{ $order->note }}</div>
    @endif

    @if ($settings['footer'])
      <hr>
      <div class="center">{{ $settings['footer'] }}</div>
    @endif
  </div>

  @if (request('print'))
    <script>
      window.addEventListener('load', function () { setTimeout(function () { window.print(); }, 400); });
    </script>
  @endif
</body>
</html>
