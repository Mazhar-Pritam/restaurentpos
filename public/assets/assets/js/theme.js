/* Shared shell: builds the sidebar, top bar and footer once, so every page stays consistent. */
(function () {
  // href is null for pages that are not designed yet (shown as "soon").
  const MENU = [
    ['Overview', [['Dashboard', 'bi-speedometer2', 'dashboard.html']]],
    ['Orders', [['POS terminal', 'bi-receipt', 'pos.html'], ['All orders', 'bi-list-check', 'orders/index.html'], ['Sales returns', 'bi-arrow-return-left', 'orders/returns.html']]],
    ['Restaurant', [['Tables', 'bi-grid-3x3-gap', 'tables/index.html'], ['Kitchen display', 'bi-fire', 'kitchen/index.html'], ['Reservations', 'bi-calendar-check', 'tables/reservations.html']]],
    ['Menu', [['Menu items', 'bi-egg-fried', 'menu/index.html'], ['Categories', 'bi-tags', 'menu/categories.html'], ['Recipes', 'bi-journal-text', 'menu/recipes.html']]],
    ['Inventory', [['Ingredients', 'bi-box-seam', 'inventory/index.html'], ['Low stock', 'bi-exclamation-triangle', 'inventory/low-stock.html'], ['Stock adjustments', 'bi-sliders', 'inventory/adjust.html'], ['Damage and waste', 'bi-trash3', 'inventory/waste.html']]],
    ['Purchasing', [['Purchases', 'bi-truck', 'purchases/index.html'], ['Purchase returns', 'bi-box-arrow-left', 'purchases/returns.html'], ['Suppliers', 'bi-shop', 'suppliers/index.html']]],
    ['People', [['Customers', 'bi-people', 'customers/index.html'], ['Users and roles', 'bi-person-gear', 'users/index.html']]],
    ['Money', [['Expenses', 'bi-wallet2', 'expenses/index.html'], ['Cash register', 'bi-cash-stack', 'expenses/cash-register.html']]],
    ['Reports', [['Sales report', 'bi-bar-chart-line', 'reports/sales.html'], ['Purchase report', 'bi-bag', 'reports/purchases.html'], ['Inventory report', 'bi-boxes', 'reports/inventory.html'], ['Profit and loss', 'bi-graph-up-arrow', 'reports/profit-loss.html']]],
    ['Admin', [['Settings', 'bi-gear', 'settings/index.html']]],
  ];

  const root = document.body.dataset.root || '';
  const active = document.body.dataset.page;
  const nav = MENU.map(([group, items]) =>
    `<div class="grp">${group}</div>` + items.map(([label, icon, href]) =>
      href
        ? `<a class="nav-item ${href === active ? 'active' : ''}" href="${root + href}"><i class="bi ${icon}"></i>${label}</a>`
        : `<a class="nav-item" href="#" aria-disabled="true"><i class="bi ${icon}"></i>${label}<span class="soon">soon</span></a>`
    ).join('')
  ).join('');

  document.getElementById('app-sidebar').outerHTML = `
    <aside class="sidebar" aria-label="Main navigation">
      <a class="brand" href="${root}dashboard.html"><i class="bi bi-fire"></i>Restaurant POS</a>${nav}
    </aside>`;

  document.getElementById('app-topbar').outerHTML = `
    <header class="topbar">
      <button class="btn btn-light" id="toggle" aria-label="Toggle sidebar"><i class="bi bi-list"></i></button>
      <input class="form-control search d-none d-md-block" type="search" placeholder="Search orders, items, tables">
      <div class="ms-auto d-flex align-items-center gap-2">
        <button class="btn btn-light position-relative" aria-label="Notifications"><i class="bi bi-bell"></i>
          <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">3</span></button>
        <div class="dropdown">
          <button class="btn btn-light dropdown-toggle" data-bs-toggle="dropdown"><i class="bi bi-person-circle"></i> Manager Demo</button>
          <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="#">Profile</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="#">Log out</a></li>
          </ul>
        </div>
      </div>
    </header>`;

  const footer = document.getElementById('app-footer');
  if (footer) footer.outerHTML = '<footer class="footer">Restaurant POS demo theme. Sample data only.</footer>';

  document.getElementById('toggle').addEventListener('click', () =>
    document.body.classList.toggle(window.innerWidth < 992 ? 'sidebar-open' : 'collapsed'));
})();

// Money display only. Real totals are always calculated by the server.
window.money = (n) => '৳' + Number(n).toLocaleString('en-BD', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
