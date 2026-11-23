@extends('layouts.app')

@section('title', 'Définir mon mot de passe — TECHFLEX')

@section('content')
<section class="showcase page-fill section-apple">
    <div class="container">
        <div class="panel panel-narrow">
            <h1>Définir mon mot de passe</h1>
            <p class="muted">Bonjour {{ $user->name }}, choisissez votre mot de passe pour accéder à votre espace revendeur.</p>

            <form method="POST" action="{{ route('reseller.invitation.store', $token) }}" class="form">
                @csrf
                <label>Email
                    <input type="email" value="{{ $user->email }}" disabled>
                </label>
                <label>Mot de passe (8 caractères minimum)
                    <input type="password" name="password" required autofocus autocomplete="new-password">
                </label>
                <label>Confirmer le mot de passe
                    <input type="password" name="password_confirmation" required autocomplete="new-password">
                </label>
                <button type="submit" class="btn">Créer mon accès et commencer</button>
            </form>
        </div>
    </div>
</section>
@endsection
