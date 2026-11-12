<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->unique()->after('email');
            $table->string('role')->default('client')->after('phone');       // admin | revendeur | client
            $table->string('status')->default('actif')->after('role');       // actif | bloque
            $table->timestamp('blocked_at')->nullable()->after('status');
            $table->string('blocked_reason')->nullable()->after('blocked_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'role', 'status', 'blocked_at', 'blocked_reason']);
        });
    }
};
