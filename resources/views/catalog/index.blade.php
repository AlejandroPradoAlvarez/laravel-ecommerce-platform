@extends('layouts.app')
@section('title', 'Catálogo')
@section('content')
<section class="hero py-5 mb-4">
    <div class="container py-4">
        <div class="row align-items-center g-4">
            <div class="col-lg-7">
                <span class="badge text-bg-light mb-3">Ecommerce Laravel</span>
                <h1 class="display-5 fw-bold">Tecnología para el día a día</h1>
                <p class="lead mb-0">Explora el catálogo, filtra productos y gestiona tus pedidos desde una aplicación completa.</p>
            </div>
            <div class="col-lg-5"><img class="img-fluid rounded-4 shadow" src="{{ asset('img/ofertas.jpg') }}" alt="Ofertas de la tienda"></div>
        </div>
    </div>
</section>
<div class="container">
    <form class="card card-body border-0 shadow-sm mb-4" method="GET">
        <div class="row g-2">
            <div class="col-md-5"><input class="form-control" name="q" value="{{ request('q') }}" placeholder="Buscar por nombre o descripción"></div>
            <div class="col-md-3"><select class="form-select" name="category"><option value="">Todas las categorías</option>@foreach($categories as $category)<option value="{{ $category->slug }}" @selected(request('category') === $category->slug)>{{ $category->name }}</option>@endforeach</select></div>
            <div class="col-md-2"><select class="form-select" name="sort"><option value="">Novedades</option><option value="price_asc" @selected(request('sort') === 'price_asc')>Precio ↑</option><option value="price_desc" @selected(request('sort') === 'price_desc')>Precio ↓</option><option value="name" @selected(request('sort') === 'name')>Nombre</option></select></div>
            <div class="col-md-2 d-grid"><button class="btn btn-dark">Filtrar</button></div>
        </div>
    </form>
    <div class="row g-4">
        @forelse($products as $product)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm product-card">
                    <div class="product-image d-flex align-items-center justify-content-center">@if($product->image)<img src="{{ asset($product->image) }}" alt="{{ $product->name }}">@else<span class="display-4">🛍️</span>@endif</div>
                    <div class="card-body d-flex flex-column">
                        <div class="small text-secondary mb-2">{{ $product->category->name }}</div>
                        <h2 class="h5">{{ $product->name }}</h2>
                        <p class="text-secondary flex-grow-1">{{ \Illuminate\Support\Str::limit($product->description, 110) }}</p>
                        <div class="d-flex justify-content-between align-items-center"><strong class="fs-4">{{ number_format($product->price, 2, ',', '.') }} €</strong><span class="badge {{ $product->stock > 5 ? 'text-bg-success' : 'text-bg-warning' }}">Stock {{ $product->stock }}</span></div>
                        <div class="d-flex gap-2 mt-3"><a class="btn btn-outline-dark flex-grow-1" href="{{ route('catalog.show', $product) }}">Ver detalle</a><form method="POST" action="{{ route('cart.store', $product) }}">@csrf<button class="btn btn-primary">Añadir</button></form></div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12"><div class="alert alert-info">No hay productos que coincidan con los filtros.</div></div>
        @endforelse
    </div>
    <div class="mt-4">{{ $products->links() }}</div>
</div>
@endsection
