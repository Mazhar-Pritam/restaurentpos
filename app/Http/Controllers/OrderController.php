<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Models\Customer;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\RestaurantTable;
use App\Models\Setting;
use App\Services\OrderService;
use DomainException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    public function __construct(private OrderService $orders)
    {
    }

    /** POS terminal */
    public function create()
    {
        $menuItems = MenuItem::with(['category', 'recipes.ingredient'])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $posItems = $menuItems->map(function ($item) {
            // স্টকে আর কয়টা বানানো যাবে (রেসিপি না থাকলে null = সীমা নেই)
            $max = null;
            if ($item->recipes->isNotEmpty()) {
                $max = $item->recipes->map(function ($r) {
                    $per = (float) $r->quantity;

                    return $per > 0 ? (int) floor(((float) $r->ingredient->stock_quantity + 1e-9) / $per) : PHP_INT_MAX;
                })->min();
            }

            return [
                'id'       => $item->id,
                'name'     => $item->name,
                'price'    => (float) $item->price,
                'category' => $item->category?->name ?? 'Other',
                'image'    => $item->image_url,
                'max'      => $max,
            ];
        })->values();

        return view('orders.pos', [
            'posItems'  => $posItems,
            'categories' => $posItems->pluck('category')->unique()->sort()->values(),
            'tables'    => RestaurantTable::where('is_active', true)->orderBy('name')->get(),
            'customers' => Customer::where('is_active', true)->orderBy('name')->get(),
            'vatRate'   => (float) Setting::get('vat_rate', 0),
            'oldItems'  => array_values(old('items', [])),
        ]);
    }

    public function store(OrderRequest $request)
    {
        try {
            $order = $this->orders->create($request->validated(), auth()->id());
        } catch (DomainException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        // রিসিট খুলে নিজে থেকে প্রিন্ট ডায়ালগ আসবে
        return redirect()->route('orders.show', ['order' => $order, 'print' => 1])
            ->with('success', 'Order '.$order->order_no.' placed.');
    }

    public function index(Request $request)
    {
        $query = Order::with(['table', 'customer'])->withCount('items')
            ->when($request->filled('search'), fn ($q) => $q->where('order_no', 'like', '%'.$request->input('search').'%'))
            ->when($request->filled('type'), fn ($q) => $q->where('type', $request->input('type')))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->input('status')))
            ->when($request->filled('from'), fn ($q) => $q->whereDate('created_at', '>=', $request->input('from')))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('created_at', '<=', $request->input('to')));

        $totalSales = (clone $query)->sum('total');
        $orders     = $query->orderByDesc('id')->paginate(10)->withQueryString();

        return view('orders.index', compact('orders', 'totalSales'));
    }

    public function show(Order $order)
    {
        $order->load(['items', 'table', 'customer', 'user']);

        $settings = [
            'name'    => Setting::get('restaurant_name', 'Restaurant POS'),
            'address' => Setting::get('address', ''),
            'phone'   => Setting::get('phone', ''),
            'footer'  => Setting::get('receipt_footer', ''),
        ];

        return view('orders.show', compact('order', 'settings'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(['ready', 'completed'])],
        ]);

        $this->orders->updateStatus($order, $data['status']);

        return back()->with('success', 'Order '.$order->order_no.' marked '.strtolower(Order::STATUSES[$data['status']]).'.');
    }

    public function kitchen()
    {
        $cooking = Order::with(['items', 'table', 'customer'])
            ->where('status', 'cooking')
            ->orderBy('created_at')
            ->get();

        $ready = Order::with(['items', 'table', 'customer'])
            ->where('status', 'ready')
            ->orderBy('created_at')
            ->get();

        return view('kitchen.index', compact('cooking', 'ready'));
    }

    public function destroy(Order $order)
    {
        $this->orders->void($order);

        return redirect()->route('orders.index')->with('success', 'Order '.$order->order_no.' voided. Stock restored.');
    }
}
