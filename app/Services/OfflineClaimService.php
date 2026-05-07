<?php

namespace App\Services;

use App\DataTransferObjects\OfflineClaimData;
use App\Exceptions\OfflineClaimBlockedException;
use App\Models\BloodRequest;
use App\Models\OfflineDonationClaim;
use App\Models\User;
use App\Notifications\OfflineClaimVerificationNotification;
use App\Support\PhoneNormalizer;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;

class OfflineClaimService
{
    public function __construct(
        private readonly GamificationService $gamification,
    ) {}

    public function processClaim(User $donor, OfflineClaimData $data, string $ipAddress): OfflineDonationClaim
    {
        if (($donor->spam_strikes ?? 0) >= 3) {
            throw new OfflineClaimBlockedException('আপনার অ্যাকাউন্ট সাময়িকভাবে ক্লেইম সাবমিট করতে পারছে না।');
        }

        $this->assertCooldown($donor, $data->donationDate);

        $normalizedPhone = $this->normalizeToPlus880($data->recipientPhone);
        $ipHash = $this->hashIp($ipAddress);
        $matchedRequest = $this->findMatchedRequest($normalizedPhone, $data->donationDate);
        $hasRecipientAccount = (bool) $matchedRequest?->requester;
        $expiresAt = now()->addHours(48);

        $claim = OfflineDonationClaim::create([
            'donor_id' => $donor->id,
            'recipient_phone' => $data->recipientPhone,
            'recipient_phone_normalized' => $normalizedPhone,
            'patient_name' => $data->patientName,
            'district_id' => $data->districtId,
            'hospital_name' => $data->hospitalName,
            'donation_date' => $data->donationDate->toDateString(),
            'proof_path' => $data->proofPath,
            'status' => ($matchedRequest && $hasRecipientAccount) ? 'pending' : 'admin_review',
            'verification_method' => null,
            'matched_request_id' => $matchedRequest?->id,
            'verified_by_id' => null,
            'risk_score' => $this->calculateRiskScore($data->proofPath, (bool) $matchedRequest),
            'expires_at' => ($matchedRequest && $hasRecipientAccount) ? $expiresAt : null,
            'ip_hash' => $ipHash,
            'rejection_reason' => null,
        ]);

        if ($matchedRequest && $hasRecipientAccount) {
            $verifyUrl = URL::temporarySignedRoute('offline.verify', $expiresAt, ['claim' => $claim->id]);
            $matchedRequest->requester->notify(new OfflineClaimVerificationNotification($claim->loadMissing('donor'), $verifyUrl));
        }

        return $claim;
    }

    public function confirmByRecipient(OfflineDonationClaim $claim, bool $approved): OfflineDonationClaim
    {
        if ($claim->status !== 'pending') {
            throw ValidationException::withMessages([
                'claim' => 'এই ক্লেইমটি ইতোমধ্যে প্রসেস করা হয়েছে।',
            ]);
        }

        return DB::transaction(function () use ($claim, $approved) {
            $claim->refresh();
            $claim->loadMissing(['donor', 'matchedRequest']);

            if ($approved) {
                $this->gamification->processOfflineDonationReward(
                    donor: $claim->donor,
                    bloodRequest: $claim->matchedRequest,
                    donationDate: Carbon::parse((string) $claim->donation_date),
                    rewardPercentage: 100,
                );

                $claim->update([
                    'status' => 'verified',
                    'verification_method' => 'recipient',
                    'rejection_reason' => null,
                ]);
            } else {
                $claim->update([
                    'status' => 'rejected',
                    'verification_method' => 'recipient',
                    'rejection_reason' => 'Denied by recipient',
                ]);
                $claim->donor()->increment('spam_strikes');
            }

            return $claim->fresh(['donor', 'matchedRequest']);
        });
    }

    public function approveByAdmin(OfflineDonationClaim $claim, User $admin): OfflineDonationClaim
    {
        if (! $admin->isAdmin()) {
            throw ValidationException::withMessages([
                'admin' => 'শুধুমাত্র অ্যাডমিন এই ক্লেইম অনুমোদন করতে পারবেন।',
            ]);
        }

        if ($claim->status !== 'admin_review') {
            throw ValidationException::withMessages([
                'claim' => 'শুধুমাত্র admin_review ক্লেইম অনুমোদন করা যাবে।',
            ]);
        }

        $rewardPercentage = $claim->proof_path ? 100 : 50;

        return DB::transaction(function () use ($claim, $admin, $rewardPercentage) {
            $claim->refresh();
            $claim->loadMissing(['donor', 'matchedRequest']);

            $this->gamification->processOfflineDonationReward(
                donor: $claim->donor,
                bloodRequest: $claim->matchedRequest,
                donationDate: Carbon::parse((string) $claim->donation_date),
                rewardPercentage: $rewardPercentage,
            );

            $claim->update([
                'status' => 'verified',
                'verification_method' => 'admin',
                'verified_by_id' => $admin->id,
                'rejection_reason' => null,
            ]);

            return $claim->fresh(['donor', 'matchedRequest']);
        });
    }

    private function normalizeToPlus880(string $phone): string
    {
        $local = PhoneNormalizer::normalizeBdPhone($phone);
        if (preg_match('/^01\d{9}$/', $local) !== 1) {
            throw ValidationException::withMessages([
                'recipient_phone' => 'সঠিক মোবাইল নম্বর দিন (যেমন: 01XXXXXXXXX)।',
            ]);
        }

        return '+880'.substr($local, 1);
    }

    private function hashIp(string $ipAddress): string
    {
        return hash('sha256', $ipAddress.'|'.(string) config('app.key'));
    }

    private function assertCooldown(User $donor, CarbonImmutable $donationDate): void
    {
        if (! $donor->last_donated_at) {
            return;
        }

        $cooldownEnd = CarbonImmutable::parse((string) $donor->last_donated_at)->addDays(120)->startOfDay();
        if ($cooldownEnd->gt($donationDate)) {
            throw ValidationException::withMessages([
                'donation_date' => 'শেষ রক্তদানের ১২০ দিন পূর্ণ হওয়ার আগে নতুন ক্লেইম করা যাবে না।',
            ]);
        }
    }

    private function findMatchedRequest(string $normalizedPhone, CarbonImmutable $donationDate): ?BloodRequest
    {
        $startDate = $donationDate->subDays(7)->startOfDay();
        $endDate = $donationDate->addDays(7)->endOfDay();

        $localNormalized = '0'.ltrim(substr($normalizedPhone, 4), '0');

        return BloodRequest::query()
            ->with('requester:id,name')
            ->whereIn('contact_number_normalized', [$normalizedPhone, $localNormalized])
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate])
                    ->orWhereBetween('needed_at', [$startDate, $endDate]);
            })
            ->get()
            ->sortBy(function (BloodRequest $request) use ($donationDate): int {
                $referenceDate = $request->needed_at ?? $request->created_at;

                return abs(CarbonImmutable::parse((string) $referenceDate)->diffInDays($donationDate, false));
            })
            ->first();
    }

    private function calculateRiskScore(?string $proofPath, bool $hasMatch): int
    {
        $score = 0;
        if (! $proofPath) {
            $score += 30;
        }
        if (! $hasMatch) {
            $score += 60;
        }

        return $score;
    }
}
