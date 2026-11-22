@extends('Layouts.master')

@section('title', 'Tables')

@section('content')
  @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif
  @if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      {{ session('error') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  <div class="page-head">
    <div>
      <h1>Tables</h1>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb small mb-0">
          <li class="breadcrumb-item">Restaurant</li>
          <li class="breadcrumb-item active" aria-current="page">Tables</li>
        </ol>
      </nav>
    </div>
    @can('tables.create')
      <a class="btn btn-accent" href="{{ route('tables.create') }}">
        <i class="bi bi-plus-lg"></i> Add table
      </a>
    @endcan
  </div>

  <div class="card-x">
    <div class="card-h">
      <span>All tables</span>
      <form class="d-flex gap-2" method="GET" action="{{ route('tables.index') }}">
        <select class="form-select" name="status" aria-label="Status">
          <option value="">All statuses</option>
          @foreach (\App\Models\RestaurantTable::STATUSES as $value => $label)
            <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
          @endforeach
        </select>
        <input class="form-control" type="search" name="search" value="{{ request('search') }}"
               placeholder="Search table name" aria-label="Search table name">
        <button class="btn btn-light" type="submit"><i class="bi bi-search"></i> Search</button>
        @if (request()->filled('search') || request()->filled('status'))
          <a class="btn btn-light" href="{{ route('tables.index') }}">Clear</a>
        @endif
      </form>
    </div>

    <div class="table-responsive">
      <table class="table table-x align-middle mb-0">
        <thead>
          <tr>
            <th scope="col">Table</th>
            <th scope="col">Seats</th>
            <th scope="col">Status</th>
            <th scope="col">In use</th>
            @canany(['tables.edit', 'tables.delete'])
              <th scope="col" class="text-end">Actions</th>
            @endcanany
          </tr>
        </thead>
        <tbody>
          @forelse ($tables as $table)
            <tr>
              <td>{{ $table->name }}</td>
              <td>{{ $table->capacity }}</td>
              <td>
                <span class="status {{ $table->status === 'available' ? 'paid' : ($table->status === 'occupied' ? 'low' : 'cooking') }}">
                  {{ \App\Models\RestaurantTable::STATUSES[$table->status] ?? ucfirst($table->status) }}
                </span>
              </td>
              <td>
                @if ($table->is_active)
                  <span class="status paid">Active</span>
                @else
                  <span class="status low">Inactive</span>
                @endif
              </td>
              @canany(['tables.edit', 'tables.delete'])
                <td class="text-end">
                  @can('tables.edit')
                    <a class="btn btn-sm btn-light" href="{{ route('tables.edit', $table) }}">
                      <i class="bi bi-pencil"></i> Edit
                    </a>
                  @endcan
                  @can('tables.delete')
                    <form class="d-inline" method="POST" action="{{ route('tables.destroy', $table) }}"
                          onsubmit="return confirm('Delete this table?');">
                      @csrf
                      @method('DELETE')
                      <button class="btn btn-sm btn-light text-danger" type="submit">
                        <i class="bi bi-trash"></i> Delete
                      </button>
                    </form>
                  @endcan
                </td>
              @endcanany
            </tr>
          @empty
            <tr>
              <td colspan="{{ auth()->user()->can('tables.edit') || auth()->user()->can('tables.delete') ? 5 : 4 }}"
                  class="text-center text-muted py-4">No tables found.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if ($tables->hasPages())
      <div class="card-b">{{ $tables->links('pagination::bootstrap-5') }}</div>
    @endif
  </div>
@endsection
