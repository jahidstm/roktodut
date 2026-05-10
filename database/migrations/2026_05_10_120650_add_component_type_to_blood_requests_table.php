<?php

use App\Enums\BloodComponentType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('blood_requests', 'component_type')) {
            Schema::table('blood_requests', function (Blueprint $table) {
                $table->string('component_type')
                    ->default(BloodComponentType::WHOLE_BLOOD->value)
                    ->after('blood_group');
            });
        }

        if (!$this->hasIndexByName('blood_requests', 'blood_requests_component_type_idx')) {
            Schema::table('blood_requests', function (Blueprint $table) {
                $table->index('component_type', 'blood_requests_component_type_idx');
            });
        }

        DB::table('blood_requests')
            ->whereNull('component_type')
            ->update(['component_type' => BloodComponentType::WHOLE_BLOOD->value]);
    }

    public function down(): void
    {
        Schema::table('blood_requests', function (Blueprint $table) {
            if ($this->hasIndexByName('blood_requests', 'blood_requests_component_type_idx')) {
                $table->dropIndex('blood_requests_component_type_idx');
            }

            if (Schema::hasColumn('blood_requests', 'component_type')) {
                $table->dropColumn('component_type');
            }
        });
    }

    private function hasIndexByName(string $tableName, string $indexName): bool
    {
        $schema = DB::getDatabaseName();

        $result = DB::selectOne(
            'SELECT 1
             FROM information_schema.statistics
             WHERE table_schema = ?
               AND table_name = ?
               AND index_name = ?
             LIMIT 1',
            [$schema, $tableName, $indexName]
        );

        return $result !== null;
    }
};
