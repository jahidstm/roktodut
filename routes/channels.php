<?php

use App\Models\BloodRequestResponse;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| এখানে application-এর সকল broadcast channel authorization define করা হয়।
| Private চ্যানেল শুধুমাত্র authorized user-ই শুনতে পারবে।
|
*/

/**
 * Private User Channel
 *
 * প্রতিটি user তার নিজের channel-এ listen করতে পারবে।
 * অন্য user-এর channel subscribe করলে 403 পাবে।
 *
 * Client-side: Echo.private(`user.${userId}`)
 */
Broadcast::channel('user.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

/**
 * Private Chat Channel
 *
 * শুধু রিকোয়েস্টার এবং সেই নির্দিষ্ট একসেপ্টেড ডোনারই listen করতে পারবে।
 *
 * Client-side: Echo.private(`chat.response.${responseId}`)
 */
Broadcast::channel('chat.response.{responseId}', function ($user, $responseId) {
    $response = BloodRequestResponse::query()
        ->with('bloodRequest:id,requested_by')
        ->find($responseId);

    if (!$response || $response->status !== 'accepted') {
        return false;
    }

    $requesterId = (int) ($response->bloodRequest?->requested_by ?? 0);

    return (int) $user->id === (int) $response->user_id
        || (int) $user->id === $requesterId;
});
