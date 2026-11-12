<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    protected $fillable = [
        'reference', 'client_id', 'client_name', 'client_phone', 'reseller_id',
        'total', 'montant_du_techflex',
        'payment_method', 'payment_status', 'momo_transaction_id', 'paid_at',
        'status', 'taken_over_by_admin', 'taken_over_at',
    ];

    protected function casts(): array
    {
        return [
            'paid_at' => 'datetime',
            'taken_over_at' => 'datetime',
            'taken_over_by_admin' => 'boolean',
        ];
    }

    public function client(): BelongsTo   { return $this->belongsTo(User::class, 'client_id'); }
    public function reseller(): BelongsTo { return $this->belongsTo(Reseller::class); }
    public function items(): HasMany      { return $this->hasMany(OrderItem::class); }
    public function delivery(): HasOne    { return $this->hasOne(Delivery::class); }

    /** Calcule total et montant dû à partir des lignes de commande. */
    public function recalculate(): void
    {
        $this->total = $this->items->sum(fn ($i) => $i->prix_vente * $i->quantity);
        $this->montant_du_techflex = $this->items->sum(fn ($i) => $i->prix_techflex * $i->quantity);
        $this->save();
    }

    public function isPaid(): bool
    {
        return $this->payment_status === 'paye';
    }
}
