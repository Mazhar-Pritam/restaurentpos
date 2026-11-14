/* Dashboard charts (sample numbers). */
const css = (v) => getComputedStyle(document.documentElement).getPropertyValue(v).trim();
const accent = css('--accent'), ink = css('--ink');

new Chart(document.getElementById('salesChart'), {
  type: 'line',
  data: {
    labels: ['Sat', 'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri'],
    datasets: [{ label: 'Net sales (BDT)', data: [48200, 51800, 39400, 42100, 45600, 53900, 61200],
      borderColor: accent, backgroundColor: 'rgba(217,72,15,.1)', fill: true, tension: .3 }],
  },
  options: { maintainAspectRatio: false, plugins: { legend: { display: false } } },
});

new Chart(document.getElementById('typeChart'), {
  type: 'doughnut',
  data: { labels: ['Dine-in', 'Takeaway'], datasets: [{ data: [68, 32], backgroundColor: [accent, ink] }] },
  options: { maintainAspectRatio: false, cutout: '65%', plugins: { legend: { position: 'bottom' } } },
});
