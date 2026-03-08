<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('blood_requests', function (Blueprint $table) {
            $table->string('patient_name')->nullable()->change();
            $table->string('hospital')->nullable()->change();
            $table->string('contact_name')->nullable()->change();

            if (!Schema::hasColumn('blood_requests', 'division')) {
                $table->string('division')->nullable()->before('district');
            }

            if (!Schema::hasColumn('blood_requests', 'upazila')) {
                $table->string('upazila')->nullable()->after('district');
            }

            if (!Schema::hasColumn('blood_requests', 'needed_at')) {
                $table->dateTime('needed_at')->nullable()->after('needed_by');
                $table->index('needed_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('blood_requests', function (Blueprint $table) {
            if (Schema::hasColumn('blood_requests', 'needed_at')) {
                $table->dropIndex(['needed_at']);
                $table->dropColumn('needed_at');
            }
            if (Schema::hasColumn('blood_requests', 'upazila')) $table->dropColumn('upazila');
            if (Schema::hasColumn('blood_requests', 'division')) $table->dropColumn('division');
        });
    }
};