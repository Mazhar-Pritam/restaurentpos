@extends('Layouts.master')

@section('title', 'Add menu item')

@section('content')
  <div class="page-head">
    <div>
      <h1>Add menu item</h1>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb small mb-0">
          <li class="breadcrumb-item"><a href="{{ route('menu-items.index') }}">Menu items</a></li>
          <li class="breadcrumb-item active" aria-current="page">Add</li>
        </ol>
      </nav>
    </div>
  </div>

  @include('menu_items._form')
@endsection
