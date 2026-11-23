@extends('layouts.app')

@section('title', 'Nouvel avis — TECHFLEX')

@section('content')
<section class="showcase page-fill section-apple">
    <div class="container">
        <a href="{{ route('admin.reviews.index') }}" class="back">← Avis</a>

        <div class="panel panel-medium">
            <h1>Ajouter un avis</h1>

            <div class="notice">
                Publiez uniquement de <strong>vrais avis</strong>, avec l'accord du client. Mettez son prénom (et l'initiale
                de son nom), jamais son numéro de téléphone.
            </div>

            <form method="POST" action="{{ route('admin.reviews.store') }}" enctype="multipart/form-data" class="form">
                @csrf
                @include('admin.reviews._fields', ['review' => null])
                <button type="submit" class="btn">Ajouter l'avis</button>
            </form>
        </div>
    </div>
</section>
@endsection
