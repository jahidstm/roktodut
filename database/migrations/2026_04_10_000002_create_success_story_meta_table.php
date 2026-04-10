<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * RoktoDut Blog Module — Success Story Meta Table
 *
 * One-to-one extension of `posts` (type = 'story').
 * Stores donor-story-specific metadata including the optional
 * polymorphic link to an existing Donation or BloodRequest record
 * and the anonymisation preference chosen by the author.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('success_story_meta', function (Blueprint $table) {
            $table->id();

            // Parent post — one-to-one, cascade delete
            $table->foreignId('post_id')
                  ->unique()
                  ->constrained('posts')
                  ->cascadeOnDelete();

            // Location context (non-relational: plain district name for flexibility)
            $table->string('district')->nullable();

            // Polymorphic soft-reference to a Donation or BloodRequest record
            // Kept as separate type/id columns (not morphs()) for explicit
            // enum validation at the application layer.
            $table->string('donation_ref_type')->nullable()
                  ->comment('Model class shorthand: "donation" | "blood_request"');
            $table->unsignedBigInteger('donation_ref_id')->nullable()
                  ->comment('PK of the referenced Donation / BloodRequest row');

            // Story verification flag (set by admin/moderator)
            $table->boolean('is_verified_story')->default(false);

            // Privacy level chosen by the donor-author
            $table->enum('anonymize_level', ['public', 'initials', 'anonymous'])
                  ->default('public');

            $table->timestamps();

            // Allow fast lookup of all verified stories
            $table->index(['is_verified_story', 'anonymize_level'], 'story_meta_verified_anon_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('success_story_meta');
    }
};
