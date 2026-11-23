<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductMedia extends Model
{
    protected $table = 'product_media';

    protected $fillable = ['product_id', 'type', 'path', 'position'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function url(): string
    {
        // Les médias d'origine sont dans storage/app/public et servis via public/storage.
        // Une URL externe reste possible si un média a été ajouté avec ce type de chemin.
        if (str_starts_with((string) $this->path, 'http://')
            || str_starts_with((string) $this->path, 'https://')) {
            return $this->path;
        }

        return asset('storage/' . $this->path);
    }

    public function isVideo(): bool
    {
        return $this->type === 'video';
    }
}
