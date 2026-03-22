<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
            // ১. ভেরিফিকেশন বা অন্যান্য নোটিফিকেশন (যাদের নির্দিষ্ট URL আছে)
            return redirect($notification->data['url']);
        } elseif (isset($notification->data['request_id'])) {
            // ২. পুরনো ব্লাড রিকোয়েস্ট নোটিফিকেশন (যাদের request_id আছে)
            return redirect()->route('requests.show', $notification->data['request_id']);
        }

        // ৩. কোনো কন্ডিশন না মিললে ডিফল্ট ড্যাশবোর্ডে যাবে
        return redirect()->route('dashboard');
    }
}