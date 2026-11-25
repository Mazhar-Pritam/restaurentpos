@extends('Layouts.master')
@section('title', 'Reservations')
@section('content')
  @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
  @endif
  @if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
  @endif
  <div class="page-head">
    <div><h1>Reservations</h1></div>
    @can('reservations.create')
      <a class="btn btn-accent" href="{{ route('reservations.create') }}"><i class="bi bi-plus-lg"></i> New reservation</a>
    @endcan
  </div>
  <div class="card-x">
    <div class="card-h">
      <span>Bookings</span>
      <form class="d-flex gap-2" method="GET" action="{{ route('reservations.index') }}">
        <select class="form-select" name="status">
          <option value="">All statuses</option>
          @foreach (\App\Models\Reservation::STATUSES as $value => $label)
            <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
          @endforeach
        </select>
        <input class="form-control" type="date" name="date" value="{{ request('date') }}">
        <input class="form-control" type="search" name="search" value="{{ request('search') }}" placeholder="Guest or phone">
        <button class="btn btn-light" type="submit">Search</button>
      </form>
    </div>
    <div class="table-responsive">
      <table class="table table-x align-middle mb-0">
        <thead><tr><th>Guest</th><th>Date & time</th><th>Table</th><th>Party</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
        <tbody>
          @forelse ($reservations as $reservation)
            <tr>
              <td>
                <strong>{{ $reservation->guest_name }}</strong>
                @if ($reservation->phone)<div class="small text-muted">{{ $reservation->phone }}</div>@endif
              </td>
              <td>{{ $reservation->reservation_at->format('d M Y, h:i A') }}</td>
              <td>{{ $reservation->table?->name ?? '·' }}</td>
              <td>{{ $reservation->party_size }}</td>
              <td>
                <span class="status {{ $reservation->status === 'cancelled' ? 'low' : ($reservation->status === 'seated' ? 'cooking' : 'paid') }}">
                  {{ \App\Models\Reservation::STATUSES[$reservation->status] ?? $reservation->status }}
                </span>
              </td>
              <td class="text-end">
                @can('reservations.edit')
                  <a class="btn btn-sm btn-light" href="{{ route('reservations.edit', $reservation) }}"><i class="bi bi-pencil"></i> Edit</a>
                @endcan
                @can('reservations.delete')
                  <form class="d-inline" method="POST" action="{{ route('reservations.destroy', $reservation) }}" onsubmit="return confirm('Cancel this reservation?');">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-light text-danger" type="submit"><i class="bi bi-x-circle"></i> Cancel</button>
                  </form>
                @endcan
              </td>
            </tr>
          @empty
            <tr><td colspan="6" class="text-center text-muted py-4">No reservations found.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if ($reservations->hasPages())<div class="card-b">{{ $reservations->links('pagination::bootstrap-5') }}</div>@endif
  </div>
@endsection
