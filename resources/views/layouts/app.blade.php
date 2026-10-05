<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'NovaStore') · {{ config('app.name', 'NovaStore') }}</title>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body>
<div id="app" class="min-vh-100 d-flex flex-column">
    <nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top">
        <div class="container py-2">
            <a class="navbar-brand fw-bold" href="{{ route('home') }}">NovaStore</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav"><span class="navbar-toggler-icon"></span></button>
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="{{ route('catalog.index') }}">Productos</a></li>
                    @auth
                        <li class="nav-item"><a class="nav-link" href="{{ route('orders.index') }}">Mis pedidos</a></li>
                        @if(auth()->user()->isAdmin())
                            <li class="nav-item"><a class="nav-link fw-semibold" href="{{ route('admin.dashboard') }}">Administración</a></li>
                        @endif
                    @endauth
                </ul>
                <ul class="navbar-nav align-items-lg-center gap-lg-2">
                    <li class="nav-item"><a class="nav-link" href="{{ route('cart.index') }}">Carrito <span class="badge text-bg-primary">{{ array_sum(session('cart', [])) }}</span></a></li>
                    @guest
                        <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Entrar</a></li>
                        <li class="nav-item"><a class="btn btn-primary" href="{{ route('register') }}">Crear cuenta</a></li>
                    @else
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">{{ auth()->user()->name }}</a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Mi perfil</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">@csrf<button class="dropdown-item" type="submit">Cerrar sesión</button></form>
                                </li>
                            </ul>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <main class="flex-grow-1">
        @if(session('success'))<div class="container mt-4"><div class="alert alert-success mb-0">{{ session('success') }}</div></div>@endif
        @if($errors->any())
            <div class="container mt-4"><div class="alert alert-danger mb-0"><strong>Revisa los datos:</strong><ul class="mb-0 mt-1">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div></div>
        @endif
        @yield('content')
    </main>

    <footer class="border-top bg-dark text-white mt-5">
        <div class="container py-4 d-flex flex-column flex-md-row justify-content-between gap-2">
            <span>NovaStore · Demo ecommerce con Laravel</span>
            <span class="text-white-50">Catálogo, pedidos y administración</span>
        </div>
    </footer>
</div>
</body>
</html>
