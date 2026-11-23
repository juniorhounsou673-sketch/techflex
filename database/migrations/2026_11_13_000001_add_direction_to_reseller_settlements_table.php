<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reseller_settlements', function (Blueprint $table) {
            // reseller_to_techflex : le revendeur a versé de l'argent à TECHFLEX
            // techflex_to_reseller : TECHFLEX a versé son gain au revendeur
            $table->string('direction')->default('reseller_to_techflex')->after('reseller_id');
        });
    }

    public function down(): void
    {
        Schema::table('reseller_settlements', function (Blueprint $table) {
            $table->dropColumn('direction');
        });
    }
};
