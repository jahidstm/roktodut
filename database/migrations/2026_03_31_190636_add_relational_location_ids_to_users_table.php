<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // নতুন রিলেশনাল আইডি কলাম যুক্ত করা হচ্ছে
            $table->foreignId('division_id')->after('role')->nullable()->constrained('divisions')->nullOnDelete();
            $table->foreignId('district_id')->after('division_id')->nullable()->constrained('districts')->nullOnDelete();
            $table->foreignId('upazila_id')->after('district_id')->nullable()->constrained('upazilas')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['division_id']);
            $table->dropForeign(['district_id']);
            $table->dropForeign(['upazila_id']);
            $table->dropColumn(['division_id', 'district_id', 'upazila_id']);
        });
    }
};