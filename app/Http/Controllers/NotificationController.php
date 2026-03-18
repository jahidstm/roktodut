<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * নোটিফিকেশন রিড হিসেবে মার্ক করা এবং রিকোয়েস্ট পেজে রিডাইরেক্ট করা
     */
    public function markAsRead(Request $request, $id)
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        
        $notification->markAsRead();

        // নোটিফিকেশনের ডেটা থেকে রিকোয়েস্টের আইডি নিয়ে সেখানে রিডাইরেক্ট করা
        return redirect()->route('requests.show', $notification->data['request_id']);
    }
}