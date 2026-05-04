<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hospitals', function (Blueprint $table) {
            $table->id();

            // ─── প্রাথমিক তথ্য ─────────────────────────────────
            $table->string('name');          // Standard English name: "Dhaka Medical College Hospital"
            $table->string('name_bn')->nullable();  // বাংলা নাম: "ঢাকা মেডিকেল কলেজ হাসপাতাল"
            $table->json('aliases')->nullable();    // ["DMCH", "Dhaka Medical", "D.M.C.H"]

            // ─── লোকেশন (Geo-filtering) ─────────────────────────
            $table->foreignId('district_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('upazila_id')->nullable()->constrained()->nullOnDelete();

            // ─── অ্যাডমিন কন্ট্রোল ──────────────────────────────
            // true  → আমাদের Seeder থেকে বা অ্যাডমিন কর্তৃক verify করা
            // false → ইউজার ম্যানুয়ালি টাইপ করেছে, অ্যাডমিন রিভিউ প্রয়োজন
            $table->boolean('is_verified')->default(false)->index();

            $table->timestamps();

            // Full-text search index (aliases ছাড়া — JSON full-text MySQL 8-এ সমস্যা হয়)
            $table->index('name');
            $table->index('district_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hospitals');
    }
};
