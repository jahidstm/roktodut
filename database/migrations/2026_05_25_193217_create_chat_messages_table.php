<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blood_request_response_id')
                ->constrained('blood_request_responses')
                ->cascadeOnDelete();
            $table->foreignId('sender_id')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->text('message');
            $table->boolean('is_read')->default(false);
            $table->timestamps();

            $table->index(['blood_request_response_id', 'created_at'], 'chat_messages_response_created_idx');
            $table->index(['blood_request_response_id', 'is_read'], 'chat_messages_response_read_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
    }
};
