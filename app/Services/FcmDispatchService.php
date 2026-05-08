<?php

namespace App\Services;

use App\Enums\BloodGroup;
use App\Models\BloodRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class FcmDispatchService
{
    private const MULTICAST_LIMIT = 500;

    public function sendEmergencyAlertToDonors(BloodRequest $bloodRequest): void
    {
        $bloodRequest->loadMissing(['hospital:id,name,name_bn']);

        $conditions = $this->getMatchingConditions($bloodRequest);
        $tokenQuery = $this->buildMatchingDonorQuery($bloodRequest);

        $sql = (clone $tokenQuery)->select('fcm_token')->toSql();
        $bindings = (clone $tokenQuery)->getBindings();
        $rawTokens = (clone $tokenQuery)->pluck('fcm_token')->all();
        $tokens = collect($rawTokens)
            ->filter(fn ($token) => is_string($token) && $token !== '')
            ->unique()
            ->values();

        Log::info('FCM donor matching diagnostics', [
            'blood_request_id' => $bloodRequest->id,
            'conditions' => $conditions,
            'sql' => $sql,
            'bindings' => $bindings,
            'raw_tokens' => $rawTokens,
            'tokens' => $tokens->all(),
            'token_count' => $tokens->count(),
        ]);

        if ($tokens->isEmpty()) {
            return;
        }

        $bloodGroup = $conditions['blood_group'];
        $hospitalName = $bloodRequest->hospital?->display_name
            ?? $bloodRequest->hospital?->name
            ?? 'হাসপাতাল';
        $patientName = filled($bloodRequest->patient_name)
            ? (string) $bloodRequest->patient_name
            : 'উল্লেখ নেই';

        $messaging = app('firebase.messaging');
        $message = CloudMessage::new()
            ->withNotification(Notification::create(
                "🚨 জরুরি: {$hospitalName}-এ আপনার {$bloodGroup} রক্ত প্রয়োজন!",
                "রোগীর নাম: {$patientName}. এখনই বিস্তারিত দেখুন!"
            ))
            ->withData([
                'blood_request_id' => (string) $bloodRequest->id,
            ]);

        $invalidOrUnknownTokens = [];

        foreach ($tokens->chunk(self::MULTICAST_LIMIT) as $chunkedTokens) {
            $report = $messaging->sendMulticast($message, $chunkedTokens->all());
            $invalidOrUnknownTokens = array_merge(
                $invalidOrUnknownTokens,
                $report->unknownTokens(),
                $report->invalidTokens(),
            );
        }

        $invalidOrUnknownTokens = array_values(array_unique(array_filter(
            $invalidOrUnknownTokens,
            fn($token) => is_string($token) && $token !== ''
        )));

        if ($invalidOrUnknownTokens === []) {
            return;
        }

        User::query()
            ->whereIn('fcm_token', $invalidOrUnknownTokens)
            ->update(['fcm_token' => null]);
    }

    public function buildMatchingDonorQuery(BloodRequest $bloodRequest): Builder
    {
        $bloodGroup = $this->resolveBloodGroupValue($bloodRequest->blood_group);

        // Temporary creator-testing note:
        // No creator exclusion is applied here on purpose.
        // If needed later, re-enable:
        // ->whereKeyNot($bloodRequest->requested_by);
        return User::query()
            ->where('blood_group', $bloodGroup)
            ->where('district_id', $bloodRequest->district_id)
            ->where('is_available', true)
            ->whereNotNull('fcm_token');
    }

    public function getMatchingConditions(BloodRequest $bloodRequest): array
    {
        return [
            'blood_group' => $this->resolveBloodGroupValue($bloodRequest->blood_group),
            'district_id' => $bloodRequest->district_id,
            'is_available' => true,
            'fcm_token_not_null' => true,
            'creator_exclusion' => false,
        ];
    }

    private function resolveBloodGroupValue(mixed $bloodGroup): string
    {
        if ($bloodGroup instanceof BloodGroup) {
            return $bloodGroup->value;
        }

        return (string) $bloodGroup;
    }
}
