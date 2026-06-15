<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$donations = \App\Models\Donation::whereNull('certificate_token')->get();
$count = 0;
foreach($donations as $d) {
    $token = bin2hex(random_bytes(16));
    $d->update(['certificate_token' => $token, 'certificate_generated_at' => now()]);
    $count++;
}
echo "Generated tokens for {$count} legacy seeded donations.\n";
