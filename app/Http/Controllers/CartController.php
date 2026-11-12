<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Support\Shop;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart = session('cart', []);

        $products = Shop::products()->whereIn('id', array_keys($cart))->get();

        $lines = $products->map(function (Product $p) use ($cart) {
            $price = Shop::price($p);
            $qty = (int) $cart[$p->id];

            return [
                'product' => $p,
                'qty' => $qty,
                'price' => $price,
                'subtotal' => $price * $qty,
            ];
        });

        $total = $lines->sum('subtotal');

        return view('cart', compact('lines', 'total'));
    }

    public function add(Request $request, Product $product)
    {
        $request->validate(['quantity' => 'required|integer|min:1|max:10']);

        $product = Shop::products()->findOrFail($product->id);

        if ($product->stock < 1) {
            return back()->with('error', 'Ce produit est en rupture de stock.');
        }

        $cart = session('cart', []);
        $qty = ($cart[$product->id] ?? 0) + (int) $request->quantity;
        $cart[$product->id] = min($qty, $product->stock);

        session(['cart' => $cart]);

        return redirect()->route('cart')->with('success', 'Produit ajouté au panier.');
    }

    public function remove(Product $product)
    {
        $cart = session('cart', []);
        unset($cart[$product->id]);
        session(['cart' => $cart]);

        return redirect()->route('cart');
    }
}
