<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'Sign in') · Restaurant POS</title>
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="{{ asset('assets/assets/css/theme.css') }}" rel="stylesheet">
  <style>
    .auth-wrap { min-height: 100vh; display: flex; }
    .auth-side { flex: 1; background: #1b2233; color: #fff; display: none; padding: 48px; flex-direction: column; justify-content: space-between; }
    .auth-side .brand-lg { font-size: 1.4rem; font-weight: 800; display: flex; align-items: center; gap: 10px; }
    .auth-side .brand-lg i { color: #e8401c; }
    .auth-side h2 { font-weight: 800; font-size: 2rem; line-height: 1.25; max-width: 420px; }
    .auth-side p { color: #aab2c5; max-width: 420px; }
    .auth-side ul { list-style: none; padding: 0; margin: 24px 0 0; color: #cfd5e3; }
    .auth-side li { margin-bottom: 10px; }
    .auth-side li i { color: #e8401c; margin-right: 8px; }
    .auth-main { flex: 1; display: flex; align-items: center; justify-content: center; padding: 24px; }
    .auth-card { width: 100%; max-width: 400px; }
    .auth-card h1 { font-size: 1.6rem; font-weight: 800; margin-bottom: 4px; }
    .auth-card .sub { color: #6b7385; margin-bottom: 22px; }
    .auth-mobile-brand { display: flex; align-items: center; gap: 8px; font-weight: 800; font-size: 1.2rem; margin-bottom: 24px; }
    .auth-mobile-brand i { color: #e8401c; }
    @media (min-width: 992px) {
      .auth-side { display: flex; }
      .auth-mobile-brand { display: none; }
    }
  </style>
</head>
<body>
  <div class="auth-wrap">
    <div class="auth-side">
      <div class="brand-lg"><i class="bi bi-fire"></i>Restaurant POS</div>
      <div>
        <h2>Run your restaurant from one place.</h2>
        <p>Orders, kitchen, inventory, purchases and profit reports, all connected.</p>
        <ul>
          <li><i class="bi bi-check2-circle"></i>Fast POS and kitchen display</li>
          <li><i class="bi bi-check2-circle"></i>Ingredient stock with low-stock alerts</li>
          <li><i class="bi bi-check2-circle"></i>Daily sales and profit at a glance</li>
        </ul>
      </div>
      <div class="small" style="color:#7d869b">Restaurant POS demo theme.</div>
    </div>

    <div class="auth-main">
      <div class="auth-card">
        <div class="auth-mobile-brand"><i class="bi bi-fire"></i>Restaurant POS</div>
        <div class="card-x p-4">
          @yield('content')
        </div>
      </div>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // show / hide password
    document.querySelectorAll('[data-toggle-password]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var input = document.getElementById(btn.dataset.togglePassword);
        var show = input.type === 'password';
        input.type = show ? 'text' : 'password';
        btn.querySelector('i').className = 'bi ' + (show ? 'bi-eye-slash' : 'bi-eye');
      });
    });
  </script>
</body>
</html>
