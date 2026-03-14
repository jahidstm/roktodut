<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('blood_request_responses', function (Blueprint $table) {
            if (
                Schema::hasColumn('blood_request_responses', 'donor_user_id') &&
                !Schema::hasColumn('blood_request_responses', 'user_id')
            ) {
                $table->renameColumn('donor_user_id', 'user_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('blood_request_responses', function (Blueprint $table) {
            if (
                Schema::hasColumn('blood_request_responses', 'user_id') &&
                !Schema::hasColumn('blood_request_responses', 'donor_user_id')
            ) {
                $table->renameColumn('user_id', 'donor_user_id');
            }
        });
    }
};