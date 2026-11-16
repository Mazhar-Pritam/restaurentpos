@extends('Layouts.master')

@section('title', 'Menu items')

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
      <h1>Menu items</h1>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb small mb-0">
          <li class="breadcrumb-item">Menu</li>
          <li class="breadcrumb-item active" aria-current="page">Menu items</li>
        </ol>
      </nav>
    </div>
    @can('menu_items.create')
      <a class="btn btn-accent" href="{{ route('menu-items.create') }}">
        <i class="bi bi-plus-lg"></i> Add menu item
      </a>
    @endcan
  </div>

  <div class="card-x">
    <div class="card-h">
      <span>All menu items</span>
      <form class="d-flex gap-2" method="GET" action="{{ route('menu-items.index') }}">
        <select class="form-select" name="category" aria-label="Filter by category">
          <option value="">All categories</option>
          @foreach ($categories as $category)
            <option value="{{ $category->id }}" @selected((string) request('category') === (string) $category->id)>{{ $category->name }}</option>
          @endforeach
        </select>
        <input class="form-control" type="search" name="search" value="{{ request('search') }}"
               placeholder="Search item name" aria-label="Search item name">
        <button class="btn btn-light" type="submit"><i class="bi bi-search"></i> Search</button>
        @if (request()->filled('search') || request()->filled('category'))
          <a class="btn btn-light" href="{{ route('menu-items.index') }}">Clear</a>
        @endif
      </form>
    </div>

    <div class="table-responsive">
      <table class="table table-x align-middle mb-0">
        <thead>
          <tr>
            <th scope="col">Image</th>
            <th scope="col">Name</th>
            <th scope="col">Category</th>
            <th scope="col">Price</th>
            <th scope="col">Status</th>
            @canany(['menu_items.edit', 'menu_items.delete'])
              <th scope="col" class="text-end">Actions</th>
            @endcanany
          </tr>
        </thead>
        <tbody>
          @forelse ($items as $item)
            <tr>
              <td>
                @if ($item->image_url)
                  <img src="{{ $item->image_url }}" alt="{{ $item->name }}"
                       style="height:44px;width:44px;object-fit:cover;border-radius:8px">
                @else
                  <span class="text-muted"><i class="bi bi-image"></i></span>
                @endif
              </td>
              <td>{{ $item->name }}</td>
              <td>{{ $item->category?->name ?? '—' }}</td>
              <td>৳{{ number_format($item->price, 2) }}</td>
              <td>
                @if ($item->is_active)
                  <span class="status paid">Active</span>
                @else
                  <span class="status low">Inactive</span>
                @endif
              </td>
              @canany(['menu_items.edit', 'menu_items.delete'])
                <td class="text-end">
                  @can('menu_items.edit')
                    <a class="btn btn-sm btn-light" href="{{ route('menu-items.edit', $item) }}">
                      <i class="bi bi-pencil"></i> Edit
                    </a>
                  @endcan
                  @can('menu_items.delete')
                    <form class="d-inline" method="POST" action="{{ route('menu-items.destroy', $item) }}"
                          onsubmit="return confirm('Delete this menu item?');">
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
              <td colspan="{{ auth()->user()->can('menu_items.edit') || auth()->user()->can('menu_items.delete') ? 6 : 5 }}"
                  class="text-center text-muted py-4">No menu items found.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if ($items->hasPages())
      <div class="card-b">{{ $items->links('pagination::bootstrap-5') }}</div>
    @endif
  </div>
@endsection
