<?php

namespace App\Http\Controllers;

use App\Models\BloodRequest;
use App\Models\BloodRequestResponse;
use App\Notifications\BloodResponseNotification;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BloodRequestResponseController extends Controller
{
    /**
     * রিকোয়েস্টে ডোনারের রেসপন্স সেভ করা এবং নোটিফিকেশন পাঠানো
     *
     * 🔥 The Unidirectional Push Architecture:
     * যদি bloodRequest->is_phone_hidden = true হয়, তবে:
     * - ডোনার "রক্ত দিতে চাই" ক্লিক করার সাথে সাথেই
     * - রোগীর Telegram-এ ডোনারের নম্বর পৌঁছে যাবে
     * - কোনো accept/reject লজিক ছাড়াই
     * - Analytics-এর জন্য status = 'contacted' সেভ হবে
     */
    public function store(Request $request, BloodRequest $bloodRequest)
    {
        // ১. ভ্যালিডেশন
        $request->validate([
            'status' => 'required|in:pending,declined,withdrawn',
        ]);

        $user = $request->user();

        // ২. মেডিকেল কমপ্লায়েন্স চেক (৯০-দিনের রুল)
        if ($request->status === 'pending' && !$user->canDonate()) {
            $remainingDays = $user->daysUntilNextDonation();
            return back()->with('error', "মেডিকেল গাইডলাইন অনুযায়ী আপনি আগামী {$remainingDays} দিন রক্ত দিতে পারবেন না।");
        }

        // ৩. নিজের রিকোয়েস্টে রেসপন্স করা যাবে না
        abort_if(
            $bloodRequest->requested_by === $user->id,
            403,
            'আপনি নিজের রিকোয়েস্টে রেসপন্স করতে পারবেন না।'
        );

        // 🛡️ দ্য স্প্যামিং লুপহোল ফিক্স: রেট-লিমিটিং এবং ইউনিক কনস্ট্রেইন্ট
        $existingResponse = BloodRequestResponse::where('blood_request_id', $bloodRequest->id)
            ->where('user_id', $user->id)
            ->first();

        // ─────────────────────────────────────────────────────────────
        // 🚨 THE UNIDIRECTIONAL PUSH FLOW (is_phone_hidden = true)
        // ─────────────────────────────────────────────────────────────
        if ($bloodRequest->is_phone_hidden && $request->status === 'pending') {

            // যদি ডোনার আগে থেকেই পিং পাঠিয়ে থাকে, তবে ব্লক করো
            if ($existingResponse && $existingResponse->is_ping_sent) {
                return back()->with('error', 'আপনি ইতিমধ্যে রোগীকে পিং করেছেন।');
            }

            // ৪a. রেসপন্স সেভ করো — status = 'contacted' (analytics tracked)
            $response = BloodRequestResponse::updateOrCreate(
                [
                    'blood_request_id' => $bloodRequest->id,
                    'user_id'          => $user->id,
                ],
                [
                    'status'       => 'contacted',
                    'is_ping_sent' => true,
                    'pinged_at'    => now(),
                ]
            );

            // ৪b. রোগীর Telegram-এ ডোনারের নম্বর সরাসরি পুশ করো
            $requester = $bloodRequest->requester;
            if ($requester && $requester->telegram_chat_id) {

                $bloodGroup = is_object($user->blood_group)
                    ? $user->blood_group->value
                    : (string) $user->blood_group;

                $donorPhone = $user->phone ?? 'উল্লেখ নেই';

                try {
                    app(TelegramService::class)->sendDonorPingToRequester(
                        requesterChatId: $requester->telegram_chat_id,
                        donorName:       $user->name,
                        donorBloodGroup: $bloodGroup,
                        donorPhone:      $donorPhone,
                        requestUrl:      route('requests.show', $bloodRequest->id),
                    );
                } catch (\Throwable $e) {
                    Log::error('[DonorPing] Telegram send failed: ' . $e->getMessage());
                }
            }

            return back()->with('success', '✅ আপনার সাড়া পাঠানো হয়েছে! রোগীর পরিবার তার ফোনে আপনার নম্বর পেয়েছেন এবং যেকোনো মুহূর্তে কল করবেন।');
        }

        // ─────────────────────────────────────────────────────────────
        // 📞 STANDARD FLOW (is_phone_hidden = false)
        // ─────────────────────────────────────────────────────────────

        if ($request->status === 'pending' && $existingResponse && $existingResponse->status === 'pending') {
            return back()->with('error', 'আপনি ইতিমধ্যে এই রিকোয়েস্টে সাড়া দিয়েছেন। রোগীর অনুমোদনের জন্য অপেক্ষা করুন।');
        }

        // ৫. রেসপন্স সেভ বা আপডেট
        $response = BloodRequestResponse::updateOrCreate(
            [
                'blood_request_id' => $bloodRequest->id,
                'user_id'          => $user->id,
            ],
            [
                'status' => $request->status,
            ]
        );

        // ৬. ইন-অ্যাপ নোটিফিকেশন পাঠানো (শুধু pending হলে)
        $owner = $bloodRequest->requester;
        if ($owner && $request->status === 'pending') {
            $owner->notify(new BloodResponseNotification($bloodRequest, $user, $request->status));
        }

        // ৭. ইউজার ফিডব্যাক
        $message = $request->status === 'pending'
            ? '✅ আপনার আগ্রহ জানানো হয়েছে। রোগীর অনুমোদনের জন্য অপেক্ষা করুন।'
            : 'আপনি রিকোয়েস্টটি ডিক্লাইন/ক্যান্সেল করেছেন।';

        return back()->with('success', $message);
    }

    /**
     * রোগীর ড্যাশবোর্ড থেকে ডোনারকে Accept বা Decline করা (Standard Flow এর জন্য)
     */
    public function updateStatus(Request $request, BloodRequestResponse $response)
    {
        $request->validate([
            'status' => 'required|in:accepted,rejected',
        ]);

        $bloodRequest = $response->bloodRequest;

        // শুধু রিকোয়েস্টের মালিক (রোগী) এটি করতে পারবে
        abort_if(
            $bloodRequest->requested_by !== $request->user()->id,
            403,
            'আপনি এই অ্যাকশনটি করতে পারবেন না।'
        );

        \Illuminate\Support\Facades\DB::transaction(function () use ($request, $response, $bloodRequest) {
            $response->update([
                'status' => $request->status,
            ]);

            if ($request->status === 'accepted') {
                $bloodRequest->update([
                    'status' => 'in_progress',
                ]);
            }
        });

        $msg = $request->status === 'accepted'
            ? 'ডোনারকে সফলভাবে অ্যাকসেপ্ট করা হয়েছে। এখন আপনি তার ফোন নম্বর দেখতে পারবেন।'
            : 'ডোনারকে ডিক্লাইন করা হয়েছে।';

        return back()->with('success', $msg);
    }
}