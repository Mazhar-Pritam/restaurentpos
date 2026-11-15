@extends('Layouts.auth')
@section('title', 'Create account')
@section('content')
  <h1>Create account</h1>
  <div class="sub">Fill in your details to get started.</div>

  <form method="POST" action="{{ url('/register') }}" novalidate>
    @csrf

    <label class="form-label" for="name">Full name</label>
    <div class="input-group mb-3">
      <span class="input-group-text"><i class="bi bi-person"></i></span>
      <input id="name" class="form-control @error('name') is-invalid @enderror" type="text" name="name"
             value="{{ old('name') }}" placeholder="Your name" required autofocus>
      @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <label class="form-label" for="email">Email</label>
    <div class="input-group mb-3">
      <span class="input-group-text"><i class="bi bi-envelope"></i></span>
      <input id="email" class="form-control @error('email') is-invalid @enderror" type="email" name="email"
             value="{{ old('email') }}" placeholder="you@example.com" required>
      @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <label class="form-label" for="password">Password</label>
    <div class="input-group mb-3">
      <span class="input-group-text"><i class="bi bi-lock"></i></span>
      <input id="password" class="form-control @error('password') is-invalid @enderror" type="password" name="password"
             placeholder="At least 8 characters" required>
      <button class="btn btn-light border" type="button" data-toggle-password="password" aria-label="Show password"><i class="bi bi-eye"></i></button>
      @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <label class="form-label" for="password_confirmation">Confirm password</label>
    <div class="input-group mb-3">
      <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
      <input id="password_confirmation" class="form-control" type="password" name="password_confirmation"
             placeholder="Repeat password" required>
      <button class="btn btn-light border" type="button" data-toggle-password="password_confirmation" aria-label="Show password"><i class="bi bi-eye"></i></button>
    </div>

    <button class="btn btn-accent w-100 py-2" type="submit">Create account</button>
  </form>

  <div class="text-center small mt-3">
    Already have an account? <a href="{{ url('/login') }}" class="text-accent fw-semibold">Sign in</a>
  </div>
@endsection
