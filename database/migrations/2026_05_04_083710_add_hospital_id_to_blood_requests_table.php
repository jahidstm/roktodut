<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('blood_requests', function (Blueprint $table) {
            // পুরনো string কলামটি drop করা
            $table->dropColumn('hospital');

            // নতুন FK দিয়ে প্রতিস্থাপন (nullOnDelete → হসপিটাল মুছলে request null হবে, orphan নয়)
            $table->foreignId('hospital_id')
                ->nullable()
                ->after('bags_needed')
                ->constrained('hospitals')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('blood_requests', function (Blueprint $table) {
            $table->dropForeign(['hospital_id']);
            $table->dropColumn('hospital_id');
            $table->string('hospital')->nullable()->after('bags_needed');
        });
    }
};
