@extends('layouts.app')

@section('title', 'Admin — TECHFLEX')

@section('content')
<section class="showcase page-fill section-apple">
    <div class="container">
        <div class="showcase-head"><h1>Espace admin</h1></div>

        <div class="stats">
            <div class="stat"><div class="stat-num">{{ $stats['actifs'] }}</div><div class="muted">Revendeurs actifs</div></div>
            <div class="stat"><div class="stat-num">{{ $stats['bloques'] }}</div><div class="muted">Revendeurs bloqués</div></div>
            <div class="stat"><div class="stat-num">{{ \App\Support\Shop::money($stats['ils_doivent']) }}</div><div class="muted">Les revendeurs vous doivent</div></div>
            <div class="stat"><div class="stat-num">{{ \App\Support\Shop::money($stats['vous_devez']) }}</div><div class="muted">Vous devez aux revendeurs</div></div>
            <div class="stat"><div class="stat-num">{{ $stats['commandes'] }}</div><div class="muted">Commandes</div></div>
            <div class="stat"><div class="stat-num">{{ $stats['a_confirmer'] }}</div><div class="muted">Paiements MoMo à confirmer</div></div>
        </div>

        <p class="btn-row">
            <a href="{{ route('admin.orders.index', ['filtre' => 'a_confirmer']) }}" class="btn">Commandes</a>
            <a href="{{ route('admin.products.index') }}" class="btn btn-outline">Produits</a>
            <a href="{{ route('admin.reviews.index') }}" class="btn btn-outline">Avis</a>
            <a href="{{ route('admin.deliveries.index') }}" class="btn btn-outline">Livraisons</a>
            <a href="{{ route('admin.resellers.index') }}" class="btn btn-outline">Revendeurs</a>
        </p>
    </div>
</section>
@endsection
