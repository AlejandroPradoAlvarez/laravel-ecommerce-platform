<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = $request->user()->orders()->with('items.product')->latest()->paginate(10);

        return view('orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $this->authorize('view', $order);
        $order->load('items.product');

        return view('orders.show', compact('order'));
    }

    public function checkout(Request $request)
    {
        $cartData = CartController::cartData($request);
        abort_if($cartData['items']->isEmpty(), 422, 'El carrito está vacío.');

        return view('orders.checkout', $cartData + ['user' => $request->user()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'shipping_name' => ['required', 'string', 'max:255'],
            'shipping_address' => ['required', 'string', 'max:255'],
            'shipping_city' => ['required', 'string', 'max:120'],
            'shipping_postal_code' => ['required', 'string', 'max:12'],
        ]);
        $cart = $request->session()->get('cart', []);

        if ($cart === []) {
            throw ValidationException::withMessages(['cart' => 'El carrito está vacío.']);
        }

        $order = DB::transaction(function () use ($request, $data, $cart) {
            $products = Product::whereIn('id', array_keys($cart))->lockForUpdate()->get()->keyBy('id');
            $total = 0;

            foreach ($cart as $productId => $quantity) {
                $product = $products->get($productId);
                if (! $product || ! $product->active || $quantity > $product->stock) {
                    throw ValidationException::withMessages(['cart' => 'El stock ha cambiado. Revisa el carrito.']);
                }
                $total += (float) $product->price * $quantity;
            }

            $order = $request->user()->orders()->create($data + ['status' => 'pending', 'total' => $total]);

            foreach ($cart as $productId => $quantity) {
                $product = $products->get($productId);
                $order->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $product->price,
                    'subtotal' => (float) $product->price * $quantity,
                ]);
                $product->decrement('stock', $quantity);
            }

            return $order;
        });

        $request->session()->forget('cart');

        return redirect()->route('orders.show', $order)->with('success', 'Pedido creado correctamente.');
    }
}
