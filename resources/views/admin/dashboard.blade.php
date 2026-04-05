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

    {{-- 🛡️ ২. পেন্ডিং ভেরিফিকেশন (প্রুফ রিভিউ) সেকশন --}}
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
                    {{-- Proof Image Section --}}
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
                        
                        {{-- Status Badge --}}
                        @if($claim->verification_status === 'disputed')
                            <span class="absolute top-3 right-3 bg-red-600 text-white text-[10px] font-black uppercase tracking-wider px-2.5 py-1 rounded-md shadow-sm">
                                Disputed
                            </span>
                        @else
                            <span class="absolute top-3 right-3 bg-amber-500 text-white text-[10px] font-black uppercase tracking-wider px-2.5 py-1 rounded-md shadow-sm">
                                Claimed
                            </span>
                        @endif
                    </div>

                    {{-- Details Section --}}
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

                        {{-- Admin Action Buttons --}}
                        <div class="grid grid-cols-2 gap-2 mt-5">
                            <form action="{{ route('admin.donations.verify', $claim->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="status" value="verified">
                                <button type="submit" class="w-full bg-emerald-600 text-white py-2.5 rounded-xl text-sm font-extrabold shadow-sm hover:bg-emerald-700 transition">
                                    অ্যাপ্রুভ
                                </button>
                            </form>

                            <form action="{{ route('admin.donations.verify', $claim->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="status" value="rejected">
                                <button type="submit" onclick="return confirm('আপনি কি নিশ্চিত? এটি বাতিল করলে ডোনার পয়েন্ট পাবে না।')" class="w-full bg-white border-2 border-red-100 text-red-600 py-2.5 rounded-xl text-sm font-extrabold shadow-sm hover:bg-red-50 hover:border-red-200 transition">
                                    বাতিল
                                </button>
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

    {{-- 📈 ৩. চার্ট সেকশন (For Developer Alif) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-6 bg-slate-50 rounded-2xl border-2 border-dashed border-slate-300">
        <div>
            <h3 class="font-extrabold text-slate-800 mb-2">@Alif: ব্লাড গ্রুপ ডিমান্ড (Pie Chart)</h3>
            <p class="text-sm text-slate-500 mb-4">Chart.js ব্যবহার করে নিচের JSON ডেটা দিয়ে পাই-চার্ট রেন্ডার করো।</p>
            <pre class="bg-slate-900 text-emerald-400 p-4 rounded-xl text-xs overflow-auto">
const bloodGroupData = @json($bloodGroupDemand);
            </pre>
        </div>
        <div>
            <h3 class="font-extrabold text-slate-800 mb-2">@Alif: ইমার্জেন্সি জোন (Bar Chart)</h3>
            <p class="text-sm text-slate-500 mb-4">টপ ৫ জেলার ডেটা দিয়ে একটি বার-চার্ট তৈরি করো।</p>
            <pre class="bg-slate-900 text-blue-400 p-4 rounded-xl text-xs overflow-auto">
const districtData = @json($districtDemand);
            </pre>
        </div>
    </div>

</div>
@endsection