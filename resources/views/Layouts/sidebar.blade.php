@php
  // [group, [[label, icon, path (without .html), or null for "soon"], ...]]
  // Path = the URL after your site root. Change to match your routes.
  $menu = [
    ['Overview',   [['Dashboard', 'bi-speedometer2', 'dashboard']]],
    ['Orders',     [['POS terminal', 'bi-receipt', 'pos'], ['All orders', 'bi-list-check', 'orders'], ['Sales returns', 'bi-arrow-return-left', 'orders/returns']]],
    ['Restaurant', [['Tables', 'bi-grid-3x3-gap', 'tables'], ['Kitchen display', 'bi-fire', 'kitchen'], ['Reservations', 'bi-calendar-check', 'tables/reservations']]],
    ['Menu',       [['Menu items', 'bi-egg-fried', 'menu'], ['Categories', 'bi-tags', 'menu/categories'], ['Recipes', 'bi-journal-text', 'menu/recipes']]],
    ['Inventory',  [['Ingredients', 'bi-box-seam', 'inventory'], ['Low stock', 'bi-exclamation-triangle', 'inventory/low-stock'], ['Stock adjustments', 'bi-sliders', 'inventory/adjust'], ['Damage and waste', 'bi-trash3', 'inventory/waste']]],
    ['Purchasing', [['Purchases', 'bi-truck', 'purchases'], ['Purchase returns', 'bi-box-arrow-left', 'purchases/returns'], ['Suppliers', 'bi-shop', 'suppliers']]],
    ['People',     [['Customers', 'bi-people', 'customers'], ['Users and roles', 'bi-person-gear', 'users']]],
    ['Money',      [['Expenses', 'bi-wallet2', 'expenses'], ['Cash register', 'bi-cash-stack', 'expenses/cash-register']]],
    ['Reports',    [['Sales report', 'bi-bar-chart-line', 'reports/sales'], ['Purchase report', 'bi-bag', 'reports/purchases'], ['Inventory report', 'bi-boxes', 'reports/inventory'], ['Profit and loss', 'bi-graph-up-arrow', 'reports/profit-loss']]],
    ['Admin',      [['Settings', 'bi-gear', 'settings']]],
  ];
@endphp

<aside class="sidebar" aria-label="Main navigation">
  <a class="brand" href="{{ url('dashboard') }}"><i class="bi bi-fire"></i>Restaurant POS</a>

  @foreach ($menu as [$group, $items])
    <div class="grp">{{ $group }}</div>
    @foreach ($items as [$label, $icon, $path])
      @if ($path)
        <a class="nav-item {{ request()->is($path, $path.'/*') ? 'active' : '' }}" href="{{ url($path) }}">
          <i class="bi {{ $icon }}"></i>{{ $label }}
        </a>
      @else
        <a class="nav-item" href="#" aria-disabled="true"><i class="bi {{ $icon }}"></i>{{ $label }}<span class="soon">soon</span></a>
      @endif
    @endforeach
  @endforeach
</aside>
