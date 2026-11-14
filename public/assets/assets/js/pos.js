/* POS terminal demo. Totals here are for display only; the real system recalculates on the server. */
const SETTINGS = { vatRate: 5, vatInclusive: true, serviceRate: 0 }; // sample values, set in Settings later

const CATS = ['All', 'Burgers', 'Pizza', 'Drinks', 'Desserts'];
const MENU = [
  { id: 1, name: 'Classic Beef Burger', cat: 'Burgers', price: 320, hue: 18 },
  { id: 2, name: 'Chicken Burger', cat: 'Burgers', price: 280, hue: 32 },
  { id: 3, name: 'Margherita Pizza', cat: 'Pizza', price: 550, hue: 4 },
  { id: 4, name: 'Pepperoni Pizza', cat: 'Pizza', price: 650, hue: 12 },
  { id: 5, name: 'Bottled Water', cat: 'Drinks', price: 30, hue: 200 },
  { id: 6, name: 'Cola Can', cat: 'Drinks', price: 60, hue: 350 },
  { id: 7, name: 'Fresh Lime Soda', cat: 'Drinks', price: 90, hue: 95 },
  { id: 8, name: 'Chocolate Brownie', cat: 'Desserts', price: 150, hue: 25, out: true },
];

const state = { cat: 'All', q: '', cart: {}, type: 'Dine-in', pay: 'Cash', discount: 0 };
const $ = (id) => document.getElementById(id);

function renderItems() {
  const list = MENU.filter((m) => (state.cat === 'All' || m.cat === state.cat) &&
    m.name.toLowerCase().includes(state.q.toLowerCase()));
  $('items').innerHTML = list.length ? list.map((m) => `
    <button class="item ${m.out ? 'out' : ''}" data-id="${m.id}">
      <div class="pic" style="background:hsl(${m.hue} 55% 45%)">${m.name[0]}</div>
      <div class="nm">${m.name}</div>
      <div class="pr">${money(m.price)}${m.out ? ' · sold out' : ''}</div>
    </button>`).join('') : '<p class="text-muted p-3">No items match your search.</p>';
}

function totals() {
  const lines = Object.values(state.cart);
  const subtotal = lines.reduce((s, l) => s + l.price * l.qty, 0);
  const discount = Math.min(subtotal, subtotal * (state.discount / 100));
  const net = subtotal - discount;
  const service = net * SETTINGS.serviceRate / 100;
  const vat = SETTINGS.vatInclusive ? net * SETTINGS.vatRate / (100 + SETTINGS.vatRate) : net * SETTINGS.vatRate / 100;
  const total = net + service + (SETTINGS.vatInclusive ? 0 : vat);
  return { lines, subtotal, discount, service, vat, total };
}

function renderCart() {
  const t = totals();
  $('lines').innerHTML = t.lines.length ? t.lines.map((l) => `
    <div class="line"><div>${l.name}<div class="text-muted small">${money(l.price)}</div></div>
      <div class="qty"><button data-dec="${l.id}" aria-label="Less">−</button><span>${l.qty}</span><button data-inc="${l.id}" aria-label="More">+</button></div>
      <strong>${money(l.price * l.qty)}</strong></div>`).join('')
    : '<p class="text-muted text-center my-4">Tap a menu item to start the order.</p>';
  $('sums').innerHTML = `
    <div class="sum"><span>Subtotal</span><span>${money(t.subtotal)}</span></div>
    <div class="sum"><span>Discount (${state.discount}%)</span><span>−${money(t.discount)}</span></div>
    ${SETTINGS.serviceRate ? `<div class="sum"><span>Service charge (${SETTINGS.serviceRate}%)</span><span>${money(t.service)}</span></div>` : ''}
    <div class="sum text-muted"><span>VAT ${SETTINGS.vatRate}% ${SETTINGS.vatInclusive ? '(included)' : ''}</span><span>${money(t.vat)}</span></div>
    <div class="sum total"><span>Total</span><span>${money(t.total)}</span></div>`;
  $('charge').disabled = !t.lines.length;
  $('charge').textContent = t.lines.length ? `Charge ${money(t.total)}` : 'Charge';
}

function add(id, d) {
  const m = MENU.find((x) => x.id === id);
  const l = state.cart[id] || { id, name: m.name, price: m.price, qty: 0 };
  l.qty += d;
  if (l.qty <= 0) delete state.cart[id]; else state.cart[id] = l;
  renderCart();
}

$('cats').innerHTML = CATS.map((c) => `<button class="chip ${c === 'All' ? 'active' : ''}" data-cat="${c}">${c}</button>`).join('');
$('cats').addEventListener('click', (e) => {
  const b = e.target.closest('[data-cat]'); if (!b) return;
  state.cat = b.dataset.cat;
  document.querySelectorAll('#cats .chip').forEach((c) => c.classList.toggle('active', c === b));
  renderItems();
});
$('q').addEventListener('input', (e) => { state.q = e.target.value; renderItems(); });
$('items').addEventListener('click', (e) => { const b = e.target.closest('[data-id]'); if (b) add(+b.dataset.id, 1); });
$('lines').addEventListener('click', (e) => {
  const i = e.target.closest('[data-inc]'), d = e.target.closest('[data-dec]');
  if (i) add(+i.dataset.inc, 1); if (d) add(+d.dataset.dec, -1);
});
$('type').addEventListener('change', (e) => { state.type = e.target.value; $('tableWrap').hidden = state.type !== 'Dine-in'; });
$('discount').addEventListener('input', (e) => { state.discount = Math.min(100, Math.max(0, +e.target.value || 0)); renderCart(); });
$('pay').addEventListener('click', (e) => {
  const b = e.target.closest('[data-pay]'); if (!b) return;
  state.pay = b.dataset.pay;
  document.querySelectorAll('#pay button').forEach((x) => x.classList.toggle('active', x === b));
});
$('clear').addEventListener('click', () => { state.cart = {}; renderCart(); });

$('charge').addEventListener('click', () => {
  const t = totals();
  $('receipt').innerHTML = `<div style="text-align:center"><strong>RESTAURANT POS</strong><br>${state.type}${state.type === 'Dine-in' ? ' · ' + $('table').value : ''}<br>${new Date().toLocaleString()}</div><hr>` +
    t.lines.map((l) => `${l.qty} x ${l.name}<div style="text-align:right">${money(l.price * l.qty)}</div>`).join('') +
    `<hr>Subtotal ${money(t.subtotal)}<br>Discount −${money(t.discount)}<br>VAT ${SETTINGS.vatRate}% ${money(t.vat)}<br><strong>TOTAL ${money(t.total)}</strong><br>Paid by ${state.pay}<hr><div style="text-align:center">Thank you!</div>`;
  window.print();
  state.cart = {}; renderCart();
});

renderItems(); renderCart();
