<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class SuspendedDonorController extends Controller
{
    public function uploadMedicalClearance(Request $request): RedirectResponse
    {
        $request->validate([
            'medical_clearance_document' => [
                'required',
                'file',
                'max:4096',
                'mimes:pdf,jpg,jpeg,png',
            ],
        ], [
            'medical_clearance_document.required' => 'মেডিকেল ক্লিয়ারেন্স ডকুমেন্ট আপলোড করুন।',
            'medical_clearance_document.mimes' => 'শুধুমাত্র PDF, JPG বা PNG ফাইল আপলোড করা যাবে।',
            'medical_clearance_document.max' => 'ফাইলের সাইজ সর্বোচ্চ 4MB হতে হবে।',
        ]);

        $user = $request->user();
        $file = $request->file('medical_clearance_document');

        if (!$file || !$user) {
            return back()->with('error', 'ডকুমেন্ট আপলোড করা সম্ভব হয়নি। আবার চেষ্টা করুন।');
        }

        if ($user->medical_clearance_document && Storage::disk('local')->exists($user->medical_clearance_document)) {
            Storage::disk('local')->delete($user->medical_clearance_document);
        }

        $filename = 'clearance_' . $user->id . '_' . now()->format('YmdHis') . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('private/medical_documents', $filename, 'local');

        $user->update([
            'medical_clearance_document' => $path,
        ]);

        return back()->with('success', '✅ মেডিকেল ক্লিয়ারেন্স ডকুমেন্ট সফলভাবে জমা হয়েছে। অ্যাডমিন যাচাই করবেন।');
    }
}
