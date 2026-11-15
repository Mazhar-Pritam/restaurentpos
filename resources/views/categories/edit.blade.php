@extends('Layouts.master')

@section('title', 'Edit category')

@section('content')
  <div class="page-head">
    <div>
      <h1>Edit category</h1>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb small mb-0">
          <li class="breadcrumb-item"><a href="{{ route('categories.index') }}">Categories</a></li>
          <li class="breadcrumb-item active" aria-current="page">{{ $category->name }}</li>
        </ol>
      </nav>
    </div>
  </div>

  @include('categories._form')
@endsection
