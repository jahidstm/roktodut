<?php

use App\Enums\DonationStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('donor_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('blood_request_id')->nullable()->constrained()->nullOnDelete();
            $table->date('donation_date');
            $table->string('hospital')->nullable();
            $table->string('district');
            $table->string('claim_status')->default(DonationStatus::PENDING->value);
            $table->timestamp('claim_deadline')->nullable();
            $table->integer('points_earned')->default(50);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('donor_id');
            $table->index('claim_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donations');
    }
};
