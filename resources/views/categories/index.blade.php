@extends('Layouts.master')

@section('title', 'Categories')

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
      <h1>Categories</h1>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb small mb-0">
          <li class="breadcrumb-item">Menu</li>
          <li class="breadcrumb-item active" aria-current="page">Categories</li>
        </ol>
      </nav>
    </div>
    @can('categories.create')
      <a class="btn btn-accent" href="{{ route('categories.create') }}">
        <i class="bi bi-plus-lg"></i> Add category
      </a>
    @endcan
  </div>

  <div class="card-x">
    <div class="card-h">
      <span>Menu categories</span>
      <form class="d-flex gap-2" method="GET" action="{{ route('categories.index') }}">
        <input class="form-control" type="search" name="search" value="{{ request('search') }}"
               placeholder="Search category name" aria-label="Search category name">
        <button class="btn btn-light" type="submit"><i class="bi bi-search"></i> Search</button>
        @if (request()->filled('search'))
          <a class="btn btn-light" href="{{ route('categories.index') }}">Clear</a>
        @endif
      </form>
    </div>

    <div class="table-responsive">
      <table class="table table-x align-middle mb-0">
        <thead>
          <tr>
            <th scope="col">Name</th>
            <th scope="col">Slug</th>
            <th scope="col">Status</th>
            @canany(['categories.edit', 'categories.delete'])
              <th scope="col" class="text-end">Actions</th>
            @endcanany
          </tr>
        </thead>
        <tbody>
          @forelse ($categories as $category)
            <tr>
              <td>{{ $category->name }}</td>
              <td>{{ $category->slug }}</td>
              <td>
                @if ($category->is_active)
                  <span class="status paid">Active</span>
                @else
                  <span class="status low">Inactive</span>
                @endif
              </td>
              @canany(['categories.edit', 'categories.delete'])
                <td class="text-end">
                  @can('categories.edit')
                    <a class="btn btn-sm btn-light" href="{{ route('categories.edit', $category) }}">
                      <i class="bi bi-pencil"></i> Edit
                    </a>
                  @endcan
                  @can('categories.delete')
                    <form class="d-inline" method="POST" action="{{ route('categories.destroy', $category) }}"
                          onsubmit="return confirm('Delete this category?');">
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
              <td colspan="{{ auth()->user()->can('categories.edit') || auth()->user()->can('categories.delete') ? 4 : 3 }}"
                  class="text-center text-muted py-4">No categories found.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if ($categories->hasPages())
      <div class="card-b">{{ $categories->links('pagination::bootstrap-5') }}</div>
    @endif
  </div>
@endsection
