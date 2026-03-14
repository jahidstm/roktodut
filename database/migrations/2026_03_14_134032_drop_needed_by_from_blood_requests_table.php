<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('blood_requests', function (Blueprint $table) {
            if (Schema::hasColumn('blood_requests', 'needed_by')) {
                $table->dropColumn('needed_by');
            }
        });
    }

    public function down(): void
    {
        Schema::table('blood_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('blood_requests', 'needed_by')) {
                $table->dateTime('needed_by')->nullable()->after('urgency');
            }
        });
    }
};