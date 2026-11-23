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
        $this->seedAdmin();

        $categories = [
            'accessoires-apple' => Category::firstOrCreate(['slug' => 'accessoires-apple'], ['name' => 'Accessoires Apple']),
            'montres-de-luxe' => Category::firstOrCreate(['slug' => 'montres-de-luxe'], ['name' => 'Montres de luxe']),
        ];

        // Catalogue d'origine TECHFLEX : noms, prix, descriptions et médias conservés tels quels.
        // firstOrCreate évite d'écraser les changements faits depuis l'administration.
        $catalogue = array (
  0 => 
  array (
    'name' => 'AirPods Pro 3',
    'category' => 'accessoires-apple',
    'brand' => 'Apple',
    'description' => 'AirPods Pro 3 : l’écoute la plus silencieuse d’Apple. Jusqu’à 2 fois plus de réduction de bruit active que les AirPods Pro 2, pour s’isoler dans les transports, au travail ou en voyage. Jusqu’à 8 heures d’écoute avec la réduction de bruit activée sur une seule charge, et jusqu’à 24 heures avec le boîtier de charge USB-C compatible MagSafe. Un capteur de fréquence cardiaque intégré pour suivre vos entraînements avec l’app Fitness de l’iPhone, et une résistance à la poussière, à la sueur et à l’eau (IP57) pour le sport comme pour la pluie. Connexion instantanée avec votre iPhone. Disponible chez TECHFLEX, avec livraison au jour et à l’heure de votre choix.',
    'stock' => 10,
    'prix_client' => 25000,
    'gain_revendeur' => 2999,
    'media' => 
    array (
      0 => 
      array (
        'type' => 'image',
        'path' => 'products/1/EzrBXB2GS1xLFcmmFeTVg83aFG12lEUKff3K6t9d.jpg',
        'position' => 1,
      ),
      1 => 
      array (
        'type' => 'image',
        'path' => 'products/1/XAT4pMUU9Ku5T6SRMnqk2qoY1283lVNUJ3IRmckr.jpg',
        'position' => 2,
      ),
      2 => 
      array (
        'type' => 'image',
        'path' => 'products/1/DrPcsFPwvU0mH946dilVJ4TysibeOvGXUYjdfIiX.jpg',
        'position' => 3,
      ),
      3 => 
      array (
        'type' => 'image',
        'path' => 'products/1/6etjhd7Bxkc3Uvjakzp92FpEvT2cCNL3gJuHBXR6.jpg',
        'position' => 4,
      ),
      4 => 
      array (
        'type' => 'image',
        'path' => 'products/1/nXH3QKBGsrlSHyeoJo1Hgl7SQA8myPTGoeh2Njgi.jpg',
        'position' => 5,
      ),
      5 => 
      array (
        'type' => 'video',
        'path' => 'products/1/KkbEYJHrNrzTzK4xn7R7nVhUBHeUksiOGshzOlLM.mp4',
        'position' => 6,
      ),
    ),
  ),
  1 => 
  array (
    'name' => 'CHARGEUR SAMSUNG',
    'category' => 'accessoires-apple',
    'brand' => 'Samsung',
    'description' => '⚡ Chargeur ultra-rapide 45W USB-C + câble
📱 Samsung Galaxy et appareils USB-C
🚚 Livraison au jour et à l’heure de votre choix
💳 Paiement MoMo ou à la livraison
TECHFLEX',
    'stock' => 20,
    'prix_client' => 8000,
    'gain_revendeur' => 1500,
    'media' => 
    array (
      0 => 
      array (
        'type' => 'image',
        'path' => 'products/2/PQwHVsnz3zt5okYynypEqWdP1wDpfNVpqFVmQvH6.jpg',
        'position' => 1,
      ),
      1 => 
      array (
        'type' => 'image',
        'path' => 'products/2/NXFEIgBXJOJl5kZdiQzeLEju03zdzLqjSJiYPJhX.jpg',
        'position' => 2,
      ),
      2 => 
      array (
        'type' => 'image',
        'path' => 'products/2/KftAOmGx1ZLNYfdqbRApaUKPCxiLEtvFR04jixUv.jpg',
        'position' => 3,
      ),
      3 => 
      array (
        'type' => 'image',
        'path' => 'products/2/trOnalq2EifUBe9exmviDsxyQ4vo8vn9RUbcMfNE.jpg',
        'position' => 4,
      ),
      4 => 
      array (
        'type' => 'image',
        'path' => 'products/2/P0NLCz83RI2BdG6TcmU4Hi9mk5A7sUOxH4uLLTjD.jpg',
        'position' => 5,
      ),
      5 => 
      array (
        'type' => 'image',
        'path' => 'products/2/P1L84Vu7wSDCXZprf1sDrc2AmbicJpVDhjUeziRT.jpg',
        'position' => 6,
      ),
      6 => 
      array (
        'type' => 'image',
        'path' => 'products/2/srh4KTr6cUCXV2a73v2xBz0WzAQBFu5MS9k4KUpD.jpg',
        'position' => 7,
      ),
      7 => 
      array (
        'type' => 'image',
        'path' => 'products/2/ziwNSX2YRjyDlxYhIi1NVx1Dq7ye9r7JWJd1sty0.jpg',
        'position' => 8,
      ),
    ),
  ),
  2 => 
  array (
    'name' => 'Rolex Datejust',
    'category' => 'montres-de-luxe',
    'brand' => 'Rolex',
    'description' => 'Montre de luxe automatique',
    'stock' => 2,
    'prix_client' => 4500000,
    'gain_revendeur' => 450000,
    'media' => 
    array (
    ),
  ),
  3 => 
  array (
    'name' => 'CASIO',
    'category' => 'montres-de-luxe',
    'brand' => 'Casio',
    'description' => 'Montre CASIO classique — un style pour chaque occasion

Avec son cadran épuré, son affichage à trois aiguilles et son guichet de date, cette montre CASIO allie élégance et praticité. Son design intemporel se décline en plusieurs coloris pour s’accorder facilement à votre style, au quotidien comme pour les occasions. Le cadran indique WR 50M.',
    'stock' => 20,
    'prix_client' => 13000,
    'gain_revendeur' => 2000,
    'media' => 
    array (
      0 => 
      array (
        'type' => 'image',
        'path' => 'products/6/qfK3AATBQDv6VJ9fOpZyN4nZwWUkstdlBrMvOZz7.jpg',
        'position' => 1,
      ),
      1 => 
      array (
        'type' => 'image',
        'path' => 'products/6/RMIJS0hsRld2ttLyKh3CGOKefaoWMWdLj2lrq7uS.jpg',
        'position' => 2,
      ),
      2 => 
      array (
        'type' => 'image',
        'path' => 'products/6/Dn55ODk0No8pz16bncL7yMxP4ixvYl9hu9AGgklS.jpg',
        'position' => 3,
      ),
      3 => 
      array (
        'type' => 'image',
        'path' => 'products/6/uds640M4Z9XfgXkavVxOfk5AASWASuGTLNwqJwns.jpg',
        'position' => 4,
      ),
      4 => 
      array (
        'type' => 'image',
        'path' => 'products/6/zQxUWDnZNR94kI2cGhoRcWpUgmtOzj8g2qLT4ixa.jpg',
        'position' => 5,
      ),
      5 => 
      array (
        'type' => 'image',
        'path' => 'products/6/1xNBHHWQLdD6Hhmam1ZPCET4yR0htdkPN5Wna5MV.jpg',
        'position' => 6,
      ),
      6 => 
      array (
        'type' => 'image',
        'path' => 'products/6/55XblPlsjawclUXg400bmWNXektwDY8umhh0W8aJ.jpg',
        'position' => 7,
      ),
    ),
  ),
  4 => 
  array (
    'name' => 'CASIO+BRACELET',
    'category' => 'montres-de-luxe',
    'brand' => 'CASIO',
    'description' => 'Coffret CASIO montre et bracelet assorti

Offrez un joli cadeau avec ce coffret CASIO comprenant une montre à cadran rose et un bracelet assorti. Son bracelet métallique et son cadran coloré apportent une touche originale à un style classique. Présenté dans son écrin CASIO, le coffret est prêt à offrir. Disponible en plusieurs versions.',
    'stock' => 20,
    'prix_client' => 18000,
    'gain_revendeur' => 3000,
    'media' => 
    array (
      0 => 
      array (
        'type' => 'image',
        'path' => 'products/7/nCoXFDeoC5Qv6MKga4tCh81PIUUE8RPVbHPbkN24.jpg',
        'position' => 1,
      ),
      1 => 
      array (
        'type' => 'image',
        'path' => 'products/7/82GQ2N9atiHN8JMs8o73CUqV6nfsSHB1eRq8pcAT.jpg',
        'position' => 2,
      ),
    ),
  ),
  5 => 
  array (
    'name' => 'CHARGEUR IPHONE (6 a 14 PRO MAX)',
    'category' => 'accessoires-apple',
    'brand' => 'Apple',
    'description' => 'chargeur authetique chappp',
    'stock' => 20,
    'prix_client' => 5000,
    'gain_revendeur' => 1000,
    'media' => 
    array (
      0 => 
      array (
        'type' => 'image',
        'path' => 'products/8/WIREkSgPasnGmwM6kMn9Q74s8BNqbkHQgQeSRrRN.jpg',
        'position' => 1,
      ),
      1 => 
      array (
        'type' => 'image',
        'path' => 'products/8/V8vJckhh4qQsGgunt2pIY6VQ7cZpnW2gNtDXQnQq.jpg',
        'position' => 2,
      ),
      2 => 
      array (
        'type' => 'image',
        'path' => 'products/8/sifrJ0Q2lxDjMhNb0M9ijEko3t4Kve6vLs48MKlU.jpg',
        'position' => 3,
      ),
      3 => 
      array (
        'type' => 'image',
        'path' => 'products/8/6iOaRLm9PPaRkzGTaCvoqkUmZy9J0eGElPU4ZLsl.jpg',
        'position' => 4,
      ),
      4 => 
      array (
        'type' => 'image',
        'path' => 'products/8/LIbjFzUfCpHypPZkOv3agVfkbRcJ6uNWVhG6Udzq.jpg',
        'position' => 5,
      ),
      5 => 
      array (
        'type' => 'image',
        'path' => 'products/8/W2tpxk8VKH2vm0rMwoJjw3u9Rbrkvrb5TQV3qRwl.jpg',
        'position' => 6,
      ),
      6 => 
      array (
        'type' => 'image',
        'path' => 'products/8/VSPecI30JvAGpo8jqU465mQuH9Eel9aHoRqtVf9M.jpg',
        'position' => 7,
      ),
    ),
  ),
  6 => 
  array (
    'name' => 'POWERBANK',
    'category' => 'accessoires-apple',
    'brand' => 'ORAiMO',
    'description' => '🔋 Powerbank Oraimo 
📱 Rechargez votre téléphone partout, même sans courant
🚚 Livraison au jour et à l’heure de votre choix
💳 Paiement MoMo ou à la livraison
TECHFLEX',
    'stock' => 10,
    'prix_client' => 17000,
    'gain_revendeur' => 2500,
    'media' => 
    array (
      0 => 
      array (
        'type' => 'video',
        'path' => 'products/9/c3ly9fT7zYMe7BTFIcOHLR3gUHMLv3mSKtxfw6NI.mp4',
        'position' => 1,
      ),
      1 => 
      array (
        'type' => 'video',
        'path' => 'products/9/ACWeCmCnt1ykZB0SkV4JUOQJ7gMOE0jCydzORSOX.mp4',
        'position' => 2,
      ),
      2 => 
      array (
        'type' => 'image',
        'path' => 'products/9/F9rXBwBJOXDbXsJOOZ3pbhFgOvhnlmcVXsoTOhiJ.jpg',
        'position' => 3,
      ),
      3 => 
      array (
        'type' => 'image',
        'path' => 'products/9/0wSIhPeAHPGN9sg40KyshZns1S44HeSdm9Ucflxt.jpg',
        'position' => 4,
      ),
      4 => 
      array (
        'type' => 'image',
        'path' => 'products/9/qc5CXHVqWrnB1CPf4HNTeO4uvO5YSytrPewDZOgt.jpg',
        'position' => 5,
      ),
      5 => 
      array (
        'type' => 'image',
        'path' => 'products/9/gouMCu2LfdjppdblOUWgA82Lhzq0wBLHg0S8u4v2.jpg',
        'position' => 6,
      ),
    ),
  ),
  7 => 
  array (
    'name' => 'MAGSAFE',
    'category' => 'accessoires-apple',
    'brand' => 'Apple',
    'description' => '🧲 Powerbank magnétique sans fil 
📱 Se fixe au dos de l’iPhone 12 et plus récents
🚚 Livraison au jour et à l’heure de votre choix
💳 Paiement MoMo ou à la livraison
TECHFLEX',
    'stock' => 10,
    'prix_client' => 15000,
    'gain_revendeur' => 3000,
    'media' => 
    array (
      0 => 
      array (
        'type' => 'image',
        'path' => 'products/10/gHl6meACVjzGxF9OztwFLjc1FAx2NOiY6DqQrnjl.jpg',
        'position' => 1,
      ),
      1 => 
      array (
        'type' => 'image',
        'path' => 'products/10/A3Qu68p4R9yNrcGdI41vBgPnxy1L1axFcd3VuI9i.jpg',
        'position' => 2,
      ),
      2 => 
      array (
        'type' => 'image',
        'path' => 'products/10/P54YKPAb4YlXztvHvvQ5SjiP3HwC7WVJM1mFDEKc.jpg',
        'position' => 3,
      ),
      3 => 
      array (
        'type' => 'image',
        'path' => 'products/10/eM9C9y3T1nW63XA9scoVlUTpGWlYal2tecuijUJ8.jpg',
        'position' => 4,
      ),
      4 => 
      array (
        'type' => 'image',
        'path' => 'products/10/OjiUfFva1ALp2TcgCjJ6boAwoAHJs4nhUzXjz6FO.jpg',
        'position' => 5,
      ),
      5 => 
      array (
        'type' => 'image',
        'path' => 'products/10/TixXyEsd0uwFGJOej4q1VZLN07Laxck4wKdRl1af.jpg',
        'position' => 6,
      ),
      6 => 
      array (
        'type' => 'image',
        'path' => 'products/10/PoBm108KLeMZhQLUUpjmYPveXu5U2POxJ7fMlfeO.jpg',
        'position' => 7,
      ),
      7 => 
      array (
        'type' => 'image',
        'path' => 'products/10/evzY1IbVbEICq4dZT511hwydBPJJVLI2FrY5arxw.jpg',
        'position' => 8,
      ),
    ),
  ),
  8 => 
  array (
    'name' => 'AIRPOD MAX',
    'category' => 'accessoires-apple',
    'brand' => 'Apple',
    'description' => 'AirPods Max : le casque audio haut de gamme d’Apple. Une réduction de bruit active de niveau pro pour vous isoler du monde, et un mode Transparence pour entendre ce qui vous entoure sans retirer le casque. Jusqu’à 20 heures d’écoute avec la réduction de bruit activée, et 5 minutes de charge suffisent pour environ 1 h 30 d’écoute. Audio spatial personnalisé, avec suivi des mouvements de la tête, pour un son qui vous enveloppe en musique comme en film. Un arceau en maille respirante et des coussinets en mousse à mémoire de forme pour porter le casque longtemps sans fatigue. Recharge par USB-C, avec le même câble que votre iPhone. Livré avec son étui Smart Case et son câble USB-C. Disponible chez TECHFLEX, en coloris [couleur], avec livraison au jour et à l’heure de votre choix.',
    'stock' => 10,
    'prix_client' => 50000,
    'gain_revendeur' => 8000,
    'media' => 
    array (
      0 => 
      array (
        'type' => 'image',
        'path' => 'products/11/yT2S8Y2uxMEbFjmkZS4QvxuaSBBM5XjWjf98CgwS.jpg',
        'position' => 1,
      ),
      1 => 
      array (
        'type' => 'image',
        'path' => 'products/11/qeBJpZi3nFO2oerle7Pt28H90PzmAZFiaQ22COWf.jpg',
        'position' => 2,
      ),
      2 => 
      array (
        'type' => 'image',
        'path' => 'products/11/OG0lgCZJLEy29MowEETfVjsMVUyAvaWDiKcKrzas.jpg',
        'position' => 3,
      ),
      3 => 
      array (
        'type' => 'image',
        'path' => 'products/11/ylsUnYyFsUDoEEhnlfFbDGLXZdd8qzmx6uWLXGb1.jpg',
        'position' => 4,
      ),
      4 => 
      array (
        'type' => 'image',
        'path' => 'products/11/QxFxf9ZyKfmjrEFqv0M1loTFs0ymGQhNd2DNvf3M.jpg',
        'position' => 5,
      ),
      5 => 
      array (
        'type' => 'image',
        'path' => 'products/11/9PXURytT645WolBXRei8lMpTtYRQt1cCJkYcCsSe.jpg',
        'position' => 6,
      ),
      6 => 
      array (
        'type' => 'image',
        'path' => 'products/11/Rn5NXBoCqYHlTfxmmwa9QUmcV2za0ypJsrDUSWFW.jpg',
        'position' => 7,
      ),
    ),
  ),
  9 => 
  array (
    'name' => 'AIRPO 4',
    'category' => 'accessoires-apple',
    'brand' => 'Apple',
    'description' => 'AirPods 4 : des écouteurs sans fil légers, au son riche et naturel. Puce Apple H2, audio spatial personnalisé avec suivi des mouvements de la tête pour un son qui vous enveloppe. Isolation de la voix pour des appels clairs, même dans le bruit. Jusqu’à 5 heures d’écoute sur une seule charge, et jusqu’à 30 heures avec le boîtier de charge USB-C. 5 minutes dans le boîtier donnent environ 1 heure d’écoute. Résistants à la poussière, à la sueur et à l’eau (IP54). Disponible chez TECHFLEX, avec livraison au jour et à l’heure de votre choix.',
    'stock' => 5,
    'prix_client' => 15000,
    'gain_revendeur' => 2498,
    'media' => 
    array (
      0 => 
      array (
        'type' => 'image',
        'path' => 'products/12/v3IOvcEYae3YBSAFWan24GODnOMeF2vYnt40iJcI.jpg',
        'position' => 1,
      ),
      1 => 
      array (
        'type' => 'image',
        'path' => 'products/12/aK09zRpbRiejm83hKwBAHfsT1csuVz4GwtY8tROm.jpg',
        'position' => 2,
      ),
      2 => 
      array (
        'type' => 'image',
        'path' => 'products/12/X6EHuRThIjkxM95Ps9OZzuB42RPAWCSBdtP3BQ8n.jpg',
        'position' => 3,
      ),
      3 => 
      array (
        'type' => 'image',
        'path' => 'products/12/p7edopj5NB8j23HfT4716wt62RpejJiRXLBCDmfG.jpg',
        'position' => 4,
      ),
    ),
  ),
  10 => 
  array (
    'name' => 'AIRPOD PRO 2',
    'category' => 'accessoires-apple',
    'brand' => 'Apple',
    'description' => NULL,
    'stock' => 20,
    'prix_client' => 10000,
    'gain_revendeur' => 1497,
    'media' => 
    array (
      0 => 
      array (
        'type' => 'image',
        'path' => 'products/13/4is6X6moUx5FFjNJTduPTdXkTL1lrYRH6lbGHJ3D.jpg',
        'position' => 1,
      ),
      1 => 
      array (
        'type' => 'image',
        'path' => 'products/13/laslthZQCu3aGV7Xjn116d45hwFAmZfHzQZSt3w5.jpg',
        'position' => 2,
      ),
      2 => 
      array (
        'type' => 'image',
        'path' => 'products/13/GOkEEpdsynEVlWFjeyOKzglYqbM8MXXaRtrEJll1.jpg',
        'position' => 3,
      ),
      3 => 
      array (
        'type' => 'image',
        'path' => 'products/13/LDsLH1bOp5rIcVriRphoWdvvHK95tqCRzbXCyrAr.jpg',
        'position' => 4,
      ),
      4 => 
      array (
        'type' => 'image',
        'path' => 'products/13/qmyktFAVOLQUzLvnYmuNGfTNrRzNmwBDMDXSFieN.jpg',
        'position' => 5,
      ),
      5 => 
      array (
        'type' => 'image',
        'path' => 'products/13/YV5rBufD2IDvrxQqEY7LMnWHxvzWGrO59yfYX9tH.jpg',
        'position' => 6,
      ),
      6 => 
      array (
        'type' => 'video',
        'path' => 'products/13/IKtH9lj4Hthg90r4hhX75ul8k5Vbt8mUhPrzf3yG.mp4',
        'position' => 7,
      ),
      7 => 
      array (
        'type' => 'video',
        'path' => 'products/13/v4EXynrLoyZ2y0wOjFAsejCUyqHfHOeNo9V7DyHj.mp4',
        'position' => 8,
      ),
    ),
  ),
  11 => 
  array (
    'name' => 'GALAXY BUDS 3 PRO',
    'category' => 'accessoires-apple',
    'brand' => 'Samsung',
    'description' => NULL,
    'stock' => 10,
    'prix_client' => 16000,
    'gain_revendeur' => 2971,
    'media' => 
    array (
    ),
  ),
);

        foreach ($catalogue as $item) {
            $product = Product::firstOrCreate(['name' => $item['name']], [
                'category_id' => $categories[$item['category']]->id,
                'brand' => $item['brand'],
                'description' => $item['description'],
                'stock' => $item['stock'],
                'prix_client' => $item['prix_client'],
                'gain_revendeur' => $item['gain_revendeur'],
                'is_active' => true,
            ]);

            foreach ($item['media'] as $media) {
                ProductMedia::firstOrCreate(
                    ['product_id' => $product->id, 'path' => $media['path']],
                    ['type' => $media['type'], 'position' => (int) $media['position']]
                );
            }
        }

        // Les revendeurs de test n'existent jamais sur le site en ligne.
        if (! $this->seedDemoResellers()) {
            return;
        }

        foreach ([
            ['Revendeur Un',   'revendeur1@techflex.test', '22911111111', 'REV001'],
            ['Revendeur Deux', 'revendeur2@techflex.test', '22922222222', 'REV002'],
        ] as [$name, $email, $momo, $code]) {
            $user = User::updateOrCreate(['email' => $email], [
                'name' => $name,
                'phone' => $momo,
                'password' => Hash::make('password'),
                'password_set_at' => now(),
                'role' => 'revendeur',
                'status' => 'actif',
            ]);

            Reseller::updateOrCreate(['user_id' => $user->id], [
                'code' => $code,
                'momo_number' => $momo,
            ]);
        }
    }

    private function seedAdmin(): void
    {
        if (! app()->environment('production')) {
            User::updateOrCreate(['email' => 'admin@techflex.test'], [
                'name' => 'TECHFLEX Admin',
                'phone' => '22900000000',
                'password' => Hash::make('password'),
                'password_set_at' => now(),
                'role' => 'admin',
                'status' => 'actif',
            ]);

            return;
        }

        $email = env('TECHFLEX_ADMIN_EMAIL');
        $password = env('TECHFLEX_ADMIN_PASSWORD');

        if (! $email || ! $password) {
            // Configuration transitoire : en attendant les secrets Render, on garde
            // le compte de démonstration pour ne pas bloquer le déploiement.
            $email = 'admin@techflex.test';
            $password = 'password';
        }

        $user = User::firstOrNew(['email' => $email]);
        $user->name = 'TECHFLEX Admin';
        $user->phone = env('TECHFLEX_DELIVERY_PHONE', '22900000000');
        $user->role = 'admin';
        $user->status = 'actif';
        $user->password_set_at = $user->password_set_at ?: now();

        if (! $user->exists || ! $user->password || ! Hash::check($password, $user->password)) {
            $user->password = Hash::make($password);
        }

        $user->save();
    }

    private function seedDemoResellers(): bool
    {
        if (! app()->environment('production')) {
            return true;
        }

        return filter_var(env('TECHFLEX_SEED_DEMO', false), FILTER_VALIDATE_BOOLEAN);
    }
}