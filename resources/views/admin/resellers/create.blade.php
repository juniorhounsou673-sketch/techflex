@extends('layouts.app')

@section('title', 'Nouveau revendeur — TECHFLEX')

@section('content')
<section class="showcase page-fill section-apple">
    <div class="container">
        <a href="{{ route('admin.resellers.index') }}" class="back">← Revendeurs</a>

        <div class="panel panel-narrow">
            <h1>Donner accès à un revendeur</h1>
            <p class="muted small">Son gain sur chaque produit se règle dans la page du produit.</p>

            <form method="POST" action="{{ route('admin.resellers.store') }}" class="form">
                @csrf
                <label>Nom complet
                    <input type="text" name="name" value="{{ old('name') }}" required>
                </label>
                <label>Email (pour se connecter)
                    <input type="email" name="email" value="{{ old('email') }}" required>
                </label>
                <label>Téléphone
                    <input type="text" name="phone" value="{{ old('phone') }}">
                </label>
                <label>Numéro MoMo du revendeur
                    <input type="text" name="momo_number" value="{{ old('momo_number') }}" required>
                </label>
                <label>Code revendeur (laissez vide pour un code automatique)
                    <input type="text" name="code" value="{{ old('code') }}" placeholder="REV003">
                </label>
                <label>Mot de passe (8 caractères minimum)
                    <input type="text" name="password" value="{{ old('password') }}" required>
                </label>
                <button type="submit" class="btn">Créer le compte</button>
            </form>
        </div>
    </div>
</section>
@endsection
