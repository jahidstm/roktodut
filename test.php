<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    $mlApiKey = (string) config('services.roktodut_ml.api_key');
    $mlBaseUrl = rtrim((string) config('services.roktodut_ml.base_url'), '/');
    
    $parseResponse = Illuminate\Support\Facades\Http::timeout(15)->withHeaders(['X-API-Key' => $mlApiKey])->post("{$mlBaseUrl}/api/v1/parse-request", ['text' => 'আজ রাতে ঢাকা মেডিকেলে ২ ব্যাগ ও পজিটিভ রক্ত লাগবে খুব জরুরি।']);
    
    $parseResponse->throw();
    $parsed = $parseResponse->json();
    
    $internalSecret = (string) config('services.roktodut_ml.internal_secret');
    $internalUrl = url('/api/internal/requests/nlp');
    
    $persistPayload = array_merge($parsed, [
        'requested_by' => null,
        'telegram_chat_id' => '5219949818',
        'raw_text' => 'আজ রাতে ঢাকা মেডিকেলে ২ ব্যাগ ও পজিটিভ রক্ত লাগবে খুব জরুরি।',
    ]);
    
    $saveResponse = Illuminate\Support\Facades\Http::timeout(15)->withHeaders(['X-Internal-Secret' => $internalSecret])->post($internalUrl, $persistPayload);
    
    $saveResponse->throw();
    dump('Success!', $saveResponse->json());
} catch (\Throwable $e) {
    dump('Error:', $e->getMessage());
    if (method_exists($e, 'response') && $e->response) {
        $body = $e->response->body();
        dump('Response:', substr(strip_tags($body), 0, 1000));
    }
}
