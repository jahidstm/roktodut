<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('districts', function (Blueprint $table) {
            $table->id();
            $table->string('division');
            $table->string('name')->unique();
            $table->string('bn_name');
            $table->timestamps();
        });

        Schema::create('thanas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('district_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->timestamps();

            $table->index('district_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('thanas');
        Schema::dropIfExists('districts');
    }
};
