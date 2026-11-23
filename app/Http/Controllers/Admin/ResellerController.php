<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reseller;
use App\Models\ResellerSettlement;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ResellerController extends Controller
{
    public function index()
    {
        $resellers = Reseller::with('user')->latest('id')->get();

        return view('admin.resellers.index', compact('resellers'));
    }

    public function create()
    {
        return view('admin.resellers.create');
    }

    public function store(Request $request)
    {
        $request->merge([
            'code' => $request->filled('code') ? strtoupper(trim($request->input('code'))) : null,
        ]);

        $data = $request->validate([
            'name' => 'required|string|max:120',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20|unique:users,phone',
            'momo_number' => 'required|string|max:20',
            'code' => 'nullable|alpha_dash|max:20|unique:resellers,code',
            'password' => 'required|string|min:8',
        ]);

        $reseller = DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'password' => $data['password'],   // haché automatiquement (cast "hashed")
                'role' => 'revendeur',
                'status' => 'actif',
            ]);

            return Reseller::create([
                'user_id' => $user->id,
                'code' => $data['code'] ?: $this->nextCode(),
                'momo_number' => $data['momo_number'],
                'status' => 'actif',
            ]);
        });

        return redirect()->route('admin.resellers.index')
            ->with('success', "Accès donné à {$reseller->user->name}. Lien de vente : " . url('/r/' . $reseller->code));
    }

    public function block(Request $request, Reseller $reseller)
    {
        $request->validate(['reason' => 'nullable|string|max:190']);

        $reseller->block($request->input('reason'));

        return back()->with('success', "{$reseller->user->name} est bloqué. Il n'a plus accès à rien.");
    }

    public function unblock(Reseller $reseller)
    {
        $reseller->unblock();

        return back()->with('success', "{$reseller->user->name} est réactivé.");
    }

    /** Compte d'un revendeur : ventes, gains, soldes dans les deux sens, versements. */
    public function show(Reseller $reseller)
    {
        $reseller->load('user');

        $orders = $reseller->orders()
            ->where('payment_status', 'paye')
            ->where('status', '!=', 'annulee')
            ->where('taken_over_by_admin', false)
            ->latest('id')
            ->get();

        // Commandes reprises (revendeur bloqué) pour lesquelles le revendeur avait encaissé l'argent
        $takenOver = $reseller->orders()
            ->where('payment_status', 'paye')
            ->where('taken_over_by_admin', true)
            ->where('payment_method', 'momo_revendeur')
            ->get();

        return view('admin.resellers.show', [
            'reseller' => $reseller,
            'orders' => $orders,
            'takenOver' => $takenOver,
            'settlements' => $reseller->settlements()->latest('paid_at')->latest('id')->get(),
            'gain' => $reseller->totalGain(),
            'owedToTechflex' => $reseller->owedToTechflex(),
            'owedToReseller' => $reseller->owedToReseller(),
            'paidIn' => $reseller->settledToTechflex(),
            'paidOut' => $reseller->settledToReseller(),
            'balance' => $reseller->balance(),
        ]);
    }

    /** Enregistre un versement (dans un sens ou dans l'autre). */
    public function settle(Request $request, Reseller $reseller)
    {
        $data = $request->validate([
            'direction' => 'required|in:reseller_to_techflex,techflex_to_reseller',
            'amount' => 'required|integer|min:1',
            'momo_reference' => 'nullable|string|max:60',
            'note' => 'nullable|string|max:190',
        ]);

        $reseller->settlements()->create($data + ['paid_at' => now()]);

        return back()->with('success', 'Versement enregistré.');
    }

    /** Annule un versement saisi par erreur. */
    public function settlementDestroy(ResellerSettlement $settlement)
    {
        $settlement->delete();

        return back()->with('success', 'Versement supprimé.');
    }

    private function nextCode(): string
    {
        $n = (int) Reseller::max('id') + 1;

        do {
            $code = 'REV' . str_pad((string) $n, 3, '0', STR_PAD_LEFT);
            $n++;
        } while (Reseller::where('code', $code)->exists());

        return $code;
    }
}
