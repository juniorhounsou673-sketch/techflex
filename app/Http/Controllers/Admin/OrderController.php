<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;

class OrderController extends Controller
{
    /** Confirme que l'argent a bien été reçu (paiement à la livraison, ou MoMo vers le compte d'un revendeur). */
    public function markPaid(Order $order)
    {
        if (! $order->isPaid()) {
            $order->update(['payment_status' => 'paye', 'paid_at' => now()]);
        }

        return back()->with('success', "Paiement de la commande {$order->reference} confirmé.");
    }
}
