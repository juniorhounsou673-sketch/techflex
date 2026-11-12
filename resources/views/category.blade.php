@extends('layouts.app')

@section('title', $category->name . ' — TECHFLEX')

@section('content')
@php $themeClass = $category->slug === 'montres-de-luxe' ? 'section-gold' : 'section-apple'; @endphp
<section class="showcase page-fill {{ $themeClass }}">
    <div class="container">
        <div class="showcase-head">
            <h1>{{ $category->name }}</h1>
        </div>

        @if($products->isEmpty())
            <p class="muted">Aucun produit disponible pour le moment.</p>
        @else
            <div class="grid">
                @foreach($products as $product)
                    @include('partials.product-card', ['product' => $product])
                @endforeach
            </div>
        @endif
    </div>
</section>
@endsection
