<?php

namespace App\Jobs;

use App\Models\BloodRequest;
use App\Services\FcmDispatchService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DispatchEmergencyAlert implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public BloodRequest $bloodRequest
    ) {}

    public function handle(FcmDispatchService $fcmDispatchService): void
    {
        $fcmDispatchService->sendEmergencyAlertToDonors($this->bloodRequest);
    }
}
