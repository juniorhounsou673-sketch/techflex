<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->string('author_name');
            $table->string('author_city')->nullable();
            $table->unsignedTinyInteger('rating')->default(5);          // 1 à 5 étoiles
            $table->text('body');
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('image')->nullable();                        // capture du message (facultatif)
            $table->date('reviewed_at')->nullable();
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
