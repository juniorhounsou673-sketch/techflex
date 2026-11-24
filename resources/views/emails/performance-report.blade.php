<x-mail::message>
# Tops {{ $periodLabel }} — TECHFLEX

Bonjour {{ $notifiable->name }},

Voici les classements **{{ $periodLabel }}** ({{ $periodDates }}).

## Produits les plus vendus

@forelse($topProducts as $product)
{{ $loop->iteration }}. **{{ $product['name'] }}** — {{ $product['quantity'] }} vendu{{ $product['quantity'] > 1 ? 's' : '' }} · {{ \App\Support\Shop::money($product['amount']) }}
@empty
Aucune vente payée sur cette période.
@endforelse

## Meilleurs revendeurs

@forelse($topResellers as $reseller)
{{ $loop->iteration }}. **{{ $reseller['name'] }}** ({{ $reseller['code'] }}) — {{ $reseller['orders'] }} commande{{ $reseller['orders'] > 1 ? 's' : '' }} · {{ \App\Support\Shop::money($reseller['amount']) }} de ventes · {{ \App\Support\Shop::money($reseller['profit']) }} de gain
@empty
Aucune vente revendeur payée sur cette période.
@endforelse

Cordialement,<br>
{{ config('app.name') }}
</x-mail::message>
