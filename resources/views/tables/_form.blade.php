<div class="card-x">
  <div class="card-h">{{ $table->exists ? 'Update table details' : 'Table details' }}</div>
  <div class="card-b">
    <form method="POST"
          action="{{ $table->exists ? route('tables.update', $table) : route('tables.store') }}">
      @csrf
      @if ($table->exists)
        @method('PUT')
      @endif

      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label" for="name">Table name</label>
          <input class="form-control @error('name') is-invalid @enderror" id="name" name="name" type="text"
                 value="{{ old('name', $table->name) }}" maxlength="50" placeholder="T1" required autofocus>
          @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-6">
          <label class="form-label" for="capacity">Capacity (seats)</label>
          <input class="form-control @error('capacity') is-invalid @enderror" id="capacity" name="capacity" type="number"
                 min="1" max="100" value="{{ old('capacity', $table->capacity) }}" required>
          @error('capacity') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-6">
          <label class="form-label" for="status">Current status</label>
          <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
            @foreach (\App\Models\RestaurantTable::STATUSES as $value => $label)
              <option value="{{ $value }}" @selected(old('status', $table->status) === $value)>{{ $label }}</option>
            @endforeach
          </select>
          @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-6">
          <label class="form-label" for="is_active">Availability for use</label>
          <select class="form-select @error('is_active') is-invalid @enderror" id="is_active" name="is_active" required>
            <option value="1" @selected((string) old('is_active', $table->exists ? (int) $table->is_active : 1) === '1')>Active</option>
            <option value="0" @selected((string) old('is_active', $table->exists ? (int) $table->is_active : 1) === '0')>Inactive</option>
          </select>
          @error('is_active') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
      </div>

      <div class="d-flex gap-2 mt-4">
        <button class="btn btn-accent" type="submit">
          <i class="bi bi-check-lg"></i> {{ $table->exists ? 'Save changes' : 'Create table' }}
        </button>
        <a class="btn btn-light" href="{{ route('tables.index') }}">Cancel</a>
      </div>
    </form>
  </div>
</div>
