@extends('Layouts.master')

@section('title', 'Kitchen display')

@section('content')
  @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  <div class="page-head">
    <div>
      <h1>Kitchen display</h1>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb small mb-0">
          <li class="breadcrumb-item">Restaurant</li>
          <li class="breadcrumb-item active">Kitchen</li>
        </ol>
      </nav>
    </div>
  </div>

  <div class="row g-3">
    <div class="col-lg-8">
      <div class="card-x">
        <div class="card-h"><span>Cooking · {{ $cooking->count() }}</span></div>
        <div class="card-b">
          <div class="row g-3">
            @forelse ($cooking as $order)
              <div class="col-md-6">
                <div class="card-x h-100">
                  <div class="card-h d-flex justify-content-between">
                    <strong>{{ $order->order_no }}</strong>
                    <span class="status cooking">Cooking</span>
                  </div>
                  <div class="card-b">
                    <div class="small text-muted mb-2">
                      {{ $order->created_at->format('h:i A') }} ·
                      {{ $order->table?->name ?? ($order->type === 'takeaway' ? 'Takeaway' : 'Dine-in') }}
                    </div>
                    <ul class="list-unstyled mb-3">
                      @foreach ($order->items as $item)
                        <li class="d-flex justify-content-between border-bottom py-1">
                          <span>{{ $item->item_name }}</span>
                          <strong>× {{ $item->quantity }}</strong>
                        </li>
                      @endforeach
                    </ul>
                    @if ($order->note)
                      <div class="alert alert-warning py-2 mb-3">{{ $order->note }}</div>
                    @endif
                    @can('orders.update_status')
                      <form method="POST" action="{{ route('orders.status', $order) }}">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="ready">
                        <button class="btn btn-accent w-100" type="submit">
                          <i class="bi bi-bell"></i> Mark ready
                        </button>
                      </form>
                    @endcan
                  </div>
                </div>
              </div>
            @empty
              <div class="col-12 text-center text-muted py-4">No cooking orders.</div>
            @endforelse
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-4">
      <div class="card-x">
        <div class="card-h"><span>Ready · {{ $ready->count() }}</span></div>
        <div class="card-b">
          @forelse ($ready as $order)
            <div class="border-bottom py-2">
              <div class="d-flex justify-content-between">
                <strong>{{ $order->order_no }}</strong>
                <span class="status open">Ready</span>
              </div>
              <div class="small text-muted">{{ $order->table?->name ?? 'Takeaway' }} · {{ $order->created_at->format('h:i A') }}</div>
            </div>
          @empty
            <div class="text-center text-muted py-3">No ready orders.</div>
          @endforelse
        </div>
      </div>
    </div>
  </div>
@endsection
