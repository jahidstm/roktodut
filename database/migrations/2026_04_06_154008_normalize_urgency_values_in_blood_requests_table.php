<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        /**
         * Old UI values (low/medium/high/critical) -> Enum backing values (normal/urgent/emergency)
         * Enum: App\Enums\UrgencyLevel { normal, urgent, emergency }
         */

        DB::table('blood_requests')
            ->whereIn('urgency', ['critical', 'high'])
            ->update(['urgency' => 'emergency']);

        DB::table('blood_requests')
            ->where('urgency', 'medium')
            ->update(['urgency' => 'urgent']);

        DB::table('blood_requests')
            ->where('urgency', 'low')
            ->update(['urgency' => 'normal']);

        // Safety: null/empty হলে default normal (যদি কিছু রেকর্ডে null থাকে)
        DB::table('blood_requests')
            ->whereNull('urgency')
            ->orWhere('urgency', '')
            ->update(['urgency' => 'normal']);
    }

    public function down(): void
    {
        // Intentionally no-op: original values recover করা safe না
    }
};