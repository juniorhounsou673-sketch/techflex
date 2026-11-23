@extends('layouts.app')

@section('title', 'Commander — TECHFLEX')

@section('content')
@php
    $labels = [
        'momo_site' => ['Payer maintenant par MoMo', 'Paiement sur le site, puis choix de la livraison.'],
        'momo_direct' => ['Payer par MoMo vers TECHFLEX', 'Le numéro vous est donné juste après la commande.'],
        'momo_revendeur' => ['Payer par MoMo vers le numéro du vendeur', 'Le numéro vous est donné juste après la commande.'],
        'livraison' => ['Payer à la livraison', 'Vous choisissez ensuite le jour, l\'heure et le lieu de livraison.'],
    ];
@endphp
<section class="showcase page-fill section-apple">
    <div class="container">
        <a href="{{ route('cart') }}" class="back">← Retour au panier</a>
        <div class="showcase-head"><h1>Finaliser la commande</h1></div>

        <div class="checkout-grid">
            <div class="panel">
                <h2 class="panel-title">Vos informations</h2>

                <form method="POST" action="{{ route('checkout.store') }}" class="form">
                    @csrf
                    <label>Nom complet
                        <input type="text" name="client_name" value="{{ old('client_name') }}" required>
                    </label>
                    <label>Téléphone
                        <input type="text" name="client_phone" value="{{ old('client_phone') }}" placeholder="22997000000" required>
                    </label>

                    <div class="pay-options">
                        <div class="pay-title">Mode de paiement</div>

                        @foreach($methods as $m)
                            <label class="pay-option">
                                <input type="radio" name="payment_method" value="{{ $m }}" {{ old('payment_method', $methods[0]) === $m ? 'checked' : '' }}>
                                <span><strong>{{ $labels[$m][0] }}</strong><br>
                                <span class="muted small">{{ $labels[$m][1] }}</span></span>
                            </label>
                        @endforeach
                    </div>

                    <button type="submit" class="btn">Valider la commande</button>
                </form>
            </div>

            <div class="panel">
                <h2 class="panel-title">Récapitulatif</h2>
                <table class="table">
                    <tbody>
                    @foreach($lines as $line)
                        <tr>
                            <td>{{ $line['qty'] }} × {{ $line['product']->name }}</td>
                            <td>{{ \App\Support\Shop::money($line['subtotal']) }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <div class="cart-total">Total : <strong>{{ \App\Support\Shop::money($total) }}</strong></div>
            </div>
        </div>
    </div>
</section>
@endsection
