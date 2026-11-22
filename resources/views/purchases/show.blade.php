<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Purchase {{ $purchase->invoice_no }} · Restaurant POS</title>
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <style>
    body { font-family: 'Manrope', system-ui, sans-serif; background: #f4f2ee; color: #1b2233; }
    .sheet { max-width: 820px; margin: 24px auto; background: #fff; border-radius: 14px; padding: 36px; box-shadow: 0 1px 4px rgba(0,0,0,.08); }
    .brand { font-weight: 800; font-size: 1.3rem; }
    .brand i { color: #e8401c; }
    .btn-accent { background: #e8401c; border-color: #e8401c; color: #fff; }
    .btn-accent:hover { background: #cf3515; border-color: #cf3515; color: #fff; }
    .table thead th { background: #f4f2ee; font-size: .8rem; text-transform: uppercase; letter-spacing: .03em; }
    @media print {
      body { background: #fff; }
      .no-print { display: none !important; }
      .sheet { margin: 0; max-width: 100%; box-shadow: none; border-radius: 0; padding: 0; }
    }
  </style>
</head>
<body>
  <div class="container no-print" style="max-width:820px">
    <div class="d-flex justify-content-between align-items-center pt-3">
      <a class="btn btn-light" href="{{ route('purchases.index') }}"><i class="bi bi-arrow-left"></i> Back to purchases</a>
      <div class="d-flex gap-2">
        @can('purchases.edit')
          <a class="btn btn-light" href="{{ route('purchases.edit', $purchase) }}"><i class="bi bi-pencil"></i> Edit</a>
        @endcan
        <button class="btn btn-accent" type="button" onclick="window.print()"><i class="bi bi-printer"></i> Print</button>
      </div>
    </div>
  </div>

  <div class="sheet">
    <div class="d-flex justify-content-between flex-wrap gap-3 mb-4">
      <div>
        <div class="brand"><i class="bi bi-fire"></i> Restaurant POS</div>
        <div class="text-muted small">Purchase invoice</div>
      </div>
      <div class="text-md-end">
        <div class="fw-bold fs-5">{{ $purchase->invoice_no }}</div>
        <div class="small text-muted">Date: {{ $purchase->purchase_date->format('d M Y') }}</div>
        @php($st = $purchase->payment_status)
        <span class="badge {{ $st === 'Paid' ? 'text-bg-success' : ($st === 'Partial' ? 'text-bg-warning' : 'text-bg-danger') }}">{{ $st }}</span>
      </div>
    </div>

    <div class="mb-4">
      <div class="small text-muted">Supplier</div>
      <div class="fw-semibold">{{ $purchase->supplier?->name ?? '—' }}</div>
      @if ($purchase->supplier?->phone) <div class="small">{{ $purchase->supplier->phone }}</div> @endif
      @if ($purchase->supplier?->address) <div class="small">{{ $purchase->supplier->address }}</div> @endif
    </div>

    <div class="table-responsive">
      <table class="table align-middle">
        <thead>
          <tr>
            <th scope="col">#</th>
            <th scope="col">Ingredient</th>
            <th scope="col" class="text-end">Quantity</th>
            <th scope="col" class="text-end">Unit cost</th>
            <th scope="col" class="text-end">Line total</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($purchase->items as $i => $item)
            <tr>
              <td>{{ $i + 1 }}</td>
              <td>{{ $item->ingredient?->name ?? '—' }}</td>
              <td class="text-end">{{ rtrim(rtrim(number_format((float) $item->quantity, 3, '.', ''), '0'), '.') }} {{ $item->ingredient?->unit }}</td>
              <td class="text-end">৳{{ number_format($item->unit_cost, 2) }}</td>
              <td class="text-end">৳{{ number_format($item->line_total, 2) }}</td>
            </tr>
          @endforeach
        </tbody>
        <tfoot>
          <tr><th colspan="4" class="text-end">Total</th><th class="text-end">৳{{ number_format($purchase->total_amount, 2) }}</th></tr>
          <tr><td colspan="4" class="text-end">Paid</td><td class="text-end">৳{{ number_format($purchase->paid_amount, 2) }}</td></tr>
          <tr><th colspan="4" class="text-end">Due</th><th class="text-end">৳{{ number_format($purchase->due, 2) }}</th></tr>
        </tfoot>
      </table>
    </div>

    @if ($purchase->note)
      <div class="small text-muted mt-3">Note: {{ $purchase->note }}</div>
    @endif
    @if ($purchase->user)
      <div class="small text-muted mt-1">Entered by {{ $purchase->user->name }}</div>
    @endif
  </div>
</body>
</html>
