<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chronic_request_subscriptions', function (Blueprint $table) {
            $table->unsignedTinyInteger('buddy_rotation_index')
                ->default(0)
                ->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('chronic_request_subscriptions', function (Blueprint $table) {
            $table->dropColumn('buddy_rotation_index');
        });
    }
};
