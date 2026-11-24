<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0d1117">
    <meta name="description" content="TECHFLEX — accessoires Apple et montres de luxe au Bénin, livrés au jour et à l'heure de votre choix.">
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
        <a href="{{ route('home') }}" class="logo">TECH<span>FLEX</span><small>Boutique premium</small></a>
        <nav>
            @if($inStaffArea && $authUser->isAdmin())
                <a href="{{ route('admin.dashboard') }}">Tableau de bord</a>
                <a href="{{ route('admin.products.index') }}">Produits</a>
                <a href="{{ route('admin.orders.index') }}">Commandes</a>
                <a href="{{ route('admin.deliveries.index') }}">Livraisons</a>
                <a href="{{ route('admin.resellers.index') }}">Revendeurs</a>
                <a href="{{ route('admin.reviews.index') }}">Avis</a>
                <a href="{{ route('home') }}">Voir le site</a>
            @elseif($inStaffArea && $authUser->isReseller())
                <a href="{{ route('reseller.dashboard') }}">Mon espace</a>
                <a href="{{ route('home') }}">Voir le site</a>
            @else
                <a href="{{ route('home') }}">Accueil</a>
                <a href="{{ route('direct') }}">Achat direct</a>
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
    <div class="container footer-grid">
        <div class="footer-brand">
            <a href="{{ route('home') }}" class="logo">TECH<span>FLEX</span><small>Boutique premium</small></a>
            <p>Une sélection d'accessoires Apple et de montres de luxe, livrée au Bénin au jour et à l'heure de votre choix.</p>
        </div>
        <div class="footer-col">
            <h3>Boutique</h3>
            <a href="{{ route('home') }}">Accueil</a>
            <a href="{{ route('direct') }}">Achat direct</a>
            <a href="{{ route('category', 'accessoires-apple') }}">Accessoires Apple</a>
            <a href="{{ route('category', 'montres-de-luxe') }}">Montres de luxe</a>
            <a href="{{ route('reviews') }}">Avis clients</a>
        </div>
        <div class="footer-col">
            <h3>Espace pro</h3>
            @if($authUser && $authUser->isAdmin())
                <a href="{{ route('admin.dashboard') }}">Tableau de bord</a>
            @elseif($authUser && $authUser->isReseller())
                <a href="{{ route('reseller.dashboard') }}">Mon espace revendeur</a>
            @else
                <a href="{{ route('login') }}">Connexion pro</a>
            @endif
            <a href="{{ route('cart') }}">Mon panier</a>
        </div>
    </div>
    <div class="container footer-bottom">
        <span>© {{ date('Y') }} TECHFLEX. Tous droits réservés.</span>
        <span>Paiement MoMo · Livraison à domicile</span>
    </div>
</footer>
</body>
</html>
