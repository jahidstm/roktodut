<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('divisions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('bn_name');
            $table->timestamps();
        });

        Schema::create('districts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('division_id')->constrained()->cascadeOnDelete();
            $table->string('name')->unique();
            $table->string('bn_name');
            $table->timestamps();

            $table->index('division_id');
        });

        Schema::create('upazilas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('district_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('type')->default('upazila'); // upazila, area
            $table->timestamps();

            $table->index('district_id');
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('upazilas');
        Schema::dropIfExists('districts');
        Schema::dropIfExists('divisions');
    }
};
