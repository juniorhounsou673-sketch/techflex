<x-mail::message>
# Compte revendeur bloqué

Bonjour {{ $notifiable->name }},

Le compte revendeur **{{ $resellerName }}** ({{ $resellerCode }}) a été bloqué le {{ $blockedAt }}.
L'accès à l'espace revendeur et aux liens de vente est immédiatement suspendu.

@if($reason)
Motif indiqué : {{ $reason }}
@endif

Les commandes payées mais non encore livrées ont été reprises par TECHFLEX. L'historique et le solde du revendeur sont conservés.

Cordialement,<br>
{{ config('app.name') }}
</x-mail::message>
