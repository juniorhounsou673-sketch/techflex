<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DeliveryController as AdminDeliveryController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\ResellerController as AdminResellerController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Reseller\DashboardController as ResellerDashboardController;
use Illuminate\Support\Facades\Route;

// ---------- Boutique publique ----------
Route::get('/', [CatalogController::class, 'home'])->name('home');
Route::get('/categorie/{slug}', [CatalogController::class, 'category'])->name('category');
Route::get('/produit/{product}', [CatalogController::class, 'product'])->name('product');

// Lien de vente des revendeurs
Route::get('/r/{code}', [CatalogController::class, 'viaReseller'])->name('reseller.entry');
Route::get('/direct', [CatalogController::class, 'direct'])->name('direct');

// Panier
Route::get('/panier', [CartController::class, 'index'])->name('cart');
Route::post('/panier/ajouter/{product}', [CartController::class, 'add'])->name('cart.add');
Route::post('/panier/retirer/{product}', [CartController::class, 'remove'])->name('cart.remove');

// Commande
Route::get('/commander', [CheckoutController::class, 'show'])->name('checkout');
Route::post('/commander', [CheckoutController::class, 'store'])->name('checkout.store');

Route::prefix('commande/{reference}')->name('order.')->group(function () {
    Route::get('/', [OrderController::class, 'show'])->name('show');
    Route::get('/paiement', [OrderController::class, 'payForm'])->name('pay');
    Route::post('/paiement', [OrderController::class, 'pay'])->name('pay.submit');
    Route::post('/reference', [OrderController::class, 'saveReference'])->name('reference');
    Route::get('/livraison', [OrderController::class, 'deliveryForm'])->name('delivery');
    Route::post('/livraison', [OrderController::class, 'deliveryStore'])->name('delivery.store');
});

// ---------- Connexion ----------
Route::middleware('guest')->group(function () {
    Route::get('/connexion', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/connexion', [AuthController::class, 'login'])->middleware('throttle:6,1')->name('login.submit');
});
Route::post('/deconnexion', [AuthController::class, 'logout'])->name('logout');

// ---------- Espace admin (vous) ----------
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Produits
    Route::get('/produits', [AdminProductController::class, 'index'])->name('products.index');
    Route::get('/produits/creer', [AdminProductController::class, 'create'])->name('products.create');
    Route::post('/produits', [AdminProductController::class, 'store'])->name('products.store');
    Route::get('/produits/{product}/modifier', [AdminProductController::class, 'edit'])->name('products.edit');
    Route::post('/produits/{product}', [AdminProductController::class, 'update'])->name('products.update');
    Route::post('/produits/{product}/supprimer', [AdminProductController::class, 'destroy'])->name('products.destroy');
    Route::post('/medias/{media}/supprimer', [AdminProductController::class, 'mediaDestroy'])->name('media.destroy');
    Route::post('/medias/{media}/principale', [AdminProductController::class, 'mediaCover'])->name('media.cover');

    // Revendeurs
    Route::get('/revendeurs', [AdminResellerController::class, 'index'])->name('resellers.index');
    Route::get('/revendeurs/creer', [AdminResellerController::class, 'create'])->name('resellers.create');
    Route::post('/revendeurs', [AdminResellerController::class, 'store'])->name('resellers.store');
    Route::post('/revendeurs/{reseller}/bloquer', [AdminResellerController::class, 'block'])->name('resellers.block');
    Route::post('/revendeurs/{reseller}/debloquer', [AdminResellerController::class, 'unblock'])->name('resellers.unblock');

    // Livraisons
    Route::get('/livraisons', [AdminDeliveryController::class, 'index'])->name('deliveries.index');
    Route::post('/livraisons/{delivery}/statut', [AdminDeliveryController::class, 'status'])->name('deliveries.status');
    Route::post('/commandes/{order}/paye', [AdminOrderController::class, 'markPaid'])->name('orders.paid');
});

// ---------- Espace revendeur ----------
Route::middleware(['auth', 'role:revendeur'])->prefix('revendeur')->name('reseller.')->group(function () {
    Route::get('/', [ResellerDashboardController::class, 'index'])->name('dashboard');
});
