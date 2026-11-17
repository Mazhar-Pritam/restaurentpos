@extends('Layouts.master')

@section('title', 'Edit supplier')

@section('content')
  <div class="page-head">
    <div>
      <h1>Edit supplier</h1>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb small mb-0">
          <li class="breadcrumb-item"><a href="{{ route('suppliers.index') }}">Suppliers</a></li>
          <li class="breadcrumb-item active" aria-current="page">{{ $supplier->name }}</li>
        </ol>
      </nav>
    </div>
  </div>

  @include('suppliers._form')
@endsection
