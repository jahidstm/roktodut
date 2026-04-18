@extends('layouts.app')

@section('title', 'ডোনেশন প্রুফ রিভিউ কিউ | রক্তদূত অ্যাডমিন')

@section('content')
<section class="relative bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 overflow-hidden">
    <div class="absolute inset-0 opacity-[0.04]"
         style="background-image: linear-gradient(rgba(255,255,255,1) 1px,transparent 1px),
                                  linear-gradient(90deg,rgba(255,255,255,1) 1px,transparent 1px);
                background-size: 28px 28px;"></div>

    <div class="relative mx-auto max-w-7xl px-4 sm:px-6 py-10 md:py-14">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <span class="inline-flex items-center gap-2 bg-red-500/15 border border-red-500/30 text-red-300 text-xs font-extrabold uppercase tracking-widest px-3 py-1 rounded-full mb-3">
                    🛡️ অ্যাডমিন প্যানেল
                </span>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-white">পেন্ডিং ভেরিফিকেশন (প্রুফ রিভিউ)</h1>
                <p class="mt-1.5 text-slate-400 text-sm font-medium">এখান থেকে ডোনেশন প্রুফ approve/reject করুন।</p>
            </div>
            <a href="{{ route('admin.dashboard') }}"
               class="inline-flex items-center gap-2 text-slate-400 hover:text-white text-sm font-semibold transition-colors duration-150 shrink-0">
                ← অ্যাডমিন ড্যাশবোর্ড
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-8">
            <div class="bg-white/5 border border-white/10 rounded-xl p-4 text-center">
                <div class="text-xl mb-1">⏳</div>
                <div class="text-2xl font-extrabold text-white">{{ $reviewStats['total_pending'] }}</div>
                <div class="text-xs text-slate-400 font-semibold mt-0.5">মোট পেন্ডিং</div>
            </div>
            <div class="bg-white/5 border border-white/10 rounded-xl p-4 text-center">
                <div class="text-xl mb-1">🟡</div>
                <div class="text-2xl font-extrabold text-white">{{ $reviewStats['claimed'] }}</div>
                <div class="text-xs text-slate-400 font-semibold mt-0.5">Claimed</div>
            </div>
            <div class="bg-white/5 border border-white/10 rounded-xl p-4 text-center">
                <div class="text-xl mb-1">🔴</div>
                <div class="text-2xl font-extrabold text-white">{{ $reviewStats['disputed'] }}</div>
                <div class="text-xs text-slate-400 font-semibold mt-0.5">Disputed</div>
            </div>
        </div>
    </div>
</section>

<div class="mx-auto max-w-7xl px-4 sm:px-6 pt-6">
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
</div>

<div class="mx-auto max-w-7xl px-4 sm:px-6 py-6 pb-16">
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
@endsection
