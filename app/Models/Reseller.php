<?php

namespace App\Models;

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

    /** Total dû à TECHFLEX sur les ventes payées (hors annulées). */
    public function totalDue(): int
    {
        return (int) $this->orders()
            ->where('payment_status', 'paye')
            ->where('status', '!=', 'annulee')
            ->where('taken_over_by_admin', false)
            ->sum('montant_du_techflex');
    }

    public function totalSettled(): int
    {
        return (int) $this->settlements()->sum('amount');
    }

    /** Solde que le revendeur doit encore reverser à TECHFLEX. */
    public function balance(): int
    {
        return $this->totalDue() - $this->totalSettled();
    }

    /**
     * Bloque le revendeur :
     *  - statut bloqué (revendeur + compte utilisateur)
     *  - déconnexion immédiate (sessions supprimées)
     *  - commandes payées mais non livrées reprises par l'admin
     * L'historique et le solde dû sont conservés.
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
