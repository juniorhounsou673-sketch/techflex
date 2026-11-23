<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reseller;
use App\Models\ResellerSettlement;
use App\Models\User;
use App\Notifications\ResellerInvitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

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
        ]);

        $reseller = DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                // Mot de passe inconnu de tous : le revendeur choisira le sien via l'email.
                'password' => Str::password(32),
                'role' => 'revendeur',
                'status' => 'actif',
                'password_set_at' => null,
            ]);

            return Reseller::create([
                'user_id' => $user->id,
                'code' => $data['code'] ?: $this->nextCode(),
                'momo_number' => $data['momo_number'],
                'status' => 'actif',
            ]);
        });

        $sent = $this->sendInvitation($reseller->user);
        $message = "Accès créé pour {$reseller->user->name}. ";
        $message .= $sent
            ? "Un email d'invitation lui a été envoyé pour définir son mot de passe. Lien de vente : " . url('/r/' . $reseller->code)
            : "L'email d'invitation n'a pas pu être envoyé. Vérifiez la configuration mail puis cliquez sur « Renvoyer l'invitation ».";

        return redirect()->route('admin.resellers.index')
            ->with($sent ? 'success' : 'error', $message);
    }

    /** Renvoyer l'email d'invitation (lien perdu, email non reçu...). */
    public function resendInvitation(Reseller $reseller)
    {
        $user = $reseller->user;

        if (! $user->isReseller()) {
            abort(404);
        }

        if ($user->password_set_at) {
            return back()->with('error', "{$user->name} a déjà défini son mot de passe.");
        }

        $sent = $this->sendInvitation($user);

        return back()->with(
            $sent ? 'success' : 'error',
            $sent
                ? "Nouvelle invitation envoyée à {$user->email}."
                : "Envoi impossible : vérifiez la configuration mail de TECHFLEX."
        );
    }

    private function sendInvitation(User $user): bool
    {
        $token = Str::random(64);
        $days = (int) config('techflex.invitation_ttl_days', 7);

        $user->forceFill([
            'invitation_token' => hash('sha256', $token),
            'invitation_expires_at' => now()->addDays($days),
            'password_set_at' => null,
        ])->save();

        try {
            $user->notify(new ResellerInvitation(route('reseller.invitation.show', $token), $days));

            return true;
        } catch (Throwable $e) {
            report($e);

            return false;
        }
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
