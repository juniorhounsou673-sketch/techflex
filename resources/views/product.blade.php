@extends('layouts.app')

@section('title', $product->name . ' — TECHFLEX')

@section('content')
@php
    $themeClass = $product->category->slug === 'montres-de-luxe' ? 'section-gold' : 'section-apple';
    $price = \App\Support\Shop::price($product);
    $media = $product->media;
    $first = $media->first();
    $viewer = auth()->user();
    $seesResellerPrice = $viewer && ($viewer->isReseller() || $viewer->isAdmin());
@endphp
<section class="showcase page-fill {{ $themeClass }}">
    <div class="container">
        <a href="{{ route('category', $product->category->slug) }}" class="back">← {{ $product->category->name }}</a>

        <div class="product-detail">
            <div class="gallery">
                <div class="gallery-main" id="gallery-main">
                    @if(! $first)
                        <span class="gallery-placeholder">{{ mb_strtoupper(mb_substr($product->brand ?: $product->name, 0, 1)) }}</span>
                    @elseif($first->isVideo())
                        <video src="{{ $first->url() }}" controls playsinline preload="metadata"></video>
                    @else
                        <img src="{{ $first->url() }}" alt="{{ $product->name }}">
                    @endif
                </div>

                @if($media->count() > 1)
                    <div class="gallery-thumbs">
                        @foreach($media as $m)
                            <button type="button" class="thumb-btn {{ $loop->first ? 'active' : '' }}"
                                    data-type="{{ $m->type }}" data-src="{{ $m->url() }}">
                                @if($m->isVideo())
                                    <span class="thumb-video">▶</span>
                                @else
                                    <img src="{{ $m->url() }}" alt="" loading="lazy">
                                @endif
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="product-info">
                <span class="eyebrow">Fiche produit</span>
                @if($product->brand)<div class="card-brand">{{ $product->brand }}</div>@endif
                <h1>{{ $product->name }}</h1>
                <div class="product-price">{{ \App\Support\Shop::money($price) }}</div>
                @if($seesResellerPrice)
                    <div class="notice">
                        Prix revendeur : <strong>{{ \App\Support\Shop::money($product->prixRevendeur()) }}</strong>
                        · Gain du revendeur : <strong>{{ \App\Support\Shop::money($product->gain_revendeur) }}</strong>
                        <div class="small">Visible uniquement par vous et vos revendeurs connectés.</div>
                    </div>
                @endif
                <p class="product-desc">{{ $product->description }}</p>

                @if($product->stock > 0)
                    <p class="stock-line">{{ $product->stock }} en stock</p>
                    <form method="POST" action="{{ route('cart.add', $product) }}" class="add-form">
                        @csrf
                        <label>Quantité
                            <input type="number" name="quantity" value="1" min="1" max="{{ min(10, $product->stock) }}">
                        </label>
                        <button type="submit" class="btn">Ajouter au panier</button>
                    </form>
                @else
                    <p class="card-stock">Rupture de stock</p>
                @endif
            </div>
        </div>
    </div>
</section>

@isset($reviews)
@if($reviews->isNotEmpty())
    <section class="showcase section-apple reviews-section">
        <div class="container">
            <div class="showcase-head">
                <div>
                    <span class="eyebrow">Retours clients</span>
                    <h2>Avis sur ce produit</h2>
                </div>
            </div>
            <div class="reviews-grid">
                @foreach($reviews as $review)
                    @include('partials.review-card', ['review' => $review, 'showProduct' => false])
                @endforeach
            </div>
        </div>
    </section>
@endif
@endisset

<script>
document.querySelectorAll('.thumb-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
        var main = document.getElementById('gallery-main');
        var el;
        main.innerHTML = '';
        if (btn.dataset.type === 'video') {
            el = document.createElement('video');
            el.controls = true;
            el.playsInline = true;
        } else {
            el = document.createElement('img');
        }
        el.src = btn.dataset.src;
        main.appendChild(el);
        document.querySelectorAll('.thumb-btn').forEach(function (b) { b.classList.remove('active'); });
        btn.classList.add('active');
    });
});
</script>
@endsection
