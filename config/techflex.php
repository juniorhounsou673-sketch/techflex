<?php

return [
    // Numéro unique qui reçoit TOUTES les demandes de livraison (WhatsApp), quel que soit le revendeur
    'delivery_phone' => env('TECHFLEX_DELIVERY_PHONE', '22900000000'),

    // true = paiement MoMo simulé (aucun argent réel). Mettre false quand l'API réelle sera branchée.
    'momo_simulation' => env('TECHFLEX_MOMO_SIMULATION', true),
];
