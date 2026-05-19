<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

Illuminate\Support\Facades\DB::statement("ALTER TABLE blood_requests MODIFY urgency VARCHAR(255) NOT NULL");
Illuminate\Support\Facades\DB::statement("UPDATE blood_requests SET urgency = 'emergency' WHERE urgency = 'high'");
Illuminate\Support\Facades\DB::statement("UPDATE blood_requests SET urgency = 'normal' WHERE urgency = 'medium'");
Illuminate\Support\Facades\DB::statement("UPDATE blood_requests SET urgency = 'normal' WHERE urgency NOT IN ('normal', 'urgent', 'emergency')");
Illuminate\Support\Facades\DB::statement("ALTER TABLE blood_requests MODIFY urgency ENUM('normal', 'urgent', 'emergency') NOT NULL DEFAULT 'normal'");
dump('Fixed urgency ENUM in blood_requests');
