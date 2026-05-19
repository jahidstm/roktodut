<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$candidates = collect([
    ['donor_id' => 1, 'distance_km' => 5.0, 'days_since_last_donation' => 100, 'temporal_hour' => 12, 'is_weekend' => 0, 'historical_response_rate' => 0.5],
    ['donor_id' => 2, 'distance_km' => 2.0, 'days_since_last_donation' => 200, 'temporal_hour' => 12, 'is_weekend' => 0, 'historical_response_rate' => 0.8]
]);

try {
    $response = Illuminate\Support\Facades\Http::timeout(2)
        ->post('http://127.0.0.1:8001/api/v1/rank-donors', [
            'request_details' => ['request_id' => 1, 'blood_group' => 'O+', 'urgency' => 'emergency', 'units_needed' => 1],
            'candidate_donors' => $candidates->all(),
        ]);
    $response->throw();
    dump('AI Worked', $response->json());
} catch (\Throwable $e) {
    dump('AI Server Failed. Expected connection refused: ' . $e->getMessage());
    dump('Fallback Activated: Sorted by Distance');
    $fallback = $candidates->sortBy('distance_km')->values()->toArray();
    dump($fallback);
}
