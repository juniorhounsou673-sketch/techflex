<?php

return [
    // Numéro unique qui reçoit TOUTES les demandes de livraison (WhatsApp), quel que soit le revendeur
    'delivery_phone' => env('TECHFLEX_DELIVERY_PHONE', '22900000000'),

    // Votre numéro MoMo : les clients directs (sans revendeur) y envoient leur paiement
    'momo_number' => env('TECHFLEX_MOMO_NUMBER', ''),
    'momo_name' => env('TECHFLEX_MOMO_NAME', 'TECHFLEX'),

    // true = paiement MoMo « sur le site » simulé (aucun argent réel).
    // Tant que c'est true, ce mode n'est proposé qu'en local (APP_ENV=local), jamais en ligne.
    'momo_simulation' => env('TECHFLEX_MOMO_SIMULATION', true),

    // Durée de validité du lien d'invitation envoyé au revendeur.
    'invitation_ttl_days' => 7,
];
