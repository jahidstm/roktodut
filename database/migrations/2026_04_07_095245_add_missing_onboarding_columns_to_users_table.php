<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // লোকেশন ডেটা
            if (!Schema::hasColumn('users', 'division_id')) {
                $table->unsignedBigInteger('division_id')->nullable();
            }
            if (!Schema::hasColumn('users', 'district_id')) {
                $table->unsignedBigInteger('district_id')->nullable();
            }
            if (!Schema::hasColumn('users', 'upazila_id')) {
                $table->unsignedBigInteger('upazila_id')->nullable();
            }

            // ডোনার ডেটা
            if (!Schema::hasColumn('users', 'gender')) {
                $table->string('gender')->nullable();
            }
            if (!Schema::hasColumn('users', 'weight')) {
                $table->integer('weight')->nullable();
            }
            if (!Schema::hasColumn('users', 'last_donated_at')) {
                $table->date('last_donated_at')->nullable();
            }

            // অর্গানাইজেশন ও ভেরিফিকেশন ডেটা
            if (!Schema::hasColumn('users', 'organization_id')) {
                $table->unsignedBigInteger('organization_id')->nullable();
            }
            if (!Schema::hasColumn('users', 'nid_status')) {
                $table->string('nid_status')->default('none');
            }
            if (!Schema::hasColumn('users', 'is_onboarded')) {
                $table->boolean('is_onboarded')->default(false);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'division_id',
                'district_id',
                'upazila_id',
                'gender',
                'weight',
                'last_donated_at',
                'organization_id',
                'nid_status',
                'is_onboarded'
            ]);
        });
    }
};
