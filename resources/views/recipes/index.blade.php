@extends('Layouts.master')

@section('title', 'Recipes')

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
      <h1>Recipes</h1>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb small mb-0">
          <li class="breadcrumb-item">Menu</li>
          <li class="breadcrumb-item active" aria-current="page">Recipes</li>
        </ol>
      </nav>
    </div>
    @can('recipes.create')
      <a class="btn btn-accent" href="{{ route('recipes.create') }}">
        <i class="bi bi-plus-lg"></i> Add recipe
      </a>
    @endcan
  </div>

  <div class="card-x">
    <div class="card-h">
      <span>Menu item recipes</span>
      <form class="d-flex gap-2" method="GET" action="{{ route('recipes.index') }}">
        <input class="form-control" type="search" name="search" value="{{ request('search') }}"
               placeholder="Search menu item" aria-label="Search menu item">
        <button class="btn btn-light" type="submit"><i class="bi bi-search"></i> Search</button>
        @if (request()->filled('search'))
          <a class="btn btn-light" href="{{ route('recipes.index') }}">Clear</a>
        @endif
      </form>
    </div>

    <div class="table-responsive">
      <table class="table table-x align-middle mb-0">
        <thead>
          <tr>
            <th scope="col">Menu item</th>
            <th scope="col">Ingredients</th>
            <th scope="col">Cost / item</th>
            <th scope="col">Price</th>
            <th scope="col">Food cost</th>
            @canany(['recipes.edit', 'recipes.delete'])
              <th scope="col" class="text-end">Actions</th>
            @endcanany
          </tr>
        </thead>
        <tbody>
          @forelse ($items as $item)
            @php
              $cost = $item->recipes->sum(fn ($r) => (float) $r->quantity * (float) ($r->ingredient->average_cost ?? 0));
              $pct  = (float) $item->price > 0 ? round($cost / (float) $item->price * 100) : null;
              $names = $item->recipes->map(fn ($r) => $r->ingredient?->name)->filter();
            @endphp
            <tr>
              <td>
                {{ $item->name }}
                <div class="small text-muted">{{ $item->category?->name }}</div>
              </td>
              <td>
                {{ $names->take(3)->implode(', ') }}
                @if ($names->count() > 3)
                  <span class="text-muted">+{{ $names->count() - 3 }} more</span>
                @endif
              </td>
              <td>৳{{ number_format($cost, 2) }}</td>
              <td>৳{{ number_format($item->price, 2) }}</td>
              <td>
                @if ($pct === null)
                  —
                @else
                  <span class="status {{ $pct > 40 ? 'low' : 'paid' }}">{{ $pct }}%</span>
                @endif
              </td>
              @canany(['recipes.edit', 'recipes.delete'])
                <td class="text-end">
                  @can('recipes.edit')
                    <a class="btn btn-sm btn-light" href="{{ route('recipes.edit', $item) }}">
                      <i class="bi bi-pencil"></i> Edit
                    </a>
                  @endcan
                  @can('recipes.delete')
                    <form class="d-inline" method="POST" action="{{ route('recipes.destroy', $item) }}"
                          onsubmit="return confirm('Delete the recipe of this menu item?');">
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
              <td colspan="{{ auth()->user()->can('recipes.edit') || auth()->user()->can('recipes.delete') ? 6 : 5 }}"
                  class="text-center text-muted py-4">No recipes found.</td>
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
