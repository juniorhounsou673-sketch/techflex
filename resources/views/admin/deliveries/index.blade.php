@extends('layouts.app')

@section('title', 'Livraisons — TECHFLEX')

@section('content')
@php
    $statuses = ['planifiee' => 'Planifiée', 'en_route' => 'En route', 'livree' => 'Livrée', 'echouee' => 'Échouée'];
    $methods = ['momo_site' => 'MoMo site', 'momo_revendeur' => 'MoMo vendeur', 'livraison' => 'À la livraison'];
@endphp
<section class="showcase page-fill section-apple">
    <div class="container">
        <a href="{{ route('admin.dashboard') }}" class="back">← Admin</a>
        <div class="showcase-head"><h1>Livraisons</h1></div>

        @if($deliveries->isEmpty())
            <p class="muted">Aucune livraison programmée pour le moment.</p>
        @else
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Commande</th>
                        <th>Client</th>
                        <th>Jour / heure</th>
                        <th>Lieu</th>
                        <th>Paiement</th>
                        <th>Vendu par</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($deliveries as $d)
                    @php $o = $d->order; @endphp
                    <tr>
                        <td>
                            <strong>{{ $o->reference }}</strong>
                            @foreach($o->items as $i)
                                <div class="muted small">{{ $i->quantity }} × {{ $i->product_name }}</div>
                            @endforeach
                        </td>
                        <td>{{ $o->client_name }}<br><a href="tel:{{ $d->contact_phone }}">{{ $d->contact_phone }}</a></td>
                        <td>{{ $d->delivery_date->format('d/m/Y') }}<br>{{ substr($d->delivery_time, 0, 5) }}</td>
                        <td>
                            {{ $d->address }}
                            @if($d->mapsUrl())<br><a href="{{ $d->mapsUrl() }}" target="_blank" rel="noopener">📍 Voir sur la carte</a>@endif
                        </td>
                        <td>
                            <div class="muted small">{{ $methods[$o->payment_method] ?? $o->payment_method }}</div>
                            @if($o->isPaid())
                                <span class="badge badge-green">Payé</span>
                            @else
                                <span class="badge badge-gold">À encaisser</span>
                                <div><strong>{{ \App\Support\Shop::money($o->total) }}</strong></div>
                                @if($o->momo_transaction_id)<div class="muted small">Réf. {{ $o->momo_transaction_id }}</div>@endif
                                <form method="POST" action="{{ route('admin.orders.paid', $o) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-small">Marquer payé</button>
                                </form>
                            @endif
                        </td>
                        <td>{{ $o->reseller?->user->name ?? 'TECHFLEX (direct)' }}</td>
                        <td>
                            <form method="POST" action="{{ route('admin.deliveries.status', $d) }}" class="inline-form">
                                @csrf
                                <select name="status">
                                    @foreach($statuses as $value => $label)
                                        <option value="{{ $value }}" {{ $d->status === $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="btn btn-small">OK</button>
                            </form>
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
