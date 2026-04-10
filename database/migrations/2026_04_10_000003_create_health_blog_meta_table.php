<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * RoktoDut Blog Module — Health Blog Meta Table
 *
 * One-to-one extension of `posts` (type = 'health').
 * Captures medical-credibility metadata: who reviewed the article
 * and the structured list of citations/sources.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('health_blog_meta', function (Blueprint $table) {
            $table->id();

            // Parent post — one-to-one, cascade delete
            $table->foreignId('post_id')
                  ->unique()
                  ->constrained('posts')
                  ->cascadeOnDelete();

            // Free-text name/credentials of the medical reviewer (nullable)
            $table->string('medically_reviewed_by')->nullable()
                  ->comment('Full name and title of the reviewing clinician, e.g. "Dr. Arif Hossain, MBBS"');

            // Structured sources: [{title, url, accessed_at}, …]
            $table->json('sources_json')
                  ->nullable()
                  ->comment('JSON array of citation objects with keys: title, url, accessed_at');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('health_blog_meta');
    }
};
