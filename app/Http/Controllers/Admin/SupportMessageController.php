<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * SupportMessageController (Admin Inbox)
 *
 * যোগাযোগ বার্তার অ্যাডমিন ইনবক্স:
 *   - সব বার্তার তালিকা (স্প্যাম বাদে, eager load করা)
 *   - একটি বার্তার বিস্তারিত
 *   - স্ট্যাটাস আপডেট
 *
 * N+1 নেই: user eager load করা হয়েছে।
 */
class SupportMessageController extends Controller
{
    // ─── তালিকা ───────────────────────────────────────────────────────────────

    public function index(Request $request): View
    {
        $status = $request->query('status'); // ফিল্টার: new | in_progress | resolved | spam

        $messages = ContactMessage::with('user:id,name,email,profile_image')
            ->when(
                $status && in_array($status, ['new', 'in_progress', 'resolved', 'spam']),
                fn($q) => $q->where('status', $status),
                fn($q) => $q->notSpam()  // ডিফল্ট: স্প্যাম বাদে সব
            )
            ->latest()
            ->paginate(20)
            ->withQueryString(); // ফিল্টার প্যারামিটার pagination লিংকে রাখে

        // সারসংক্ষেপ কাউন্ট (filter bar-এর জন্য)
        $counts = [
            'new'         => ContactMessage::where('status', 'new')->count(),
            'in_progress' => ContactMessage::where('status', 'in_progress')->count(),
            'resolved'    => ContactMessage::where('status', 'resolved')->count(),
            'spam'        => ContactMessage::where('status', 'spam')->count(),
            'total'       => ContactMessage::notSpam()->count(),
        ];

        return view('admin.support.index', compact('messages', 'counts', 'status'));
    }

    // ─── বিস্তারিত ────────────────────────────────────────────────────────────

    public function show(ContactMessage $message): View
    {
        $message->load('user:id,name,email,profile_image,phone,role');

        // নতুন বার্তা খুললে 'in_progress' করে দাও
        if ($message->status === 'new') {
            $message->update(['status' => 'in_progress']);
        }

        return view('admin.support.show', compact('message'));
    }

    // ─── স্ট্যাটাস আপডেট ────────────────────────────────────────────────────

    public function updateStatus(Request $request, ContactMessage $message): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:new,in_progress,resolved,spam'],
        ], [
            'status.required' => 'স্ট্যাটাস নির্বাচন করুন।',
            'status.in'       => 'অবৈধ স্ট্যাটাস নির্বাচিত হয়েছে।',
        ]);

        $message->update(['status' => $validated['status']]);

        $statusLabel = $message->fresh()->status_label;

        return redirect()->back()
            ->with('success', "বার্তার স্ট্যাটাস \"{$statusLabel}\" করা হয়েছে।");
    }
}
