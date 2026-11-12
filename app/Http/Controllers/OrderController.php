<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\MomoGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class OrderController extends Controller
{
    /** Le client n'a pas de compte : il ne peut ouvrir que les commandes passées depuis son navigateur. */
    private function mine(string $reference): Order
    {
        abort_unless(in_array($reference, session('my_orders', []), true), 403);

        return Order::with(['items', 'delivery', 'reseller'])->where('reference', $reference)->firstOrFail();
    }

    public function show(string $reference)
    {
        $order = $this->mine($reference);

        if ($order->delivery) {
            $order->delivery->setRelation('order', $order);
        }

        return view('order.show', compact('order'));
    }

    // ---------- Paiement MoMo sur le site ----------
    public function payForm(string $reference)
    {
        $order = $this->mine($reference);

        if ($order->payment_method !== 'momo_site' || $order->isPaid()) {
            return redirect()->route('order.show', $reference);
        }

        return view('order.pay', compact('order'));
    }

    public function pay(Request $request, string $reference, MomoGateway $momo)
    {
        $order = $this->mine($reference);

        if ($order->payment_method !== 'momo_site' || $order->isPaid()) {
            return redirect()->route('order.show', $reference);
        }

        $data = $request->validate([
            'momo_phone' => ['required', 'string', 'regex:/^[0-9+ ]{8,20}$/'],
        ], ['momo_phone.regex' => 'Numéro MoMo invalide.']);

        try {
            $result = $momo->charge($order, $data['momo_phone']);
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        if (! $result['success']) {
            $order->update(['payment_status' => 'echoue']);

            return back()->with('error', 'Le paiement a échoué. Réessayez.');
        }

        $order->update([
            'payment_status' => 'paye',
            'paid_at' => now(),
            'momo_transaction_id' => $result['transaction_id'],
        ]);

        return redirect()->route('order.show', $reference)
            ->with('success', 'Paiement reçu. Vous pouvez maintenant programmer votre livraison.');
    }

    // ---------- Référence de transaction (paiement MoMo vers le compte du revendeur) ----------
    public function saveReference(Request $request, string $reference)
    {
        $order = $this->mine($reference);

        abort_unless($order->payment_method === 'momo_revendeur' && ! $order->isPaid(), 403);

        $data = $request->validate(['momo_transaction_id' => 'required|string|max:60']);

        $order->update(['momo_transaction_id' => $data['momo_transaction_id']]);

        return back()->with('success', 'Référence enregistrée. TECHFLEX confirmera la réception de votre paiement.');
    }

    // ---------- Livraison : jour, heure, localisation ----------
    public function deliveryForm(string $reference)
    {
        $order = $this->mine($reference);

        if ($order->delivery) {
            return redirect()->route('order.show', $reference);
        }

        if ($order->payment_method === 'momo_site' && ! $order->isPaid()) {
            return redirect()->route('order.pay', $reference);
        }

        return view('order.delivery', compact('order'));
    }

    public function deliveryStore(Request $request, string $reference)
    {
        $order = $this->mine($reference);

        if ($order->delivery) {
            return redirect()->route('order.show', $reference);
        }

        abort_if($order->payment_method === 'momo_site' && ! $order->isPaid(), 403);

        $data = $request->validate([
            'delivery_date' => 'required|date_format:Y-m-d',
            'delivery_time' => 'required|date_format:H:i',
            'address' => 'required|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'contact_phone' => ['required', 'string', 'regex:/^[0-9+ ]{8,20}$/'],
        ], ['contact_phone.regex' => 'Numéro de téléphone invalide.']);

        // Heure du Bénin ; au moins 1 h d'avance
        $when = Carbon::createFromFormat('Y-m-d H:i', $data['delivery_date'] . ' ' . $data['delivery_time'], 'Africa/Porto-Novo');

        if ($when->lt(Carbon::now('Africa/Porto-Novo')->addHour())) {
            return back()->withInput()->with('error', 'Choisissez un jour et une heure au moins 1 heure à l\'avance.');
        }

        $order->delivery()->create($data);
        $order->update(['status' => 'confirmee']);

        return redirect()->route('order.show', $reference)
            ->with('success', 'Livraison programmée. Envoyez la demande par WhatsApp pour la confirmer.');
    }
}
