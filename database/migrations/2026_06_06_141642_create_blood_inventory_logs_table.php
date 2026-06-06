<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Time-Series Ledger — Append-only historical dataset.
     * প্রতিবার stock update হলে এখানে নতুন row insert হয়।
     * এই ডেটা কখনো delete হয় না — ML forecasting-এর জন্য।
     */
    public function up(): void
    {
        Schema::create('blood_inventory_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->string('blood_group', 5);
            $table->unsignedInteger('units'); // সেই মুহূর্তের stock value
            $table->enum('action', ['snapshot', 'manual_update', 'auto_deduct'])->default('manual_update');
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            // created_at only — immutable log (no updated_at)
            $table->timestamp('created_at')->useCurrent()->index();

            // Compound index for time-series queries:
            // "org X-এর A+ রক্তের গত ৬ মাসের ইতিহাস"
            $table->index(['organization_id', 'blood_group', 'created_at'], 'idx_inv_log_timeseries');
            // Global blood group trend query index
            $table->index(['blood_group', 'created_at'], 'idx_inv_log_group_trend');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blood_inventory_logs');
    }
};
