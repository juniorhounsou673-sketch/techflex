@extends('layouts.app')

@section('title', 'Commande ' . $order->reference . ' — TECHFLEX')

@section('content')
@php
    $delivery = $order->delivery;
    $deliveryLabels = ['planifiee' => 'Planifiée', 'en_route' => 'En route', 'livree' => 'Livrée', 'echouee' => 'Échouée'];
@endphp
<section class="showcase page-fill section-apple">
    <div class="container">
        <div class="showcase-head"><h1>Commande {{ $order->reference }}</h1></div>

        <div class="checkout-grid">
            <div>
                {{-- Paiement --}}
                <div class="panel">
                    <h2 class="panel-title">Paiement</h2>

                    @if($order->isPaid())
                        <span class="badge badge-green">Paiement reçu</span>
                    @elseif($order->payment_method === 'momo_site')
                        <p>Paiement MoMo en attente.</p>
                        <a href="{{ route('order.pay', $order->reference) }}" class="btn">Payer {{ \App\Support\Shop::money($order->total) }}</a>
                    @elseif($order->payment_method === 'momo_revendeur')
                        <p>Envoyez <strong>{{ \App\Support\Shop::money($order->total) }}</strong> par MoMo au numéro :</p>
                        <p class="pay-amount">{{ $order->reseller?->momo_number }}</p>

                        @if($order->momo_transaction_id)
                            <p><span class="badge badge-gold">En attente de confirmation</span></p>
                            <p class="muted small">Référence enregistrée : {{ $order->momo_transaction_id }}</p>
                        @else
                            <form method="POST" action="{{ route('order.reference', $order->reference) }}" class="form">
                                @csrf
                                <label>Référence de la transaction MoMo (reçue par SMS)
                                    <input type="text" name="momo_transaction_id" required>
                                </label>
                                <button type="submit" class="btn btn-small">Enregistrer la référence</button>
                            </form>
                        @endif
                    @else
                        <p>Vous paierez <strong>{{ \App\Support\Shop::money($order->total) }}</strong> à la livraison.</p>
                    @endif
                </div>

                {{-- Livraison --}}
                <div class="panel">
                    <h2 class="panel-title">Livraison</h2>

                    @if($delivery)
                        <p>
                            <strong>{{ $delivery->delivery_date->format('d/m/Y') }}</strong> à
                            <strong>{{ substr($delivery->delivery_time, 0, 5) }}</strong><br>
                            {{ $delivery->address }}<br>
                            <span class="muted small">Contact : {{ $delivery->contact_phone }}</span>
                        </p>
                        <p><span class="badge badge-gold">{{ $deliveryLabels[$delivery->status] ?? $delivery->status }}</span></p>
                        <a href="{{ $delivery->whatsappUrl() }}" target="_blank" rel="noopener" class="btn">Envoyer ma demande par WhatsApp</a>
                        <p class="muted small">Un message est prêt : il vous suffit de l'envoyer.</p>
                    @elseif($order->payment_method === 'momo_site' && ! $order->isPaid())
                        <p class="muted">La livraison se programme après le paiement.</p>
                    @else
                        <p>Choisissez le jour, l'heure et le lieu de votre livraison.</p>
                        <a href="{{ route('order.delivery', $order->reference) }}" class="btn">Programmer ma livraison</a>
                    @endif
                </div>
            </div>

            <div class="panel">
                <h2 class="panel-title">Récapitulatif</h2>
                <table class="table">
                    <tbody>
                    @foreach($order->items as $item)
                        <tr>
                            <td>{{ $item->quantity }} × {{ $item->product_name }}</td>
                            <td>{{ \App\Support\Shop::money($item->prix_vente * $item->quantity) }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <div class="cart-total">Total : <strong>{{ \App\Support\Shop::money($order->total) }}</strong></div>
                <p class="muted small">Gardez ce numéro de commande : {{ $order->reference }}</p>
            </div>
        </div>
    </div>
</section>
@endsection
