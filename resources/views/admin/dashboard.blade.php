@extends('layouts.app')

@section('title', 'সিস্টেম অ্যাডমিন ড্যাশবোর্ড — রক্তদূত')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    
    {{-- 🎯 হেডার --}}
    <div class="mb-8 border-b border-slate-200 pb-5">
        <h1 class="text-3xl font-extrabold text-slate-900">অ্যাডমিন ড্যাশবোর্ড</h1>
        <p class="text-slate-500 font-medium mt-2">পুরো সিস্টেমের রিয়েল-টাইম ডেটা অ্যানালিটিক্স এবং পেন্ডিং ভেরিফিকেশন ম্যানেজ করুন।</p>
    </div>

    {{-- সাকসেস/এরর মেসেজ --}}
    @if(session('success'))
        <div class="mb-8 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl font-bold flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-8 p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl font-bold flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            {{ session('error') }}
        </div>
    @endif

    {{-- 📊 ১. গ্লোবাল স্ট্যাটিস্টিকস --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
            <div class="text-slate-500 text-sm font-bold uppercase tracking-wider">মোট ইউজার</div>
            <div class="mt-2 text-4xl font-black text-slate-900">{{ $totalUsers }}</div>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
            <div class="text-blue-600 text-sm font-bold uppercase tracking-wider">মোট ডোনার</div>
            <div class="mt-2 text-4xl font-black text-blue-600">{{ $totalDonors }}</div>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
            <div class="text-red-600 text-sm font-bold uppercase tracking-wider">সফল রিকোয়েস্ট</div>
            <div class="mt-2 text-4xl font-black text-red-600">{{ $fulfilledRequests }} / {{ $totalRequests }}</div>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
            <div class="text-emerald-600 text-sm font-bold uppercase tracking-wider">সাকসেস রেট</div>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-4xl font-black text-emerald-600">{{ $successRate }}</span>
                <span class="text-emerald-400 font-bold text-sm">%</span>
            </div>
        </div>
    </div>

    {{-- 🪪 ২. NID ভেরিফিকেশন রিভিউ (Primary Queue) --}}
    <div class="mb-12">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-extrabold text-slate-900 flex items-center gap-2">
                <span class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center">🪪</span>
                NID ভেরিফিকেশন রিভিউ (অর্গানাইজেশন-বিহীন ইউজার)
            </h2>
            <span class="text-xs font-extrabold bg-blue-100 text-blue-700 px-3 py-1.5 rounded-full">
                {{ $pendingNids->count() }} টি পেন্ডিং
            </span>
        </div>
        <p class="text-sm font-semibold text-slate-500 mb-6 bg-blue-50 border border-blue-100 p-3 rounded-xl border-l-4 border-l-blue-500">
            ℹ️ এখানে শুধুমাত্র সেইসব ইউজারদের NID অনুমোদনের জন্য দেখাবে যারা কোনো ক্লাব বা অর্গানাইজেশনের অধীনে নেই। অর্গানাইজেশনের মেম্বারদের NID তাদের নিজস্ব অর্গ-অ্যাডমিন ভেরিফাই করবেন।
        </p>

        @if($pendingNids->isEmpty())
            <div class="bg-white rounded-3xl border border-slate-200 p-12 text-center flex flex-col items-center">
                <div class="w-16 h-16 bg-blue-50 rounded-full flex items-center justify-center text-blue-300 mb-4 text-3xl">✅</div>
                <h3 class="text-xl font-extrabold text-slate-800">কোনো পেন্ডিং NID নেই</h3>
                <p class="font-medium text-slate-500 mt-2">সকল ডোনার ভেরিফাই করা হয়েছে। অসাধারণ কাজ!</p>
            </div>
        @else
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-xs font-extrabold text-slate-500 uppercase tracking-wider">
                                <th class="text-left px-6 py-4">ডোনার</th>
                                <th class="text-left px-6 py-4">জেলা</th>
                                <th class="text-center px-6 py-4">ডকুমেন্ট</th>
                                <th class="text-center px-6 py-4">অ্যাকশন</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($pendingNids as $donor)
                                <tr class="hover:bg-slate-50/60 transition">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center font-black text-sm shrink-0">
                                                {{ mb_substr($donor->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <p class="font-bold text-slate-900">{{ $donor->name }}</p>
                                                <p class="text-xs text-slate-400 font-medium">{{ $donor->email }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-600 font-semibold">
                                        {{ $donor->district?->name ?? '—' }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <a href="{{ route('donor.view_nid', $donor->id) }}"
                                           target="_blank"
                                           class="inline-flex items-center justify-center gap-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold px-3 py-1.5 rounded-lg transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            দেখুন
                                        </a>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-center gap-2">
                                            <form action="{{ route('admin.nid.verify', $donor) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="decision" value="approve">
                                                <button type="submit"
                                                        class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-extrabold px-4 py-2 rounded-xl transition shadow-sm flex items-center gap-1">
                                                    ✅ অ্যাপ্রুভ
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.nid.verify', $donor) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="decision" value="reject">
                                                <button type="submit"
                                                        onclick="return confirm('{{ $donor->name }}-এর NID বাতিল করবেন?')"
                                                        class="bg-white border text-slate-600 hover:bg-red-50 hover:text-red-600 hover:border-red-200 text-xs font-extrabold px-4 py-2 rounded-xl transition flex items-center gap-1">
                                                    ❌ বাতিল
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>

    {{-- 🏢 ৩. অর্গানাইজেশন/হাসপাতাল যাচাই --}}
    <div class="mb-12" x-data="{ rejectModalOpen: false, currentOrgId: null, currentOrgName: '' }">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-extrabold text-slate-900 flex items-center gap-2">
                <span class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center">🏥</span>
                অর্গানাইজেশন/হাসপাতাল যাচাই
            </h2>
            <span class="text-xs font-extrabold bg-indigo-100 text-indigo-700 px-3 py-1.5 rounded-full">
                {{ $pendingOrgs->count() }} টি পেন্ডিং
            </span>
        </div>

        @if($pendingOrgs->isEmpty())
            <div class="bg-white rounded-3xl border border-slate-200 p-12 text-center flex flex-col items-center">
                <div class="w-16 h-16 bg-indigo-50 rounded-full flex items-center justify-center text-indigo-300 mb-4 text-3xl">🎉</div>
                <h3 class="text-xl font-extrabold text-slate-800">কোনো পেন্ডিং অর্গানাইজেশন নেই</h3>
                <p class="font-medium text-slate-500 mt-2">সকল অ্যাপলিকেশন প্রসেসড। দুর্দান্ত কাজ!</p>
            </div>
        @else
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-xs font-extrabold text-slate-500 uppercase tracking-wider">
                                <th class="text-left px-6 py-4">নাম ও ধরন</th>
                                <th class="text-left px-6 py-4">ঠিকানা</th>
                                <th class="text-left px-6 py-4">যোগাযোগ</th>
                                <th class="text-center px-6 py-4">অফিশিয়াল ডকুমেন্ট</th>
                                <th class="text-center px-6 py-4">অ্যাকশন</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($pendingOrgs as $org)
                                <tr class="hover:bg-slate-50/60 transition">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            @if($org->logo)
                                                <img src="{{ asset('storage/' . $org->logo) }}" class="w-10 h-10 rounded-full object-cover shadow-sm bg-white p-0.5 border border-slate-200">
                                            @else
                                                <div class="w-10 h-10 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center font-black text-sm border border-indigo-100 shrink-0">
                                                    {{ mb_substr($org->name, 0, 1) }}
                                                </div>
                                            @endif
                                            <div>
                                                <p class="font-bold text-slate-900">{{ $org->name }}</p>
                                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 border border-slate-200">
                                                    {{ ucfirst($org->type) }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm font-medium text-slate-600">
                                        {{ $org->address }}, {{ $org->locationUpazila?->name ?? 'N/A' }}, {{ $org->locationDistrict?->name ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 text-sm font-medium text-slate-600">
                                        <div class="flex flex-col gap-0.5">
                                            <span class="flex items-center gap-1.5"><svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg> {{ $org->phone }}</span>
                                            @if($org->email)
                                            <span class="flex items-center gap-1.5"><svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg> {{ $org->email }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <a href="{{ route('admin.org.document', $org->id) }}" target="_blank"
                                           class="inline-flex items-center justify-center gap-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold px-3 py-1.5 rounded-lg transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            ডকুমেন্ট দেখুন
                                        </a>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-center gap-2">
                                            <form action="{{ route('admin.org.verify', $org->id) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="status" value="approved">
                                                <button type="submit"
                                                        class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-extrabold px-3 py-1.5 rounded-lg transition shadow-sm">
                                                    অ্যাপ্রুভ
                                                </button>
                                            </form>
                                            <button type="button" 
                                                    @click="currentOrgId = {{ $org->id }}; currentOrgName = '{{ addslashes($org->name) }}'; rejectModalOpen = true;"
                                                    class="bg-white border text-slate-600 hover:bg-red-50 hover:text-red-600 hover:border-red-200 text-xs font-extrabold px-3 py-1.5 rounded-lg transition">
                                                বাতিল
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Reject Reason Modal -->
            <div x-show="rejectModalOpen" 
                 class="fixed inset-0 z-50 overflow-y-auto" 
                 aria-labelledby="modal-title" role="dialog" aria-modal="true" style="display: none;">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div x-show="rejectModalOpen" 
                         x-transition:enter="ease-out duration-300"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="ease-in duration-200"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         class="fixed inset-0 bg-slate-900 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                    
                    <div x-show="rejectModalOpen"
                         @click.away="rejectModalOpen = false"
                         x-transition:enter="ease-out duration-300"
                         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave="ease-in duration-200"
                         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                        <form :action="'{{ url('/admin/orgs') }}/' + currentOrgId + '/verify'" method="POST">
                            @csrf
                            <input type="hidden" name="status" value="rejected">
                            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                <div class="sm:flex sm:items-start">
                                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                        <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                        </svg>
                                    </div>
                                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                        <h3 class="text-lg leading-6 font-extrabold text-slate-900" id="modal-title">
                                            অর্গানাইজেশন বাতিল করুন
                                        </h3>
                                        <div class="mt-2">
                                            <p class="text-sm font-medium text-slate-500 mb-3">আপনি <span x-text="currentOrgName" class="font-bold text-slate-800"></span> এর আবেদন বাতিল করতে যাচ্ছেন। অনুগ্রহ করে কারণ উল্লেখ করুন:</p>
                                            <textarea name="rejection_reason" rows="3" required
                                                class="w-full border-slate-200 rounded-xl focus:ring-red-500 focus:border-red-500 text-sm font-medium"
                                                placeholder="উদাঃ অফিশিয়াল ডকুমেন্ট স্পষ্ট নয়..."></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-slate-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-slate-100">
                                <button type="submit" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-extrabold text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm transition">
                                    নিশ্চিত করুন
                                </button>
                                <button type="button" @click="rejectModalOpen = false" class="mt-3 w-full inline-flex justify-center rounded-xl border border-slate-300 shadow-sm px-4 py-2 bg-white text-base font-bold text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition">
                                    ক্যান্সেল
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- 🛡️ ৪. পেন্ডিং ভেরিফিকেশন (প্রুফ রিভিউ - Secondary Queue) --}}
    <div class="mb-12">
        <div class="mb-6 flex items-center justify-between">
            <h2 class="text-xl font-extrabold text-slate-800 flex items-center gap-2">
                <span class="bg-red-100 text-red-600 p-1.5 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </span>
                পেন্ডিং ভেরিফিকেশন (প্রুফ রিভিউ)
            </h2>
            <span class="bg-slate-800 text-white text-sm font-bold px-3 py-1 rounded-full">{{ $pendingClaims->count() }} টি পেন্ডিং</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($pendingClaims as $claim)
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">
                    <div class="bg-slate-100 h-48 w-full relative group">
                        @if($claim->proof_image_path)
                            <img src="{{ asset('storage/' . $claim->proof_image_path) }}" class="w-full h-full object-cover">
                            <a href="{{ asset('storage/' . $claim->proof_image_path) }}" target="_blank" class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                                <span class="bg-white text-slate-800 text-xs font-bold px-3 py-1.5 rounded-lg shadow-sm">বড় করে দেখুন</span>
                            </a>
                        @else
                            <div class="w-full h-full flex flex-col items-center justify-center text-slate-400">
                                <svg class="w-10 h-10 mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                <span class="text-xs font-bold uppercase tracking-widest">ছবি আপলোড করা হয়নি</span>
                            </div>
                        @endif
                        
                        @if($claim->verification_status === 'disputed')
                            <span class="absolute top-3 right-3 bg-red-600 text-white text-[10px] font-black uppercase tracking-wider px-2.5 py-1 rounded-md shadow-sm">Disputed</span>
                        @else
                            <span class="absolute top-3 right-3 bg-amber-500 text-white text-[10px] font-black uppercase tracking-wider px-2.5 py-1 rounded-md shadow-sm">Claimed</span>
                        @endif
                    </div>

                    <div class="p-5 flex-1 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center gap-2 mb-3">
                                <div class="w-8 h-8 rounded-full bg-red-50 text-red-600 flex items-center justify-center font-bold text-sm shrink-0">
                                    {{ mb_substr($claim->user->name ?? 'D', 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-xs text-slate-400 font-bold uppercase tracking-wider">ডোনার</p>
                                    <p class="text-sm font-extrabold text-slate-900">{{ $claim->user->name ?? 'Unknown' }}</p>
                                </div>
                            </div>
                            
                            <div class="bg-slate-50 border border-slate-100 rounded-xl p-3 text-sm">
                                <div class="flex justify-between mb-1">
                                    <span class="text-slate-500 font-medium">রোগীর লোক:</span>
                                    <span class="font-bold text-slate-800">{{ $claim->bloodRequest->requester->name ?? 'N/A' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-slate-500 font-medium">তারিখ:</span>
                                    <span class="font-bold text-slate-800">{{ $claim->donor_claimed_at ? \Carbon\Carbon::parse($claim->donor_claimed_at)->format('d M, y • h:i A') : 'N/A' }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-2 mt-5">
                            <form action="{{ route('admin.donations.verify', $claim->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="status" value="verified">
                                <button type="submit" class="w-full bg-emerald-600 text-white py-2.5 rounded-xl text-sm font-extrabold shadow-sm hover:bg-emerald-700 transition">অ্যাপ্রুভ</button>
                            </form>
                            <form action="{{ route('admin.donations.verify', $claim->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="status" value="rejected">
                                <button type="submit" onclick="return confirm('আপনি কি নিশ্চিত? এটি বাতিল করলে ডোনার পয়েন্ট পাবে না।')" class="w-full bg-white border-2 border-red-100 text-red-600 py-2.5 rounded-xl text-sm font-extrabold shadow-sm hover:bg-red-50 hover:border-red-200 transition">বাতিল</button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white rounded-3xl border border-slate-200 p-12 text-center flex flex-col items-center">
                    <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center text-slate-300 mb-4">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h3 class="text-xl font-extrabold text-slate-800">কোনো পেন্ডিং রিভিউ নেই</h3>
                    <p class="text-slate-500 font-medium mt-2">বর্তমানে যাচাই করার মতো কোনো ডোনেশন প্রুফ নেই। দারুণ কাজ!</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- ⚙️ ৫. Governance & Moderation Tools (Expandable Accordion) --}}
    <div x-data="{ activeAccordion: null }" class="space-y-4 mb-8">
        <div class="flex items-center gap-2 mb-4 mt-8">
            <h2 class="text-xl font-extrabold text-slate-900">⚙️ গভার্নেন্স ও মডারেশন প্যানেল</h2>
        </div>

        {{-- Gamification Governance --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden transition-all duration-200"
             :class="{'border-red-200 ring-2 ring-red-50': activeAccordion === 1}">
            <button @click="activeAccordion = activeAccordion === 1 ? null : 1" 
                    @keydown.enter="activeAccordion = activeAccordion === 1 ? null : 1"
                    @keydown.space.prevent="activeAccordion = activeAccordion === 1 ? null : 1"
                    class="w-full flex items-center justify-between p-5 bg-white hover:bg-red-50/30 transition-colors focus:outline-none">
                <div class="flex items-center gap-4 text-left">
                    <div class="w-12 h-12 rounded-xl bg-red-100 text-red-600 flex items-center justify-center text-2xl shrink-0 transition-transform" :class="{'scale-110': activeAccordion === 1}">🎮</div>
                    <div>
                        <h3 class="text-lg font-extrabold text-slate-900">Gamification Governance Panel</h3>
                        <p class="text-sm text-slate-500 font-medium">ডোনার পয়েন্ট অ্যাডজাস্ট, শ্যাডোব্যান এবং অ্যাক্টিভিটি অডিট</p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <div class="hidden sm:flex flex-wrap gap-2">
                        <span class="bg-red-50 text-red-600 text-[10px] font-bold px-2.5 py-1 rounded-md border border-red-100">🚫 Shadowban</span>
                        <span class="bg-blue-50 text-blue-600 text-[10px] font-bold px-2.5 py-1 rounded-md border border-blue-100">🔧 Point Adjust</span>
                    </div>
                    <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-500 transition-transform duration-300" :class="{'rotate-180 bg-red-100 text-red-600': activeAccordion === 1}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                    </div>
                </div>
            </button>
            <div x-show="activeAccordion === 1" 
                 x-collapse
                 style="display: none;">
                <div class="p-6 border-t border-slate-100 bg-slate-50/50">
                    <p class="text-sm text-slate-600 font-semibold mb-5 max-w-2xl">
                        ডোনারদের অর্জিত পয়েন্ট ম্যানুয়ালি রিভিউ ও মডিফাই করার জন্য এই প্যানেল ব্যবহার করুন। কোনো অস্বাভাবিক পয়েন্ট গেইন দেখা গেলে অ্যাকাউন্ট শ্যাডোব্যান বা রিস্টোর করতে পারবেন।
                    </p>
                    <a href="{{ route('admin.gamification.index') }}" class="inline-flex items-center gap-2 bg-slate-900 hover:bg-slate-800 text-white font-extrabold px-6 py-3 rounded-xl text-sm transition shadow-sm">
                        প্যানেলে প্রবেশ করুন
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                </div>
            </div>
        </div>

        {{-- Blog Governance --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden transition-all duration-200"
             :class="{'border-violet-200 ring-2 ring-violet-50': activeAccordion === 2}">
            <button @click="activeAccordion = activeAccordion === 2 ? null : 2" 
                    @keydown.enter="activeAccordion = activeAccordion === 2 ? null : 2"
                    @keydown.space.prevent="activeAccordion = activeAccordion === 2 ? null : 2"
                    class="w-full flex items-center justify-between p-5 bg-white hover:bg-violet-50/30 transition-colors focus:outline-none">
                <div class="flex items-center gap-4 text-left">
                    <div class="w-12 h-12 rounded-xl bg-violet-100 text-violet-600 flex items-center justify-center text-2xl shrink-0 transition-transform" :class="{'scale-110': activeAccordion === 2}">📝</div>
                    <div>
                        <div class="flex items-center gap-2 flex-wrap mb-0.5">
                            <h3 class="text-lg font-extrabold text-slate-900">ব্লগ ও কন্টেন্ট মডারেশন</h3>
                            @if(isset($pendingBlogCount) && $pendingBlogCount > 0)
                                <span class="bg-red-100 text-red-600 text-[10px] font-black px-2 py-0.5 rounded-full border border-red-200 animate-pulse">
                                    🔴 {{ $pendingBlogCount }} পেন্ডিং
                                </span>
                            @else
                                <span class="bg-emerald-50 text-emerald-600 text-[10px] font-bold px-2 py-0.5 rounded-full border border-emerald-100">
                                    ✅ সব ক্লিয়ার
                                </span>
                            @endif
                        </div>
                        <p class="text-sm text-slate-500 font-medium">ইউজারদের সাবমিট করা ব্লগ পোস্ট রিভিউ করুন</p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <div class="hidden sm:flex flex-wrap gap-2">
                        <span class="bg-violet-50 text-violet-600 text-[10px] font-bold px-2.5 py-1 rounded-md border border-violet-100">✍️ রিভিউ</span>
                        <span class="bg-emerald-50 text-emerald-600 text-[10px] font-bold px-2.5 py-1 rounded-md border border-emerald-100">✅ অ্যাপ্রুভ</span>
                    </div>
                    <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-500 transition-transform duration-300" :class="{'rotate-180 bg-violet-100 text-violet-600': activeAccordion === 2}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                    </div>
                </div>
            </button>
            <div x-show="activeAccordion === 2" 
                 x-collapse
                 style="display: none;">
                <div class="p-6 border-t border-slate-100 bg-slate-50/50">
                    <p class="text-sm text-slate-600 font-semibold mb-5 max-w-2xl">
                        পুরো সিস্টেমের কন্টেন্ট কোয়ালিটি কন্ট্রোল করুন। অপ্রাসঙ্গিক বা ভুয়া খবর ফিল্টার আউট করে শুধু মানসম্মত ব্লগ অ্যাপ্রুভ করুন।
                    </p>
                    <a href="{{ route('admin.blog.moderation.index') }}" class="inline-flex items-center gap-2 bg-slate-900 hover:bg-slate-800 text-white font-extrabold px-6 py-3 rounded-xl text-sm transition shadow-sm">
                        মডারেশন শুরু করুন
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- 🔒 ৫. সিকিউরিটি ও অডিট প্যানেল --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-12">
        {{-- Security Radar Widget --}}
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden flex flex-col h-[400px]">
            <div class="p-6 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center shrink-0">
                <div>
                    <h3 class="text-lg font-extrabold text-slate-900 flex items-center gap-2">
                        <span class="w-8 h-8 rounded-lg bg-red-100 text-red-600 flex items-center justify-center">🚨</span>
                        সন্দেহজনক কার্যক্রম
                    </h3>
                    <p class="text-xs text-slate-500 font-bold mt-1">সিস্টেম সিকিউরিটি রাডার (MVP-Lite)</p>
                </div>
                <div class="flex flex-col items-end">
                    <span class="text-2xl font-black {{ $todaysSecurityEventsCount > 0 ? 'text-red-600' : 'text-emerald-500' }}">
                        {{ $todaysSecurityEventsCount }}
                    </span>
                    <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">আজকের ইভেন্ট</span>
                </div>
            </div>
            <div class="p-0 flex-1 overflow-y-auto">
                @if($recentSecurityLogs->isEmpty())
                    <div class="p-8 text-center flex flex-col items-center justify-center h-full">
                        <div class="w-12 h-12 bg-emerald-50 rounded-full flex items-center justify-center text-emerald-400 mb-3 text-2xl">🛡️</div>
                        <p class="text-sm font-bold text-slate-500">কোনো সন্দেহজনক কার্যক্রম পাওয়া যায়নি। সিস্টেম নিরাপদ।</p>
                    </div>
                @else
                    <ul class="divide-y divide-slate-100">
                        @foreach($recentSecurityLogs as $log)
                            <li class="p-4 hover:bg-slate-50 transition flex gap-3">
                                <div class="w-2 h-2 mt-1.5 rounded-full bg-red-500 shrink-0 animate-pulse"></div>
                                <div class="flex-1">
                                    <div class="flex justify-between items-start mb-1">
                                        <span class="text-[10px] font-bold uppercase tracking-wider text-red-600 bg-red-50 px-2 py-0.5 rounded-md border border-red-100">{{ str_replace('_', ' ', $log->event_type) }}</span>
                                        <span class="text-[10px] font-bold text-slate-400">{{ $log->created_at->diffForHumans() }}</span>
                                    </div>
                                    <p class="text-sm font-bold text-slate-800">{{ $log->description }}</p>
                                    @if($log->user)
                                        <p class="text-xs font-semibold text-slate-500 mt-1">ইউজার: <span class="text-slate-700">{{ $log->user->name }}</span> (ID: {{ $log->user->id }}) @if($log->ip_address) • IP: {{ $log->ip_address }} @endif</p>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        {{-- Admin Audit Trail Widget --}}
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden flex flex-col h-[400px]">
            <div class="p-6 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center shrink-0">
                <div>
                    <h3 class="text-lg font-extrabold text-slate-900 flex items-center gap-2">
                        <span class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center">📑</span>
                        অ্যাডমিন অডিট ট্রেইল
                    </h3>
                    <p class="text-xs text-slate-500 font-bold mt-1">সর্বশেষ ২০টি অ্যাকশন</p>
                </div>
            </div>
            <div class="p-0 flex-1 overflow-y-auto">
                @if($recentAuditLogs->isEmpty())
                    <div class="p-8 text-center flex flex-col items-center justify-center h-full">
                        <div class="w-12 h-12 bg-slate-50 rounded-full flex items-center justify-center text-slate-400 mb-3 text-2xl">📋</div>
                        <p class="text-sm font-bold text-slate-500">এখনো কোনো অডিট লগ তৈরি হয়নি।</p>
                    </div>
                @else
                    <ul class="divide-y divide-slate-100">
                        @foreach($recentAuditLogs as $audit)
                            <li class="p-4 hover:bg-slate-50 transition flex items-start gap-3">
                                <div class="w-8 h-8 rounded-full bg-slate-100 text-indigo-600 flex items-center justify-center font-black text-xs shrink-0 border border-slate-200">
                                    {{ mb_substr($audit->admin->name ?? 'A', 0, 1) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex justify-between items-start mb-0.5">
                                        <p class="text-sm font-extrabold text-slate-900 truncate pr-2">{{ $audit->admin->name ?? 'System Admin' }}</p>
                                        <span class="text-[10px] font-bold text-slate-400 whitespace-nowrap shrink-0">{{ $audit->created_at->diffForHumans() }}</span>
                                    </div>
                                    <div class="text-xs font-semibold text-slate-500 mb-1.5 flex items-center gap-1.5 flex-wrap">
                                        <span class="bg-indigo-50 text-indigo-700 px-1.5 py-0.5 rounded border border-indigo-100 uppercase tracking-wider text-[9px]">{{ str_replace('_', ' ', $audit->action_type) }}</span>
                                        @if($audit->target_id)
                                            <span>টার্গেট আইডি: <strong class="text-slate-700">{{ $audit->target_id }}</strong></span>
                                        @endif
                                    </div>
                                    @if(isset($audit->details['reason']))
                                        <p class="text-xs font-medium text-red-600 bg-red-50 p-2 rounded border border-red-100 mt-1">কারণ: {{ $audit->details['reason'] }}</p>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>

    {{-- 📈 ৬. চার্ট সেকশন (Professional Horizontal Bars) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-12">

        {{-- Pie Chart: ব্লাড গ্রুপ ডিমান্ড --}}
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-7">
            <div class="mb-5 border-b border-slate-100 pb-4">
                <h3 class="text-lg font-extrabold text-slate-900 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-xl bg-red-100 text-red-600 flex items-center justify-center text-sm">🩸</span>
                    ব্লাড গ্রুপ ডিমান্ড
                </h3>
                <p class="text-xs text-slate-500 font-semibold mt-1.5">কোন রক্তের গ্রুপ সবচেয়ে বেশি রিকোয়েস্ট হয়েছে</p>
            </div>
            
            @if(empty($bloodGroupDemand))
                <div class="flex flex-col items-center justify-center h-[240px] text-slate-400">
                    <span class="text-4xl mb-3">📊</span>
                    <span class="text-sm font-semibold">গত ৩০ দিনে কোনো ডিমান্ড নেই</span>
                </div>
            @else
                <div style="height:260px;">
                    <canvas id="bloodGroupChart"></canvas>
                </div>
            @endif
        </div>

        {{-- Bar Chart: জেলা ভিত্তিক ইমার্জেন্সি --}}
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-7">
            <div class="mb-5 border-b border-slate-100 pb-4">
                <h3 class="text-lg font-extrabold text-slate-900 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center text-sm">📍</span>
                    শীর্ষ ৫ ইমার্জেন্সি জেলা (গত ৩০ দিন)
                </h3>
            </div>
            
            @if(empty($districtDemand))
                <div class="flex flex-col items-center justify-center h-[240px] text-slate-400">
                    <span class="text-4xl mb-3">📉</span>
                    <span class="text-sm font-semibold">গত ৩০ দিনে কোনো রিকোয়েস্ট নেই</span>
                </div>
            @else
                <div style="height:260px;">
                    <canvas id="districtChart"></canvas>
                </div>
            @endif
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Check if Chart.js is loaded
    if(typeof Chart === 'undefined') return;

    Chart.defaults.font.family = "'Inter', 'Hind Siliguri', sans-serif";
    Chart.defaults.color = '#64748b';

    // ─── ১. ব্লাড গ্রুপ Pie Chart ────────────────────────────────
    @if(!empty($bloodGroupDemand))
    const bloodGroupData = @json($bloodGroupDemand);
    const bgColors = ['#ef4444','#f97316','#eab308','#22c55e','#06b6d4','#3b82f6','#8b5cf6','#ec4899'];
    new Chart(document.getElementById('bloodGroupChart'), {
        type: 'doughnut',
        data: {
            labels: Object.keys(bloodGroupData),
            datasets: [{
                data: Object.values(bloodGroupData),
                backgroundColor: bgColors.slice(0, Object.keys(bloodGroupData).length),
                borderWidth: 2,
                borderColor: '#ffffff',
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { 
                    position: 'right', 
                    labels: { font: { size: 12, weight: 'bold', family: "'Inter', sans-serif" }, padding: 15, usePointStyle: true, pointStyle: 'circle' } 
                },
                tooltip: { 
                    backgroundColor: 'rgba(15, 23, 42, 0.9)',
                    titleFont: { size: 13 },
                    bodyFont: { size: 14, weight: 'bold' },
                    padding: 12,
                    cornerRadius: 8,
                    callbacks: { label: ctx => ` ${ctx.label}: ${ctx.parsed} রিকোয়েস্ট` } 
                }
            },
            cutout: '65%'
        }
    });
    @endif

    // ─── ২. জেলা Bar Chart ───────────────────────────────────────
    @if(!empty($districtDemand))
    const districtData = @json($districtDemand);
    new Chart(document.getElementById('districtChart'), {
        type: 'bar',
        data: {
            labels: Object.keys(districtData),
            datasets: [{
                label: 'রিকোয়েস্ট সংখ্যা',
                data: Object.values(districtData),
                backgroundColor: 'rgba(59, 130, 246, 0.85)',
                hoverBackgroundColor: 'rgba(37, 99, 235, 1)',
                borderRadius: 4,
                barThickness: 20,
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: { 
                legend: { display: false },
                tooltip: { 
                    backgroundColor: 'rgba(15, 23, 42, 0.9)',
                    titleFont: { size: 13 },
                    bodyFont: { size: 14, weight: 'bold' },
                    padding: 12,
                    cornerRadius: 8,
                    callbacks: { label: ctx => ` ${ctx.label} — ${ctx.parsed.x} টি জরুরি রিকোয়েস্ট` } 
                } 
            },
            scales: {
                x: { 
                    beginAtZero: true, 
                    ticks: { precision: 0, font: { weight: '600' } }, 
                    grid: { color: '#f1f5f9', drawBorder: false } 
                },
                y: { 
                    grid: { display: false },
                    ticks: { font: { weight: 'bold', size: 12 }, color: '#1e293b' }
                }
            }
        }
    });
    @endif
});
</script>
@endsection