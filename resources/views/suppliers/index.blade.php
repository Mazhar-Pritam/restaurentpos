@extends('Layouts.master')

@section('title', 'Suppliers')

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
      <h1>Suppliers</h1>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb small mb-0">
          <li class="breadcrumb-item">Purchasing</li>
          <li class="breadcrumb-item active" aria-current="page">Suppliers</li>
        </ol>
      </nav>
    </div>
    @can('suppliers.create')
      <a class="btn btn-accent" href="{{ route('suppliers.create') }}">
        <i class="bi bi-plus-lg"></i> Add supplier
      </a>
    @endcan
  </div>

  <div class="card-x">
    <div class="card-h">
      <span>All suppliers</span>
      <form class="d-flex gap-2" method="GET" action="{{ route('suppliers.index') }}">
        <input class="form-control" type="search" name="search" value="{{ request('search') }}"
               placeholder="Search name or phone" aria-label="Search name or phone">
        <button class="btn btn-light" type="submit"><i class="bi bi-search"></i> Search</button>
        @if (request()->filled('search'))
          <a class="btn btn-light" href="{{ route('suppliers.index') }}">Clear</a>
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
            @canany(['suppliers.edit', 'suppliers.delete'])
              <th scope="col" class="text-end">Actions</th>
            @endcanany
          </tr>
        </thead>
        <tbody>
          @forelse ($suppliers as $supplier)
            <tr>
              <td>{{ $supplier->name }}</td>
              <td>{{ $supplier->phone ?: '—' }}</td>
              <td>{{ $supplier->email ?: '—' }}</td>
              <td>{{ $supplier->address ?: '—' }}</td>
              <td>
                @if ($supplier->is_active)
                  <span class="status paid">Active</span>
                @else
                  <span class="status low">Inactive</span>
                @endif
              </td>
              @canany(['suppliers.edit', 'suppliers.delete'])
                <td class="text-end">
                  @can('suppliers.edit')
                    <a class="btn btn-sm btn-light" href="{{ route('suppliers.edit', $supplier) }}">
                      <i class="bi bi-pencil"></i> Edit
                    </a>
                  @endcan
                  @can('suppliers.delete')
                    <form class="d-inline" method="POST" action="{{ route('suppliers.destroy', $supplier) }}"
                          onsubmit="return confirm('Delete this supplier?');">
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
              <td colspan="{{ auth()->user()->can('suppliers.edit') || auth()->user()->can('suppliers.delete') ? 6 : 5 }}"
                  class="text-center text-muted py-4">No suppliers found.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if ($suppliers->hasPages())
      <div class="card-b">{{ $suppliers->links('pagination::bootstrap-5') }}</div>
    @endif
  </div>
@endsection
