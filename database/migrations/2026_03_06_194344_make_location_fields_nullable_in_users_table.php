<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // কলামগুলোকে nullable করা হচ্ছে
            $table->string('district')->nullable()->change();
            
            // যদি division এবং upazila কলাম ডাটাবেসে থাকে, তবে নিচের লাইনগুলো আনকমেন্ট করে দেবে
            // $table->string('division')->nullable()->change();
            // $table->string('upazila')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('district')->nullable(false)->change();
            // $table->string('division')->nullable(false)->change();
            // $table->string('upazila')->nullable(false)->change();
        });
    }
};