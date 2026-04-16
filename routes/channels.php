<?php

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
