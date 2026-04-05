<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            if (!Schema::hasColumn('organizations', 'short_name')) {
                $table->string('short_name')->nullable()->after('name');
            }
            if (!Schema::hasColumn('organizations', 'established_year')) {
                $table->integer('established_year')->nullable()->after('type');
            }
            if (!Schema::hasColumn('organizations', 'division')) {
                $table->unsignedBigInteger('division')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('organizations', 'upazila')) {
                $table->unsignedBigInteger('upazila')->nullable()->after('district');
            }
            if (!Schema::hasColumn('organizations', 'document_path')) {
                $table->string('document_path')->nullable()->after('logo');
            }
            if (!Schema::hasColumn('organizations', 'status')) {
                $table->string('status')->default('pending')->after('is_verified');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropColumn([
                'short_name',
                'established_year',
                'division',
                'upazila',
                'document_path',
                'status'
            ]);
        });
    }
};
