@extends('Layouts.master')
@section('title', 'Create reservation')
@section('content')
  <div class="page-head">
    <div><h1>New reservation</h1></div>
  </div>
  @include('reservations._form')
@endsection
