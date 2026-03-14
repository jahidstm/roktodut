<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('blood_request_responses', function (Blueprint $table) {
            // avoid duplicate responses per user per request
            $table->unique(['blood_request_id', 'user_id'], 'br_responses_request_user_unique');
        });
    }

    public function down(): void
    {
        Schema::table('blood_request_responses', function (Blueprint $table) {
            $table->dropUnique('br_responses_request_user_unique');
        });
    }
};