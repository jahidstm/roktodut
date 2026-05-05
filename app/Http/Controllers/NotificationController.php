<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * নোটিফিকেশন রিড হিসেবে মার্ক করা এবং ধরন অনুযায়ী নির্দিষ্ট পেজে রিডাইরেক্ট করা
     */
    public function markAsRead(Request $request, $id)
    {
        $notification = $request->user()->notifications()->findOrFail($id);

        // নোটিফিকেশনটি 'Read' হিসেবে মার্ক করা
        $notification->markAsRead();

        // 🧠 ডাইনামিক রিডাইরেক্ট লজিক (Smart Routing)
        if (isset($notification->data['url'])) {
            return redirect($notification->data['url']);
        } elseif (isset($notification->data['request_id'])) {
            return redirect()->route('requests.show', $notification->data['request_id']);
        }

        return redirect()->route('dashboard');
    }

    /**
     * সকল unread নোটিফিকেশন একসাথে read মার্ক করা।
     * POST /notifications/read-all
     */
    public function markAllRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();

        if ($request->expectsJson()) {
            return response()->json(['status' => 'ok']);
        }

        return back()->with('success', 'সব নোটিফিকেশন পড়া হয়েছে বলে চিহ্নিত করা হয়েছে।');
    }

    /**
     * সাম্প্রতিক নোটিফিকেশন JSON আকারে ফেরত দেয় (dropdown lazy-load)
     * GET /notifications/recent
     */
    public function getRecent(Request $request)
    {
        $user = $request->user();

        $notifications = $user->notifications()
            ->latest()
            ->limit(10)
            ->get()
            ->map(function ($n) {
                return [
                    'id'         => $n->id,
                    'message'    => $n->data['message']    ?? 'নতুন নোটিফিকেশন',
                    'blood_group'=> $n->data['blood_group'] ?? null,
                    'urgency'    => $n->data['urgency']    ?? 'normal',
                    'url'        => route('notifications.read', $n->id),
                    'read_at'    => $n->read_at?->toISOString(),
                    'time_ago'   => $n->created_at->diffForHumans(),
                ];
            });

        return response()->json([
            'notifications' => $notifications,
            'unread_count'  => $user->unreadNotifications()->count(),
        ]);
    }
}