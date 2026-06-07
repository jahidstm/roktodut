<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Enums\BloodJourneyStatus;
use App\Models\BloodRequestResponse;
use App\Models\ChatMessage;
use App\Models\Donation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index(BloodRequestResponse $response)
    {
        $response->loadMissing(['bloodRequest.requester', 'user']);

        $userId = Auth::id();
        abort_unless($userId, 403, 'Unauthorized');

        $isRequester = (int) ($response->bloodRequest?->requested_by ?? 0) === (int) $userId;
        $isDonor = (int) $response->user_id === (int) $userId;
        abort_unless($isRequester || $isDonor, 403, 'Unauthorized');

        $journeyStatus = Donation::query()
            ->where('blood_request_id', $response->blood_request_id)
            ->where('donor_id', $response->user_id)
            ->value('journey_status');

        $journeyValue = $journeyStatus instanceof BloodJourneyStatus
            ? $journeyStatus->value
            : ($journeyStatus ? (string) $journeyStatus : null);

        $isClosed = in_array($journeyValue, ['delivered', 'discarded'], true);

        $oppositePartyPhone = $isRequester
            ? ($response->user?->phone ?? null)
            : ($response->bloodRequest?->requester?->phone ?? null);

        $messages = ChatMessage::query()
            ->where('blood_request_response_id', $response->id)
            ->orderBy('id')
            ->get(['id', 'sender_id', 'message', 'created_at'])
            ->map(fn(ChatMessage $message) => [
                'id' => $message->id,
                'sender_id' => $message->sender_id,
                'message' => $message->message,
                'created_at' => $message->created_at?->toISOString(),
            ])
            ->values();

        $storeUrl = url()->current();

        return view('chat.show', [
            'response' => $response,
            'messages' => $messages,
            'oppositePartyPhone' => $oppositePartyPhone,
            'journeyStatus' => $journeyStatus,
            'isClosed' => $isClosed,
            'storeUrl' => $storeUrl,
            'isRequester' => $isRequester,
        ]);
    }

    public function store(Request $request, BloodRequestResponse $response)
    {
        $response->loadMissing(['bloodRequest', 'user']);

        $user = $request->user();
        abort_unless($user, 403, 'Unauthorized');

        $isRequester = (int) ($response->bloodRequest?->requested_by ?? 0) === (int) $user->id;
        $isDonor = (int) $response->user_id === (int) $user->id;
        abort_unless($isRequester || $isDonor, 403, 'Unauthorized');

        $journeyStatus = Donation::query()
            ->where('blood_request_id', $response->blood_request_id)
            ->where('donor_id', $response->user_id)
            ->value('journey_status');

        $journeyValue = $journeyStatus instanceof BloodJourneyStatus
            ? $journeyStatus->value
            : ($journeyStatus ? (string) $journeyStatus : null);

        if (in_array($journeyValue, ['delivered', 'discarded'], true)) {
            abort(403, 'Chat is closed');
        }

        $validated = $request->validate([
            'message' => ['required', 'string', 'max:1000'],
        ]);

        $messageText = trim($validated['message']);
        if ($messageText === '') {
            return response()->json(['message' => 'Message cannot be empty.'], 422);
        }

        $message = ChatMessage::create([
            'blood_request_response_id' => $response->id,
            'sender_id' => $user->id,
            'message' => $messageText,
            'is_read' => false,
        ]);

        try {
            broadcast(new MessageSent($message))->toOthers();
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Chat broadcast failed: ' . $e->getMessage());
        }

        // Notify the OTHER user
        $requesterId = (int) ($response->bloodRequest?->requested_by ?? 0);
        $donorId = (int) $response->user_id;
        $otherUserId = ((int) $user->id === $donorId) ? $requesterId : $donorId;

        if ($otherUserId > 0) {
            $otherUser = \App\Models\User::find($otherUserId);
            if ($otherUser) {
                $otherUser->notify(new \App\Notifications\NewChatMessageNotification($response, $user, $messageText));
            }
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => [
                    'id' => $message->id,
                    'sender_id' => $message->sender_id,
                    'message' => $message->message,
                    'created_at' => $message->created_at?->toISOString(),
                ],
            ]);
        }

        return back();
    }
}
