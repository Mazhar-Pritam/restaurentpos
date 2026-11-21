@php($isEdit = isset($menuItem) && $menuItem && $menuItem->exists)

<div class="card-x">
  <div class="card-h">{{ $isEdit ? 'Update recipe' : 'Recipe details' }}</div>
  <div class="card-b">
    <form method="POST" action="{{ $isEdit ? route('recipes.update', $menuItem) : route('recipes.store') }}">
      @csrf
      @if ($isEdit)
        @method('PUT')
      @endif

      <div class="mb-3">
        <label class="form-label" for="menu_item_id">Menu item</label>
        @if ($isEdit)
          <input class="form-control" type="text" disabled value="{{ $menuItem->name }}">
        @else
          <select class="form-select @error('menu_item_id') is-invalid @enderror" id="menu_item_id" name="menu_item_id" required>
            <option value="">Choose menu item</option>
            @foreach ($menuItems as $mi)
              <option value="{{ $mi->id }}" @selected((string) old('menu_item_id') === (string) $mi->id)>{{ $mi->name }}</option>
            @endforeach
          </select>
          @error('menu_item_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
          @if ($menuItems->isEmpty())
            <div class="form-text text-danger">Every active menu item already has a recipe, or no menu item exists yet.</div>
          @endif
        @endif
      </div>

      <label class="form-label">Ingredients (quantity needed to make <strong>one</strong> item)</label>
      @if ($errors->has('items'))
        <div class="alert alert-danger py-2 small">{{ $errors->first('items') }}</div>
      @endif

      <div id="recipe-rows"></div>

      <button type="button" class="btn btn-light mb-3" id="add-row"><i class="bi bi-plus-lg"></i> Add ingredient</button>

      <div class="d-flex gap-2 mt-2">
        <button class="btn btn-accent" type="submit">
          <i class="bi bi-check-lg"></i> {{ $isEdit ? 'Save changes' : 'Create recipe' }}
        </button>
        <a class="btn btn-light" href="{{ route('recipes.index') }}">Cancel</a>
      </div>
    </form>
  </div>
</div>

@push('scripts')
<script>
  (function () {
    var ingredients = @json($ingredients->map(fn ($i) => ['id' => $i->id, 'name' => $i->name, 'unit' => $i->unit])->values());
    var initial = @json(old('items', $rows));
    var errs = @json($errors->messages());
    var box = document.getElementById('recipe-rows');
    var next = 0;

    function esc(s) {
      return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    function updateUnit(row) {
      var sel = row.querySelector('select');
      var opt = sel.options[sel.selectedIndex];
      row.querySelector('.unit-label').textContent = (opt && opt.dataset.unit) ? opt.dataset.unit : 'unit';
    }

    function addRow(i, d) {
      d = d || {};
      var opts = '<option value="">Choose ingredient</option>' + ingredients.map(function (x) {
        var sel = String(d.ingredient_id) === String(x.id) ? ' selected' : '';
        return '<option value="' + x.id + '" data-unit="' + esc(x.unit) + '"' + sel + '>' + esc(x.name) + '</option>';
      }).join('');
      var eI = errs['items.' + i + '.ingredient_id'];
      var eQ = errs['items.' + i + '.quantity'];
      var row = document.createElement('div');
      row.className = 'row g-2 align-items-start mb-2';
      row.innerHTML =
        '<div class="col-md-6"><select class="form-select' + (eI ? ' is-invalid' : '') + '" name="items[' + i + '][ingredient_id]" required>' + opts + '</select>' +
          (eI ? '<div class="invalid-feedback d-block">' + esc(eI[0]) + '</div>' : '') + '</div>' +
        '<div class="col-md-4"><div class="input-group"><input class="form-control' + (eQ ? ' is-invalid' : '') + '" type="number" step="0.001" min="0.001" name="items[' + i + '][quantity]" value="' + esc(d.quantity == null ? '' : d.quantity) + '" placeholder="Quantity" required>' +
          '<span class="input-group-text unit-label">unit</span></div>' +
          (eQ ? '<div class="invalid-feedback d-block">' + esc(eQ[0]) + '</div>' : '') + '</div>' +
        '<div class="col-md-2"><button type="button" class="btn btn-light text-danger w-100 remove-row" aria-label="Remove ingredient"><i class="bi bi-trash"></i></button></div>';
      box.appendChild(row);
      row.querySelector('select').addEventListener('change', function () { updateUnit(row); });
      row.querySelector('.remove-row').addEventListener('click', function () {
        if (box.children.length > 1) { row.remove(); }
        else { row.querySelector('select').value = ''; row.querySelector('input').value = ''; updateUnit(row); }
      });
      updateUnit(row);
    }

    Object.keys(initial || {}).forEach(function (k) {
      addRow(k, initial[k]);
      next = Math.max(next, parseInt(k, 10) + 1);
    });
    if (!box.children.length) { addRow(next++, {}); }

    document.getElementById('add-row').addEventListener('click', function () { addRow(next++, {}); });
  })();
</script>
@endpush
