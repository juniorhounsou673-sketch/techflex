<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Reseller;
use App\Models\Review;
use App\Support\Shop;

class CatalogController extends Controller
{
    public function home()
    {
        $sections = Category::orderBy('id')->get()->map(function (Category $cat) {
            $cat->setRelation(
                'products',
                Shop::products()->where('category_id', $cat->id)->orderBy('name')->take(4)->get()
            );

            return $cat;
        });

        $reviews = Review::published()->with('product')->latest('id')->take(6)->get();

        return view('home', compact('sections', 'reviews'));
    }

    public function category(string $slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        $products = Shop::products()->where('category_id', $category->id)->orderBy('name')->get();

        return view('category', compact('category', 'products'));
    }

    public function product(Product $product)
    {
        // Vérifie aussi que le produit est visible pour ce revendeur (sinon 404)
        $product = Shop::products()->findOrFail($product->id);

        $reviews = Review::published()->with('product')->where('product_id', $product->id)->latest('id')->get();

        return view('product', compact('product', 'reviews'));
    }

    /** Lien de vente d'un revendeur : /r/REV001 */
    public function viaReseller(string $code)
    {
        $reseller = Reseller::active()->where('code', $code)->firstOrFail(); // bloqué => 404

        session(['reseller_code' => $reseller->code]);

        return redirect()->route('home');
    }

    /** Revenir à la vente directe TECHFLEX */
    public function direct()
    {
        session()->forget('reseller_code');

        return redirect()->route('home');
    }
}
