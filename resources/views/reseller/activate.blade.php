@extends('layouts.app')

@section('title', 'Activer mon compte — TECHFLEX')

@section('content')
<section class="showcase page-fill section-apple">
    <div class="container">
        <div class="panel panel-narrow" style="text-align:center">
            <h1>BIENVENUE DANS L’ÉQUIPE DE TECHFLEX !</h1>
            <p class="muted">Bonjour {{ $user->name }}, votre accès revendeur est prêt. Cliquez sur le bouton ci-dessous pour définir votre mot de passe et ouvrir votre espace revendeur.</p>

            <a href="{{ route('reseller.invitation.activate', $token) }}" class="btn">Activer mon compte</a>

            <p class="muted small" style="margin-top:20px">
                Vous pourrez ensuite vous connecter à tout moment depuis <a href="{{ route('login') }}">l’espace revendeur</a>.
            </p>
        </div>
    </div>
</section>
@endsection
