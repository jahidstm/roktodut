<?php

namespace App\Jobs;

use App\Models\BloodRequest;
use App\Models\DonorResponseLog;
use App\Models\User;
use App\Notifications\BloodRequestMatchedNotification;
use App\Services\TelegramService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;

class DispatchEmergencyAlertsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private const BATCH_SIZE = 5;

    /**
     * @param array<int, array<string, mixed>> $rankedDonors
     */
    public function __construct(
        public int $bloodRequestId,
        public array $rankedDonors
    ) {}

    public function handle(TelegramService $telegram): void
    {
        $bloodRequest = BloodRequest::with(['district', 'upazila', 'hospital'])->find($this->bloodRequestId);
        if (!$bloodRequest) {
            return;
        }

        Cache::put($this->rankCacheKey(), $this->rankedDonors, now()->addHours(6));

        $initialBatch = array_slice($this->rankedDonors, 0, self::BATCH_SIZE);
        if ($initialBatch === []) {
            return;
        }

        $this->sendAlerts($bloodRequest, $initialBatch, $telegram);

        CheckAndCascadeRequestJob::dispatch(
            bloodRequestId: $bloodRequest->id,
            offset: self::BATCH_SIZE
        )->delay(now()->addMinutes(10));
    }

    private function rankCacheKey(): string
    {
        return "blood_request:ranked_donors:{$this->bloodRequestId}";
    }

    /**
     * @param array<int, array<string, mixed>> $batch
     */
    private function sendAlerts(BloodRequest $bloodRequest, array $batch, TelegramService $telegram): void
    {
        $donorIds = collect($batch)
            ->pluck('donor_id')
            ->map(fn($id) => (int) $id)
            ->filter()
            ->values()
            ->all();

        if ($donorIds === []) {
            return;
        }

        $donorsById = User::query()
            ->whereIn('id', $donorIds)
            ->get()
            ->keyBy('id');

        $orderedDonors = collect($donorIds)
            ->map(fn(int $id) => $donorsById->get($id))
            ->filter()
            ->values();

        if ($orderedDonors->isEmpty()) {
            return;
        }

        $districtName = $bloodRequest->district?->name ?? 'আপনার';
        Notification::sendNow($orderedDonors, new BloodRequestMatchedNotification($bloodRequest, $districtName));

        $alertData = [
            'blood_group' => is_object($bloodRequest->blood_group) ? $bloodRequest->blood_group->value : (string) $bloodRequest->blood_group,
            'component' => $bloodRequest->componentLabel(),
            'hospital' => $bloodRequest->hospital?->display_name ?? $bloodRequest->hospital?->name ?? 'উল্লেখ নেই',
            'location' => implode(', ', array_filter([
                $bloodRequest->upazila?->name,
                $bloodRequest->district?->name,
            ])),
            'bags_needed' => (int) ($bloodRequest->units_needed ?? $bloodRequest->bags_needed ?? 1),
            'needed_at' => $bloodRequest->needed_at ? $bloodRequest->needed_at->format('d M, Y — h:i A') : 'যত দ্রুত সম্ভব (ASAP)',
            'urgency' => (string) $bloodRequest->urgency,
            'request_url' => route('requests.show', $bloodRequest->id),
        ];

        foreach ($orderedDonors as $donor) {
            if (!empty($donor->telegram_chat_id)) {
                $telegram->sendBloodAlert($donor->telegram_chat_id, $alertData);
            }
        }

        $this->sendFcmToDonors($orderedDonors, $bloodRequest);
        $this->persistDispatchLogs($bloodRequest, $batch);
    }

    private function sendFcmToDonors($donors, BloodRequest $bloodRequest): void
    {
        $tokens = collect($donors)
            ->pluck('fcm_token')
            ->filter(fn($token) => is_string($token) && trim($token) !== '')
            ->unique()
            ->values();

        if ($tokens->isEmpty()) {
            return;
        }

        try {
            $messaging = app('firebase.messaging');
            $message = CloudMessage::new()
                ->withNotification(
                    FirebaseNotification::create(
                        "🚨 জরুরি: {$bloodRequest->blood_group} রক্ত প্রয়োজন!",
                        "রিকোয়েস্ট #{$bloodRequest->id} - বিস্তারিত দেখতে অ্যাপ খুলুন।"
                    )
                )
                ->withData([
                    'blood_request_id' => (string) $bloodRequest->id,
                ]);

            foreach ($tokens->chunk(500) as $chunk) {
                $messaging->sendMulticast($message, $chunk->all());
            }
        } catch (\Throwable $e) {
            Log::warning('Targeted FCM dispatch failed', [
                'blood_request_id' => $bloodRequest->id,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * @param array<int, array<string, mixed>> $batch
     */
    private function persistDispatchLogs(BloodRequest $bloodRequest, array $batch): void
    {
        $now = now();
        $historicalRates = $this->getHistoricalRates(
            collect($batch)->pluck('donor_id')->map(fn($id) => (int) $id)->all()
        );

        foreach ($batch as $row) {
            $donorId = (int) ($row['donor_id'] ?? 0);
            if ($donorId <= 0) {
                continue;
            }

            DonorResponseLog::query()->firstOrCreate(
                [
                    'request_id' => $bloodRequest->id,
                    'donor_id' => $donorId,
                ],
                [
                    'notified_at' => $now,
                    'status' => 'pending',
                    'response_time_minutes' => null,
                    'distance_km' => round((float) ($row['distance_km'] ?? 0), 2),
                    'days_since_last_donation' => (int) ($row['days_since_last_donation'] ?? 0),
                    'temporal_hour' => (int) ($row['temporal_hour'] ?? $now->hour),
                    'is_weekend' => (bool) ($row['is_weekend'] ?? $now->isWeekend()),
                    'historical_response_rate' => (float) ($row['historical_response_rate'] ?? ($historicalRates[$donorId] ?? 0.0)),
                ]
            );
        }
    }

    /**
     * @param array<int> $donorIds
     * @return array<int, float>
     */
    private function getHistoricalRates(array $donorIds): array
    {
        if ($donorIds === []) {
            return [];
        }

        $stats = DonorResponseLog::query()
            ->whereIn('donor_id', $donorIds)
            ->selectRaw('donor_id, SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) AS accepted_count, COUNT(*) AS total_count', ['accepted'])
            ->groupBy('donor_id')
            ->get();

        $rates = [];
        foreach ($stats as $row) {
            $total = (int) $row->total_count;
            $accepted = (int) $row->accepted_count;
            $rates[(int) $row->donor_id] = $total > 0 ? round($accepted / $total, 4) : 0.0;
        }

        return $rates;
    }
}
