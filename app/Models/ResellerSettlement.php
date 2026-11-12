<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResellerSettlement extends Model
{
    protected $fillable = ['reseller_id', 'amount', 'momo_reference', 'note', 'paid_at'];

    protected function casts(): array
    {
        return ['paid_at' => 'datetime'];
    }

    public function reseller(): BelongsTo
    {
        return $this->belongsTo(Reseller::class);
    }
}
