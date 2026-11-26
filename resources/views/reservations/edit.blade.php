@extends('Layouts.master')
@section('title', 'Edit reservation')
@section('content')
  <div class="page-head">
    <div><h1>Edit reservation</h1></div>
  </div>
  @include('reservations._form')
@endsection
