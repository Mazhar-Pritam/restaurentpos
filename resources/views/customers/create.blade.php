@extends('Layouts.master')

@section('title', 'Add customer')

@section('content')
  <div class="page-head">
    <div>
      <h1>Add customer</h1>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb small mb-0">
          <li class="breadcrumb-item"><a href="{{ route('customers.index') }}">Customers</a></li>
          <li class="breadcrumb-item active" aria-current="page">Add</li>
        </ol>
      </nav>
    </div>
  </div>

  @include('customers._form')
@endsection
