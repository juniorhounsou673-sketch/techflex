@extends('layouts.app')

@section('title', 'TECHFLEX — Accessoires Apple & montres de luxe')

@section('content')
<section class="hero">
    <div class="container hero-inner">
        <div class="hero-copy">
            <span class="eyebrow">Boutique premium · Bénin</span>
            <h1>Accessoires Apple &amp; <em>montres de luxe</em></h1>
            <p>Des pièces choisies pour leur style et leur qualité. Payez par MoMo ou à la livraison, et recevez votre commande au jour et à l'heure de votre choix.</p>
            <div class="hero-actions">
                <a href="#collections" class="btn btn-gold">Découvrir la sélection</a>
                <a href="{{ route('direct') }}" class="btn btn-ghost">Acheter en direct</a>
            </div>
            <div class="hero-notes">
                <span>Livraison au créneau choisi</span>
                <span>Paiement MoMo sécurisé</span>
                <span>Service client sur WhatsApp</span>
            </div>
        </div>
        <aside class="hero-card" aria-label="Points forts TECHFLEX">
            <span class="hero-card-kicker">L'expérience TECHFLEX</span>
            <strong>Le bon produit, au bon moment, au bon endroit.</strong>
            <ul>
                <li><span>01</span> Choisissez votre pièce dans la sélection</li>
                <li><span>02</span> Payez sur le site ou à la livraison</li>
                <li><span>03</span> Fixez le jour, l'heure et le lieu</li>
            </ul>
        </aside>
    </div>
</section>

<div id="collections"></div>
@foreach($sections as $cat)
    @php $themeClass = $cat->slug === 'montres-de-luxe' ? 'section-gold' : 'section-apple'; @endphp
    <section class="showcase {{ $themeClass }}">
        <div class="container">
            <div class="showcase-head">
                <div>
                    <span class="eyebrow">Collection {{ $loop->iteration }}</span>
                    <h2>{{ $cat->name }}</h2>
                </div>
                <a href="{{ route('category', $cat->slug) }}" class="show-link">Tout voir <span aria-hidden="true">→</span></a>
            </div>

            @if($cat->products->isEmpty())
                <p class="muted">Aucun produit disponible pour le moment.</p>
            @else
                <div class="grid">
                    @foreach($cat->products as $product)
                        @include('partials.product-card', ['product' => $product])
                    @endforeach
                </div>
            @endif
        </div>
    </section>
@endforeach

@if($reviews->isNotEmpty())
    <section class="showcase section-apple reviews-section">
        <div class="container">
            <div class="showcase-head">
                <div>
                    <span class="eyebrow">Témoignages</span>
                    <h2>Ce que disent nos clients</h2>
                </div>
            </div>
            <div class="reviews-grid">
                @foreach($reviews as $review)
                    @include('partials.review-card', ['review' => $review])
                @endforeach
            </div>
        </div>
    </section>
@endif
@endsection
