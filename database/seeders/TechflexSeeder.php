<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductMedia;
use App\Models\Reseller;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TechflexSeeder extends Seeder
{
    public function run(): void
    {
        // Admin (vous)
        User::updateOrCreate(['email' => 'admin@techflex.test'], [
            'name' => 'TECHFLEX Admin',
            'phone' => '22900000000',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Catégories
        $apple = Category::updateOrCreate(['slug' => 'accessoires-apple'], ['name' => 'Accessoires Apple']);
        $watches = Category::updateOrCreate(['slug' => 'montres-de-luxe'], ['name' => 'Montres de luxe']);

        // Produits : prix client (FCFA), gain du revendeur = 10 % de départ.
        // Les photos sont des URL distantes : elles restent visibles après chaque
        // redéploiement, contrairement aux fichiers uploadés dans storage/.
        $catalogue = [
            [$apple->id,   'AirPods Pro 2',       'Apple',  'Écouteurs à réduction de bruit active', 5, 130000,   'https://images.unsplash.com/photo-1600294037681-c80b4cb5b434?w=800'],
            [$apple->id,   'Chargeur MagSafe',    'Apple',  'Chargeur sans fil MagSafe',            20, 28000,    'https://images.unsplash.com/photo-1586953208448-b95a79798f07?w=800'],
            [$apple->id,   'Coque iPhone 15 Pro', 'Apple',  'Coque de protection',                  30, 15000,    'https://images.unsplash.com/photo-1601784551446-20c9e07cdbdb?w=800'],
            [$apple->id,   'Apple Pencil 2',      'Apple',  'Stylet pour iPad',                     8, 85000,     'https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?w=800'],
            [$watches->id, 'Rolex Datejust',      'Rolex',  'Montre de luxe automatique',           2, 4500000,   'https://images.unsplash.com/photo-1523170335258-f5ed11844a49?w=800'],
            [$watches->id, 'Hublot Big Bang',     'Hublot', 'Chronographe de luxe',                 2, 3800000,   'https://images.unsplash.com/photo-1547996160-81dfa63595aa?w=800'],
            [$watches->id, 'Omega Seamaster',     'Omega',  'Montre de plongée de luxe',            3, 2600000,   'https://images.unsplash.com/photo-1533139502658-0198f920d8e8?w=800'],
        ];

        foreach ($catalogue as [$cat, $name, $brand, $desc, $stock, $prix, $photo]) {
            $product = Product::updateOrCreate(['name' => $name], [
                'category_id' => $cat,
                'brand' => $brand,
                'description' => $desc,
                'stock' => $stock,
                'prix_client' => $prix,
                'gain_revendeur' => (int) (floor($prix * 0.10 / 100) * 100),
            ]);

            ProductMedia::firstOrCreate(
                ['product_id' => $product->id, 'path' => $photo],
                ['type' => 'image', 'position' => 1]
            );
        }

        // Deux revendeurs de test
        foreach ([
            ['Revendeur Un',   'revendeur1@techflex.test', '22911111111', 'REV001'],
            ['Revendeur Deux', 'revendeur2@techflex.test', '22922222222', 'REV002'],
        ] as [$name, $email, $momo, $code]) {
            $user = User::updateOrCreate(['email' => $email], [
                'name' => $name,
                'phone' => $momo,
                'password' => Hash::make('password'),
                'role' => 'revendeur',
            ]);

            Reseller::updateOrCreate(['user_id' => $user->id], [
                'code' => $code,
                'momo_number' => $momo,
            ]);
        }
    }
}
