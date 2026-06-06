<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blood_inventories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->string('blood_group', 5); // A+, A-, B+, B-, AB+, AB-, O+, O-
            $table->unsignedInteger('units_available')->default(0);
            $table->boolean('is_accepting_donations')->default(true);
            $table->text('notes')->nullable(); // সময়সূচি, বিশেষ তথ্য
            $table->timestamps();

            // প্রতিটি organization-এ প্রতি blood group-এর জন্য শুধু একটি row
            $table->unique(['organization_id', 'blood_group']);
            $table->index(['blood_group', 'units_available']); // search performance
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blood_inventories');
    }
};
