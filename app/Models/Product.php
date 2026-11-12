<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    public const MAX_MEDIA = 8;

    protected $fillable = [
        'category_id', 'name', 'brand', 'description', 'image', 'stock',
        'prix_client', 'gain_revendeur', 'is_active',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function category(): BelongsTo     { return $this->belongsTo(Category::class); }
    public function resellerPrices(): HasMany { return $this->hasMany(ResellerProduct::class); }

    public function media(): HasMany
    {
        return $this->hasMany(ProductMedia::class)->orderBy('position')->orderBy('id');
    }

    /** Prix affiché au revendeur = prix client - son gain. C'est ce qu'il doit reverser à TECHFLEX. */
    public function prixRevendeur(): int
    {
        return max(0, (int) $this->prix_client - (int) $this->gain_revendeur);
    }

    /** Photo principale : première image de la galerie (sinon ancienne colonne image). */
    public function coverUrl(): ?string
    {
        $img = $this->media->firstWhere('type', 'image');

        if ($img) {
            return $img->url();
        }

        return $this->image ? asset('storage/' . $this->image) : null;
    }
}
