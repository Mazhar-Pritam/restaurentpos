@extends('Layouts.master')

@section('title', 'Customers')

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
      <h1>Customers</h1>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb small mb-0">
          <li class="breadcrumb-item">People</li>
          <li class="breadcrumb-item active" aria-current="page">Customers</li>
        </ol>
      </nav>
    </div>
    @can('customers.create')
      <a class="btn btn-accent" href="{{ route('customers.create') }}">
        <i class="bi bi-plus-lg"></i> Add customer
      </a>
    @endcan
  </div>

  <div class="card-x">
    <div class="card-h">
      <span>All customers</span>
      <form class="d-flex gap-2" method="GET" action="{{ route('customers.index') }}">
        <input class="form-control" type="search" name="search" value="{{ request('search') }}"
               placeholder="Search name or phone" aria-label="Search name or phone">
        <button class="btn btn-light" type="submit"><i class="bi bi-search"></i> Search</button>
        @if (request()->filled('search'))
          <a class="btn btn-light" href="{{ route('customers.index') }}">Clear</a>
        @endif
      </form>
    </div>

    <div class="table-responsive">
      <table class="table table-x align-middle mb-0">
        <thead>
          <tr>
            <th scope="col">Name</th>
            <th scope="col">Phone</th>
            <th scope="col">Email</th>
            <th scope="col">Address</th>
            <th scope="col">Status</th>
            @canany(['customers.edit', 'customers.delete'])
              <th scope="col" class="text-end">Actions</th>
            @endcanany
          </tr>
        </thead>
        <tbody>
          @forelse ($customers as $customer)
            <tr>
              <td>{{ $customer->name }}</td>
              <td>{{ $customer->phone ?: '—' }}</td>
              <td>{{ $customer->email ?: '—' }}</td>
              <td>{{ $customer->address ?: '—' }}</td>
              <td>
                @if ($customer->is_active)
                  <span class="status paid">Active</span>
                @else
                  <span class="status low">Inactive</span>
                @endif
              </td>
              @canany(['customers.edit', 'customers.delete'])
                <td class="text-end">
                  @can('customers.edit')
                    <a class="btn btn-sm btn-light" href="{{ route('customers.edit', $customer) }}">
                      <i class="bi bi-pencil"></i> Edit
                    </a>
                  @endcan
                  @can('customers.delete')
                    <form class="d-inline" method="POST" action="{{ route('customers.destroy', $customer) }}"
                          onsubmit="return confirm('Delete this customer?');">
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
              <td colspan="{{ auth()->user()->can('customers.edit') || auth()->user()->can('customers.delete') ? 6 : 5 }}"
                  class="text-center text-muted py-4">No customers found.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if ($customers->hasPages())
      <div class="card-b">{{ $customers->links('pagination::bootstrap-5') }}</div>
    @endif
  </div>
@endsection
