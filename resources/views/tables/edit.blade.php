@extends('Layouts.master')

@section('title', 'Edit table')

@section('content')
  <div class="page-head">
    <div>
      <h1>Edit table</h1>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb small mb-0">
          <li class="breadcrumb-item"><a href="{{ route('tables.index') }}">Tables</a></li>
          <li class="breadcrumb-item active" aria-current="page">{{ $table->name }}</li>
        </ol>
      </nav>
    </div>
  </div>

  @include('tables._form')
@endsection
