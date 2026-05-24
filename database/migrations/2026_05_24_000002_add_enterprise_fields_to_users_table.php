<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->decimal('dfi_score', 6, 2)->default(0);
            $table->string('priority_tier')->default('standard');
            $table->unsignedSmallInteger('super_critical_tokens')->default(0);
            $table->string('suspension_reason')->nullable();
            $table->string('medical_clearance_document')->nullable();

            $table->index('priority_tier');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['priority_tier']);
            $table->dropColumn([
                'dfi_score',
                'priority_tier',
                'super_critical_tokens',
                'suspension_reason',
                'medical_clearance_document',
            ]);
        });
    }
};
