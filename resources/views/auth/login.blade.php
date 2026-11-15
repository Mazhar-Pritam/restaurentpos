@extends('Layouts.auth')
@section('title', 'Sign in')
@section('content')
  <h1>Welcome back</h1>
  <div class="sub">Sign in to continue to your dashboard.</div>

  @if (session('status'))
    <div class="alert alert-success py-2 small">{{ session('status') }}</div>
  @endif

  <form method="POST" action="{{ url('/login') }}" novalidate>
    @csrf

    <label class="form-label" for="email">Email</label>
    <div class="input-group mb-3">
      <span class="input-group-text"><i class="bi bi-envelope"></i></span>
      <input id="email" class="form-control @error('email') is-invalid @enderror" type="email" name="email"
             value="{{ old('email') }}" placeholder="you@example.com" required autofocus>
      @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <label class="form-label" for="password">Password</label>
    <div class="input-group mb-3">
      <span class="input-group-text"><i class="bi bi-lock"></i></span>
      <input id="password" class="form-control @error('password') is-invalid @enderror" type="password" name="password"
             placeholder="Your password" required>
      <button class="btn btn-light border" type="button" data-toggle-password="password" aria-label="Show password"><i class="bi bi-eye"></i></button>
      @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="form-check mb-3">
      <input class="form-check-input" type="checkbox" name="remember" id="remember">
      <label class="form-check-label" for="remember">Remember me</label>
    </div>

    <button class="btn btn-accent w-100 py-2" type="submit">Sign in</button>
  </form>

  <div class="text-center small mt-3">
    New here? <a href="{{ url('/register') }}" class="text-accent fw-semibold">Create an account</a>
  </div>
@endsection
