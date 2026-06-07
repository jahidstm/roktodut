<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds extended fields to chronic_request_subscriptions:
 *
 *  condition_type  — রোগের ধরন (thalassemia | dialysis | sickle_cell | other)
 *  notes_for_donor — Buddy donor-কে বিশেষ নির্দেশনা
 *  is_paused       — সাময়িক বিরতি (is_active থেকে আলাদা)
 *  paused_until    — নির্দিষ্ট সময় পর্যন্ত pause; NULL = অনির্দিষ্টকাল
 *  status_reason   — ML dataset field: কেন pause/deactivate করা হলো
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chronic_request_subscriptions', function (Blueprint $table) {
            // রোগের ধরন — default thalassemia (সবচেয়ে সাধারণ use case)
            $table->string('condition_type', 30)->default('thalassemia')->after('notes');
            // Buddy-কে প্রয়োজনীয় clinical context
            $table->string('notes_for_donor', 500)->nullable()->after('condition_type');
            // is_paused: সাময়িক বন্ধ (is_active=false মানে স্থায়ী বন্ধ)
            $table->boolean('is_paused')->default(false)->after('is_active');
            // paused_until: এই সময়ের পরে auto-resume হবে
            $table->timestamp('paused_until')->nullable()->after('is_paused');
            // ML dataset: কারণ ট্র্যাক করার জন্য
            $table->string('status_reason', 255)->nullable()->after('paused_until');

            // Scheduler query optimization
            $table->index(['is_active', 'is_paused', 'next_needed_at'], 'crs_dispatch_idx');
        });
    }

    public function down(): void
    {
        Schema::table('chronic_request_subscriptions', function (Blueprint $table) {
            $table->dropIndex('crs_dispatch_idx');
            $table->dropColumn(['condition_type', 'notes_for_donor', 'is_paused', 'paused_until', 'status_reason']);
        });
    }
};

