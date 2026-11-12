<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Str;

class MomoGateway
{
    /**
     * Demande le paiement MoMo d'une commande.
     * Retourne ['success' => bool, 'transaction_id' => string].
     *
     * Aujourd'hui : simulation. Plus tard : appel à l'API du prestataire choisi (MTN MoMo, KKiaPay, FedaPay...).
     */
    public function charge(Order $order, string $phone): array
    {
        if (config('techflex.momo_simulation', true)) {
            return [
                'success' => true,
                'transaction_id' => 'SIM-' . strtoupper(Str::random(10)),
            ];
        }

        throw new \RuntimeException("Le paiement MoMo réel n'est pas encore branché.");
    }
}
