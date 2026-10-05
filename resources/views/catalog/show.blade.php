@extends('layouts.app')
@section('title', $product->name)
@section('content')
<div class="container py-5">
    <a href="{{ route('catalog.index') }}" class="text-decoration-none">← Volver al catálogo</a>
    <div class="row g-5 mt-1 align-items-center">
        <div class="col-lg-5"><div class="product-detail-image rounded-4 d-flex align-items-center justify-content-center">@if($product->image)<img class="img-fluid" src="{{ asset($product->image) }}" alt="{{ $product->name }}">@else<span class="display-1">🛍️</span>@endif</div></div>
        <div class="col-lg-7"><span class="badge text-bg-secondary">{{ $product->category->name }}</span><h1 class="mt-3">{{ $product->name }}</h1><p class="lead text-secondary">{{ $product->description }}</p><div class="display-6 fw-bold mb-3">{{ number_format($product->price, 2, ',', '.') }} €</div><p>Unidades disponibles: <strong>{{ $product->stock }}</strong></p><form class="d-flex gap-2" method="POST" action="{{ route('cart.store', $product) }}">@csrf<input class="form-control" style="max-width:100px" type="number" name="quantity" min="1" max="{{ $product->stock }}" value="1"><button class="btn btn-primary btn-lg">Añadir al carrito</button></form></div>
    </div>
</div>
@endsection
