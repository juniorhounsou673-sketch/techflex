<x-mail::message>
# Bonjour {{ $user->name }},

TECHFLEX vous donne accès à votre espace revendeur. Cliquez sur le bouton ci-dessous
pour **définir vous-même votre mot de passe**, puis commencer à vendre.

<x-mail::button :url="$url">
Définir mon mot de passe
</x-mail::button>

Ce lien est valable {{ $days }} jours et ne peut être utilisé qu'une seule fois.
Si vous n'êtes pas à l'origine de cette invitation, ignorez cet email.

<x-slot:subcopy>
Si le bouton ne fonctionne pas, copiez ce lien dans votre navigateur : [{{ $url }}]({{ $url }})
</x-slot:subcopy>

Cordialement,<br>
{{ config('app.name') }}
</x-mail::message>
