<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chronic_subscription_buddies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')
                ->constrained('chronic_request_subscriptions')
                ->cascadeOnDelete();
            $table->foreignId('donor_user_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedTinyInteger('position')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['subscription_id', 'donor_user_id'], 'csb_unique_subscription_donor');
            $table->index(['subscription_id', 'position'], 'csb_subscription_position_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chronic_subscription_buddies');
    }
};
