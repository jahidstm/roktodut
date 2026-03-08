<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('blood_request_responses', function (Blueprint $table) {
            if (!Schema::hasColumn('blood_request_responses', 'blood_request_id')) {
                $table->foreignId('blood_request_id')
                    ->constrained('blood_requests')
                    ->cascadeOnDelete()
                    ->after('id');
            }

            if (!Schema::hasColumn('blood_request_responses', 'donor_user_id')) {
                $table->foreignId('donor_user_id')
                    ->constrained('users')
                    ->cascadeOnDelete()
                    ->after('blood_request_id');
            }

            if (!Schema::hasColumn('blood_request_responses', 'status')) {
                $table->string('status')->default('declined')->after('donor_user_id');
            }

            $table->unique(['blood_request_id', 'donor_user_id'], 'uniq_request_donor');
            $table->index(['donor_user_id', 'status']);
            $table->index(['blood_request_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('blood_request_responses', function (Blueprint $table) {
            $table->dropUnique('uniq_request_donor');
            $table->dropIndex(['donor_user_id', 'status']);
            $table->dropIndex(['blood_request_id', 'status']);

            if (Schema::hasColumn('blood_request_responses', 'status')) $table->dropColumn('status');
            if (Schema::hasColumn('blood_request_responses', 'donor_user_id')) $table->dropConstrainedForeignId('donor_user_id');
            if (Schema::hasColumn('blood_request_responses', 'blood_request_id')) $table->dropConstrainedForeignId('blood_request_id');
        });
    }
};