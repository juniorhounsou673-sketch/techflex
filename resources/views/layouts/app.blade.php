<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'TECHFLEX')</title>
    <link rel="stylesheet" href="{{ asset('css/techflex.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body>
@php
    $cartCount = array_sum(session('cart', []));
    $authUser = auth()->user();
    // Dans l'espace admin / revendeur : menu de travail, sans panier
    $inStaffArea = $authUser && request()->routeIs('admin.*', 'reseller.*');
@endphp

<header class="site-header">
    <div class="container header-inner">
        <a href="{{ route('home') }}" class="logo">TECH<span>FLEX</span></a>
        <nav>
            @if($inStaffArea && $authUser->isAdmin())
                <a href="{{ route('admin.dashboard') }}">Tableau de bord</a>
                <a href="{{ route('admin.products.index') }}">Produits</a>
                <a href="{{ route('admin.deliveries.index') }}">Livraisons</a>
                <a href="{{ route('admin.resellers.index') }}">Revendeurs</a>
                <a href="{{ route('home') }}">Voir le site</a>
            @elseif($inStaffArea && $authUser->isReseller())
                <a href="{{ route('reseller.dashboard') }}">Mon espace</a>
                <a href="{{ route('home') }}">Voir le site</a>
            @else
                <a href="{{ route('home') }}">Accueil</a>
                <a href="{{ route('category', 'montres-de-luxe') }}">Montres</a>
                <a href="{{ route('category', 'accessoires-apple') }}">Accessoires Apple</a>
                <a href="{{ route('cart') }}" class="cart-link">Panier ({{ $cartCount }})</a>

                @if($authUser && $authUser->isAdmin())
                    <a href="{{ route('admin.dashboard') }}">Admin</a>
                @elseif($authUser && $authUser->isReseller())
                    <a href="{{ route('reseller.dashboard') }}">Mon espace</a>
                @endif
            @endif

            @if($authUser)
                <form method="POST" action="{{ route('logout') }}" class="nav-form">
                    @csrf
                    <button type="submit" class="nav-btn">Déconnexion</button>
                </form>
            @endif
        </nav>
    </div>
</header>

@if(session('success'))
    <div class="flash flash-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="flash flash-error">{{ session('error') }}</div>
@endif
@if($errors->any())
    <div class="flash flash-error">{{ $errors->first() }}</div>
@endif

<main>
    @yield('content')
</main>

<footer class="site-footer">
    <div class="container">
        © {{ date('Y') }} TECHFLEX — Accessoires Apple &amp; montres de luxe
        @guest · <a href="{{ route('login') }}" class="footer-link">Espace pro</a> @endguest
    </div>
</footer>
</body>
</html>
