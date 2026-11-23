@extends('layouts.app')

@section('title', 'Modifier un avis — TECHFLEX')

@section('content')
<section class="showcase page-fill section-apple">
    <div class="container">
        <a href="{{ route('admin.reviews.index') }}" class="back">← Avis</a>

        <div class="panel panel-medium">
            <h1>Modifier l'avis de {{ $review->author_name }}</h1>

            <form method="POST" action="{{ route('admin.reviews.update', $review) }}" enctype="multipart/form-data" class="form">
                @csrf
                @include('admin.reviews._fields', ['review' => $review])
                <button type="submit" class="btn">Enregistrer</button>
            </form>
        </div>
    </div>
</section>
@endsection
