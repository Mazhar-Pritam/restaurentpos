<div class="card-x">
  <div class="card-h">{{ $ingredient->exists ? 'Update ingredient details' : 'Ingredient details' }}</div>
  <div class="card-b">
    <form method="POST"
          action="{{ $ingredient->exists ? route('ingredients.update', $ingredient) : route('ingredients.store') }}">
      @csrf
      @if ($ingredient->exists)
        @method('PUT')
      @endif

      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label" for="name">Ingredient name</label>
          <input class="form-control @error('name') is-invalid @enderror" id="name" name="name" type="text"
                 value="{{ old('name', $ingredient->name) }}" maxlength="255" required autofocus>
          @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-3">
          <label class="form-label" for="unit">Unit</label>
          <select class="form-select @error('unit') is-invalid @enderror" id="unit" name="unit" required>
            <option value="">Choose unit</option>
            @foreach (\App\Models\Ingredient::UNITS as $unit)
              <option value="{{ $unit }}" @selected(old('unit', $ingredient->unit) === $unit)>{{ $unit }}</option>
            @endforeach
          </select>
          @error('unit') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-3">
          <label class="form-label" for="reorder_level">Reorder level</label>
          <input class="form-control @error('reorder_level') is-invalid @enderror" id="reorder_level" name="reorder_level"
                 type="number" step="0.001" min="0" value="{{ old('reorder_level', $ingredient->reorder_level ?? 0) }}" required>
          @error('reorder_level') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        @if ($ingredient->exists)
          <div class="col-md-6">
            <label class="form-label">Current stock</label>
            <input class="form-control" type="text" disabled
                   value="{{ $ingredient->formatQty($ingredient->stock_quantity) }} {{ $ingredient->unit }}">
            <div class="form-text">Stock changes through purchases, adjustments and orders.</div>
          </div>
          <div class="col-md-6">
            <label class="form-label">Average cost</label>
            <input class="form-control" type="text" disabled
                   value="৳{{ number_format($ingredient->average_cost, 2) }} per {{ $ingredient->unit }}">
            <div class="form-text">Calculated automatically from purchases.</div>
          </div>
        @else
          <div class="col-md-6">
            <label class="form-label" for="stock_quantity">Opening stock <span class="text-muted">(optional)</span></label>
            <input class="form-control @error('stock_quantity') is-invalid @enderror" id="stock_quantity" name="stock_quantity"
                   type="number" step="0.001" min="0" value="{{ old('stock_quantity', 0) }}">
            @error('stock_quantity') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
          <div class="col-md-6">
            <label class="form-label" for="average_cost">Cost per unit (৳) <span class="text-muted">(optional)</span></label>
            <input class="form-control @error('average_cost') is-invalid @enderror" id="average_cost" name="average_cost"
                   type="number" step="0.01" min="0" value="{{ old('average_cost', 0) }}">
            @error('average_cost') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
        @endif

        <div class="col-md-6">
          <label class="form-label" for="is_active">Status</label>
          <select class="form-select @error('is_active') is-invalid @enderror" id="is_active" name="is_active" required>
            <option value="1" @selected((string) old('is_active', $ingredient->exists ? (int) $ingredient->is_active : 1) === '1')>Active</option>
            <option value="0" @selected((string) old('is_active', $ingredient->exists ? (int) $ingredient->is_active : 1) === '0')>Inactive</option>
          </select>
          @error('is_active') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
      </div>

      <div class="d-flex gap-2 mt-4">
        <button class="btn btn-accent" type="submit">
          <i class="bi bi-check-lg"></i> {{ $ingredient->exists ? 'Save changes' : 'Create ingredient' }}
        </button>
        <a class="btn btn-light" href="{{ route('ingredients.index') }}">Cancel</a>
      </div>
    </form>
  </div>
</div>
