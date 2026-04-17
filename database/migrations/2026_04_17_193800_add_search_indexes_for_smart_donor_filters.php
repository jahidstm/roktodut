<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $this->addIndexIfNeeded($table, 'users', 'blood_group', 'idx_users_blood_group');
            $this->addIndexIfNeeded($table, 'users', 'division_id', 'idx_users_division_id');
            $this->addIndexIfNeeded($table, 'users', 'district_id', 'idx_users_district_id');
            $this->addIndexIfNeeded($table, 'users', 'upazila_id', 'idx_users_upazila_id');
            $this->addIndexIfNeeded($table, 'users', 'is_ready_now', 'idx_users_is_ready_now');
        });

        if (Schema::hasTable('blood_requests')) {
            Schema::table('blood_requests', function (Blueprint $table): void {
                $this->addIndexIfNeeded($table, 'blood_requests', 'blood_group', 'idx_blood_requests_blood_group');
                $this->addIndexIfNeeded($table, 'blood_requests', 'division_id', 'idx_blood_requests_division_id');
                $this->addIndexIfNeeded($table, 'blood_requests', 'district_id', 'idx_blood_requests_district_id');
                $this->addIndexIfNeeded($table, 'blood_requests', 'upazila_id', 'idx_blood_requests_upazila_id');
                $this->addIndexIfNeeded($table, 'blood_requests', 'is_ready_now', 'idx_blood_requests_is_ready_now');
            });
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $this->dropIndexIfExists($table, 'users', 'idx_users_blood_group');
            $this->dropIndexIfExists($table, 'users', 'idx_users_division_id');
            $this->dropIndexIfExists($table, 'users', 'idx_users_district_id');
            $this->dropIndexIfExists($table, 'users', 'idx_users_upazila_id');
            $this->dropIndexIfExists($table, 'users', 'idx_users_is_ready_now');
        });

        if (Schema::hasTable('blood_requests')) {
            Schema::table('blood_requests', function (Blueprint $table): void {
                $this->dropIndexIfExists($table, 'blood_requests', 'idx_blood_requests_blood_group');
                $this->dropIndexIfExists($table, 'blood_requests', 'idx_blood_requests_division_id');
                $this->dropIndexIfExists($table, 'blood_requests', 'idx_blood_requests_district_id');
                $this->dropIndexIfExists($table, 'blood_requests', 'idx_blood_requests_upazila_id');
                $this->dropIndexIfExists($table, 'blood_requests', 'idx_blood_requests_is_ready_now');
            });
        }
    }

    private function addIndexIfNeeded(Blueprint $table, string $tableName, string $column, string $indexName): void
    {
        if (!Schema::hasColumn($tableName, $column)) {
            return;
        }

        if ($this->hasIndexOnColumn($tableName, $column)) {
            return;
        }

        $table->index($column, $indexName);
    }

    private function dropIndexIfExists(Blueprint $table, string $tableName, string $indexName): void
    {
        if ($this->hasIndexByName($tableName, $indexName)) {
            $table->dropIndex($indexName);
        }
    }

    private function hasIndexOnColumn(string $tableName, string $column): bool
    {
        $schema = DB::getDatabaseName();

        $result = DB::selectOne(
            'SELECT 1
             FROM information_schema.statistics
             WHERE table_schema = ?
               AND table_name = ?
               AND column_name = ?
               AND index_name <> ?
             LIMIT 1',
            [$schema, $tableName, $column, 'PRIMARY']
        );

        return $result !== null;
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

