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
    </div>
    <div class="card-body">
        @if($product->brand)<div class="card-brand">{{ $product->brand }}</div>@endif
        <div class="card-name">{{ $product->name }}</div>
        <div class="card-price">{{ \App\Support\Shop::money($price) }}</div>
        @if($product->stock < 1)<div class="card-stock">Rupture de stock</div>@endif
    </div>
</a>
