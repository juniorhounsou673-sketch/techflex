@extends('layouts.app')

@section('title', 'Mon espace — TECHFLEX')

@section('content')
<section class="showcase page-fill section-apple">
    <div class="container">
        <div class="showcase-head"><h1>Bonjour {{ $reseller->user->name }}</h1></div>

        <div class="panel">
            <strong>Votre lien de vente</strong>
            <p><code>{{ $link }}</code></p>
            <p class="muted small">Partagez ce lien à vos clients.</p>
        </div>

        <div class="stats">
            <div class="stat"><div class="stat-num">{{ \App\Support\Shop::money($sales) }}</div><div class="muted">Ventes payées</div></div>
            <div class="stat"><div class="stat-num">{{ \App\Support\Shop::money($profit) }}</div><div class="muted">Votre bénéfice</div></div>
            <div class="stat"><div class="stat-num">{{ \App\Support\Shop::money($balance) }}</div><div class="muted">Reste à reverser à TECHFLEX</div></div>
        </div>

        <h2>Vos prix revendeur</h2>
        <p class="muted small">Le prix revendeur est ce que vous reversez à TECHFLEX pour chaque produit vendu. Le client paie le prix client, et la différence est votre gain.</p>
        @if($products->isEmpty())
            <p class="muted">Aucun produit pour le moment.</p>
        @else
            <div class="table-wrap">
                <table class="table">
                    <thead><tr><th>Produit</th><th>Prix client</th><th>Prix revendeur</th><th>Votre gain</th></tr></thead>
                    <tbody>
                    @foreach($products as $p)
                        <tr>
                            <td><a href="{{ route('product', $p) }}">{{ $p->name }}</a><br><span class="muted small">{{ $p->category->name }}</span></td>
                            <td>{{ \App\Support\Shop::money($p->prix_client) }}</td>
                            <td><strong>{{ \App\Support\Shop::money($p->prixRevendeur()) }}</strong></td>
                            <td>{{ \App\Support\Shop::money($p->gain_revendeur) }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <h2 style="margin-top:32px">Vos dernières commandes</h2>
        @if($orders->isEmpty())
            <p class="muted">Aucune commande pour le moment.</p>
        @else
            <div class="table-wrap">
                <table class="table">
                    <thead><tr><th>Référence</th><th>Client</th><th>Total</th><th>Paiement</th><th>Statut</th></tr></thead>
                    <tbody>
                    @foreach($orders as $o)
                        <tr>
                            <td>{{ $o->reference }}</td>
                            <td>{{ $o->client_name }}</td>
                            <td>{{ \App\Support\Shop::money($o->total) }}</td>
                            <td>{{ $o->payment_status }}</td>
                            <td>{{ $o->status }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</section>
@endsection
