@extends('layouts.app')

@section('title', 'TECHFLEX — Accessoires Apple & montres de luxe')

@section('content')
<section class="hero">
    <div class="container">
        <h1>Accessoires Apple &amp; montres de luxe</h1>
        <p>Paiement MoMo ou à la livraison, livraison au jour et à l'heure de votre choix.</p>
    </div>
</section>

@foreach($sections as $cat)
    @php $themeClass = $cat->slug === 'montres-de-luxe' ? 'section-gold' : 'section-apple'; @endphp
    <section class="showcase {{ $themeClass }}">
        <div class="container">
            <div class="showcase-head">
                <h2>{{ $cat->name }}</h2>
                <a href="{{ route('category', $cat->slug) }}">Tout voir →</a>
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
                <h2>Ce que disent nos clients</h2>
                <a href="{{ route('reviews') }}">Voir tous les avis →</a>
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
