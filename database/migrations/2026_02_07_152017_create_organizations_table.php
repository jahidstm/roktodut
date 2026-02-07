<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type'); // hospital, club, ngo
            $table->string('district');
            $table->text('address')->nullable();
            $table->string('phone', 15)->nullable();
            $table->string('email')->nullable();
            $table->string('logo')->nullable();
            $table->text('description')->nullable();
            $table->foreignId('admin_id')->constrained('users')->cascadeOnDelete();
            $table->boolean('is_verified')->default(false);
            $table->timestamps();

            $table->index('district');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organizations');
    }
};
