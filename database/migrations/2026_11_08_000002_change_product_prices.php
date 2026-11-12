<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // prix_techflex devient prix_client : le prix que PAIE le client (le même pour tout le monde)
        Schema::table('products', function (Blueprint $table) {
            $table->renameColumn('prix_techflex', 'prix_client');
        });

        // Gain fixe du revendeur sur chaque vente (prix revendeur = prix client - gain)
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedBigInteger('gain_revendeur')->default(0)->after('prix_client');
        });

        // Produits déjà créés : gain de départ = 10 % du prix (à ajuster ensuite dans l'admin)
        DB::table('products')->update([
            'gain_revendeur' => DB::raw('FLOOR(prix_client * 0.10 / 100) * 100'),
        ]);
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('gain_revendeur');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->renameColumn('prix_client', 'prix_techflex');
        });
    }
};
