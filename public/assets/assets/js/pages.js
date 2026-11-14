/* Page helpers: table search and pagination, row sums, kitchen board, charts. */
document.querySelectorAll('.js-table').forEach((box) => {
  const rows = [...box.querySelectorAll('tbody tr')], size = 5;
  let page = 0, hits = rows;
  const info = box.querySelector('[data-info]'), prev = box.querySelector('[data-prev]'), next = box.querySelector('[data-next]');
  const draw = () => {
    const max = Math.max(0, Math.ceil(hits.length / size) - 1);
    page = Math.min(page, max);
    rows.forEach((r) => (r.hidden = true));
    hits.slice(page * size, page * size + size).forEach((r) => (r.hidden = false));
    info.textContent = hits.length ? `${page * size + 1}-${Math.min(hits.length, page * size + size)} of ${hits.length}` : 'No rows match your search';
    prev.disabled = page === 0; next.disabled = page >= max;
  };
  box.querySelector('[data-search]').addEventListener('input', (e) => {
    const q = e.target.value.toLowerCase();
    hits = rows.filter((r) => r.textContent.toLowerCase().includes(q)); page = 0; draw();
  });
  prev.onclick = () => { page--; draw(); }; next.onclick = () => { page++; draw(); };
  draw();
});

/* Line totals (purchase) and count differences (stock adjustment). Display only; the server recalculates. */
const fmt = (n) => '৳' + n.toLocaleString('en-BD', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
function calc() {
  document.querySelectorAll('[data-row]').forEach((r) => {
    const v = (s) => +(r.querySelector(s)?.value) || 0, out = r.querySelector('.line');
    if (out) out.textContent = r.querySelector('.cost') ? fmt(v('.qty') * v('.cost')) : (v('.cnt') - v('.sys')).toFixed(2);
  });
  document.querySelectorAll('[data-sum]').forEach((s) => {
    s.textContent = fmt([...document.querySelectorAll('[data-row] .line')].reduce((t, e) => t + (+e.textContent.replace(/[^0-9.-]/g, '') || 0), 0));
  });
}
document.addEventListener('input', (e) => { if (e.target.closest('[data-row]')) calc(); });
calc();

/* Kitchen board: move a ticket to the next column. */
const AFTER = { cooking: ['ready', 'Mark ready'], ready: ['served', 'Mark served'] };
document.addEventListener('click', (e) => {
  const b = e.target.closest('[data-move]'); if (!b) return;
  const card = b.closest('.kcard'), to = b.dataset.move;
  if (to === 'served') { card.remove(); return; }
  document.querySelector(`[data-col="${to}"] .kbody`).append(card);
  [b.dataset.move, b.textContent] = AFTER[to];
});
setInterval(() => document.querySelectorAll('[data-mins]').forEach((el) => { el.dataset.mins++; el.textContent = el.dataset.mins + ' min'; }), 60000);

document.querySelectorAll('canvas[data-chart]').forEach((c) => {
  if (!window.Chart) return;
  const d = JSON.parse(c.dataset.chart), a = getComputedStyle(document.documentElement).getPropertyValue('--accent').trim();
  new Chart(c, { type: d.type, data: { labels: d.labels, datasets: [{ label: d.label || '', data: d.data, backgroundColor: d.type === 'line' ? 'rgba(217,72,15,.12)' : a, borderColor: a, fill: true, tension: .3 }] },
    options: { maintainAspectRatio: false, plugins: { legend: { display: false } } } });
});
