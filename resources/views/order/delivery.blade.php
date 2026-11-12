@extends('layouts.app')

@section('title', 'Livraison — TECHFLEX')

@section('content')
<section class="showcase page-fill section-apple">
    <div class="container">
        <div class="panel panel-narrow">
            <h1>Votre livraison</h1>
            <p class="muted">Commande <strong>{{ $order->reference }}</strong>
                @if($order->payment_method === 'livraison')
                    · à payer à la livraison : <strong>{{ \App\Support\Shop::money($order->total) }}</strong>
                @elseif($order->isPaid())
                    · paiement reçu
                @endif
            </p>

            <form method="POST" action="{{ route('order.delivery.store', $order->reference) }}" class="form">
                @csrf
                <label>Jour de livraison
                    <input type="date" name="delivery_date" value="{{ old('delivery_date') }}" min="{{ now('Africa/Porto-Novo')->format('Y-m-d') }}" required>
                </label>
                <label>Heure de livraison
                    <input type="time" name="delivery_time" value="{{ old('delivery_time') }}" required>
                </label>
                <label>Adresse / description du lieu
                    <input type="text" name="address" value="{{ old('address') }}" placeholder="Quartier, rue, point de repère" required>
                </label>

                <input type="hidden" name="latitude" id="lat" value="{{ old('latitude') }}">
                <input type="hidden" name="longitude" id="lng" value="{{ old('longitude') }}">
                <div>
                    <button type="button" id="geo" class="btn btn-outline btn-small">📍 Utiliser ma position</button>
                    <span id="geo-status" class="muted small"></span>
                </div>

                <label>Téléphone pour vous joindre
                    <input type="text" name="contact_phone" value="{{ old('contact_phone', $order->client_phone) }}" required>
                </label>

                <button type="submit" class="btn">Confirmer la livraison</button>
            </form>
        </div>
    </div>
</section>

<script>
document.getElementById('geo').addEventListener('click', function () {
    var status = document.getElementById('geo-status');
    if (!navigator.geolocation) { status.textContent = ' Position indisponible sur cet appareil.'; return; }
    status.textContent = ' Recherche de votre position...';
    navigator.geolocation.getCurrentPosition(function (p) {
        document.getElementById('lat').value = p.coords.latitude.toFixed(7);
        document.getElementById('lng').value = p.coords.longitude.toFixed(7);
        status.textContent = ' Position enregistrée ✔';
    }, function () {
        status.textContent = ' Impossible de récupérer la position. Écrivez votre adresse.';
    });
});
</script>
@endsection
