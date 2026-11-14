/* Sidebar, topbar and footer now live in Blade (Layouts/sidebar, topbar, master). This file only handles behaviour. */
(function () {
  const toggle = document.getElementById('toggle');
  if (toggle) {
    toggle.addEventListener('click', () =>
      document.body.classList.toggle(window.innerWidth < 992 ? 'sidebar-open' : 'collapsed'));
  }
})();

// Money display only. Real totals are always calculated by the server.
window.money = (n) => '৳' + Number(n).toLocaleString('en-BD', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
