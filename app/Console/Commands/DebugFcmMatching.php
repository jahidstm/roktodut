<?php

namespace App\Console\Commands;

use App\Models\BloodRequest;
use App\Models\User;
use App\Services\FcmDispatchService;
use Illuminate\Console\Command;

class DebugFcmMatching extends Command
{
    protected $signature = 'debug:fcm {request_id : Blood request ID}';

    protected $description = 'Debug donor matching for FCM emergency dispatch.';

    public function handle(FcmDispatchService $fcmDispatchService): int
    {
        $requestId = (int) $this->argument('request_id');
        $bloodRequest = BloodRequest::query()->find($requestId);

        if (!$bloodRequest) {
            $this->error("BloodRequest not found for ID: {$requestId}");
            return self::FAILURE;
        }

        $conditions = $fcmDispatchService->getMatchingConditions($bloodRequest);
        $query = $fcmDispatchService->buildMatchingDonorQuery($bloodRequest);

        $this->info("Debugging FCM match for BloodRequest #{$bloodRequest->id}");
        $this->line('Conditions: ' . json_encode($conditions, JSON_UNESCAPED_UNICODE));
        $this->line('SQL: ' . (clone $query)->toSql());
        $this->line('Bindings: ' . json_encode((clone $query)->getBindings(), JSON_UNESCAPED_UNICODE));

        $matchedUsers = (clone $query)
            ->get(['id', 'name', 'blood_group', 'district_id', 'is_available', 'fcm_token']);

        if ($matchedUsers->isEmpty()) {
            $this->warn('No users matched the current FCM query.');
        } else {
            $this->table(
                ['ID', 'Name', 'Blood Group', 'District ID', 'Is Available', 'Has FCM Token'],
                $matchedUsers->map(function (User $user): array {
                    return [
                        (string) $user->id,
                        (string) $user->name,
                        $this->normalizeBloodGroup($user->blood_group),
                        (string) $user->district_id,
                        $user->is_available ? 'true' : 'false',
                        filled($user->fcm_token) ? 'true' : 'false',
                    ];
                })->all()
            );
        }

        $rawTokens = $matchedUsers->pluck('fcm_token')->all();
        $cleanTokens = collect($rawTokens)
            ->filter(fn($token) => is_string($token) && $token !== '')
            ->unique()
            ->values()
            ->all();

        $this->line('Raw matched tokens: ' . json_encode($rawTokens, JSON_UNESCAPED_UNICODE));
        $this->line('Clean tokens used for multicast: ' . json_encode($cleanTokens, JSON_UNESCAPED_UNICODE));
        $this->line('Clean token count: ' . count($cleanTokens));

        $this->debugCreatorFiltering($bloodRequest, $conditions);

        return self::SUCCESS;
    }

    private function debugCreatorFiltering(BloodRequest $bloodRequest, array $conditions): void
    {
        if (!$bloodRequest->requested_by) {
            $this->warn('This request has no creator user (guest request), so creator filtering check is skipped.');
            return;
        }

        $creator = User::query()->find($bloodRequest->requested_by);
        if (!$creator) {
            $this->warn("Creator user #{$bloodRequest->requested_by} was not found.");
            return;
        }

        $reasons = [];
        $creatorBloodGroup = $this->normalizeBloodGroup($creator->blood_group);

        if ($creatorBloodGroup !== (string) $conditions['blood_group']) {
            $reasons[] = "blood_group mismatch (creator: {$creatorBloodGroup}, request: {$conditions['blood_group']})";
        }

        if ((int) $creator->district_id !== (int) $conditions['district_id']) {
            $reasons[] = "district_id mismatch (creator: {$creator->district_id}, request: {$conditions['district_id']})";
        }

        if ((bool) $creator->is_available !== true) {
            $reasons[] = 'is_available is false';
        }

        if ($creator->fcm_token === null) {
            $reasons[] = 'fcm_token is null';
        } elseif (trim((string) $creator->fcm_token) === '') {
            $reasons[] = 'fcm_token is empty string (removed before multicast)';
        }

        if ($reasons === []) {
            $this->info("Creator user #{$creator->id} passes all current FCM filters.");
            return;
        }

        $this->warn("Creator user #{$creator->id} is filtered out due to:");
        foreach ($reasons as $reason) {
            $this->line("- {$reason}");
        }
    }

    private function normalizeBloodGroup(mixed $bloodGroup): string
    {
        if ($bloodGroup instanceof \App\Enums\BloodGroup) {
            return $bloodGroup->value;
        }

        return (string) $bloodGroup;
    }
}
