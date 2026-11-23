<?php

namespace App\Models;

use App\Support\Shop;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Reseller extends Model
{
    protected $fillable = ['user_id', 'code', 'momo_number', 'status', 'blocked_at', 'blocked_reason'];

    protected function casts(): array
    {
        return ['blocked_at' => 'datetime'];
    }

    public function user(): BelongsTo        { return $this->belongsTo(User::class); }
    public function prices(): HasMany        { return $this->hasMany(ResellerProduct::class); }
    public function orders(): HasMany        { return $this->hasMany(Order::class); }
    public function settlements(): HasMany   { return $this->hasMany(ResellerSettlement::class); }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'actif');
    }

    public function isBlocked(): bool
    {
        return $this->status === 'bloque';
    }

    // ------------------------------------------------------------------
    // COMPTE DU REVENDEUR
    //
    // Pour chaque commande payée (ni annulée, ni reprise par l'admin) :
    //  - le revendeur a encaissé (MoMo vers le vendeur) : il doit à TECHFLEX la part TECHFLEX
    //  - TECHFLEX a encaissé (livraison, MoMo...)       : TECHFLEX doit au revendeur son gain
    //
    // Solde > 0 : le revendeur doit de l'argent à TECHFLEX
    // Solde < 0 : TECHFLEX doit de l'argent au revendeur
    // ------------------------------------------------------------------

    private function paidOrders()
    {
        return $this->orders()
            ->where('payment_status', 'paye')
            ->where('status', '!=', 'annulee')
            ->where('taken_over_by_admin', false);
    }

    /** Ventes payées par les clients (total). */
    public function totalSales(): int
    {
        return (int) $this->paidOrders()->sum('total');
    }

    /** Gain total du revendeur (prix client - prix revendeur) sur ses ventes payées. */
    public function totalGain(): int
    {
        return (int) $this->paidOrders()
            ->selectRaw('COALESCE(SUM(total - montant_du_techflex), 0) as s')
            ->value('s');
    }

    /** Part TECHFLEX sur l'argent encaissé par le revendeur. */
    public function owedToTechflex(): int
    {
        return (int) $this->paidOrders()
            ->where('payment_method', 'momo_revendeur')
            ->sum('montant_du_techflex');
    }

    /** Gain du revendeur sur l'argent encaissé par TECHFLEX. */
    public function owedToReseller(): int
    {
        return (int) $this->paidOrders()
            ->where('payment_method', '!=', 'momo_revendeur')
            ->selectRaw('COALESCE(SUM(total - montant_du_techflex), 0) as s')
            ->value('s');
    }

    /** Net issu des commandes : positif = le revendeur doit, négatif = TECHFLEX doit. */
    public function totalDue(): int
    {
        return $this->owedToTechflex() - $this->owedToReseller();
    }

    /** Déjà versé par le revendeur à TECHFLEX. */
    public function settledToTechflex(): int
    {
        return (int) $this->settlements()->where('direction', 'reseller_to_techflex')->sum('amount');
    }

    /** Déjà versé par TECHFLEX au revendeur. */
    public function settledToReseller(): int
    {
        return (int) $this->settlements()->where('direction', 'techflex_to_reseller')->sum('amount');
    }

    public function totalSettled(): int
    {
        return $this->settledToTechflex() - $this->settledToReseller();
    }

    /** Solde final : positif = le revendeur doit à TECHFLEX ; négatif = TECHFLEX doit au revendeur. */
    public function balance(): int
    {
        return $this->totalDue() - $this->totalSettled();
    }

    public function balanceText(): string
    {
        $b = $this->balance();

        if ($b > 0) {
            return 'Doit ' . Shop::money($b) . ' à TECHFLEX';
        }

        if ($b < 0) {
            return 'TECHFLEX lui doit ' . Shop::money(-$b);
        }

        return 'Compte à jour';
    }

    /**
     * Bloque le revendeur :
     *  - statut bloqué (revendeur + compte utilisateur)
     *  - déconnexion immédiate (sessions supprimées)
     *  - commandes payées mais non livrées reprises par l'admin
     * L'historique et le solde sont conservés.
     */
    public function block(?string $reason = null): void
    {
        DB::transaction(function () use ($reason) {
            $now = now();

            $this->update(['status' => 'bloque', 'blocked_at' => $now, 'blocked_reason' => $reason]);
            $this->user->update(['status' => 'bloque', 'blocked_at' => $now, 'blocked_reason' => $reason]);

            // Déconnexion immédiate (SESSION_DRIVER=database)
            DB::table('sessions')->where('user_id', $this->user_id)->delete();

            // Reprise des commandes payées mais pas encore livrées
            $this->orders()
                ->where('payment_status', 'paye')
                ->whereNotIn('status', ['livree', 'annulee'])
                ->update(['taken_over_by_admin' => true, 'taken_over_at' => $now]);
        });
    }

    public function unblock(): void
    {
        DB::transaction(function () {
            $this->update(['status' => 'actif', 'blocked_at' => null, 'blocked_reason' => null]);
            $this->user->update(['status' => 'actif', 'blocked_at' => null, 'blocked_reason' => null]);
        });
    }
}
