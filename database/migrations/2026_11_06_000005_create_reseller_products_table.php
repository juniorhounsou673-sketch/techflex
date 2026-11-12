<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reseller_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reseller_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('prix_vente');       // prix payé par le client chez ce revendeur
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['reseller_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reseller_products');
    }
};
