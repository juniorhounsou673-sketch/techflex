@extends('layouts.app')

@section('title', 'Connexion — TECHFLEX')

@section('content')
<section class="showcase page-fill section-apple">
    <div class="container">
        <div class="panel panel-narrow">
            <h1>Connexion</h1>
            <p class="muted">Espace réservé à TECHFLEX et à ses revendeurs.</p>

            <form method="POST" action="{{ route('login.submit') }}" class="form">
                @csrf
                <label>Email
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus>
                </label>
                <label>Mot de passe
                    <input type="password" name="password" required>
                </label>
                <label class="check">
                    <input type="checkbox" name="remember" value="1"> Rester connecté
                </label>
                <button type="submit" class="btn">Se connecter</button>
            </form>
        </div>
    </div>
</section>
@endsection
