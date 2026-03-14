<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // অনবোর্ডিংয়ের জন্য রোলকে অপশনাল করা হলো
            if (Schema::hasColumn('users', 'role')) {
                $table->string('role')->nullable()->change();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'role')) {
                $table->string('role')->nullable(false)->change();
            }
        });
    }
};