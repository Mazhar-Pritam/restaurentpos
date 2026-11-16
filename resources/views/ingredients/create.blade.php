@extends('Layouts.master')

@section('title', 'Add ingredient')

@section('content')
  <div class="page-head">
    <div>
      <h1>Add ingredient</h1>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb small mb-0">
          <li class="breadcrumb-item"><a href="{{ route('ingredients.index') }}">Ingredients</a></li>
          <li class="breadcrumb-item active" aria-current="page">Add</li>
        </ol>
      </nav>
    </div>
  </div>

  @include('ingredients._form')
@endsection
