@extends('layouts.app')

@section('title', 'ডোনেশন প্রুফ রিভিউ কিউ | রক্তদূত অ্যাডমিন')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 flex items-center gap-2">
                <span class="w-9 h-9 rounded-xl bg-red-100 text-red-600 flex items-center justify-center text-lg">🛡️</span>
                পেন্ডিং ভেরিফিকেশন (প্রুফ রিভিউ)
            </h1>
            <p class="text-slate-500 text-sm font-semibold mt-1">ডোনেশন প্রুফ queue review করে approve/reject করুন</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="inline-flex items-center gap-1.5 bg-red-600 text-white text-sm font-black px-4 py-2 rounded-full shadow-sm">
                {{ $reviewStats['total_pending'] }} টি পেন্ডিং
            </span>
            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-2 text-sm font-bold text-slate-600 hover:text-slate-900 transition">
                ← অ্যাডমিন ড্যাশবোর্ড
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-6">
        <div class="bg-white border border-slate-200 rounded-xl p-4 text-center shadow-sm">
            <div class="text-xl mb-1">⏳</div>
            <div class="text-2xl font-extrabold text-slate-900">{{ $reviewStats['total_pending'] }}</div>
            <div class="text-xs text-slate-500 font-semibold mt-0.5">মোট পেন্ডিং</div>
        </div>
        <div class="bg-white border border-slate-200 rounded-xl p-4 text-center shadow-sm">
            <div class="text-xl mb-1">🟡</div>
            <div class="text-2xl font-extrabold text-slate-900">{{ $reviewStats['claimed'] }}</div>
            <div class="text-xs text-slate-500 font-semibold mt-0.5">Claimed</div>
        </div>
        <div class="bg-white border border-slate-200 rounded-xl p-4 text-center shadow-sm">
            <div class="text-xl mb-1">🔴</div>
            <div class="text-2xl font-extrabold text-slate-900">{{ $reviewStats['disputed'] }}</div>
            <div class="text-xs text-slate-500 font-semibold mt-0.5">Disputed</div>
        </div>
        <div class="bg-white border border-slate-200 rounded-xl p-4 text-center shadow-sm">
            <div class="text-xl mb-1">🧾</div>
            <div class="text-2xl font-extrabold text-slate-900">{{ $reviewStats['offline_admin_review'] }}</div>
            <div class="text-xs text-slate-500 font-semibold mt-0.5">Offline Admin Review</div>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 bg-emerald-50 border border-emerald-200 rounded-xl p-4 text-emerald-700 font-semibold text-sm flex items-center gap-2">
            ✅ {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 rounded-xl p-4 text-red-700 font-semibold text-sm flex items-center gap-2">
            ⚠️ {{ session('error') }}
        </div>
    @endif

    <div class="py-6 pb-16">
    @if($pendingClaims->isEmpty())
        <div class="bg-white rounded-3xl border border-slate-200 p-12 text-center flex flex-col items-center">
            <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center text-slate-300 mb-4">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <h3 class="text-xl font-extrabold text-slate-800">কোনো পেন্ডিং রিভিউ নেই</h3>
            <p class="text-slate-500 font-medium mt-2">বর্তমানে যাচাই করার মতো কোনো ডোনেশন প্রুফ নেই।</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @foreach($pendingClaims as $claim)
                <article class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">
                    <div class="bg-slate-100 h-52 w-full relative group">
                        @if($claim->proof_image_path)
                            <img src="{{ route('donations.proof', $claim->id) }}" alt="Proof image" class="w-full h-full object-cover">
                            <a href="{{ route('donations.proof', $claim->id) }}" target="_blank" class="absolute inset-0 bg-slate-900/45 opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                                <span class="bg-white text-slate-800 text-xs font-bold px-3 py-1.5 rounded-lg shadow-sm">বড় করে দেখুন</span>
                            </a>
                        @else
                            <div class="w-full h-full flex flex-col items-center justify-center text-slate-400">
                                <svg class="w-10 h-10 mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                <span class="text-xs font-bold uppercase tracking-widest">ছবি আপলোড করা হয়নি</span>
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

                            <div class="bg-slate-50 border border-slate-100 rounded-xl p-3 text-sm space-y-1.5">
                                <div class="flex justify-between gap-2">
                                    <span class="text-slate-500 font-medium">রোগীর লোক:</span>
                                    <span class="font-bold text-slate-800 text-right">{{ $claim->bloodRequest->requester->name ?? 'N/A' }}</span>
                                </div>
                                <div class="flex justify-between gap-2">
                                    <span class="text-slate-500 font-medium">ক্লেইম সময়:</span>
                                    <span class="font-bold text-slate-800 text-right">
                                        {{ $claim->donor_claimed_at ? \Carbon\Carbon::parse($claim->donor_claimed_at)->format('d M, y • h:i A') : 'N/A' }}
                                    </span>
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
                </article>
            @endforeach
        </div>

        @if($pendingClaims->hasPages())
            <div class="mt-8">
                {{ $pendingClaims->links() }}
            </div>
        @endif
    @endif
    </div>

    <div class="mt-8 py-6 pb-16">
        <h2 class="text-xl font-extrabold text-slate-900 mb-4">অফলাইন ক্লেইম (Admin Review)</h2>

        @if($offlineClaims->isEmpty())
            <div class="bg-white rounded-3xl border border-slate-200 p-10 text-center">
                <p class="font-semibold text-slate-600">অফলাইন admin_review ক্লেইম নেই।</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                @foreach($offlineClaims as $claim)
                    <article class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                        <div class="flex items-center justify-between mb-3">
                            <p class="text-xs font-black uppercase tracking-wider text-slate-500">Claim #{{ $claim->id }}</p>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-black uppercase tracking-wider bg-amber-100 text-amber-800">
                                admin_review
                            </span>
                        </div>

                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between gap-2">
                                <span class="text-slate-500 font-medium">ডোনার</span>
                                <span class="font-bold text-slate-800 text-right">{{ $claim->donor?->name ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between gap-2">
                                <span class="text-slate-500 font-medium">রোগীর নাম</span>
                                <span class="font-bold text-slate-800 text-right">{{ $claim->patient_name }}</span>
                            </div>
                            <div class="flex justify-between gap-2">
                                <span class="text-slate-500 font-medium">রক্তদানের তারিখ</span>
                                <span class="font-bold text-slate-800 text-right">{{ $claim->donation_date?->format('d M, Y') }}</span>
                            </div>
                            <div class="flex justify-between gap-2">
                                <span class="text-slate-500 font-medium">জেলা</span>
                                <span class="font-bold text-slate-800 text-right">{{ $claim->district?->name ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between gap-2">
                                <span class="text-slate-500 font-medium">প্রুফ</span>
                                <span class="font-bold {{ $claim->proof_path ? 'text-emerald-600' : 'text-red-600' }}">{{ $claim->proof_path ? 'আছে' : 'নেই' }}</span>
                            </div>
                        </div>

                        <form action="{{ route('admin.offline-claims.approve', $claim->id) }}" method="POST" class="mt-4">
                            @csrf
                            <button type="submit" class="w-full bg-emerald-600 text-white py-2.5 rounded-xl text-sm font-extrabold shadow-sm hover:bg-emerald-700 transition">
                                অ্যাপ্রুভ ({{ $claim->proof_path ? '100%' : '50%' }} পয়েন্ট)
                            </button>
                        </form>
                    </article>
                @endforeach
            </div>

            @if($offlineClaims->hasPages())
                <div class="mt-8">
                    {{ $offlineClaims->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
@endsection
