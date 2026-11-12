<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Delivery;
use Illuminate\Http\Request;

class DeliveryController extends Controller
{
    public function index()
    {
        $deliveries = Delivery::with(['order.items', 'order.reseller.user'])->get()
            ->sortBy(fn (Delivery $d) => [
                in_array($d->status, ['livree', 'echouee'], true) ? 1 : 0,
                $d->delivery_date->format('Y-m-d'),
                $d->delivery_time,
            ])
            ->values();

        return view('admin.deliveries.index', compact('deliveries'));
    }

    public function status(Request $request, Delivery $delivery)
    {
        $data = $request->validate(['status' => 'required|in:planifiee,en_route,livree,echouee']);

        $delivery->update(['status' => $data['status']]);

        if ($data['status'] === 'livree') {
            $delivery->order->update(['status' => 'livree']);
        } elseif ($data['status'] === 'en_route') {
            $delivery->order->update(['status' => 'en_livraison']);
        }

        return back()->with('success', 'Statut de la livraison mis à jour.');
    }
}
