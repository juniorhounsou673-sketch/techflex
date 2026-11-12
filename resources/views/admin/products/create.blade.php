@extends('layouts.app')

@section('title', 'Nouveau produit — TECHFLEX')

@section('content')
<section class="showcase page-fill section-apple">
    <div class="container">
        <a href="{{ route('admin.products.index') }}" class="back">← Produits</a>

        <div class="panel panel-medium">
            <h1>Ajouter un produit</h1>

            <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" class="form">
                @csrf
                @include('admin.products._fields', ['product' => null])
                <button type="submit" class="btn">Créer le produit</button>
            </form>
        </div>
    </div>
</section>
@endsection
