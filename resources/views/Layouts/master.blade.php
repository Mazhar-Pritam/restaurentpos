<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'Dashboard') · Restaurant POS</title>
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="{{ asset('assets/assets/css/theme.css') }}" rel="stylesheet">
</head>
<body>
  @include('Layouts.sidebar')
  <div class="main">
    @include('Layouts.topbar')
    <main class="content">
      @yield('content')
    </main>
    <footer class="footer">Restaurant POS demo theme. Sample data only.</footer>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
  <script src="{{ asset('assets/assets/js/theme.js') }}"></script>
  <script src="{{ asset('assets/assets/js/dashboard.js') }}"></script>
  @stack('scripts')
</body>
</html>