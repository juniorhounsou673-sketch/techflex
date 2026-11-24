@php
    $price = \App\Support\Shop::price($product);
    $cover = $product->coverUrl();
    $hasVideo = $product->media->contains('type', 'video');
@endphp
<a href="{{ route('product', $product) }}" class="card">
    <div class="card-img">
        @if($cover)
            <img src="{{ $cover }}" alt="{{ $product->name }}" loading="lazy">
        @else
            <span>{{ mb_strtoupper(mb_substr($product->brand ?: $product->name, 0, 1)) }}</span>
        @endif
        @if($hasVideo)<span class="card-video-tag">▶ Vidéo</span>@endif
        <span class="card-veil">Voir le produit <span aria-hidden="true">→</span></span>
    </div>
    <div class="card-body">
        <div class="card-topline">
            @if($product->brand)<span class="card-brand">{{ $product->brand }}</span>@endif
            @if($product->stock < 1)<span class="card-badge">Épuisé</span>@endif
        </div>
        <div class="card-name">{{ $product->name }}</div>
        @if($product->stock < 1)<div class="card-stock">Rupture de stock</div>@endif
        <div class="card-foot">
            <span class="card-price">{{ \App\Support\Shop::money($price) }}</span>
            <span class="card-arrow" aria-hidden="true">↗</span>
        </div>
    </div>
</a>
