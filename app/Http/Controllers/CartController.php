<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Request $request)
    {
        return view('cart.index', $this->cartData($request));
    }

    public function store(Request $request, Product $product): RedirectResponse
    {
        abort_unless($product->active && $product->stock > 0, 404);
        $quantity = max(1, min((int) $request->input('quantity', 1), $product->stock));
        $cart = $request->session()->get('cart', []);
        $cart[$product->id] = min(($cart[$product->id] ?? 0) + $quantity, $product->stock);
        $request->session()->put('cart', $cart);

        return back()->with('success', 'Producto añadido al carrito.');
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate(['quantity' => ['required', 'integer', 'min:1']]);
        $cart = $request->session()->get('cart', []);
        $cart[$product->id] = min($data['quantity'], $product->stock);
        $request->session()->put('cart', $cart);

        return back()->with('success', 'Carrito actualizado.');
    }

    public function destroy(Request $request, Product $product): RedirectResponse
    {
        $cart = $request->session()->get('cart', []);
        unset($cart[$product->id]);
        $request->session()->put('cart', $cart);

        return back()->with('success', 'Producto eliminado.');
    }

    public function clear(Request $request): RedirectResponse
    {
        $request->session()->forget('cart');

        return back()->with('success', 'Carrito vaciado.');
    }

    public static function cartData(Request $request): array
    {
        $cart = $request->session()->get('cart', []);
        $products = Product::whereIn('id', array_keys($cart))->get()->keyBy('id');
        $items = collect($cart)->map(function ($quantity, $productId) use ($products) {
            $product = $products->get($productId);
            if (! $product) {
                return null;
            }

            return [
                'product' => $product,
                'quantity' => $quantity,
                'subtotal' => (float) $product->price * $quantity,
            ];
        })->filter()->values();

        return ['items' => $items, 'total' => $items->sum('subtotal')];
    }
}
