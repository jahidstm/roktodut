<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'last_whole_blood_donated_at')) {
                $table->date('last_whole_blood_donated_at')->nullable()->after('last_donated_at');
            }
            if (!Schema::hasColumn('users', 'last_plasma_donated_at')) {
                $table->date('last_plasma_donated_at')->nullable()->after('last_whole_blood_donated_at');
            }
            if (!Schema::hasColumn('users', 'last_platelet_donated_at')) {
                $table->date('last_platelet_donated_at')->nullable()->after('last_plasma_donated_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'last_whole_blood_donated_at',
                'last_plasma_donated_at',
                'last_platelet_donated_at'
            ]);
        });
    }
};
