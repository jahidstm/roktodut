<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('blood_requests', function (Blueprint $table) {
            $table->dropForeign(['requested_by']);
            $table->unsignedBigInteger('requested_by')->nullable()->change();
            $table->foreign('requested_by')->references('id')->on('users')->nullOnDelete();

            $table->string('guest_token', 64)->nullable()->after('requested_by');
            $table->string('created_ip_hash', 64)->nullable()->after('guest_token');
            $table->string('contact_number_normalized', 32)->nullable()->after('contact_number');

            $table->index('guest_token', 'blood_requests_guest_token_idx');
            $table->index('created_ip_hash', 'blood_requests_created_ip_hash_idx');
            $table->index('contact_number_normalized', 'blood_requests_contact_number_normalized_idx');
            $table->index(['contact_number_normalized', 'created_at'], 'blood_requests_contact_created_at_idx');
            $table->index(
                ['contact_number_normalized', 'district_id', 'blood_group', 'status'],
                'blood_requests_contact_district_group_status_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::table('blood_requests', function (Blueprint $table) {
            $table->dropIndex('blood_requests_contact_district_group_status_idx');
            $table->dropIndex('blood_requests_contact_created_at_idx');
            $table->dropIndex('blood_requests_contact_number_normalized_idx');
            $table->dropIndex('blood_requests_created_ip_hash_idx');
            $table->dropIndex('blood_requests_guest_token_idx');

            $table->dropColumn(['guest_token', 'created_ip_hash', 'contact_number_normalized']);
        });

        $fallbackRequesterId = DB::table('users')->min('id');

        if ($fallbackRequesterId === null) {
            DB::table('blood_requests')->whereNull('requested_by')->delete();
        } else {
            DB::table('blood_requests')
                ->whereNull('requested_by')
                ->update(['requested_by' => $fallbackRequesterId]);
        }

        Schema::table('blood_requests', function (Blueprint $table) {
            $table->dropForeign(['requested_by']);
            $table->unsignedBigInteger('requested_by')->nullable(false)->change();
            $table->foreign('requested_by')->references('id')->on('users')->cascadeOnDelete();
        });
    }
};
