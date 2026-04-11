@extends('layouts.app')

@section('title', 'ডোনার ভেরিফিকেশন — রক্তদূত')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
    
    {{-- 🔙 Header & Back Button --}}
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <a href="{{ route('org.dashboard') }}" class="p-2 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition text-slate-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">ডোনার ভেরিফিকেশন</h1>
            </div>
            <p class="text-slate-500 font-medium mt-2 ml-12">মেম্বারের প্রদত্ত তথ্য এবং এনআইডি কার্ড যাচাই করুন।</p>
        </div>
        
        <div class="bg-amber-50 border border-amber-100 px-4 py-2 rounded-xl flex items-center gap-2 shadow-sm">
            <span class="relative flex h-2.5 w-2.5">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-amber-500"></span>
            </span>
            <span class="text-xs font-bold text-amber-700 uppercase tracking-widest">Pending Review</span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        {{-- 👤 Left Column: Donor Information --}}
        <div class="lg:col-span-4 space-y-6">
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="bg-slate-900 px-6 py-8 text-center relative">
                    <div class="absolute top-4 right-4 bg-red-600 text-white text-xs font-black px-3 py-1 rounded-full shadow-sm">
                        {{ $donor->blood_group?->value ?? (string) $donor->blood_group ?? 'N/A' }}
                    </div>
                    <div class="w-24 h-24 bg-slate-200 rounded-full border-4 border-slate-800 mx-auto flex items-center justify-center overflow-hidden">
                        {{-- Profile Image Placeholder --}}
                        <svg class="w-12 h-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    </div>
                    <h2 class="mt-4 text-xl font-extrabold text-white">{{ $donor->name }}</h2>
                    <p class="text-slate-400 text-sm font-medium mt-1">{{ $donor->email }}</p>
                </div>

                <div class="p-6">
                    <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-4">ব্যক্তিগত তথ্য</h3>
                    <ul class="space-y-4">
                        <li class="flex items-center justify-between border-b border-slate-50 pb-3">
                            <span class="text-sm font-semibold text-slate-500">মোবাইল</span>
                            <span class="text-sm font-bold text-slate-900">{{ $donor->phone ?? 'দেওয়া হয়নি' }}</span>
                        </li>
                        <li class="flex items-center justify-between border-b border-slate-50 pb-3">
                            <span class="text-sm font-semibold text-slate-500">জেলা</span>
                            <span class="text-sm font-bold text-slate-900">{{ $donor->district?->name ?? 'N/A' }}</span>
                        </li>
                        <li class="flex items-center justify-between border-b border-slate-50 pb-3">
                            <span class="text-sm font-semibold text-slate-500">উপজেলা</span>
                            <span class="text-sm font-bold text-slate-900">{{ $donor->upazila?->name ?? 'N/A' }}</span>
                        </li>
                        <li class="flex items-center justify-between border-b border-slate-50 pb-3">
                            <span class="text-sm font-semibold text-slate-500">লিঙ্গ</span>
                            <span class="text-sm font-bold text-slate-900 uppercase">{{ $donor->gender ?? 'N/A' }}</span>
                        </li>
                        <li class="flex items-center justify-between pb-1">
                            <span class="text-sm font-semibold text-slate-500">ওজন</span>
                            <span class="text-sm font-bold text-slate-900">{{ $donor->weight ? $donor->weight . ' কেজি' : 'N/A' }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- 📄 Right Column: Document Preview & Actions --}}
        <div class="lg:col-span-8 flex flex-col gap-6">
            
            {{-- Document Viewer --}}
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 flex-grow flex flex-col">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-extrabold text-slate-900">আইডি কার্ড/এনআইডি প্রিভিউ</h2>
                    <span class="text-xs font-bold text-slate-500 bg-slate-100 px-3 py-1 rounded-md">Front Side</span>
                </div>

                {{-- 🚀 Image Rendering Logic (Updated with iFrame for PDF) --}}
                <div class="bg-slate-50 border-2 border-dashed border-slate-200 rounded-2xl flex-grow flex flex-col items-center justify-center p-4 min-h-[400px] relative group overflow-hidden">
                    @if($donor->nid_path)
                        @if(Str::endsWith($donor->nid_path, ['.pdf']))
                            {{-- Embedded PDF Viewer --}}
                            <div class="w-full h-full flex flex-col items-center w-full gap-4">
                                <iframe src="{{ route('donor.view_nid', $donor->id) }}" class="w-full h-[350px] rounded-xl border border-slate-200 shadow-inner" style="border: none;"></iframe>
                                
                                <a href="{{ route('donor.view_nid', $donor->id) }}" target="_blank" class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-800 text-white text-sm font-bold rounded-xl hover:bg-slate-900 transition-colors shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                    নতুন ট্যাবে বড় করে দেখুন
                                </a>
                            </div>
                        @else
                            {{-- Image Preview --}}
                            <a href="{{ route('donor.view_nid', $donor->id) }}" target="_blank" class="w-full h-full flex items-center justify-center cursor-pointer">
                                <img src="{{ route('donor.view_nid', $donor->id) }}" alt="NID Document" class="max-h-[400px] w-auto object-contain rounded-xl">
                            </a>
                            <div class="absolute inset-0 bg-slate-900/10 opacity-0 group-hover:opacity-100 transition-opacity rounded-2xl flex items-center justify-center pointer-events-none">
                                <span class="bg-slate-900 text-white text-xs font-bold px-4 py-2 rounded-lg shadow-lg">ক্লিক করে বড় করুন</span>
                            </div>
                        @endif
                    @else
                        {{-- No Document Fallback --}}
                        <svg class="w-16 h-16 text-slate-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <p class="text-slate-500 font-bold text-sm">ডোনার এখনো কোনো ডকুমেন্ট আপলোড করেননি</p>
                    @endif
                </div>

                <div class="mt-6 flex gap-3 p-4 bg-blue-50 text-blue-800 rounded-xl border border-blue-100">
                    <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <p class="text-sm font-semibold leading-relaxed">
                        তথ্য যাচাই করার সময় নামের বানান এবং রক্তের গ্রুপ মনোযোগ সহকারে চেক করুন। ভুল ভেরিফিকেশন প্ল্যাটফর্মের সুনাম নষ্ট করতে পারে।
                    </p>
                </div>
            </div>

            {{-- ⚡ Action Buttons --}}
            <div x-data="{ showApproveModal: false, showRejectModal: false }" class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm flex flex-col sm:flex-row items-center gap-4 justify-end">
                
                <button @click="showRejectModal = true" type="button" class="w-full sm:w-auto px-8 py-3.5 bg-white border-2 border-red-100 text-red-600 hover:bg-red-50 hover:border-red-200 rounded-xl text-sm font-black transition-all">
                    রিজেক্ট করুন
                </button>

                <button @click="showApproveModal = true" type="button" class="w-full sm:w-auto px-8 py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-black shadow-sm shadow-emerald-200 transition-all flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                    ভেরিফাই ও অ্যাপ্রুভ
                </button>

                {{-- 🔴 Reject Confirmation Modal --}}
                <div x-show="showRejectModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm" x-transition.opacity>
                    <div @click.away="showRejectModal = false" class="bg-white rounded-2xl p-8 max-w-md w-full mx-4 shadow-2xl transform transition-all">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="bg-red-100 text-red-600 p-2 rounded-full">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            </div>
                            <h3 class="text-xl font-black text-slate-900">রিজেক্ট নিশ্চিতকরণ</h3>
                        </div>
                        <p class="text-slate-500 font-medium mb-8">আপনি কি নিশ্চিত যে আপনি এই ডোনারের ভেরিফিকেশন আবেদনটি বাতিল করতে চান? এই অ্যাকশনটি ডেটাবেসে লগ করা হবে।</p>
                        <div class="flex justify-end gap-3">
                            <button @click="showRejectModal = false" type="button" class="px-5 py-2.5 rounded-xl font-bold text-slate-600 hover:bg-slate-100 transition">বাতিল</button>
                            <form action="{{ route('org.donor.reject', $donor->id) }}" method="POST" class="flex-grow flex items-center gap-2">
                                @csrf
                                <input type="text" name="reject_reason" placeholder="বাতিলের যুক্তিসঙ্গত কারণ লিখুন..." required class="flex-grow rounded-xl border-slate-200 focus:border-red-500 focus:ring-red-500 text-sm py-2.5">
                                <button type="submit" class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl font-black transition shadow-sm whitespace-nowrap">
                                    হ্যাঁ, রিজেক্ট করুন
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- 🟢 Approve Confirmation Modal --}}
                <div x-show="showApproveModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm" x-transition.opacity>
                    <div @click.away="showApproveModal = false" class="bg-white rounded-2xl p-8 max-w-md w-full mx-4 shadow-2xl transform transition-all">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="bg-emerald-100 text-emerald-600 p-2 rounded-full">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <h3 class="text-xl font-black text-slate-900">অ্যাপ্রুভ নিশ্চিতকরণ</h3>
                        </div>
                        <p class="text-slate-500 font-medium mb-8">আপনি কি নিশ্চিত যে আপনি প্রদত্ত ডকুমেন্টের সাথে ডোনারের তথ্য পুঙ্খানুপুঙ্খভাবে যাচাই করেছেন এবং তাকে ভেরিফাইড ডোনার হিসেবে অ্যাপ্রুভ করতে চান?</p>
                        <div class="flex justify-end gap-3">
                            <button @click="showApproveModal = false" type="button" class="px-5 py-2.5 rounded-xl font-bold text-slate-600 hover:bg-slate-100 transition">বাতিল</button>
                            <form action="{{ route('org.donor.approve', $donor->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-black transition shadow-sm">
                                    হ্যাঁ, অ্যাপ্রুভ করুন
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>
@endsection