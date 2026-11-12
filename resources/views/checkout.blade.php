@extends('layouts.app')

@section('title', 'Commander — TECHFLEX')

@section('content')
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

                        <label class="pay-option">
                            <input type="radio" name="payment_method" value="momo_site" {{ old('payment_method', 'momo_site') === 'momo_site' ? 'checked' : '' }}>
                            <span><strong>Payer maintenant par MoMo</strong><br>
                            <span class="muted small">Paiement sécurisé sur le site, puis choix de la livraison.</span></span>
                        </label>

                        @if($reseller)
                        <label class="pay-option">
                            <input type="radio" name="payment_method" value="momo_revendeur" {{ old('payment_method') === 'momo_revendeur' ? 'checked' : '' }}>
                            <span><strong>Payer par MoMo vers le numéro du vendeur</strong><br>
                            <span class="muted small">Le numéro vous est donné après la commande.</span></span>
                        </label>
                        @endif

                        <label class="pay-option">
                            <input type="radio" name="payment_method" value="livraison" {{ old('payment_method') === 'livraison' ? 'checked' : '' }}>
                            <span><strong>Payer à la livraison</strong><br>
                            <span class="muted small">Vous choisissez ensuite le jour, l'heure et le lieu de livraison.</span></span>
                        </label>
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
