@extends('Layouts.master')

@section('title', 'Edit recipe')

@section('content')
  <div class="page-head">
    <div>
      <h1>Edit recipe</h1>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb small mb-0">
          <li class="breadcrumb-item"><a href="{{ route('recipes.index') }}">Recipes</a></li>
          <li class="breadcrumb-item active" aria-current="page">{{ $menuItem->name }}</li>
        </ol>
      </nav>
    </div>
  </div>

  @include('recipes._form')
@endsection
