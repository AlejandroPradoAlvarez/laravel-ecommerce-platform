<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('user')->latest();
        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        return view('admin.orders.index', ['orders' => $query->paginate(15)->withQueryString(), 'statuses' => Order::STATUSES]);
    }

    public function show(Order $order)
    {
        $order->load('user', 'items.product');

        return view('admin.orders.show', ['order' => $order, 'statuses' => Order::STATUSES]);
    }

    public function update(Request $request, Order $order)
    {
        $data = $request->validate(['status' => ['required', Rule::in(Order::STATUSES)]]);
        $newStatus = $data['status'];

        DB::transaction(function () use ($order, $newStatus) {
            $order->load('items');

            if ($order->status !== 'cancelled' && $newStatus === 'cancelled') {
                foreach ($order->items as $item) {
                    Product::whereKey($item->product_id)->lockForUpdate()->firstOrFail()->increment('stock', $item->quantity);
                }
            }

            if ($order->status === 'cancelled' && $newStatus !== 'cancelled') {
                foreach ($order->items as $item) {
                    $product = Product::whereKey($item->product_id)->lockForUpdate()->firstOrFail();
                    if ($product->stock < $item->quantity) {
                        throw ValidationException::withMessages(['status' => 'No hay stock suficiente para reactivar este pedido.']);
                    }
                    $product->decrement('stock', $item->quantity);
                }
            }

            $order->update(['status' => $newStatus]);
        });

        return back()->with('success', 'Estado del pedido actualizado.');
    }
}
