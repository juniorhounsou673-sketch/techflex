<?php

namespace App\Support;

class PaymentMethods
{
    public const LABELS = [
        'momo_site' => 'MoMo sur le site',
        'momo_direct' => 'MoMo vers TECHFLEX',
        'momo_revendeur' => 'MoMo vers le vendeur',
        'livraison' => 'À la livraison',
    ];

    public static function label(string $method): string
    {
        return self::LABELS[$method] ?? $method;
    }

    /** MoMo envoyé à la main vers un numéro : le paiement est confirmé ensuite par l'admin. */
    public static function manualMomo(string $method): bool
    {
        return in_array($method, ['momo_direct', 'momo_revendeur'], true);
    }

    /** Modes proposés au client. */
    public static function available(bool $viaReseller): array
    {
        $methods = [];

        // Paiement simulé : jamais proposé en ligne (sinon n'importe qui « paierait » gratuitement)
        if (! config('techflex.momo_simulation') || app()->environment(['local', 'testing'])) {
            $methods[] = 'momo_site';
        }

        if ($viaReseller) {
            $methods[] = 'momo_revendeur';
        } elseif (config('techflex.momo_number')) {
            $methods[] = 'momo_direct';
        }

        $methods[] = 'livraison';

        return $methods;
    }
}
