<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$users = \App\Models\User::where('name', 'সাভার ডেমো ডোনার 1')->get(['id', 'email']);
foreach($users as $u) {
    echo "ID: " . $u->id . " - Email: " . $u->email . "\n";
    $donations = \App\Models\Donation::where('donor_id', $u->id)->get();
    foreach($donations as $d) {
        if (empty($d->certificate_token)) {
            $token = bin2hex(random_bytes(16));
            $d->update(['certificate_token' => $token, 'certificate_generated_at' => now()]);
            echo "   -> Generated Token for Donation ID: {$d->id}\n";
        }
    }
}
