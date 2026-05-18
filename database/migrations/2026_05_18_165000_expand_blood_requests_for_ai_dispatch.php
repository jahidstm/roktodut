<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        Schema::table('blood_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('blood_requests', 'location_text')) {
                $table->string('location_text')->nullable()->after('blood_group');
            }

            if (!Schema::hasColumn('blood_requests', 'units_needed')) {
                $table->integer('units_needed')->default(1)->after('bags_needed');
            }

            if (!Schema::hasColumn('blood_requests', 'ml_confidence_score')) {
                $table->float('ml_confidence_score')->nullable()->after('status');
            }
        });

        if (!Schema::hasColumn('blood_requests', 'latitude')) {
            Schema::table('blood_requests', function (Blueprint $table) {
                $table->decimal('latitude', 10, 8)->nullable()->after('location_text');
            });
        }

        if (!Schema::hasColumn('blood_requests', 'longitude')) {
            Schema::table('blood_requests', function (Blueprint $table) {
                $table->decimal('longitude', 10, 8)->nullable()->after('latitude');
            });
        }

        DB::table('blood_requests')
            ->whereNull('location_text')
            ->update(['location_text' => DB::raw('address')]);

        DB::statement('UPDATE `blood_requests` SET `units_needed` = COALESCE(NULLIF(`units_needed`, 0), NULLIF(`bags_needed`, 0), 1)');

        DB::statement("UPDATE `blood_requests` SET `blood_group` = UPPER(REPLACE(TRIM(COALESCE(`blood_group`, '')), ' ', ''))");
        DB::statement("
            UPDATE `blood_requests`
            SET `blood_group` = CASE
                WHEN `blood_group` IN ('A+','A-','B+','B-','AB+','AB-','O+','O-') THEN `blood_group`
                ELSE 'O+'
            END
        ");

        DB::statement("
            UPDATE `blood_requests`
            SET `urgency` = CASE LOWER(TRIM(COALESCE(`urgency`, '')))
                WHEN 'emergency' THEN 'emergency'
                WHEN 'high' THEN 'high'
                WHEN 'urgent' THEN 'high'
                WHEN 'medium' THEN 'medium'
                WHEN 'normal' THEN 'medium'
                WHEN 'low' THEN 'medium'
                ELSE 'medium'
            END
        ");

        DB::statement("
            UPDATE `blood_requests`
            SET `status` = CASE LOWER(TRIM(COALESCE(`status`, '')))
                WHEN 'nlp_pending' THEN 'nlp_pending'
                WHEN 'pending' THEN 'pending'
                WHEN 'approved' THEN 'approved'
                WHEN 'in_progress' THEN 'approved'
                WHEN 'fulfilled' THEN 'fulfilled'
                WHEN 'rejected' THEN 'rejected'
                WHEN 'expired' THEN 'rejected'
                ELSE 'pending'
            END
        ");

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE `blood_requests` MODIFY `blood_group` ENUM(\'A+\',\'A-\',\'B+\',\'B-\',\'AB+\',\'AB-\',\'O+\',\'O-\') NOT NULL');
            DB::statement('ALTER TABLE `blood_requests` MODIFY `latitude` DECIMAL(10,8) NULL');
            DB::statement('ALTER TABLE `blood_requests` MODIFY `longitude` DECIMAL(10,8) NULL');
            DB::statement('ALTER TABLE `blood_requests` MODIFY `urgency` ENUM(\'emergency\',\'high\',\'medium\') NOT NULL DEFAULT \'medium\'');
            DB::statement('ALTER TABLE `blood_requests` MODIFY `status` ENUM(\'nlp_pending\',\'pending\',\'approved\',\'fulfilled\',\'rejected\') NOT NULL DEFAULT \'pending\'');
        }
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE `blood_requests` MODIFY `blood_group` VARCHAR(255) NOT NULL');
            DB::statement('ALTER TABLE `blood_requests` MODIFY `latitude` DECIMAL(10,7) NULL');
            DB::statement('ALTER TABLE `blood_requests` MODIFY `longitude` DECIMAL(10,7) NULL');
            DB::statement('ALTER TABLE `blood_requests` MODIFY `urgency` VARCHAR(255) NOT NULL');
            DB::statement('ALTER TABLE `blood_requests` MODIFY `status` VARCHAR(255) NOT NULL');
        }

        if (Schema::hasColumn('blood_requests', 'bags_needed') && Schema::hasColumn('blood_requests', 'units_needed')) {
            DB::statement('UPDATE `blood_requests` SET `bags_needed` = COALESCE(NULLIF(`bags_needed`, 0), NULLIF(`units_needed`, 0), 1)');
        }

        Schema::table('blood_requests', function (Blueprint $table) {
            if (Schema::hasColumn('blood_requests', 'ml_confidence_score')) {
                $table->dropColumn('ml_confidence_score');
            }

            if (Schema::hasColumn('blood_requests', 'units_needed')) {
                $table->dropColumn('units_needed');
            }

            if (Schema::hasColumn('blood_requests', 'location_text')) {
                $table->dropColumn('location_text');
            }
        });
    }
};

