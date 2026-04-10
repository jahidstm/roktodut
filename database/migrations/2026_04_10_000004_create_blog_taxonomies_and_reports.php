<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * RoktoDut Blog Module — Taxonomy & Reports
 *
 * Creates three tables:
 *   1. categories       — content classification (scoped by post type)
 *   2. category_post    — many-to-many pivot between posts and categories
 *   3. post_reports     — reader-submitted moderation flags on posts
 */
return new class extends Migration
{
    public function up(): void
    {
        // ══════════════════════════════════════════════════════════════
        // 1. categories
        // ══════════════════════════════════════════════════════════════
        Schema::create('categories', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();

            // Scope: which post type(s) this category applies to.
            // 'general' means it can appear on both health & story posts.
            $table->enum('type', ['health', 'story', 'general'])->default('general');

            // Soft display ordering in category listings / dropdowns
            $table->unsignedSmallInteger('sort_order')->default(0);

            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['type', 'is_active', 'sort_order'], 'cat_type_active_sort_idx');
        });

        // ══════════════════════════════════════════════════════════════
        // 2. category_post  (BelongsToMany pivot)
        // ══════════════════════════════════════════════════════════════
        Schema::create('category_post', function (Blueprint $table) {
            // Auto-increment id so the pivot can be referenced in queries cleanly
            $table->id();

            $table->foreignId('category_id')
                  ->constrained('categories')
                  ->cascadeOnDelete();

            $table->foreignId('post_id')
                  ->constrained('posts')
                  ->cascadeOnDelete();

            // Prevent duplicate pivot rows
            $table->unique(['category_id', 'post_id'], 'cat_post_unique');

            $table->timestamps();

            // Speed up "all posts in category X" lookups
            $table->index(['category_id', 'post_id'], 'cat_post_lookup_idx');
        });

        // ══════════════════════════════════════════════════════════════
        // 3. post_reports
        // ══════════════════════════════════════════════════════════════
        Schema::create('post_reports', function (Blueprint $table) {
            $table->id();

            $table->foreignId('post_id')
                  ->constrained('posts')
                  ->cascadeOnDelete();

            // nullable: allow guest / anonymous reports
            $table->foreignId('reporter_user_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->enum('reason', [
                'misinformation',   // Medical / factual error
                'spam',             // Promotional or repeated content
                'inappropriate',    // Offensive / graphic material
                'copyright',        // Stolen content
                'other',            // Free-text catch-all
            ]);

            $table->text('details')->nullable()
                  ->comment('Optional reporter explanation, especially for "other"');

            $table->enum('status', ['pending', 'reviewed', 'dismissed', 'actioned'])
                  ->default('pending');

            // Moderator who handled the report
            $table->foreignId('reviewed_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->timestamp('reviewed_at')->nullable();

            $table->timestamps();

            // Admin moderation queue: all open reports sorted newest first
            $table->index(['status', 'created_at'], 'reports_status_created_idx');

            // "How many reports does post X have?" lookup
            $table->index(['post_id', 'status'], 'reports_post_status_idx');

            // Prevent a single user from reporting the same post twice
            $table->unique(['post_id', 'reporter_user_id'], 'reports_post_user_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('post_reports');
        Schema::dropIfExists('category_post');
        Schema::dropIfExists('categories');
    }
};
