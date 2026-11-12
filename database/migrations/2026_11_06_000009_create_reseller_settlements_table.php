<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Chaque ligne = un reversement du revendeur vers TECHFLEX
        Schema::create('reseller_settlements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reseller_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('amount');
            $table->string('momo_reference')->nullable();
            $table->string('note')->nullable();
            $table->timestamp('paid_at')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reseller_settlements');
    }
};
