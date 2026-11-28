@extends('Layouts.master')

@section('title', 'New expense')

@section('content')
  <div class="page-head">
    <div>
      <h1>New expense</h1>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb small mb-0">
          <li class="breadcrumb-item"><a href="{{ route('expenses.index') }}">Expenses</a></li>
          <li class="breadcrumb-item active" aria-current="page">New</li>
        </ol>
      </nav>
    </div>
  </div>

  @include('expenses._form')
@endsection
