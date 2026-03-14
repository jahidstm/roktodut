<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Fix: allow registration without district (captured during onboarding later)
            if (Schema::hasColumn('users', 'district')) {
                $table->string('district')->nullable()->change();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'district')) {
                $table->string('district')->nullable(false)->change();
            }
        });
    }
};