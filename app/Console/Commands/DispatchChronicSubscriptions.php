<?php

namespace App\Console\Commands;

use App\Jobs\DispatchEmergencyAlert;
use App\Jobs\SendEmergencyBloodRequestNotificationJob;
use App\Models\BloodRequest;
use App\Models\ChronicRequestSubscription;
use App\Models\ChronicSubscriptionBuddy;
use App\Models\District;
use App\Services\DonorMatchingService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class DispatchChronicSubscriptions extends Command
{
    protected $signature = 'subscriptions:dispatch-requests';
    protected $description = 'Dispatch due chronic subscription blood requests automatically';

    public function handle(DonorMatchingService $matcher): int
    {
        $created = 0;
        $skipped = 0;

        ChronicRequestSubscription::query()
            ->where('is_active', true)
            ->where('next_needed_at', '<=', now()->addDays(30))
            ->orderBy('id')
            ->chunkById(100, function ($subscriptions) use (&$created, &$skipped, $matcher): void {
                foreach ($subscriptions as $subscription) {
                    $nextNeededAt = Carbon::parse((string) $subscription->next_needed_at);
                    $dispatchAt = $nextNeededAt->copy()->subDays((int) $subscription->lead_time_days);
                    $cycleDate = $nextNeededAt->toDateString();

                    if (now()->lt($dispatchAt)) {
                        $skipped++;
                        continue;
                    }

                    if ($subscription->last_dispatched_for?->toDateString() === $cycleDate) {
                        $skipped++;
                        continue;
                    }

                    $existing = BloodRequest::query()
                        ->where('requested_by', $subscription->user_id)
                        ->where('blood_group', $subscription->blood_group instanceof \App\Enums\BloodGroup ? $subscription->blood_group->value : (string) $subscription->blood_group)
                        ->where('component_type', $subscription->component_type instanceof \App\Enums\BloodComponentType ? $subscription->component_type->value : (string) $subscription->component_type)
                        ->where('contact_number_normalized', $subscription->contact_number_normalized)
                        ->whereDate('needed_at', $cycleDate)
                        ->whereIn('status', ['pending', 'in_progress'])
                        ->exists();

                    if (! $existing) {
                        $request = BloodRequest::create([
                            'requested_by' => $subscription->user_id,
                            'patient_name' => $subscription->patient_name,
                            'blood_group' => $subscription->blood_group instanceof \App\Enums\BloodGroup ? $subscription->blood_group->value : (string) $subscription->blood_group,
                            'component_type' => $subscription->component_type instanceof \App\Enums\BloodComponentType ? $subscription->component_type->value : (string) $subscription->component_type,
                            'bags_needed' => $subscription->bags_needed,
                            'hospital_id' => $subscription->hospital_id,
                            'division_id' => $subscription->division_id,
                            'district_id' => $subscription->district_id,
                            'upazila_id' => $subscription->upazila_id,
                            'address' => $subscription->address,
                            'contact_name' => $subscription->contact_name,
                            'contact_number' => $subscription->contact_number,
                            'contact_number_normalized' => $subscription->contact_number_normalized,
                            'urgency' => $subscription->urgency instanceof \App\Enums\UrgencyLevel ? $subscription->urgency->value : (string) $subscription->urgency,
                            'needed_at' => $nextNeededAt,
                            'status' => 'pending',
                            'notes' => trim((string) $subscription->notes . "\n[Auto-generated from chronic plan]"),
                            'is_phone_hidden' => (bool) $subscription->is_phone_hidden,
                        ]);

                        DispatchEmergencyAlert::dispatch($request)->afterCommit();

                        // Notify the requester that the chronic request was created
                        $requesterUser = \App\Models\User::find($subscription->user_id);
                        if ($requesterUser) {
                            $requesterUser->notify(new \App\Notifications\ChronicRequestCreatedNotification($request));
                        }

                        $districtName = District::find($request->district_id)?->name ?? 'আপনার';
                        $donors = $matcher->match($request);
                        if ($donors->isNotEmpty()) {
                            $donorIds = $donors->pluck('id')->all();
                            $this->ensureBuddyPool($subscription, $donorIds);
                            $priorityDonorIds = $this->resolvePriorityDonorIds($subscription, $donorIds);

                            SendEmergencyBloodRequestNotificationJob::dispatch(
                                bloodRequestId: $request->id,
                                districtName: $districtName,
                                donorIds: $priorityDonorIds
                            );
                        }

                        $created++;
                    }

                    $subscription->update([
                        'last_dispatched_for' => $cycleDate,
                        'next_needed_at' => $nextNeededAt->copy()->addDays((int) $subscription->cadence_days),
                    ]);
                }
            });

        Log::info('Chronic subscription dispatch completed', [
            'created_requests' => $created,
            'skipped' => $skipped,
        ]);

        $this->info("Chronic subscriptions processed. Created: {$created}, skipped: {$skipped}");

        return self::SUCCESS;
    }

    /**
     * @param array<int> $donorIds
     */
    private function ensureBuddyPool(ChronicRequestSubscription $subscription, array $donorIds): void
    {
        $maxBuddies = 4;

        $existingCount = $subscription->buddies()
            ->where('is_active', true)
            ->count();

        if ($existingCount >= $maxBuddies) {
            return;
        }

        $existingIds = $subscription->buddies()
            ->pluck('donor_user_id')
            ->all();

        $nextPosition = (int) $subscription->buddies()->max('position') + 1;
        $candidates = array_values(array_filter($donorIds, fn($id) => !in_array($id, $existingIds, true)));

        foreach ($candidates as $donorId) {
            if ($existingCount >= $maxBuddies) {
                break;
            }

            ChronicSubscriptionBuddy::create([
                'subscription_id' => $subscription->id,
                'donor_user_id' => $donorId,
                'position' => $nextPosition,
                'is_active' => true,
            ]);

            $existingCount++;
            $nextPosition++;
        }
    }

    /**
     * @param array<int> $donorIds
     * @return array<int>
     */
    private function resolvePriorityDonorIds(ChronicRequestSubscription $subscription, array $donorIds): array
    {
        $fallback = $donorIds;

        $buddyIds = $subscription->buddies()
            ->where('is_active', true)
            ->orderBy('position')
            ->pluck('donor_user_id')
            ->all();

        if ($buddyIds === []) {
            return $fallback;
        }

        $eligibleBuddies = array_values(array_filter($buddyIds, fn($id) => in_array($id, $donorIds, true)));
        if ($eligibleBuddies === []) {
            return $fallback;
        }

        $startIndex = (int) ($subscription->buddy_rotation_index ?? 0);
        $count = count($eligibleBuddies);
        $normalizedIndex = $count > 0 ? $startIndex % $count : 0;

        $priorityBuddy = $eligibleBuddies[$normalizedIndex] ?? $eligibleBuddies[0];
        $remaining = array_values(array_filter($fallback, fn($id) => $id !== $priorityBuddy));
        $ordered = array_values(array_unique(array_merge([$priorityBuddy], $remaining)));

        $subscription->update([
            'buddy_rotation_index' => ($normalizedIndex + 1) % max($count, 1),
        ]);

        return $ordered;
    }
}
