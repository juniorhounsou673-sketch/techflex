<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResellerProduct extends Model
{
    protected $fillable = ['reseller_id', 'product_id', 'prix_vente', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function reseller(): BelongsTo { return $this->belongsTo(Reseller::class); }
    public function product(): BelongsTo  { return $this->belongsTo(Product::class); }

    /** Marge du revendeur = prix de vente - prix TECHFLEX */
    public function margin(): int
    {
        return $this->prix_vente - $this->product->prix_techflex;
    }

    /** Offres visibles sur le site : prix actif ET revendeur actif (un revendeur bloqué disparaît). */
    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->whereHas('reseller', fn ($q) => $q->where('status', 'actif'));
    }
}
