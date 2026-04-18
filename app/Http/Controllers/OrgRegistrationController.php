<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Organization;
use App\Notifications\AdminTaskNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class OrgRegistrationController extends Controller
{
    public function create()
    {
        return view('org.register');
    }

    public function store(Request $request)
    {
        // ১. ফর্ম ভ্যালিডেশন
        $request->validate([
            // অর্গানাইজেশন ইনফো
            'org_name' => 'required|string|max:255',
            'org_type' => 'required|string|in:hospital,blood_bank,university_club,ngo,voluntary',
            'short_name' => 'nullable|string|max:50',
            'established_year' => 'nullable|digits:4|integer|min:1900|max:' . date('Y'),

            // কন্টাক্ট ইনফো (users টেবিলেও ইমেইল ইউনিক হতে হবে)
            'official_email' => 'required|string|email|max:255|unique:users,email|unique:organizations,email',
            'contact_number' => 'required|string|max:20',
            'division_id' => 'required|exists:divisions,id',
            'district_id' => 'required|exists:districts,id',
            'upazila_id' => 'required|exists:upazilas,id',
            'address_details' => 'required|string|max:500',

            // অ্যাডমিন ইনফো
            'admin_name' => 'required|string|max:255',
            'admin_designation' => 'required|string|max:100',
            'admin_phone' => 'required|string|max:20|unique:users,phone',
            'password' => 'required|string|min:8|confirmed',

            // ডকুমেন্টস
            'official_document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        try {
            DB::beginTransaction();

            // ২. ফাইল আপলোড হ্যান্ডেলিং
            $documentPath = $request->file('official_document')->store('org_documents', 'private');
            $logoPath = $request->hasFile('logo') ? $request->file('logo')->store('org_logos', 'public') : null;

            // ৩. প্রথমে ইউজারের অ্যাকাউন্ট তৈরি (Org Admin হিসেবে)
            $adminUser = User::create([
                'name' => $request->admin_name,
                'email' => $request->official_email,
                'password' => Hash::make($request->password),
                'phone' => $request->admin_phone,
                'role' => 'org_admin',
                'is_onboarded' => true,
                'nid_status' => 'pending',
            ]);

            // ৪. এরপর অর্গানাইজেশন তৈরি (ডাটাবেসের সঠিক কলাম নামের সাথে ম্যাপিং)
            $organization = Organization::create([
                'name' => $request->org_name,
                'short_name' => $request->short_name,
                'type' => $request->org_type,
                'established_year' => $request->established_year,
                'email' => $request->official_email,
                'phone' => $request->contact_number,

                // 🎯 THE FIX: Data mapping based on your DB columns
                'division' => $request->division_id,
                'district' => $request->district_id,
                'upazila' => $request->upazila_id,

                'address' => $request->address_details,
                'admin_id' => $adminUser->id,
                'document_path' => $documentPath,
                'logo' => $logoPath, // mapped to 'logo' instead of 'logo_path'
                'status' => 'pending',
            ]);

            // ৫. অ্যাডমিন ইউজারের সাথে অর্গানাইজেশন আইডি লিংক করা
            $adminUser->update(['organization_id' => $organization->id]);

            DB::commit();

            $admins = User::where('role', 'admin')->get();
            if ($admins->isNotEmpty()) {
                Notification::send($admins, new AdminTaskNotification(
                    message: "নতুন {$organization->name} যাচাইয়ের জন্য পেন্ডিং আছে।",
                    url: route('admin.org.reviews'),
                    title: '🏥 পেন্ডিং অর্গানাইজেশন/হাসপাতাল যাচাই',
                    taskType: 'organization_review',
                ));
            }

            return redirect()->route('login')->with('success', 'আপনার রেজিস্ট্রেশন সফল হয়েছে। আমাদের টিম আপনার তথ্যগুলো যাচাই করে শীঘ্রই অ্যাকাউন্টটি অ্যাক্টিভ করবে।');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Org Registration Failed: ' . $e->getMessage());
            // 🚀 ডিবাগিংয়ের জন্য আসল এরর মেসেজটি স্ক্রিনে দেখানো হচ্ছে
            return back()->withInput()->with('error', 'সার্ভার এরর: ' . $e->getMessage());
        }
    }
}
