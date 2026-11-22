@extends('Layouts.master')

@section('title', 'Edit purchase')

@section('content')
  @if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      {{ session('error') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  <div class="page-head">
    <div>
      <h1>Edit purchase</h1>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb small mb-0">
          <li class="breadcrumb-item"><a href="{{ route('purchases.index') }}">Purchases</a></li>
          <li class="breadcrumb-item active" aria-current="page">{{ $purchase->invoice_no }}</li>
        </ol>
      </nav>
    </div>
  </div>

  @include('purchases._form')
@endsection
