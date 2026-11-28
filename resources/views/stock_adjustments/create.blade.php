@extends('Layouts.master')

@section('title', 'New stock adjustment')

@section('content')
  @if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      {{ session('error') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  <div class="page-head">
    <div>
      <h1>New stock adjustment</h1>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb small mb-0">
          <li class="breadcrumb-item"><a href="{{ route('stock-adjustments.index') }}">Stock adjustments</a></li>
          <li class="breadcrumb-item active" aria-current="page">New</li>
        </ol>
      </nav>
    </div>
  </div>

  <div class="card-x">
    <div class="card-h">Adjustment details</div>
    <div class="card-b">
      <form method="POST" action="{{ route('stock-adjustments.store') }}">
        @csrf
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label" for="ingredient_id">Ingredient</label>
            <select class="form-select @error('ingredient_id') is-invalid @enderror" id="ingredient_id" name="ingredient_id" required>
              <option value="">Choose ingredient</option>
              @foreach ($ingredients as $ingredient)
                <option value="{{ $ingredient->id }}" data-unit="{{ $ingredient->unit }}" data-stock="{{ rtrim(rtrim(number_format($ingredient->stock_quantity, 3, '.', ''), '0'), '.') }}"
                  @selected((string) old('ingredient_id') === (string) $ingredient->id)>
                  {{ $ingredient->name }}
                </option>
              @endforeach
            </select>
            <div class="form-text" id="stockHint">&nbsp;</div>
            @error('ingredient_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
          </div>

          <div class="col-md-3">
            <label class="form-label" for="direction">Type</label>
            <select class="form-select @error('direction') is-invalid @enderror" id="direction" name="direction" required>
              <option value="out" @selected(old('direction', 'out') === 'out')>Remove stock (−)</option>
              <option value="in" @selected(old('direction') === 'in')>Add stock (+)</option>
            </select>
            @error('direction') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div class="col-md-3">
            <label class="form-label" for="quantity">Quantity <span id="unitLabel" class="text-muted"></span></label>
            <input class="form-control @error('quantity') is-invalid @enderror" id="quantity" name="quantity" type="number"
                   step="0.001" min="0.001" value="{{ old('quantity') }}" required>
            @error('quantity') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div class="col-md-6">
            <label class="form-label" for="reason">Reason</label>
            <select class="form-select @error('reason') is-invalid @enderror" id="reason" name="reason" required></select>
            @error('reason') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div class="col-md-6">
            <label class="form-label" for="note">Note <span class="text-muted">(required for "Other")</span></label>
            <input class="form-control @error('note') is-invalid @enderror" id="note" name="note" type="text" maxlength="255" value="{{ old('note') }}">
            @error('note') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
        </div>

        <div class="form-text mt-2">Adjustments cannot be edited or deleted. If you make a mistake, add an opposite adjustment.</div>

        <div class="d-flex gap-2 mt-4">
          <button class="btn btn-accent" type="submit"><i class="bi bi-check-lg"></i> Save adjustment</button>
          <a class="btn btn-light" href="{{ route('stock-adjustments.index') }}">Cancel</a>
        </div>
      </form>
    </div>
  </div>
@endsection

@push('scripts')
@php
  $reasonMap = $reasons;
  $oldReason = old('reason', '');
@endphp
<script>
  (function () {
    var reasons = {!! json_encode($reasonMap) !!};
    var oldReason = {!! json_encode($oldReason) !!};
    var dir = document.getElementById('direction');
    var reason = document.getElementById('reason');
    var ing = document.getElementById('ingredient_id');

    function fillReasons() {
      var list = reasons[dir.value] || {};
      reason.innerHTML = '';
      Object.keys(list).forEach(function (key) {
        var o = document.createElement('option');
        o.value = key;
        o.textContent = list[key];
        if (key === oldReason) { o.selected = true; }
        reason.appendChild(o);
      });
    }

    function showStock() {
      var opt = ing.options[ing.selectedIndex];
      var hint = document.getElementById('stockHint');
      var unit = document.getElementById('unitLabel');
      if (opt && opt.value) {
        hint.textContent = 'In stock now: ' + opt.dataset.stock + ' ' + opt.dataset.unit;
        unit.textContent = '(' + opt.dataset.unit + ')';
      } else {
        hint.innerHTML = '&nbsp;';
        unit.textContent = '';
      }
    }

    dir.addEventListener('change', function () { oldReason = ''; fillReasons(); });
    ing.addEventListener('change', showStock);
    fillReasons();
    showStock();
  })();
</script>
@endpush
