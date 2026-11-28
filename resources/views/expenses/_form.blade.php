<div class="card-x">
  <div class="card-h">{{ $expense->exists ? 'Update expense' : 'Expense details' }}</div>
  <div class="card-b">
    <form method="POST" action="{{ $expense->exists ? route('expenses.update', $expense) : route('expenses.store') }}">
      @csrf
      @if ($expense->exists)
        @method('PUT')
      @endif

      <div class="row g-3">
        <div class="col-md-4">
          <label class="form-label" for="expense_date">Date</label>
          <input class="form-control @error('expense_date') is-invalid @enderror" id="expense_date" name="expense_date" type="date"
                 value="{{ old('expense_date', $expense->expense_date?->format('Y-m-d')) }}" max="{{ now()->toDateString() }}" required>
          @error('expense_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-4">
          <label class="form-label" for="category">Category</label>
          <select class="form-select @error('category') is-invalid @enderror" id="category" name="category" required>
            <option value="">Choose category</option>
            @foreach (\App\Models\Expense::CATEGORIES as $value => $label)
              <option value="{{ $value }}" @selected(old('category', $expense->category) === $value)>{{ $label }}</option>
            @endforeach
          </select>
          @error('category') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-4">
          <label class="form-label" for="amount">Amount (৳)</label>
          <input class="form-control @error('amount') is-invalid @enderror" id="amount" name="amount" type="number" step="0.01" min="0.01"
                 value="{{ old('amount', $expense->amount) }}" required>
          @error('amount') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-4">
          <label class="form-label" for="payment_method">Paid by</label>
          <select class="form-select @error('payment_method') is-invalid @enderror" id="payment_method" name="payment_method" required>
            @foreach (\App\Models\Expense::PAYMENT_METHODS as $value => $label)
              <option value="{{ $value }}" @selected(old('payment_method', $expense->payment_method) === $value)>{{ $label }}</option>
            @endforeach
          </select>
          @error('payment_method') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-4">
          <label class="form-label" for="paid_to">Paid to <span class="text-muted">(optional)</span></label>
          <input class="form-control @error('paid_to') is-invalid @enderror" id="paid_to" name="paid_to" type="text" maxlength="255"
                 value="{{ old('paid_to', $expense->paid_to) }}">
          @error('paid_to') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-4">
          <label class="form-label" for="note">Note <span class="text-muted">(optional)</span></label>
          <input class="form-control @error('note') is-invalid @enderror" id="note" name="note" type="text" maxlength="255"
                 value="{{ old('note', $expense->note) }}">
          @error('note') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
      </div>

      <div class="d-flex gap-2 mt-4">
        <button class="btn btn-accent" type="submit"><i class="bi bi-check-lg"></i> {{ $expense->exists ? 'Save changes' : 'Save expense' }}</button>
        <a class="btn btn-light" href="{{ route('expenses.index') }}">Cancel</a>
      </div>
    </form>
  </div>
</div>
