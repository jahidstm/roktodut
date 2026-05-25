<?php

namespace App\Services;

use App\Models\HealthRecord;
use Illuminate\Support\Collection;

class HealthVelocityService
{
    /**
     * @param \Illuminate\Support\Collection<int, HealthRecord> $records
     * @return array{nudges: array<int, string>, charts: array<string, array<int, mixed>>}
     */
    public function analyze(Collection $records): array
    {
        $nudges = [];

        $sorted = $records->sortBy('recorded_at')->values();
        $latestTwo = $sorted->whereNotNull('hemoglobin_level')->take(-2)->values();

        if ($latestTwo->count() === 2) {
            $prev = (float) $latestTwo[0]->hemoglobin_level;
            $curr = (float) $latestTwo[1]->hemoglobin_level;
            $drop = $prev - $curr;

            if ($drop > 0.5) {
                $nudges[] = 'আপনার হিমোগ্লোবিন ড্রপ রেট বেশি, আগামী ডোনেশনের আগে আয়রন-সমৃদ্ধ খাবার নিশ্চিত করুন।';
            }
        }

        $latestBp = $sorted->whereNotNull('systolic_bp')
            ->whereNotNull('diastolic_bp')
            ->last();

        if ($latestBp) {
            $sys = (int) $latestBp->systolic_bp;
            $dia = (int) $latestBp->diastolic_bp;

            if ($sys >= 140 || $dia >= 90) {
                $nudges[] = 'আপনার সাম্প্রতিক রক্তচাপ উচ্চ। পরবর্তী ডোনেশনের আগে বিশ্রাম ও পর্যাপ্ত পানি গ্রহণ নিশ্চিত করুন।';
            } elseif ($sys <= 90 || $dia <= 60) {
                $nudges[] = 'আপনার সাম্প্রতিক রক্তচাপ তুলনামূলক কম। পর্যাপ্ত খাবার ও হাইড্রেশন নিশ্চিত করুন।';
            }
        }

        $charts = [
            'labels' => [],
            'hemoglobin' => [],
            'systolic' => [],
            'diastolic' => [],
        ];

        foreach ($sorted as $record) {
            $charts['labels'][] = $record->recorded_at?->format('d M');
            $charts['hemoglobin'][] = $record->hemoglobin_level !== null ? (float) $record->hemoglobin_level : null;
            $charts['systolic'][] = $record->systolic_bp !== null ? (int) $record->systolic_bp : null;
            $charts['diastolic'][] = $record->diastolic_bp !== null ? (int) $record->diastolic_bp : null;
        }

        return [
            'nudges' => $nudges,
            'charts' => $charts,
        ];
    }
}
