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
        return asset('storage/' . $this->path);
    }

    public function isVideo(): bool
    {
        return $this->type === 'video';
    }
}
