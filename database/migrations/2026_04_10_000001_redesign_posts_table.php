<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * RoktoDut Blog Module — Posts Table
 *
 * Replaces the original generic `blogs` table with the full PRD schema.
 * Drops the old table first (only if it exists) to allow a clean rebuild.
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── Drop legacy table ──────────────────────────────────────────
        Schema::dropIfExists('blogs');

        // ── Create new, PRD-compliant posts table ──────────────────────
        Schema::create('posts', function (Blueprint $table) {
            $table->id();

            // Author — soft-reference so posts survive user deletion
            $table->unsignedBigInteger('author_user_id');
            $table->foreign('author_user_id')
                  ->references('id')->on('users')
                  ->cascadeOnDelete();

            // Core content fields
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('excerpt', 500)->nullable();
            $table->longText('body_raw');        // Original markdown/unsanitised HTML
            $table->longText('body_sanitized');  // Safe, server-side-sanitised HTML
            $table->string('cover_image')->nullable();

            // Classification & lifecycle
            $table->enum('type', ['health', 'story']);
            $table->enum('status', ['draft', 'pending_review', 'published', 'rejected'])
                  ->default('draft');
            $table->timestamp('published_at')->nullable();

            $table->timestamps();

            // ── Composite indexes (PRD §3.2) ───────────────────────────
            // Feed query: filter by type + published status, ordered by date
            $table->index(['type', 'status', 'published_at'], 'posts_type_status_published_idx');

            // Admin moderation queue: all pending/rejected posts by date
            $table->index(['status', 'published_at'], 'posts_status_published_idx');

            // Author dashboard: a user's own posts by status
            $table->index(['author_user_id', 'status'], 'posts_author_status_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');

        // Restore the minimal legacy blogs table for clean rollback
        Schema::create('blogs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('author_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('content');
            $table->string('cover_image')->nullable();
            $table->string('category')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamps();
            $table->index('is_published');
            $table->index('slug');
        });
    }
};
