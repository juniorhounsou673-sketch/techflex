@php $showProduct = $showProduct ?? true; @endphp
<div class="review-card">
    <div class="stars" aria-label="{{ $review->rating }} sur 5">{{ str_repeat('★', $review->rating) }}<span class="stars-off">{{ str_repeat('★', 5 - $review->rating) }}</span></div>
    <p class="review-body">{{ $review->body }}</p>

    @if($review->image)
        <a href="{{ $review->imageUrl() }}" target="_blank" rel="noopener">
            <img src="{{ $review->imageUrl() }}" class="review-img" alt="Message d'un client" loading="lazy">
        </a>
    @endif

    <div class="review-author">
        <strong>{{ $review->author_name }}</strong>@if($review->author_city) · {{ $review->author_city }}@endif
    </div>

    @if($showProduct && $review->product && $review->product->is_active)
        <div class="muted small">À propos de : <a href="{{ route('product', $review->product) }}">{{ $review->product->name }}</a></div>
    @endif
</div>
