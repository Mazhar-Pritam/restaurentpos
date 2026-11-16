<div class="card-x">
  <div class="card-h">{{ $menuItem->exists ? 'Update menu item details' : 'Menu item details' }}</div>
  <div class="card-b">
    <form method="POST" enctype="multipart/form-data"
          action="{{ $menuItem->exists ? route('menu-items.update', $menuItem) : route('menu-items.store') }}">
      @csrf
      @if ($menuItem->exists)
        @method('PUT')
      @endif

      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label" for="name">Item name</label>
          <input class="form-control @error('name') is-invalid @enderror" id="name" name="name" type="text"
                 value="{{ old('name', $menuItem->name) }}" maxlength="255" required autofocus>
          @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-6">
          <label class="form-label" for="category_id">Category</label>
          <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
            <option value="">Choose category</option>
            @foreach ($categories as $category)
              <option value="{{ $category->id }}" @selected((string) old('category_id', $menuItem->category_id) === (string) $category->id)>
                {{ $category->name }}{{ $category->is_active ? '' : ' (inactive)' }}
              </option>
            @endforeach
          </select>
          @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-6">
          <label class="form-label" for="price">Price (৳)</label>
          <input class="form-control @error('price') is-invalid @enderror" id="price" name="price" type="number"
                 step="0.01" min="0" value="{{ old('price', $menuItem->price) }}" required>
          @error('price') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-6">
          <label class="form-label" for="is_active">Status</label>
          <select class="form-select @error('is_active') is-invalid @enderror" id="is_active" name="is_active" required>
            <option value="1" @selected((string) old('is_active', $menuItem->exists ? (int) $menuItem->is_active : 1) === '1')>Active</option>
            <option value="0" @selected((string) old('is_active', $menuItem->exists ? (int) $menuItem->is_active : 1) === '0')>Inactive</option>
          </select>
          @error('is_active') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-12">
          <label class="form-label" for="description">Description <span class="text-muted">(optional)</span></label>
          <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                    rows="3" maxlength="1000">{{ old('description', $menuItem->description) }}</textarea>
          @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-12">
          <label class="form-label" for="image">Image <span class="text-muted">(optional, JPG/PNG/WEBP, max 2 MB)</span></label>
          @if ($menuItem->image_url)
            <div class="mb-2">
              <img src="{{ $menuItem->image_url }}" alt="{{ $menuItem->name }}"
                   style="height:90px;width:90px;object-fit:cover;border-radius:10px">
              <div class="small text-muted mt-1">Choose a new file only if you want to replace this image.</div>
            </div>
          @endif
          <input class="form-control @error('image') is-invalid @enderror" id="image" name="image" type="file"
                 accept="image/jpeg,image/png,image/webp">
          @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
      </div>

      <div class="d-flex gap-2 mt-4">
        <button class="btn btn-accent" type="submit">
          <i class="bi bi-check-lg"></i> {{ $menuItem->exists ? 'Save changes' : 'Create menu item' }}
        </button>
        <a class="btn btn-light" href="{{ route('menu-items.index') }}">Cancel</a>
      </div>
    </form>
  </div>
</div>
