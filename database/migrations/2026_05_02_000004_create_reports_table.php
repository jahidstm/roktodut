<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->morphs('reportable');
            $table->enum('category', ['fake_info', 'harassment', 'spam', 'inappropriate', 'other']);
            $table->text('message')->nullable();
            $table->enum('reporter_type', ['guest', 'user']);
            $table->foreignId('reporter_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('reporter_ip_hash', 64);
            $table->enum('status', ['open', 'reviewing', 'resolved', 'dismissed'])->default('open');
            $table->text('admin_note')->nullable();
            $table->foreignId('resolved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index(['reportable_type', 'reportable_id', 'status']);
            $table->index('reporter_ip_hash');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
