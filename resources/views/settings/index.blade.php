@extends('Layouts.master')

@section('title', 'Settings')

@section('content')
  @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  <div class="page-head">
    <div>
      <h1>Settings</h1>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb small mb-0">
          <li class="breadcrumb-item">Admin</li>
          <li class="breadcrumb-item active" aria-current="page">Settings</li>
        </ol>
      </nav>
    </div>
  </div>

  <div class="card-x">
    <div class="card-h">Restaurant and receipt</div>
    <div class="card-b">
      <form method="POST" action="{{ route('settings.update') }}">
        @csrf
        @method('PUT')

        @cannot('settings.edit')
          <div class="alert alert-info py-2 small">You can view the settings but only a super admin can change them.</div>
        @endcannot

        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label" for="restaurant_name">Restaurant name</label>
            <input class="form-control @error('restaurant_name') is-invalid @enderror" id="restaurant_name" name="restaurant_name" type="text"
                   value="{{ old('restaurant_name', $values['restaurant_name']) }}" maxlength="255" required>
            @error('restaurant_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div class="col-md-6">
            <label class="form-label" for="phone">Phone</label>
            <input class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" type="text"
                   value="{{ old('phone', $values['phone']) }}" maxlength="50">
            @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div class="col-12">
            <label class="form-label" for="address">Address</label>
            <input class="form-control @error('address') is-invalid @enderror" id="address" name="address" type="text"
                   value="{{ old('address', $values['address']) }}" maxlength="255">
            @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div class="col-md-4">
            <label class="form-label" for="vat_rate">VAT rate (%)</label>
            <input class="form-control @error('vat_rate') is-invalid @enderror" id="vat_rate" name="vat_rate" type="number"
                   step="0.01" min="0" max="100" value="{{ old('vat_rate', $values['vat_rate']) }}" required>
            @error('vat_rate') <div class="invalid-feedback">{{ $message }}</div> @enderror
            <div class="form-text">Added automatically to every new order after the discount. Confirm the rate that applies to your restaurant with your accountant or the NBR.</div>
          </div>

          <div class="col-md-8">
            <label class="form-label" for="receipt_footer">Receipt footer message</label>
            <input class="form-control @error('receipt_footer') is-invalid @enderror" id="receipt_footer" name="receipt_footer" type="text"
                   value="{{ old('receipt_footer', $values['receipt_footer']) }}" maxlength="255">
            @error('receipt_footer') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
        </div>

        @can('settings.edit')
          <div class="d-flex gap-2 mt-4">
            <button class="btn btn-accent" type="submit"><i class="bi bi-check-lg"></i> Save settings</button>
          </div>
        @endcan
      </form>
    </div>
  </div>
@endsection
