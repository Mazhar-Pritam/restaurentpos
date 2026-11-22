@php
  $isEdit = isset($purchase) && $purchase && $purchase->exists;
  // Blade এর json ডিরেক্টিভে কমাওয়ালা এক্সপ্রেশন চলে না, তাই ডেটা এখানে ভ্যারিয়েবলে রাখা হলো
    $ingredientData = $ingredients->map(fn ($i) => ['id' => $i->id, 'name' => $i->name, 'unit' => $i->unit, 'cost' => (float) $i->average_cost])->values();
  $initialRows    = old('items', $rows);
  $errorData      = $errors->messages();
@endphp


<form method="POST" action="{{ $isEdit ? route('purchases.update', $purchase) : route('purchases.store') }}">
  @csrf
  @if ($isEdit)
    @method('PUT')
  @endif

  <div class="card-x mb-3">
    <div class="card-h">{{ $isEdit ? 'Update purchase ' . $purchase->invoice_no : 'Purchase details' }}</div>
    <div class="card-b">
      <div class="row g-3">
        <div class="col-md-5">
          <label class="form-label" for="supplier_id">Supplier</label>
          <select class="form-select @error('supplier_id') is-invalid @enderror" id="supplier_id" name="supplier_id" required>
            <option value="">Choose supplier</option>
            @foreach ($suppliers as $supplier)
              <option value="{{ $supplier->id }}"
                @selected((string) old('supplier_id', $purchase->supplier_id ?? '') === (string) $supplier->id)>
                {{ $supplier->name }}{{ $supplier->is_active ? '' : ' (inactive)' }}
              </option>
            @endforeach
          </select>
          @error('supplier_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-3">
          <label class="form-label" for="purchase_date">Date</label>
          <input class="form-control @error('purchase_date') is-invalid @enderror" id="purchase_date" name="purchase_date" type="date"
                 value="{{ old('purchase_date', $isEdit ? $purchase->purchase_date->format('Y-m-d') : now()->format('Y-m-d')) }}" required>
          @error('purchase_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-4">
          <label class="form-label" for="note">Note <span class="text-muted">(optional)</span></label>
          <input class="form-control @error('note') is-invalid @enderror" id="note" name="note" type="text" maxlength="255"
                 value="{{ old('note', $purchase->note ?? '') }}">
          @error('note') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
      </div>
    </div>
  </div>

  <div class="card-x mb-3">
    <div class="card-h">Items</div>
    <div class="card-b">
      @if ($errors->has('items'))
        <div class="alert alert-danger py-2 small">{{ $errors->first('items') }}</div>
      @endif

      <div class="row g-2 mb-1 small text-muted d-none d-md-flex">
        <div class="col-md-4">Ingredient</div>
        <div class="col-md-2">Quantity</div>
        <div class="col-md-2">Unit cost (৳)</div>
        <div class="col-md-3">Line total (৳)</div>
        <div class="col-md-1"></div>
      </div>

      <div id="purchase-rows"></div>

      <button type="button" class="btn btn-light" id="add-row"><i class="bi bi-plus-lg"></i> Add ingredient</button>
    </div>
  </div>

  <div class="card-x mb-3">
    <div class="card-b">
      <div class="row g-3 align-items-start">
        <div class="col-md-4">
          <label class="form-label">Total</label>
          <input class="form-control fw-bold" type="text" id="grand-total" value="৳0.00" disabled>
        </div>
        <div class="col-md-4">
          <label class="form-label" for="paid_amount">Paid amount (৳)</label>
          <input class="form-control @error('paid_amount') is-invalid @enderror" id="paid_amount" name="paid_amount"
                 type="number" step="0.01" min="0" value="{{ old('paid_amount', $isEdit ? $purchase->paid_amount : 0) }}" required>
          @error('paid_amount') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-4">
          <label class="form-label">Due</label>
          <input class="form-control fw-bold" type="text" id="due-amount" value="৳0.00" disabled>
        </div>
      </div>
    </div>
  </div>

  <div class="d-flex gap-2">
    <button class="btn btn-accent" type="submit">
      <i class="bi bi-check-lg"></i> {{ $isEdit ? 'Save changes' : 'Save purchase' }}
    </button>
    <a class="btn btn-light" href="{{ route('purchases.index') }}">Cancel</a>
  </div>
</form>

@push('scripts')
<script>
  (function () {
    var ingredients = @json($ingredientData);
    var initial = @json($initialRows);
    var errs = @json($errorData);
    var box = document.getElementById('purchase-rows');
    var paid = document.getElementById('paid_amount');
    var next = 0;

    function esc(s) {
      return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }
    function money(n) { return '৳' + (Math.round(n * 100) / 100).toFixed(2); }

    function recalc() {
      var total = 0;
      box.querySelectorAll('.p-row').forEach(function (row) {
        var q = parseFloat(row.querySelector('.q').value) || 0;
        var c = parseFloat(row.querySelector('.c').value) || 0;
        var line = Math.round(q * c * 100) / 100;
        row.querySelector('.line').value = money(line);
        total += line;
      });
      document.getElementById('grand-total').value = money(total);
      var due = total - (parseFloat(paid.value) || 0);
      document.getElementById('due-amount').value = money(due < 0 ? 0 : due);
    }

    function addRow(i, d) {
      d = d || {};
      var opts = '<option value="">Choose ingredient</option>' + ingredients.map(function (x) {
        var sel = String(d.ingredient_id) === String(x.id) ? ' selected' : '';
        return '<option value="' + x.id + '" data-unit="' + esc(x.unit) + '" data-cost="' + x.cost + '"' + sel + '>' + esc(x.name) + '</option>';
      }).join('');
      var eI = errs['items.' + i + '.ingredient_id'];
      var eQ = errs['items.' + i + '.quantity'];
      var eC = errs['items.' + i + '.unit_cost'];
      var row = document.createElement('div');
      row.className = 'row g-2 align-items-start mb-2 p-row';
      row.innerHTML =
        '<div class="col-md-4"><select class="form-select' + (eI ? ' is-invalid' : '') + '" name="items[' + i + '][ingredient_id]" required>' + opts + '</select>' +
          (eI ? '<div class="invalid-feedback d-block">' + esc(eI[0]) + '</div>' : '') + '</div>' +
        '<div class="col-md-2"><div class="input-group"><input class="form-control q' + (eQ ? ' is-invalid' : '') + '" type="number" step="0.001" min="0.001" name="items[' + i + '][quantity]" value="' + esc(d.quantity == null ? '' : d.quantity) + '" placeholder="Qty" required>' +
          '<span class="input-group-text unit-label">unit</span></div>' +
          (eQ ? '<div class="invalid-feedback d-block">' + esc(eQ[0]) + '</div>' : '') + '</div>' +
        '<div class="col-md-2"><input class="form-control c' + (eC ? ' is-invalid' : '') + '" type="number" step="any" min="0" name="items[' + i + '][unit_cost]" value="' + esc(d.unit_cost == null ? '' : d.unit_cost) + '" placeholder="Cost" required>' +
          (eC ? '<div class="invalid-feedback d-block">' + esc(eC[0]) + '</div>' : '') + '</div>' +
        '<div class="col-md-3"><input class="form-control line" type="text" value="৳0.00" disabled></div>' +
        '<div class="col-md-1"><button type="button" class="btn btn-light text-danger w-100 remove-row" aria-label="Remove ingredient"><i class="bi bi-trash"></i></button></div>';
      box.appendChild(row);

      var sel = row.querySelector('select');
      function unit() {
        var opt = sel.options[sel.selectedIndex];
        row.querySelector('.unit-label').textContent = (opt && opt.dataset.unit) ? opt.dataset.unit : 'unit';
      }
      sel.addEventListener('change', function () {
        unit();
        var opt = sel.options[sel.selectedIndex];
        var cost = row.querySelector('.c');
        if (opt && opt.dataset.cost && parseFloat(opt.dataset.cost) > 0 && !cost.value) { cost.value = opt.dataset.cost; }
        recalc();
      });
      row.querySelector('.q').addEventListener('input', recalc);
      row.querySelector('.c').addEventListener('input', recalc);
      row.querySelector('.remove-row').addEventListener('click', function () {
        if (box.children.length > 1) { row.remove(); }
        else { sel.value = ''; row.querySelector('.q').value = ''; row.querySelector('.c').value = ''; unit(); }
        recalc();
      });
      unit();
    }

    Object.keys(initial || {}).forEach(function (k) {
      addRow(k, initial[k]);
      next = Math.max(next, parseInt(k, 10) + 1);
    });
    if (!box.children.length) { addRow(next++, {}); }

    document.getElementById('add-row').addEventListener('click', function () { addRow(next++, {}); });
    paid.addEventListener('input', recalc);
    recalc();
  })();
</script>
@endpush
