<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('blood_camps', function (Blueprint $table) {
            if (!Schema::hasColumn('blood_camps', 'start_at')) {
                $table->dateTime('start_at')->nullable()->after('name');
            }
            if (!Schema::hasColumn('blood_camps', 'end_at')) {
                $table->dateTime('end_at')->nullable()->after('start_at');
            }
            if (!Schema::hasColumn('blood_camps', 'district_id')) {
                $table->foreignId('district_id')->nullable()->after('location')->constrained('districts')->nullOnDelete();
            }
            if (!Schema::hasColumn('blood_camps', 'upazila_id')) {
                $table->foreignId('upazila_id')->nullable()->after('district_id')->constrained('upazilas')->nullOnDelete();
            }
            if (!Schema::hasColumn('blood_camps', 'address_line')) {
                $table->string('address_line', 255)->nullable()->after('upazila_id');
            }
            if (!Schema::hasColumn('blood_camps', 'contact_name')) {
                $table->string('contact_name', 120)->nullable()->after('address_line');
            }
            if (!Schema::hasColumn('blood_camps', 'contact_phone')) {
                $table->string('contact_phone', 20)->nullable()->after('contact_name');
            }
            if (!Schema::hasColumn('blood_camps', 'target_donors')) {
                $table->unsignedInteger('target_donors')->nullable()->after('notes');
            }
            if (!Schema::hasColumn('blood_camps', 'is_public')) {
                $table->boolean('is_public')->default(false)->after('target_donors');
            }
            if (!Schema::hasColumn('blood_camps', 'status')) {
                $table->string('status', 20)->default('draft')->after('is_public');
            }
        });

        DB::table('blood_camps')
            ->orderBy('id')
            ->select(['id', 'camp_date', 'location', 'start_at', 'end_at', 'address_line', 'status'])
            ->get()
            ->each(function ($camp) {
                $updates = [];

                if (empty($camp->start_at) && !empty($camp->camp_date)) {
                    $start = Carbon::parse($camp->camp_date)->setTime(9, 0, 0);
                    $updates['start_at'] = $start;
                    $updates['end_at'] = $start->copy()->addHours(6);
                }

                if (empty($camp->address_line) && !empty($camp->location)) {
                    $updates['address_line'] = $camp->location;
                }

                if (empty($camp->status)) {
                    $updates['status'] = 'published';
                }

                if (!empty($updates)) {
                    DB::table('blood_camps')->where('id', $camp->id)->update($updates);
                }
            });
    }

    public function down(): void
    {
        Schema::table('blood_camps', function (Blueprint $table) {
            if (Schema::hasColumn('blood_camps', 'upazila_id')) {
                $table->dropConstrainedForeignId('upazila_id');
            }
            if (Schema::hasColumn('blood_camps', 'district_id')) {
                $table->dropConstrainedForeignId('district_id');
            }
            $columns = array_filter([
                Schema::hasColumn('blood_camps', 'start_at') ? 'start_at' : null,
                Schema::hasColumn('blood_camps', 'end_at') ? 'end_at' : null,
                Schema::hasColumn('blood_camps', 'address_line') ? 'address_line' : null,
                Schema::hasColumn('blood_camps', 'contact_name') ? 'contact_name' : null,
                Schema::hasColumn('blood_camps', 'contact_phone') ? 'contact_phone' : null,
                Schema::hasColumn('blood_camps', 'target_donors') ? 'target_donors' : null,
                Schema::hasColumn('blood_camps', 'is_public') ? 'is_public' : null,
                Schema::hasColumn('blood_camps', 'status') ? 'status' : null,
            ]);
            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
