<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('client_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('client_name');
            $table->string('client_phone');
            $table->foreignId('reseller_id')->nullable()->constrained()->nullOnDelete(); // null = vente directe TECHFLEX

            $table->unsignedBigInteger('total');                  // payé par le client
            $table->unsignedBigInteger('montant_du_techflex');    // part qui revient à TECHFLEX

            $table->string('payment_method');                     // momo_site | momo_revendeur | livraison
            $table->string('payment_status')->default('en_attente'); // en_attente | paye | echoue
            $table->string('momo_transaction_id')->nullable();
            $table->timestamp('paid_at')->nullable();

            $table->string('status')->default('en_attente');      // en_attente | confirmee | en_livraison | livree | annulee

            // Reprise par l'admin quand un revendeur est bloqué
            $table->boolean('taken_over_by_admin')->default(false);
            $table->timestamp('taken_over_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
