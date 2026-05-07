<?php

namespace App\DataTransferObjects;

use Carbon\CarbonImmutable;

final class OfflineClaimData
{
    public function __construct(
        public readonly string $recipientPhone,
        public readonly string $patientName,
        public readonly int $districtId,
        public readonly ?string $hospitalName,
        public readonly CarbonImmutable $donationDate,
        public readonly ?string $proofPath,
    ) {}

    public static function fromValidated(array $validated, ?string $proofPath = null): self
    {
        return new self(
            recipientPhone: (string) $validated['recipient_phone'],
            patientName: (string) $validated['patient_name'],
            districtId: (int) $validated['district_id'],
            hospitalName: isset($validated['hospital_name']) ? (string) $validated['hospital_name'] : null,
            donationDate: CarbonImmutable::parse((string) $validated['donation_date'])->startOfDay(),
            proofPath: $proofPath,
        );
    }
}
