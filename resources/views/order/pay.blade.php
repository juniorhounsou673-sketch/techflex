@extends('layouts.app')

@section('title', 'Paiement MoMo — TECHFLEX')

@section('content')
<section class="showcase page-fill section-apple">
    <div class="container">
        <div class="panel panel-narrow">
            <h1>Paiement MoMo</h1>
            <p class="muted">Commande <strong>{{ $order->reference }}</strong></p>
            <div class="pay-amount">{{ \App\Support\Shop::money($order->total) }}</div>

            @if(config('techflex.momo_simulation'))
                <div class="notice">Mode test : aucun argent réel n'est débité.</div>
            @endif

            <form method="POST" action="{{ route('order.pay.submit', $order->reference) }}" class="form">
                @csrf
                <label>Numéro MoMo qui paie
                    <input type="text" name="momo_phone" value="{{ old('momo_phone', $order->client_phone) }}" required>
                </label>
                <button type="submit" class="btn">Payer {{ \App\Support\Shop::money($order->total) }}</button>
            </form>
        </div>
    </div>
</section>
@endsection
