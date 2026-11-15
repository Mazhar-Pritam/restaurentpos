<div class="card-x">
  <div class="card-h">{{ $category->exists ? 'Update category details' : 'Category details' }}</div>
  <div class="card-b">
    <form method="POST"
          action="{{ $category->exists ? route('categories.update', $category) : route('categories.store') }}">
      @csrf
      @if ($category->exists)
        @method('PUT')
      @endif

      <div class="mb-3">
        <label class="form-label" for="name">Category name</label>
        <input class="form-control @error('name') is-invalid @enderror" id="name" name="name" type="text"
               value="{{ old('name', $category->name) }}" maxlength="255" required autofocus>
        @error('name')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>

      <div class="mb-3">
        <label class="form-label" for="is_active">Status</label>
        <select class="form-select @error('is_active') is-invalid @enderror" id="is_active" name="is_active" required>
          <option value="1" @selected((string) old('is_active', $category->exists ? (int) $category->is_active : 1) === '1')>
            Active
          </option>
          <option value="0" @selected((string) old('is_active', $category->exists ? (int) $category->is_active : 1) === '0')>
            Inactive
          </option>
        </select>
        @error('is_active')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>

      <div class="d-flex gap-2">
        <button class="btn btn-accent" type="submit">
          <i class="bi bi-check-lg"></i> {{ $category->exists ? 'Save changes' : 'Create category' }}
        </button>
        <a class="btn btn-light" href="{{ route('categories.index') }}">Cancel</a>
      </div>
    </form>
  </div>
</div>
