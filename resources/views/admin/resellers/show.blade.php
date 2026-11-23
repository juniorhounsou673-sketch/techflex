@extends('layouts.app')

@section('title', 'Compte de ' . $reseller->user->name . ' — TECHFLEX')

@section('content')
@php
    $money = fn ($n) => \App\Support\Shop::money($n);
    $defaultDirection = $balance < 0 ? 'techflex_to_reseller' : 'reseller_to_techflex';
@endphp
<section class="showcase page-fill section-apple">
    <div class="container">
        <a href="{{ route('admin.resellers.index') }}" class="back">← Revendeurs</a>
        <div class="showcase-head">
            <h1>Compte de {{ $reseller->user->name }}</h1>
            @if($reseller->isBlocked())<span class="badge badge-red">Bloqué</span>@else<span class="badge badge-green">Actif</span>@endif
        </div>

        {{-- Solde --}}
        <div class="panel">
            <h2 class="panel-title">Solde</h2>
            @if($balance > 0)
                <div class="pay-amount">{{ $money($balance) }}</div>
                <p>{{ $reseller->user->name }} <strong>vous doit</strong> cette somme.</p>
            @elseif($balance < 0)
                <div class="pay-amount">{{ $money(-$balance) }}</div>
                <p>Vous <strong>devez</strong> cette somme à {{ $reseller->user->name }} (son gain sur les ventes que vous avez encaissées).</p>
            @else
                <div class="pay-amount">0 FCFA</div>
                <p>Le compte est à jour.</p>
            @endif
        </div>

        {{-- Détail --}}
        <div class="stats">
            <div class="stat"><div class="stat-num">{{ $money($orders->sum('total')) }}</div><div class="muted">Ventes payées par les clients</div></div>
            <div class="stat"><div class="stat-num">{{ $money($gain) }}</div><div class="muted">Gain total du revendeur</div></div>
            <div class="stat"><div class="stat-num">{{ $money($owedToTechflex) }}</div><div class="muted">Encaissé par le revendeur : sa part à vous reverser</div></div>
            <div class="stat"><div class="stat-num">{{ $money($owedToReseller) }}</div><div class="muted">Encaissé par vous : son gain à lui verser</div></div>
            <div class="stat"><div class="stat-num">{{ $money($paidIn) }}</div><div class="muted">Déjà versé par le revendeur</div></div>
            <div class="stat"><div class="stat-num">{{ $money($paidOut) }}</div><div class="muted">Déjà versé par vous</div></div>
        </div>

        @if($takenOver->isNotEmpty())
            <div class="notice">
                <strong>Commandes reprises ({{ $takenOver->count() }}).</strong>
                Ce revendeur avait encaissé {{ $money($takenOver->sum('total')) }} sur des commandes que vous avez reprises
                après son blocage. Ces commandes ne sont pas dans le solde : c'est à vous de réclamer cet argent à part.
            </div>
        @endif

        {{-- Enregistrer un versement --}}
        <div class="panel panel-medium">
            <h2 class="panel-title">Enregistrer un versement</h2>
            <form method="POST" action="{{ route('admin.resellers.settle', $reseller) }}" class="form">
                @csrf
                <label>Qui a versé ?
                    <select name="direction">
                        <option value="reseller_to_techflex" {{ old('direction', $defaultDirection) === 'reseller_to_techflex' ? 'selected' : '' }}>Le revendeur m'a versé de l'argent</option>
                        <option value="techflex_to_reseller" {{ old('direction', $defaultDirection) === 'techflex_to_reseller' ? 'selected' : '' }}>Je lui ai versé son gain</option>
                    </select>
                </label>
                <label>Montant (FCFA)
                    <input type="number" name="amount" value="{{ old('amount', abs($balance) ?: '') }}" min="1" step="1" required>
                </label>
                <label>Référence MoMo (facultatif)
                    <input type="text" name="momo_reference" value="{{ old('momo_reference') }}" maxlength="60">
                </label>
                <label>Note (facultatif)
                    <input type="text" name="note" value="{{ old('note') }}" maxlength="190">
                </label>
                <button type="submit" class="btn">Enregistrer</button>
            </form>
        </div>

        {{-- Historique des versements --}}
        <h2>Versements</h2>
        @if($settlements->isEmpty())
            <p class="muted">Aucun versement enregistré.</p>
        @else
            <div class="table-wrap">
                <table class="table">
                    <thead><tr><th>Date</th><th>Sens</th><th>Montant</th><th>Référence</th><th>Note</th><th></th></tr></thead>
                    <tbody>
                    @foreach($settlements as $st)
                        <tr>
                            <td>{{ $st->paid_at->timezone('Africa/Porto-Novo')->format('d/m/Y H:i') }}</td>
                            <td>{{ $st->direction === 'reseller_to_techflex' ? 'Le revendeur vous a versé' : 'Vous avez versé au revendeur' }}</td>
                            <td>{{ $money($st->amount) }}</td>
                            <td>{{ $st->momo_reference }}</td>
                            <td>{{ $st->note }}</td>
                            <td>
                                <form method="POST" action="{{ route('admin.settlements.destroy', $st) }}"
                                      onsubmit="return confirm('Supprimer ce versement ? Le solde sera recalculé.');">
                                    @csrf
                                    <button type="submit" class="link-btn">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        {{-- Commandes prises en compte --}}
        <h2 style="margin-top:32px">Commandes payées prises en compte</h2>
        @if($orders->isEmpty())
            <p class="muted">Aucune commande payée pour le moment.</p>
        @else
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr><th>Commande</th><th>Mode</th><th>Total client</th><th>Part TECHFLEX</th><th>Gain revendeur</th><th>Argent encaissé par</th></tr>
                    </thead>
                    <tbody>
                    @foreach($orders as $o)
                        @php $byReseller = $o->payment_method === 'momo_revendeur'; @endphp
                        <tr>
                            <td><strong>{{ $o->reference }}</strong><div class="muted small">{{ $o->created_at->timezone('Africa/Porto-Novo')->format('d/m/Y') }}</div></td>
                            <td>{{ \App\Support\PaymentMethods::label($o->payment_method) }}</td>
                            <td>{{ $money($o->total) }}</td>
                            <td>{{ $money($o->montant_du_techflex) }}</td>
                            <td>{{ $money($o->total - $o->montant_du_techflex) }}</td>
                            <td>{{ $byReseller ? 'Le revendeur' : 'TECHFLEX' }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</section>
@endsection
