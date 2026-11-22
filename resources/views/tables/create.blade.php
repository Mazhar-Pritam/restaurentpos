@extends('Layouts.master')

@section('title', 'Add table')

@section('content')
  <div class="page-head">
    <div>
      <h1>Add table</h1>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb small mb-0">
          <li class="breadcrumb-item"><a href="{{ route('tables.index') }}">Tables</a></li>
          <li class="breadcrumb-item active" aria-current="page">Add</li>
        </ol>
      </nav>
    </div>
  </div>

  @include('tables._form')
@endsection
