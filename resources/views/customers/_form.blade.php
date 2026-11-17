<div class="card-x">
  <div class="card-h">{{ $customer->exists ? 'Update customer details' : 'Customer details' }}</div>
  <div class="card-b">
    <form method="POST"
          action="{{ $customer->exists ? route('customers.update', $customer) : route('customers.store') }}">
      @csrf
      @if ($customer->exists)
        @method('PUT')
      @endif

      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label" for="name">Name</label>
          <input class="form-control @error('name') is-invalid @enderror" id="name" name="name" type="text"
                 value="{{ old('name', $customer->name) }}" maxlength="255" required autofocus>
          @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-6">
          <label class="form-label" for="phone">Phone <span class="text-muted">(optional)</span></label>
          <input class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" type="text"
                 value="{{ old('phone', $customer->phone) }}" maxlength="30">
          @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-6">
          <label class="form-label" for="email">Email <span class="text-muted">(optional)</span></label>
          <input class="form-control @error('email') is-invalid @enderror" id="email" name="email" type="email"
                 value="{{ old('email', $customer->email) }}" maxlength="255">
          @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-6">
          <label class="form-label" for="is_active">Status</label>
          <select class="form-select @error('is_active') is-invalid @enderror" id="is_active" name="is_active" required>
            <option value="1" @selected((string) old('is_active', $customer->exists ? (int) $customer->is_active : 1) === '1')>Active</option>
            <option value="0" @selected((string) old('is_active', $customer->exists ? (int) $customer->is_active : 1) === '0')>Inactive</option>
          </select>
          @error('is_active') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-12">
          <label class="form-label" for="address">Address <span class="text-muted">(optional)</span></label>
          <input class="form-control @error('address') is-invalid @enderror" id="address" name="address" type="text"
                 value="{{ old('address', $customer->address) }}" maxlength="255">
          @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
      </div>

      <div class="d-flex gap-2 mt-4">
        <button class="btn btn-accent" type="submit">
          <i class="bi bi-check-lg"></i> {{ $customer->exists ? 'Save changes' : 'Create customer' }}
        </button>
        <a class="btn btn-light" href="{{ route('customers.index') }}">Cancel</a>
      </div>
    </form>
  </div>
</div>
