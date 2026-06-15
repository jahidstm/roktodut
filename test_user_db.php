<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$user = \App\Models\User::where('name', 'সাভার ডেমো ডোনার 1')->first();
if ($user) {
    echo "User ID: {$user->id}\n";
    $donations = \App\Models\Donation::where('donor_id', $user->id)->get(['id', 'certificate_token', 'donation_date']);
    echo "Donations Count: " . $donations->count() . "\n";
    foreach ($donations as $d) {
        echo "ID: {$d->id}, Token: " . ($d->certificate_token ?? 'NULL') . "\n";
    }

    $responses = \App\Models\BloodRequestResponse::where('user_id', $user->id)->get(['id', 'blood_request_id', 'status', 'verification_status', 'fulfilled_at']);
    echo "Responses Count: " . $responses->count() . "\n";
    foreach ($responses as $r) {
        echo "RespID: {$r->id}, ReqID: {$r->blood_request_id}, Status: {$r->status}, Ver: {$r->verification_status}, Fulfilled: {$r->fulfilled_at}\n";
    }
} else {
    echo "User not found\n";
}
