<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('author_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->text('content');
            $table->string('image')->nullable();
            $table->boolean('is_approved')->default(false);
            $table->timestamps();

            $table->index('is_approved');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stories');
    }
};
