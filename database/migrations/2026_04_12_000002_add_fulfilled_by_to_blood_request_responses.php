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
        Schema::table('blood_request_responses', function (Blueprint $table) {
            $table->foreignId('fulfilled_by')->nullable()->constrained('users')->nullOnDelete()->after('fulfilled_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blood_request_responses', function (Blueprint $table) {
            $table->dropForeign(['fulfilled_by']);
            $table->dropColumn('fulfilled_by');
        });
    }
};
