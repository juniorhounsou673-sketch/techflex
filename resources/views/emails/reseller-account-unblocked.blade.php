<x-mail::message>
# Compte revendeur réactivé

Bonjour {{ $notifiable->name }},

Le compte revendeur **{{ $resellerName }}** ({{ $resellerCode }}) a été réactivé le {{ $unblockedAt }}.
L'accès à l'espace revendeur et aux liens de vente est de nouveau disponible.

Cordialement,<br>
{{ config('app.name') }}
</x-mail::message>
