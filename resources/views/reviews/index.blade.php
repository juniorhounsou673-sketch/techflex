@extends('layouts.app')

@section('title', 'Avis clients — TECHFLEX')

@section('content')
<section class="showcase page-fill section-apple">
    <div class="container">
        <div class="showcase-head">
            <h1>Avis de nos clients</h1>
            @if($average)
                <div><span class="stars">★</span> <strong>{{ number_format($average, 1, ',', '') }} / 5</strong>
                    <span class="muted">· {{ $reviews->count() }} avis</span></div>
            @endif
        </div>

        @if($reviews->isEmpty())
            <p class="muted">Les premiers avis arrivent bientôt.</p>
        @else
            <div class="reviews-grid">
                @foreach($reviews as $review)
                    @include('partials.review-card', ['review' => $review])
                @endforeach
            </div>
        @endif
    </div>
</section>
@endsection
