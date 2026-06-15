<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            // Per-donation unique certificate token (32-char hex)
            $table->string('certificate_token', 64)->nullable()->unique()->after('notes');
            // When the certificate was first generated/issued
            $table->timestamp('certificate_generated_at')->nullable()->after('certificate_token');
        });
    }

    public function down(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->dropColumn(['certificate_token', 'certificate_generated_at']);
        });
    }
};
