<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // State machine: 0=unsent, 1=7-day sent, 2=3-day sent, 3=day-of sent
            // Resets to 0 whenever cooldown_until is renewed (new donation)
            $table->tinyInteger('reminder_stage')->unsigned()->default(0)->after('cooldown_until');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('reminder_stage');
        });
    }
};
