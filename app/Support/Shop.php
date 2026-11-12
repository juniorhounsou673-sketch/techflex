<?php

namespace App\Support;

use App\Models\Product;
use App\Models\Reseller;
use Illuminate\Database\Eloquent\Builder;

class Shop
{
    /** Revendeur actif de la session (via le lien /r/CODE), sinon null = vente directe TECHFLEX. */
    public static function reseller(): ?Reseller
    {
        $req = request();

        if ($req->attributes->has('shop_reseller')) {
            return $req->attributes->get('shop_reseller');
        }

        $code = session('reseller_code');
        $reseller = $code ? Reseller::active()->where('code', $code)->first() : null;

        // Revendeur bloqué ou inexistant : on oublie son lien
        if ($code && ! $reseller) {
            session()->forget('reseller_code');
        }

        $req->attributes->set('shop_reseller', $reseller);

        return $reseller;
    }

    /** Produits en vente (les mêmes pour tous les clients, avec ou sans lien revendeur). */
    public static function products(): Builder
    {
        return Product::query()->where('is_active', true)->with(['category', 'media']);
    }

    /** Prix payé par le client : le même pour tout le monde. */
    public static function price(Product $product): int
    {
        return (int) $product->prix_client;
    }

    /** Lignes du panier avec prix actuels : [product, qty, price, subtotal] */
    public static function cartLines(array $cart)
    {
        if (empty($cart)) {
            return collect();
        }

        return self::products()->whereIn('id', array_keys($cart))->get()->map(function (Product $p) use ($cart) {
            $price = self::price($p);
            $qty = (int) $cart[$p->id];

            return [
                'product' => $p,
                'qty' => $qty,
                'price' => $price,
                'subtotal' => $price * $qty,
            ];
        });
    }

    public static function money(?int $amount): string
    {
        return number_format((int) $amount, 0, ',', ' ') . ' FCFA';
    }
}
