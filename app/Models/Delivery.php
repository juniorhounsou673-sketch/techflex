<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Delivery extends Model
{
    protected $fillable = [
        'order_id', 'delivery_date', 'delivery_time', 'address', 'latitude', 'longitude',
        'contact_phone', 'status', 'notified_at',
    ];

    protected function casts(): array
    {
        return ['delivery_date' => 'date', 'notified_at' => 'datetime'];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function mapsUrl(): ?string
    {
        return $this->latitude ? "https://maps.google.com/?q={$this->latitude},{$this->longitude}" : null;
    }

    /** Lien WhatsApp vers le numéro unique TECHFLEX avec tous les détails de livraison. */
    public function whatsappUrl(): string
    {
        $o = $this->order;
        $articles = $o->items->map(fn ($i) => "- {$i->quantity} x {$i->product_name}")->implode("\n");
        $paiement = $o->payment_status === 'paye'
            ? 'Déjà payé'
            : 'À encaisser : ' . number_format($o->total, 0, ',', ' ') . ' FCFA';

        $text = "Livraison {$o->reference}\n"
            . "Client : {$o->client_name} ({$this->contact_phone})\n"
            . "Articles :\n{$articles}\n"
            . "Paiement : {$paiement}\n"
            . "Jour : {$this->delivery_date->format('d/m/Y')} à " . substr($this->delivery_time, 0, 5) . "\n"
            . "Lieu : {$this->address}"
            . ($this->mapsUrl() ? "\n" . $this->mapsUrl() : '');

        return 'https://wa.me/' . config('techflex.delivery_phone') . '?text=' . rawurlencode($text);
    }
}
