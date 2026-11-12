<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Support\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CheckoutController extends Controller
{
    public function show()
    {
        $lines = Shop::cartLines(session('cart', []));

        if ($lines->isEmpty()) {
            return redirect()->route('cart')->with('error', 'Votre panier est vide.');
        }

        return view('checkout', [
            'lines' => $lines,
            'total' => $lines->sum('subtotal'),
            'reseller' => Shop::reseller(),
        ]);
    }

    public function store(Request $request)
    {
        $allowed = Shop::reseller()
            ? ['momo_site', 'momo_revendeur', 'livraison']
            : ['momo_site', 'livraison'];

        $data = $request->validate([
            'client_name' => 'required|string|max:120',
            'client_phone' => ['required', 'string', 'regex:/^[0-9+ ]{8,20}$/'],
            'payment_method' => ['required', Rule::in($allowed)],
        ], [
            'client_phone.regex' => 'Numéro de téléphone invalide.',
            'payment_method.in' => 'Mode de paiement non disponible.',
        ]);

        $lines = Shop::cartLines(session('cart', []));

        if ($lines->isEmpty()) {
            return redirect()->route('cart')->with('error', 'Votre panier est vide.');
        }

        try {
            $order = DB::transaction(function () use ($data, $lines) {
                // Vérifie le stock en verrouillant les produits
                foreach ($lines as $l) {
                    $p = Product::whereKey($l['product']->id)->lockForUpdate()->first();
                    if ($p->stock < $l['qty']) {
                        throw new \RuntimeException("Stock insuffisant pour « {$p->name} ».");
                    }
                }

                $reseller = Shop::reseller();

                // Part qui revient à TECHFLEX par unité :
                //  - vente via un revendeur : le prix revendeur (prix client - gain du revendeur)
                //  - vente directe : le prix client complet
                $unitDue = fn ($l) => $reseller ? $l['product']->prixRevendeur() : (int) $l['price'];

                $order = Order::create([
                    'reference' => $this->newReference(),
                    'client_name' => $data['client_name'],
                    'client_phone' => $data['client_phone'],
                    'reseller_id' => $reseller?->id,
                    'total' => $lines->sum('subtotal'),
                    'montant_du_techflex' => $lines->sum(fn ($l) => $unitDue($l) * $l['qty']),
                    'payment_method' => $data['payment_method'],
                    'payment_status' => 'en_attente',
                    'status' => 'en_attente',
                ]);

                foreach ($lines as $l) {
                    $order->items()->create([
                        'product_id' => $l['product']->id,
                        'product_name' => $l['product']->name,
                        'quantity' => $l['qty'],
                        'prix_techflex' => $unitDue($l),   // figé : part de TECHFLEX
                        'prix_vente' => $l['price'],        // figé : prix payé par le client
                    ]);

                    Product::whereKey($l['product']->id)->decrement('stock', $l['qty']);
                }

                return $order;
            });
        } catch (\RuntimeException $e) {
            return redirect()->route('cart')->with('error', $e->getMessage());
        }

        session()->forget('cart');
        session()->push('my_orders', $order->reference);

        // MoMo sur le site : paiement d'abord. Sinon : directement la livraison.
        return $order->payment_method === 'momo_site'
            ? redirect()->route('order.pay', $order->reference)
            : redirect()->route('order.delivery', $order->reference);
    }

    private function newReference(): string
    {
        do {
            $ref = 'TF-' . strtoupper(Str::random(8));
        } while (Order::where('reference', $ref)->exists());

        return $ref;
    }
}
