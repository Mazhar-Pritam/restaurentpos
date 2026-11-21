@extends('Layouts.master')

@section('title', 'Add recipe')

@section('content')
  <div class="page-head">
    <div>
      <h1>Add recipe</h1>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb small mb-0">
          <li class="breadcrumb-item"><a href="{{ route('recipes.index') }}">Recipes</a></li>
          <li class="breadcrumb-item active" aria-current="page">Add</li>
        </ol>
      </nav>
    </div>
  </div>

  @include('recipes._form')
@endsection
