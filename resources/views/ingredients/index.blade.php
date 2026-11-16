@extends('Layouts.master')

@section('title', 'Ingredients')

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
      <h1>Ingredients</h1>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb small mb-0">
          <li class="breadcrumb-item">Inventory</li>
          <li class="breadcrumb-item active" aria-current="page">Ingredients</li>
        </ol>
      </nav>
    </div>
    @can('ingredients.create')
      <a class="btn btn-accent" href="{{ route('ingredients.create') }}">
        <i class="bi bi-plus-lg"></i> Add ingredient
      </a>
    @endcan
  </div>

  <div class="card-x">
    <div class="card-h">
      <span>All ingredients</span>
      <form class="d-flex gap-2" method="GET" action="{{ route('ingredients.index') }}">
        <select class="form-select" name="stock" aria-label="Stock filter">
          <option value="">All stock</option>
          <option value="low" @selected(request('stock') === 'low')>Low stock only</option>
        </select>
        <input class="form-control" type="search" name="search" value="{{ request('search') }}"
               placeholder="Search ingredient name" aria-label="Search ingredient name">
        <button class="btn btn-light" type="submit"><i class="bi bi-search"></i> Search</button>
        @if (request()->filled('search') || request()->filled('stock'))
          <a class="btn btn-light" href="{{ route('ingredients.index') }}">Clear</a>
        @endif
      </form>
    </div>

    <div class="table-responsive">
      <table class="table table-x align-middle mb-0">
        <thead>
          <tr>
            <th scope="col">Name</th>
            <th scope="col">In stock</th>
            <th scope="col">Reorder at</th>
            <th scope="col">Avg. cost</th>
            <th scope="col">Status</th>
            @canany(['ingredients.edit', 'ingredients.delete'])
              <th scope="col" class="text-end">Actions</th>
            @endcanany
          </tr>
        </thead>
        <tbody>
          @forelse ($ingredients as $ingredient)
            <tr>
              <td>{{ $ingredient->name }}</td>
              <td>
                <span class="status {{ $ingredient->is_low ? 'low' : 'paid' }}">
                  {{ $ingredient->formatQty($ingredient->stock_quantity) }} {{ $ingredient->unit }}
                </span>
              </td>
              <td>{{ $ingredient->formatQty($ingredient->reorder_level) }} {{ $ingredient->unit }}</td>
              <td>৳{{ number_format($ingredient->average_cost, 2) }}</td>
              <td>
                @if ($ingredient->is_active)
                  <span class="status paid">Active</span>
                @else
                  <span class="status low">Inactive</span>
                @endif
              </td>
              @canany(['ingredients.edit', 'ingredients.delete'])
                <td class="text-end">
                  @can('ingredients.edit')
                    <a class="btn btn-sm btn-light" href="{{ route('ingredients.edit', $ingredient) }}">
                      <i class="bi bi-pencil"></i> Edit
                    </a>
                  @endcan
                  @can('ingredients.delete')
                    <form class="d-inline" method="POST" action="{{ route('ingredients.destroy', $ingredient) }}"
                          onsubmit="return confirm('Delete this ingredient?');">
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
              <td colspan="{{ auth()->user()->can('ingredients.edit') || auth()->user()->can('ingredients.delete') ? 6 : 5 }}"
                  class="text-center text-muted py-4">No ingredients found.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if ($ingredients->hasPages())
      <div class="card-b">{{ $ingredients->links('pagination::bootstrap-5') }}</div>
    @endif
  </div>
@endsection
