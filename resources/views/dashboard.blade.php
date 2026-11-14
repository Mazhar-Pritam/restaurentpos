@extends("Layouts.master")
@section("content")

<div class="page-head">
  <div>
    <h1>Dashboard</h1>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb small mb-0">
        <li class="breadcrumb-item">Overview</li>
        <li class="breadcrumb-item active">Dashboard</li>
      </ol>
    </nav>
  </div>
  <a class="btn btn-accent" href="{{ asset('assets/assets/pos') }}"><i class="bi bi-receipt"></i> New order</a>
</div>

{{-- Stat cards --}}
<div class="row g-3 mb-3">
  <div class="col-6 col-xl-3"><div class="card-x stat key"><div class="label">Today's net sales</div><div class="value">৳61,200</div><div class="note">After discounts and returns</div></div></div>
  <div class="col-6 col-xl-3"><div class="card-x stat"><div class="label">Orders</div><div class="value">148</div><div class="note">Average ৳413.51</div></div></div>
  <div class="col-6 col-xl-3"><div class="card-x stat"><div class="label">Gross profit</div><div class="value">৳38,900</div><div class="note">Net sales − ingredient cost used</div></div></div>
  <div class="col-6 col-xl-3"><div class="card-x stat"><div class="label">Net profit</div><div class="value">৳21,350</div><div class="note">Gross profit − expenses</div></div></div>
  <div class="col-6 col-xl-3"><div class="card-x stat"><div class="label">Inventory value</div><div class="value">৳58,028</div><div class="note">Weighted average cost</div></div></div>
  <div class="col-6 col-xl-3"><div class="card-x stat"><div class="label">Low stock</div><div class="value text-accent">4</div><div class="note">Ingredients below reorder level</div></div></div>
  <div class="col-6 col-xl-3"><div class="card-x stat"><div class="label">Wastage today</div><div class="value">৳1,240</div><div class="note">Shown separately from cost of goods</div></div></div>
  <div class="col-6 col-xl-3"><div class="card-x stat"><div class="label">Reservations today</div><div class="value">9</div><div class="note">Next at 19:00</div></div></div>
</div>

{{-- Charts --}}
<div class="row g-3 mb-3">
  <div class="col-lg-8">
    <div class="card-x">
      <div class="card-h">Net sales, last 7 days</div>
      <div class="card-b"><div class="chart-box"><canvas id="salesChart"></canvas></div></div>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="card-x">
      <div class="card-h">Sales by order type</div>
      <div class="card-b"><div class="chart-box"><canvas id="typeChart"></canvas></div></div>
    </div>
  </div>
</div>

{{-- Tables --}}
<div class="row g-3">
  <div class="col-lg-7">
    <div class="card-x">
      <div class="card-h">Recent orders <a class="btn btn-sm btn-light" href="{{ asset('assets/assets/orders/index.html') }}">View all</a></div>
      <div class="table-responsive">
        <table class="table table-x align-middle mb-0">
          <thead><tr><th>Order</th><th>Type</th><th>Table</th><th>Total</th><th>Status</th></tr></thead>
          <tbody>
            <tr><td>#1048</td><td>Dine-in</td><td>T3</td><td>৳1,240.00</td><td><span class="status cooking">Cooking</span></td></tr>
            <tr><td>#1047</td><td>Takeaway</td><td>·</td><td>৳560.00</td><td><span class="status open">Ready</span></td></tr>
            <tr><td>#1046</td><td>Dine-in</td><td>T6</td><td>৳2,090.00</td><td><span class="status paid">Paid</span></td></tr>
            <tr><td>#1045</td><td>Dine-in</td><td>T1</td><td>৳680.00</td><td><span class="status paid">Paid</span></td></tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="col-lg-5">
    <div class="card-x">
      <div class="card-h">Low stock ingredients <a class="btn btn-sm btn-light" href="{{ asset('assets/assets/inventory/low-stock.html') }}">Reorder</a></div>
      <div class="table-responsive">
        <table class="table table-x align-middle mb-0">
          <thead><tr><th>Ingredient</th><th>In stock</th><th>Reorder at</th></tr></thead>
          <tbody>
            <tr><td>Beef patty</td><td><span class="status low">12 pcs</span></td><td>30 pcs</td></tr>
            <tr><td>Mozzarella</td><td><span class="status low">1.8 kg</span></td><td>3 kg</td></tr>
            <tr><td>Burger bun</td><td><span class="status low">20 pcs</span></td><td>40 pcs</td></tr>
            <tr><td>Lime</td><td><span class="status low">0.9 kg</span></td><td>2 kg</td></tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

@endsection
