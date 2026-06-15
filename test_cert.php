<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Donation;

// Get the first donation with a certificate token
$donation = Donation::whereNotNull('certificate_token')
    ->with(['donor.district'])
    ->first();

if (!$donation) {
    echo "No certificate tokens found.\n";
    exit;
}

$token = $donation->certificate_token;
echo "Token      : " . $token . "\n";
echo "Share URL  : http://roktodut.test/certificate/" . $token . "\n";
echo "Download   : http://roktodut.test/certificate/" . $token . "/download\n";
echo "Donor      : " . ($donation->donor->name ?? 'N/A') . "\n";
echo "Donation # : " . $donation->id . "\n";
