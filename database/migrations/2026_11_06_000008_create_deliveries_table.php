<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->unique()->constrained()->cascadeOnDelete();
            $table->date('delivery_date');                         // jour
            $table->time('delivery_time');                         // heure
            $table->string('address');                             // localisation (texte)
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('contact_phone');
            $table->string('status')->default('planifiee');        // planifiee | en_route | livree | echouee
            $table->timestamp('notified_at')->nullable();          // notification envoyée au numéro unique TECHFLEX
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deliveries');
    }
};
