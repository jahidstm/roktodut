<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BloodRequest;
use App\Models\District;
use App\Models\Division;
use App\Models\Upazila;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InternalNlpRequestController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'blood_group' => ['required', 'string', 'in:A+,A-,B+,B-,AB+,AB-,O+,O-'],
            'urgency' => ['required', 'string', 'in:emergency,high,medium'],
            'location_text' => ['required', 'string', 'max:255'],
            'units_needed' => ['required', 'integer', 'min:1', 'max:20'],
            'confidence_score' => ['required', 'numeric', 'min:0', 'max:1'],
            'requested_by' => ['nullable', 'integer', 'exists:users,id'],
            'telegram_chat_id' => ['nullable', 'string', 'max:255'],
            'raw_text' => ['nullable', 'string', 'max:1000'],
            'division_id' => ['nullable', 'integer', 'exists:divisions,id'],
            'district_id' => ['nullable', 'integer', 'exists:districts,id'],
            'upazila_id' => ['nullable', 'integer', 'exists:upazilas,id'],
        ]);

        $locationIds = $this->resolveLocationIds($validated);
        if ($locationIds === null) {
            return response()->json([
                'message' => 'Location bootstrap data missing (divisions/districts/upazilas).',
            ], 422);
        }

        $units = (int) $validated['units_needed'];
        $rawText = trim((string) ($validated['raw_text'] ?? ''));

        $bloodRequest = BloodRequest::create([
            'requested_by' => $validated['requested_by'] ?? null,
            'patient_name' => null,
            'blood_group' => $validated['blood_group'],
            'location_text' => $validated['location_text'],
            'bags_needed' => $units,
            'units_needed' => $units,
            'division_id' => $locationIds['division_id'],
            'district_id' => $locationIds['district_id'],
            'upazila_id' => $locationIds['upazila_id'],
            'address' => $validated['location_text'],
            'contact_name' => 'NLP Intake',
            'contact_number' => (string) env('NLP_PLACEHOLDER_PHONE', '00000000000'),
            'contact_number_normalized' => null,
            'urgency' => $validated['urgency'],
            'status' => 'nlp_pending',
            'ml_confidence_score' => (float) $validated['confidence_score'],
            'notes' => $this->buildNlpNotes(
                telegramChatId: $validated['telegram_chat_id'] ?? null,
                rawText: $rawText
            ),
        ]);

        $adminChatId = config('services.telegram.admin_chat_id');
        if ($adminChatId) {
            $msg = "🤖 <b>New NLP Request Needs Approval</b>\n\n";
            $msg .= "<b>ID:</b> {$bloodRequest->id}\n";
            $msg .= "<b>Group:</b> {$bloodRequest->blood_group->value}\n";
            $msg .= "<b>Location:</b> {$bloodRequest->location_text}\n";
            $msg .= "<b>Urgency:</b> {$bloodRequest->urgency}\n";
            $msg .= "<b>Confidence:</b> {$bloodRequest->ml_confidence_score}\n\n";
            $msg .= "<i>Reply with </i><code>/approve {$bloodRequest->id}</code><i> to publish this request.</i>";
            
            try {
                app(\App\Services\TelegramService::class)->send($adminChatId, $msg);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Failed to notify admin of NLP request: " . $e->getMessage());
            }
        }

        return response()->json([
            'id' => $bloodRequest->id,
            'status' => $bloodRequest->status,
            'message' => 'NLP request stored for human approval.',
        ], 201);
    }

    /**
     * @param array<string, mixed> $validated
     * @return array{division_id:int,district_id:int,upazila_id:int}|null
     */
    private function resolveLocationIds(array $validated): ?array
    {
        if (
            isset($validated['division_id'], $validated['district_id'], $validated['upazila_id'])
            && $validated['division_id']
            && $validated['district_id']
            && $validated['upazila_id']
        ) {
            return [
                'division_id' => (int) $validated['division_id'],
                'district_id' => (int) $validated['district_id'],
                'upazila_id' => (int) $validated['upazila_id'],
            ];
        }

        $divisionId = Division::query()->value('id');
        $districtId = District::query()->value('id');
        $upazilaId = Upazila::query()->value('id');

        if (!$divisionId || !$districtId || !$upazilaId) {
            return null;
        }

        return [
            'division_id' => (int) $divisionId,
            'district_id' => (int) $districtId,
            'upazila_id' => (int) $upazilaId,
        ];
    }

    private function buildNlpNotes(?string $telegramChatId, string $rawText): string
    {
        $parts = ['[NLP Intake Pending Review]'];

        if ($telegramChatId !== null && $telegramChatId !== '') {
            $parts[] = "telegram_chat_id: {$telegramChatId}";
        }

        if ($rawText !== '') {
            $parts[] = "raw_text: {$rawText}";
        }

        return implode("\n", $parts);
    }
}
