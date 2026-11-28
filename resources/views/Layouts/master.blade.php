@php
  // [group, [[label, icon, path (without .html), or null for "soon"], ...]]
  // Path = the URL after your site root. Change to match your routes.
  $menu = [
    ['Overview',   [['Dashboard', 'bi-speedometer2', 'dashboard']]],
    ['Orders',     [['POS terminal', 'bi-receipt', 'pos'], ['All orders', 'bi-list-check', 'orders'], ['Sales returns', 'bi-arrow-return-left', 'orders/returns']]],
    ['Restaurant', [['Tables', 'bi-grid-3x3-gap', 'tables'], ['Kitchen display', 'bi-fire', 'kitchen'], ['Reservations', 'bi-calendar-check', 'reservations']]],
    ['Menu',       [['Menu items', 'bi-egg-fried', 'menu'], ['Categories', 'bi-tags', 'menu/categories'], ['Recipes', 'bi-journal-text', 'menu/recipes']]],
    ['Inventory',  [['Ingredients', 'bi-box-seam', 'inventory'], ['Low stock', 'bi-exclamation-triangle', 'inventory/low-stock'], ['Stock adjustments', 'bi-sliders', 'stock-adjustments']]],
    ['Purchasing', [['Purchases', 'bi-truck', 'purchases'], ['Purchase returns', 'bi-box-arrow-left', 'purchases/returns'], ['Suppliers', 'bi-shop', 'suppliers']]],
    ['People',     [['Customers', 'bi-people', 'customers'], ['Users and roles', 'bi-person-gear', 'users']]],
    ['Money',      [['Expenses', 'bi-wallet2', 'expenses'], ['Cash register', 'bi-cash-stack', 'expenses/cash-register']]],
    ['Reports',    [['Sales report', 'bi-bar-chart-line', 'reports/sales'], ['Purchase report', 'bi-bag', 'reports/purchases'], ['Inventory report', 'bi-boxes', 'reports/inventory'], ['Profit and loss', 'bi-graph-up-arrow', 'reports/profit-loss']]],
    ['Admin',      [['Settings', 'bi-gear', 'settings']]],
  ];
    $paths   = collect($menu)->flatMap(fn ($g) => collect($g[1])->pluck(2))->filter();
  $current = $paths->filter(fn ($p) => request()->is($p, $p.'/*'))
                   ->sortByDesc(fn ($p) => strlen($p))->first();
@endphp
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

  {{-- ===== SIDEBAR ===== --}}
  <aside class="sidebar" aria-label="Main navigation">
    <a class="brand" href="{{ url('dashboard') }}"><i class="bi bi-fire"></i>Restaurant POS</a>

    @foreach ($menu as [$group, $items])
      <div class="grp">{{ $group }}</div>
      @foreach ($items as [$label, $icon, $path])
        @if ($path)
        <a class="nav-item {{ $path === $current ? 'active' : '' }}" href="{{ url($path) }}">
            <i class="bi {{ $icon }}"></i>{{ $label }}
          </a>
        @else
          <a class="nav-item" href="#" aria-disabled="true"><i class="bi {{ $icon }}"></i>{{ $label }}<span class="soon">soon</span></a>
        @endif
      @endforeach
    @endforeach
  </aside>

  <div class="main">

    {{-- ===== TOPBAR ===== --}}
    <header class="topbar">
      <button class="btn btn-light" id="toggle" aria-label="Toggle sidebar"><i class="bi bi-list"></i></button>
      <input class="form-control search d-none d-md-block" type="search" placeholder="Search orders, items, tables">
      <div class="ms-auto d-flex align-items-center gap-2">
        <button class="btn btn-light position-relative" aria-label="Notifications"><i class="bi bi-bell"></i>
          <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">3</span></button>
        <div class="dropdown">
          <button class="btn btn-light dropdown-toggle" data-bs-toggle="dropdown"><i class="bi bi-person-circle"></i> {{ auth()->user()->name ?? 'Guest' }}</button>
          <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="#">Profile</a></li>
            <li><hr class="dropdown-divider"></li>
                
            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <button class="dropdown-item text-danger" type="submit"><i class="bi bi-box-arrow-right me-1"></i>Log out</button>
            </form>
          </li>
          </ul>
        </div>
      </div>
    </header>

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