@extends('layouts.app')

@section('title', 'Commandes — TECHFLEX')

@section('content')
<section class="showcase page-fill section-apple">
    <div class="container">
        <a href="{{ route('admin.dashboard') }}" class="back">← Admin</a>
        <div class="showcase-head">
            <h1>Commandes</h1>
            <div class="btn-row">
                <a href="{{ route('admin.orders.index', ['filtre' => 'a_confirmer']) }}"
                   class="btn btn-small {{ $filter === 'a_confirmer' ? '' : 'btn-outline' }}">Paiements à confirmer</a>
                <a href="{{ route('admin.orders.index') }}"
                   class="btn btn-small {{ $filter === 'toutes' ? '' : 'btn-outline' }}">Toutes</a>
            </div>
        </div>

        @if($orders->isEmpty())
            <p class="muted">
                @if($filter === 'a_confirmer') Aucun paiement MoMo en attente de confirmation. @else Aucune commande pour le moment. @endif
            </p>
        @else
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Commande</th>
                        <th>Client</th>
                        <th>Total</th>
                        <th>Paiement</th>
                        <th>Vendu par</th>
                        <th>Livraison</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($orders as $o)
                    <tr>
                        <td>
                            <strong>{{ $o->reference }}</strong>
                            <div class="muted small">{{ $o->created_at->timezone('Africa/Porto-Novo')->format('d/m/Y H:i') }}</div>
                            @foreach($o->items as $i)
                                <div class="muted small">{{ $i->quantity }} × {{ $i->product_name }}</div>
                            @endforeach
                        </td>
                        <td>{{ $o->client_name }}<br><a href="tel:{{ $o->client_phone }}">{{ $o->client_phone }}</a></td>
                        <td>{{ \App\Support\Shop::money($o->total) }}</td>
                        <td>
                            <div class="muted small">{{ \App\Support\PaymentMethods::label($o->payment_method) }}</div>
                            @if($o->isPaid())
                                <span class="badge badge-green">Payé</span>
                            @else
                                <span class="badge badge-gold">Non payé</span>
                                @if($o->momo_transaction_id)
                                    <div class="small">Réf. MoMo : <strong>{{ $o->momo_transaction_id }}</strong></div>
                                @endif
                                <form method="POST" action="{{ route('admin.orders.paid', $o) }}"
                                      onsubmit="return confirm('Confirmer que vous avez bien reçu {{ \App\Support\Shop::money($o->total) }} ?');">
                                    @csrf
                                    <button type="submit" class="btn btn-small">Marquer payé</button>
                                </form>
                            @endif
                        </td>
                        <td>{{ $o->reseller?->user->name ?? 'TECHFLEX (direct)' }}</td>
                        <td>
                            @if($o->delivery)
                                {{ $o->delivery->delivery_date->format('d/m/Y') }} à {{ substr($o->delivery->delivery_time, 0, 5) }}
                            @else
                                <span class="muted">Pas encore programmée</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</section>
@endsection
