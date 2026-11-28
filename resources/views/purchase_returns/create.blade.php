@extends('Layouts.master')

@section('title', 'New purchase return')

@section('content')
  @if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      {{ session('error') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  <div class="page-head">
    <div>
      <h1>New purchase return</h1>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb small mb-0">
          <li class="breadcrumb-item"><a href="{{ route('purchase-returns.index') }}">Purchase returns</a></li>
          <li class="breadcrumb-item active" aria-current="page">New</li>
        </ol>
      </nav>
    </div>
  </div>

  @if (! $purchase)
    {{-- ধাপ ১: ইনভয়েস বাছা --}}
    <div class="card-x">
      <div class="card-h">Step 1 · Choose the purchase invoice</div>
      <div class="card-b">
        <form method="GET" action="{{ route('purchase-returns.create') }}">
          <label class="form-label" for="purchase">Purchase invoice</label>
          <select class="form-select" id="purchase" name="purchase" required>
            <option value="">Choose invoice</option>
            @foreach ($purchases as $p)
              <option value="{{ $p->id }}">
                {{ $p->invoice_no }} · {{ $p->supplier?->name }} · {{ $p->purchase_date->format('d M Y') }} · ৳{{ number_format($p->total_amount, 2) }}
              </option>
            @endforeach
          </select>
          <div class="form-text">A return is always made against a purchase invoice, so the correct price and the maximum quantity are known.</div>
          <button class="btn btn-accent mt-3" type="submit">Next <i class="bi bi-arrow-right"></i></button>
        </form>
      </div>
    </div>
  @else
    {{-- ধাপ ২: পরিমাণ লেখা --}}
    <div class="card-x">
      <div class="card-h">
        <span>Step 2 · {{ $purchase->invoice_no }} · {{ $purchase->supplier?->name }}</span>
        <a class="btn btn-sm btn-light" href="{{ route('purchase-returns.create') }}">Change invoice</a>
      </div>
      <div class="card-b">
        @if ($errors->has('items'))
          <div class="alert alert-danger">{{ $errors->first('items') }}</div>
        @endif

        <form method="POST" action="{{ route('purchase-returns.store') }}">
          @csrf
          <input type="hidden" name="purchase_id" value="{{ $purchase->id }}">

          <div class="table-responsive mb-3">
            <table class="table table-x align-middle mb-0">
              <thead>
                <tr>
                  <th scope="col">Ingredient</th>
                  <th scope="col" class="text-end">Bought</th>
                  <th scope="col" class="text-end">Already returned</th>
                  <th scope="col" class="text-end">Can return</th>
                  <th scope="col" class="text-end">Unit cost</th>
                  <th scope="col" style="width:160px">Return qty</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($returnable as $id => $row)
                  <tr>
                    <td>{{ $row['ingredient']->name }}</td>
                    <td class="text-end">{{ rtrim(rtrim(number_format($row['purchased'], 3, '.', ''), '0'), '.') }} {{ $row['ingredient']->unit }}</td>
                    <td class="text-end">{{ rtrim(rtrim(number_format($row['returned'], 3, '.', ''), '0'), '.') ?: '0' }}</td>
                    <td class="text-end">{{ rtrim(rtrim(number_format($row['remaining'], 3, '.', ''), '0'), '.') ?: '0' }}</td>
                    <td class="text-end">৳{{ number_format($row['unit_cost'], 2) }}</td>
                    <td>
                      <input class="form-control @error('items.'.$id) is-invalid @enderror" type="number" step="0.001" min="0"
                             max="{{ $row['remaining'] }}" name="items[{{ $id }}]" value="{{ old('items.'.$id) }}"
                             {{ $row['remaining'] <= 0 ? 'disabled' : '' }}>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>

          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label" for="return_date">Return date</label>
              <input class="form-control @error('return_date') is-invalid @enderror" id="return_date" name="return_date" type="date"
                     value="{{ old('return_date', now()->toDateString()) }}" max="{{ now()->toDateString() }}" required>
              @error('return_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-4">
              <label class="form-label" for="refund_amount">Cash refund received (৳)</label>
              <input class="form-control @error('refund_amount') is-invalid @enderror" id="refund_amount" name="refund_amount" type="number"
                     step="0.01" min="0" value="{{ old('refund_amount', 0) }}">
              <div class="form-text">Leave 0 if the supplier adjusts it against what you owe.</div>
              @error('refund_amount') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-4">
              <label class="form-label" for="note">Note</label>
              <input class="form-control @error('note') is-invalid @enderror" id="note" name="note" type="text" maxlength="255"
                     value="{{ old('note') }}" placeholder="Damaged, wrong item...">
              @error('note') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
          </div>

          <div class="form-text mt-2">Stock is reduced at the invoice's price. If there is not enough stock left (it was already used), the return is refused.</div>

          <div class="d-flex gap-2 mt-4">
            <button class="btn btn-accent" type="submit"><i class="bi bi-check-lg"></i> Save return</button>
            <a class="btn btn-light" href="{{ route('purchase-returns.index') }}">Cancel</a>
          </div>
        </form>
      </div>
    </div>
  @endif
@endsection
