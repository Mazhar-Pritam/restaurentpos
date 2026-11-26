<div class="card-x">
  <div class="card-h">{{ $reservation->exists ? 'Update reservation' : 'Reservation details' }}</div>
  <div class="card-b">
    <form method="POST" action="{{ $reservation->exists ? route('reservations.update', $reservation) : route('reservations.store') }}">
      @csrf
      @if ($reservation->exists) @method('PUT') @endif
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label" for="guest_name">Guest name</label>
          <input class="form-control @error('guest_name') is-invalid @enderror" id="guest_name" name="guest_name"
                 value="{{ old('guest_name', $reservation->guest_name) }}" maxlength="255" required autofocus>
          @error('guest_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-6">
          <label class="form-label" for="phone">Phone</label>
          <input class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone"
                 value="{{ old('phone', $reservation->phone) }}" maxlength="30">
          @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-6">
          <label class="form-label" for="customer_id">Customer</label>
          <select class="form-select @error('customer_id') is-invalid @enderror" id="customer_id" name="customer_id">
            <option value="">Walk-in guest</option>
            @foreach ($customers as $customer)
              <option value="{{ $customer->id }}" @selected((string) old('customer_id', $reservation->customer_id) === (string) $customer->id)>
                {{ $customer->name }}
              </option>
            @endforeach
          </select>
          @error('customer_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-6">
          <label class="form-label" for="table_id">Table</label>
          <select class="form-select @error('table_id') is-invalid @enderror" id="table_id" name="table_id">
            <option value="">Assign later</option>
            @foreach ($tables as $table)
              <option value="{{ $table->id }}" @selected((string) old('table_id', $reservation->table_id) === (string) $table->id)>
                {{ $table->name }} ({{ $table->capacity }} seats)
              </option>
            @endforeach
          </select>
          @error('table_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-4">
          <label class="form-label" for="party_size">Party size</label>
          <input class="form-control @error('party_size') is-invalid @enderror" id="party_size" name="party_size" type="number"
                 min="1" max="100" value="{{ old('party_size', $reservation->party_size) }}" required>
          @error('party_size') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-4">
          <label class="form-label" for="reservation_at">Date and time</label>
          <input class="form-control @error('reservation_at') is-invalid @enderror" id="reservation_at" name="reservation_at" type="datetime-local"
                 value="{{ old('reservation_at', $reservation->reservation_at?->format('Y-m-d\TH:i') ?? now()->format('Y-m-d\TH:i')) }}" required>
          @error('reservation_at') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-4">
          <label class="form-label" for="duration_minutes">Duration (minutes)</label>
          <input class="form-control @error('duration_minutes') is-invalid @enderror" id="duration_minutes" name="duration_minutes" type="number"
                 min="15" max="1440" value="{{ old('duration_minutes', $reservation->duration_minutes) }}" required>
          @error('duration_minutes') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-6">
          <label class="form-label" for="status">Status</label>
          <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
            @foreach (\App\Models\Reservation::STATUSES as $value => $label)
              <option value="{{ $value }}" @selected(old('status', $reservation->status) === $value)>{{ $label }}</option>
            @endforeach
          </select>
          @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-12">
          <label class="form-label" for="note">Note</label>
          <textarea class="form-control @error('note') is-invalid @enderror" id="note" name="note" rows="3"
                    maxlength="1000">{{ old('note', $reservation->note) }}</textarea>
          @error('note') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
      </div>
      <div class="d-flex gap-2 mt-4">
        <button class="btn btn-accent" type="submit"><i class="bi bi-check-lg"></i> {{ $reservation->exists ? 'Save changes' : 'Create reservation' }}</button>
        <a class="btn btn-light" href="{{ route('reservations.index') }}">Cancel</a>
      </div>
    </form>
  </div>
</div>
