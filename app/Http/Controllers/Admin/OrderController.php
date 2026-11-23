<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->query('filtre') === 'a_confirmer' ? 'a_confirmer' : 'toutes';

        $query = Order::with(['reseller.user', 'delivery', 'items'])->latest('id');

        if ($filter === 'a_confirmer') {
            $query->where('payment_status', 'en_attente')
                ->whereIn('payment_method', ['momo_direct', 'momo_revendeur'])
                ->whereNotNull('momo_transaction_id');
        }

        $orders = $query->take(100)->get();

        return view('admin.orders.index', compact('orders', 'filter'));
    }

    /** Confirme que l'argent a bien été reçu (paiement MoMo manuel, ou paiement à la livraison). */
    public function markPaid(Order $order)
    {
        if (! $order->isPaid()) {
            $order->update(['payment_status' => 'paye', 'paid_at' => now()]);
        }

        return back()->with('success', "Paiement de la commande {$order->reference} confirmé.");
    }
}
