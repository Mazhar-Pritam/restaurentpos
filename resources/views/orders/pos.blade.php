@extends('Layouts.master')

@section('title', 'POS terminal')

@section('content')
  @if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      {{ session('error') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif
  @if ($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0 small">
        @foreach ($errors->all() as $message)
          <li>{{ $message }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div class="page-head">
    <div>
      <h1>POS terminal</h1>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb small mb-0">
          <li class="breadcrumb-item">Orders</li>
          <li class="breadcrumb-item active" aria-current="page">POS terminal</li>
        </ol>
      </nav>
    </div>
    <a class="btn btn-light" href="{{ route('orders.index') }}"><i class="bi bi-list-check"></i> All orders</a>
  </div>

  <div class="row g-3">
    {{-- মেনু --}}
    <div class="col-lg-8">
      <div class="card-x">
        <div class="card-h">
          <span>Menu</span>
          <input class="form-control" style="max-width:260px" type="search" id="menu-search" placeholder="Search item" aria-label="Search item">
        </div>
        <div class="card-b">
          <div class="d-flex flex-wrap gap-2 mb-3" id="cat-pills">
            <button type="button" class="btn btn-sm btn-accent" data-cat="">All</button>
            @foreach ($categories as $category)
              <button type="button" class="btn btn-sm btn-light" data-cat="{{ $category }}">{{ $category }}</button>
            @endforeach
          </div>
          <div class="row g-2" id="menu-grid"></div>
          <div class="text-center text-muted py-4 d-none" id="menu-empty">No items found.</div>
        </div>
      </div>
    </div>

    {{-- অর্ডার / কার্ট --}}
    <div class="col-lg-4">
      <form method="POST" action="{{ route('orders.store') }}" id="pos-form" class="card-x" style="position:sticky;top:12px">
        @csrf
        <div class="card-h">Current order</div>
        <div class="card-b">
          <div class="btn-group w-100 mb-3" role="group" aria-label="Order type">
            <input type="radio" class="btn-check" name="type" id="type-dine" value="dine_in" @checked(old('type', 'dine_in') === 'dine_in')>
            <label class="btn btn-outline-secondary" for="type-dine"><i class="bi bi-cup-hot"></i> Dine-in</label>
            <input type="radio" class="btn-check" name="type" id="type-take" value="takeaway" @checked(old('type') === 'takeaway')>
            <label class="btn btn-outline-secondary" for="type-take"><i class="bi bi-bag"></i> Takeaway</label>
          </div>

          <div class="mb-2" id="table-box">
            <select class="form-select" name="table_id" id="table_id" aria-label="Table">
              <option value="">Choose table</option>
              @foreach ($tables as $table)
                <option value="{{ $table->id }}" @selected((string) old('table_id') === (string) $table->id)>
                  {{ $table->name }} ({{ $table->capacity }} seats){{ $table->status === 'available' ? '' : ' · ' . ucfirst($table->status) }}
                </option>
              @endforeach
            </select>
          </div>

          <div class="mb-3">
            <select class="form-select" name="customer_id" aria-label="Customer">
              <option value="">Walk-in customer</option>
              @foreach ($customers as $customer)
                <option value="{{ $customer->id }}" @selected((string) old('customer_id') === (string) $customer->id)>
                  {{ $customer->name }}{{ $customer->phone ? ' · ' . $customer->phone : '' }}
                </option>
              @endforeach
            </select>
          </div>

          <div id="cart-lines" class="mb-2"></div>
          <div class="text-center text-muted small py-3" id="cart-empty">Tap an item to add it.</div>
          <div id="cart-hidden"></div>

          <div class="row g-2 mb-2">
            <div class="col-5">
              <select class="form-select" name="discount_type" id="discount_type" aria-label="Discount type">
                <option value="" @selected(old('discount_type') === null || old('discount_type') === '')>No discount</option>
                <option value="percent" @selected(old('discount_type') === 'percent')>Discount %</option>
                <option value="flat" @selected(old('discount_type') === 'flat')>Discount ৳</option>
              </select>
            </div>
            <div class="col-7">
              <input class="form-control" type="number" step="0.01" min="0" name="discount_value" id="discount_value"
                     value="{{ old('discount_value') }}" placeholder="Discount value" aria-label="Discount value">
            </div>
          </div>

          <div class="border rounded p-2 mb-3 small">
            <div class="d-flex justify-content-between"><span>Subtotal</span><span id="t-sub">৳0.00</span></div>
            <div class="d-flex justify-content-between"><span>Discount</span><span id="t-disc">−৳0.00</span></div>
            <div class="d-flex justify-content-between"><span>VAT (<span id="t-rate">0</span>%)</span><span id="t-vat">৳0.00</span></div>
            <hr class="my-1">
            <div class="d-flex justify-content-between fw-bold fs-6"><span>Total</span><span id="t-total">৳0.00</span></div>
          </div>

          <div class="mb-2">
            <select class="form-select" name="payment_method" aria-label="Payment method" required>
              @foreach (\App\Models\Order::PAYMENT_METHODS as $value => $label)
                <option value="{{ $value }}" @selected(old('payment_method', 'cash') === $value)>Paid by {{ $label }}</option>
              @endforeach
            </select>
          </div>

          <div class="mb-3">
            <input class="form-control" type="text" name="note" maxlength="255" value="{{ old('note') }}" placeholder="Order note (optional)">
          </div>

          <button class="btn btn-accent w-100 py-2" type="submit" id="place-btn" disabled>
            <i class="bi bi-check-lg"></i> Place order &amp; print receipt
          </button>
        </div>
      </form>
    </div>
  </div>
@endsection

@push('scripts')
<script>
  (function () {
    var items = @json($posItems);
    var vatRate = @json($vatRate);
    var oldItems = @json($oldItems);

    var cart = {};          // menu_item_id -> { id, name, price, qty, max }
    var activeCat = '';

    var grid = document.getElementById('menu-grid');
    var emptyMsg = document.getElementById('menu-empty');
    var search = document.getElementById('menu-search');
    var linesBox = document.getElementById('cart-lines');
    var cartEmpty = document.getElementById('cart-empty');
    var hidden = document.getElementById('cart-hidden');
    var placeBtn = document.getElementById('place-btn');
    var discType = document.getElementById('discount_type');
    var discValue = document.getElementById('discount_value');
    var tableBox = document.getElementById('table-box');
    var tableSel = document.getElementById('table_id');

    function esc(s) {
      return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }
    function round2(n) { return Math.round(n * 100) / 100; }
    function money(n) { return '৳' + round2(n).toFixed(2); }

    // ---------- মেনু গ্রিড ----------
    function renderMenu() {
      var term = search.value.trim().toLowerCase();
      var html = '';
      var shown = 0;
      items.forEach(function (it) {
        if (activeCat && it.category !== activeCat) { return; }
        if (term && it.name.toLowerCase().indexOf(term) === -1) { return; }
        shown++;
        var out = it.max !== null && it.max <= 0;
        html += '<div class="col-6 col-md-4 col-xl-3">' +
          '<button type="button" class="btn btn-light w-100 text-start p-2 menu-card" data-id="' + it.id + '"' + (out ? ' disabled' : '') + '>' +
          (it.image ? '<img src="' + esc(it.image) + '" alt="" style="width:100%;height:80px;object-fit:cover;border-radius:8px;margin-bottom:6px">' : '') +
          '<div class="fw-semibold small">' + esc(it.name) + '</div>' +
          '<div class="small text-muted">' + esc(it.category) + '</div>' +
          '<div class="fw-bold">' + money(it.price) + '</div>' +
          (out ? '<div class="small text-danger">Out of stock</div>' : (it.max !== null && it.max <= 5 ? '<div class="small text-warning">Only ' + it.max + ' left</div>' : '')) +
          '</button></div>';
      });
      grid.innerHTML = html;
      emptyMsg.classList.toggle('d-none', shown > 0);
    }

    grid.addEventListener('click', function (e) {
      var btn = e.target.closest('.menu-card');
      if (!btn) { return; }
      addToCart(parseInt(btn.dataset.id, 10), 1);
    });

    document.getElementById('cat-pills').addEventListener('click', function (e) {
      var btn = e.target.closest('button[data-cat]');
      if (!btn) { return; }
      activeCat = btn.dataset.cat;
      this.querySelectorAll('button').forEach(function (b) {
        b.className = 'btn btn-sm ' + (b === btn ? 'btn-accent' : 'btn-light');
      });
      renderMenu();
    });
    search.addEventListener('input', renderMenu);

    // ---------- কার্ট ----------
    function findItem(id) {
      for (var i = 0; i < items.length; i++) { if (items[i].id === id) { return items[i]; } }
      return null;
    }

    function addToCart(id, qty) {
      var it = findItem(id);
      if (!it) { return; }
      var line = cart[id] || { id: id, name: it.name, price: it.price, qty: 0, max: it.max };
      var next = line.qty + qty;
      if (line.max !== null && next > line.max) { next = line.max; }
      if (next <= 0) { delete cart[id]; } else { line.qty = next; cart[id] = line; }
      renderCart();
    }

    linesBox.addEventListener('click', function (e) {
      var btn = e.target.closest('button[data-act]');
      if (!btn) { return; }
      var id = parseInt(btn.dataset.id, 10);
      if (btn.dataset.act === 'plus') { addToCart(id, 1); }
      if (btn.dataset.act === 'minus') { addToCart(id, -1); }
      if (btn.dataset.act === 'remove') { delete cart[id]; renderCart(); }
    });

    function totals() {
      var sub = 0;
      Object.keys(cart).forEach(function (k) { sub += cart[k].price * cart[k].qty; });
      sub = round2(sub);
      var type = discType.value;
      var val = parseFloat(discValue.value) || 0;
      var disc = 0;
      if (type === 'percent') { disc = round2(sub * Math.min(val, 100) / 100); }
      if (type === 'flat') { disc = round2(Math.min(val, sub)); }
      var taxable = round2(sub - disc);
      var vat = round2(taxable * vatRate / 100);
      return { sub: sub, disc: disc, vat: vat, total: round2(taxable + vat) };
    }

    function renderCart() {
      var ids = Object.keys(cart);
      var html = '';
      var hid = '';
      ids.forEach(function (k, idx) {
        var l = cart[k];
        html += '<div class="d-flex align-items-center justify-content-between border-bottom py-2">' +
          '<div class="me-2" style="min-width:0"><div class="small fw-semibold text-truncate">' + esc(l.name) + '</div>' +
          '<div class="small text-muted">' + money(l.price) + ' × ' + l.qty + ' = ' + money(l.price * l.qty) + '</div></div>' +
          '<div class="d-flex align-items-center gap-1">' +
          '<button type="button" class="btn btn-sm btn-light" data-act="minus" data-id="' + l.id + '" aria-label="Less">−</button>' +
          '<span class="px-1 fw-semibold">' + l.qty + '</span>' +
          '<button type="button" class="btn btn-sm btn-light" data-act="plus" data-id="' + l.id + '" aria-label="More">+</button>' +
          '<button type="button" class="btn btn-sm btn-light text-danger" data-act="remove" data-id="' + l.id + '" aria-label="Remove"><i class="bi bi-x-lg"></i></button>' +
          '</div></div>';
        hid += '<input type="hidden" name="items[' + idx + '][menu_item_id]" value="' + l.id + '">' +
               '<input type="hidden" name="items[' + idx + '][quantity]" value="' + l.qty + '">';
      });
      linesBox.innerHTML = html;
      hidden.innerHTML = hid;
      cartEmpty.classList.toggle('d-none', ids.length > 0);
      placeBtn.disabled = ids.length === 0;

      var t = totals();
      document.getElementById('t-sub').textContent = money(t.sub);
      document.getElementById('t-disc').textContent = '−' + money(t.disc);
      document.getElementById('t-vat').textContent = money(t.vat);
      document.getElementById('t-total').textContent = money(t.total);
      document.getElementById('t-rate').textContent = vatRate;
    }

    // ---------- ডিসকাউন্ট ও অর্ডারের ধরন ----------
    function syncDiscount() {
      discValue.disabled = discType.value === '';
      if (discType.value === '') { discValue.value = ''; }
      renderCart();
    }
    discType.addEventListener('change', syncDiscount);
    discValue.addEventListener('input', renderCart);

    function syncType() {
      var dine = document.getElementById('type-dine').checked;
      tableBox.classList.toggle('d-none', !dine);
      tableSel.disabled = !dine;
      tableSel.required = dine;
    }
    document.getElementById('type-dine').addEventListener('change', syncType);
    document.getElementById('type-take').addEventListener('change', syncType);

    // ভুল হয়ে পেজ ফিরে এলে আগের কার্ট ফিরিয়ে আনা
    oldItems.forEach(function (row) {
      addToCart(parseInt(row.menu_item_id, 10), parseInt(row.quantity, 10) || 1);
    });

    renderMenu();
    syncType();
    syncDiscount();
  })();
</script>
@endpush
