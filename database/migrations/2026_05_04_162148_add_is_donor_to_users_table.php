<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Architecture Decision:
     * "Donor" and "Recipient" are NOT mutually exclusive identities.
     * A person who donates blood today may need blood tomorrow.
     * Instead of a rigid 'role' enum, we use a flexible is_donor boolean flag.
     * is_donor = true  → appears in search results, can donate
     * is_donor = false → can still make blood requests as a recipient
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_donor')->default(false)->after('role')
                  ->comment('true = user is a blood donor and appears in search. false = recipient only.');
        });

        // Backfill: existing donors get is_donor = true
        DB::table('users')->where('role', 'donor')->update(['is_donor' => true]);
        DB::table('users')->where('role', 'org_admin')->update(['is_donor' => true]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_donor');
        });
    }
};
