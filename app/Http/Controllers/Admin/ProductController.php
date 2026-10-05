<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;

class ProductController extends Controller
{
    public function index()
    {
        return view('admin.products.index', ['products' => Product::with('category')->latest()->paginate(12)]);
    }

    public function create()
    {
        return view('admin.products.form', ['product' => new Product(), 'categories' => Category::orderBy('name')->get()]);
    }

    public function store(StoreProductRequest $request)
    {
        Product::create($request->validated() + ['active' => $request->boolean('active')]);

        return redirect()->route('admin.products.index')->with('success', 'Producto creado.');
    }

    public function edit(Product $product)
    {
        return view('admin.products.form', ['product' => $product, 'categories' => Category::orderBy('name')->get()]);
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $product->update($request->validated() + ['active' => $request->boolean('active')]);

        return redirect()->route('admin.products.index')->with('success', 'Producto actualizado.');
    }

    public function destroy(Product $product)
    {
        abort_if($product->orderItems()->exists(), 422, 'No se puede eliminar un producto incluido en pedidos.');
        $product->delete();

        return back()->with('success', 'Producto eliminado.');
    }
}
