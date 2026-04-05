@extends('layouts.app')

@section('title', 'রিকোয়েস্ট ডিটেইলস — রক্তদূত')

@section('content')
<div class="max-w-5xl mx-auto">
    
    {{-- হেডার সেকশন --}}
    <div class="flex flex-col md:flex-row md:items-start justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-extrabold tracking-tight">রিকোয়েস্ট ডিটেইলস</h1>
            <p class="text-slate-500 font-medium mt-1">Accepted ডোনার লিস্ট এবং স্ট্যাটাস</p>
        </div>

        <div class="flex items-center gap-3 shrink-0">
            {{-- ৩. রিকোয়েস্ট ফুলফিলমেন্ট বাটন --}}
            @if(auth()->id() === $bloodRequest->requested_by && strtolower($bloodRequest->status) !== 'fulfilled')
                <form action="{{ route('requests.fulfill', $bloodRequest) }}" method="POST" onsubmit="return confirm('আপনি কি রক্ত পেয়েছেন? এটি কনফার্ম করলে রিকোয়েস্টটি ক্লোজ হয়ে যাবে।')">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-extrabold rounded-lg shadow-sm shadow-emerald-200 transition">
                        <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        ✓ Mark as Fulfilled
                    </button>
                </form>
            @endif

            <a href="{{ route('requests.index') }}"
               class="inline-flex items-center justify-center border border-slate-200 bg-white hover:bg-slate-50 text-slate-800 px-4 py-2.5 rounded-lg font-extrabold shadow-sm transition-colors">
                ফিডে ফিরে যান
            </a>
        </div>
    </div>

    {{-- রিকোয়েস্ট কার্ড --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex items-start justify-between gap-3">
            <div class="min-w-0">
                <div class="text-lg font-extrabold truncate">{{ $bloodRequest->patient_name ?? 'রোগী' }}</div>
                <div class="text-sm text-slate-500 font-medium truncate mt-1">{{ $bloodRequest->hospital ?? 'হাসপাতাল উল্লেখ নেই' }}</div>
                <div class="text-sm text-slate-500 font-semibold mt-2">
                    লোকেশন: <span class="text-slate-800 font-extrabold">{{ $bloodRequest->upazila?->name ?? '-' }}, {{ $bloodRequest->district?->name ?? '-' }}</span>
                </div>
            </div>

            <div class="shrink-0 px-3 py-1 rounded-lg bg-red-50 text-red-700 border border-red-100 font-extrabold">
                {{ $bloodRequest->blood_group?->value ?? (string) $bloodRequest->blood_group }}
            </div>
        </div>

        <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-3 text-sm">
            <div class="rounded-xl bg-slate-50 border border-slate-100 p-3">
                <div class="text-xs text-slate-500 font-semibold">দরকার</div>
                <div class="font-extrabold text-slate-800 mt-1">{{ $bloodRequest->needed_at?->format('d M, Y h:i A') ?? 'ASAP' }}</div>
            </div>
            <div class="rounded-xl bg-slate-50 border border-slate-100 p-3">
                <div class="text-xs text-slate-500 font-semibold">ব্যাগ</div>
                <div class="font-extrabold text-slate-800 mt-1">{{ $bloodRequest->bags_needed ?? '-' }}</div>
            </div>
            <div class="rounded-xl bg-slate-50 border border-slate-100 p-3">
                <div class="text-xs text-slate-500 font-semibold">স্ট্যাটাস</div>
                <div class="font-extrabold text-slate-800 mt-1 uppercase">{{ $bloodRequest->status }}</div>
            </div>
        </div>

        @if($bloodRequest->notes)
            <div class="mt-4">
                <div class="text-xs text-slate-500 font-semibold">নোট</div>
                <div class="mt-1 font-semibold text-slate-800 whitespace-pre-line">{{ $bloodRequest->notes }}</div>
            </div>
        @endif
    </div>

    {{-- 🎯 ডোনারের জন্য Donation Claim Action Box --}}
    @php
        // বর্তমান ইউজারের রেসপন্সটি খুঁজে বের করা হচ্ছে
        $myResponse = \App\Models\BloodRequestResponse::where('blood_request_id', $bloodRequest->id)
                        ->where('user_id', auth()->id())
                        ->first();
    @endphp

    @if($myResponse && strtolower($myResponse->status) === 'accepted' && $myResponse->verification_status === 'pending')
        <div x-data="{ claimMethod: 'pin' }" class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden mt-6">
            <div class="px-6 py-4 bg-slate-50 border-b border-slate-100">
                <h3 class="text-lg font-extrabold text-slate-800">রক্তদান কনফার্ম করুন</h3>
                <p class="text-sm font-medium text-slate-500">পয়েন্ট এবং ব্যাজ পেতে আপনার রক্তদান ভেরিফাই করুন।</p>
            </div>

            <form action="{{ route('donations.claim', $myResponse->id) }}" method="POST" enctype="multipart/form-data" class="p-6">
                @csrf
                
                {{-- Method Selector (Tabs) --}}
                <div class="flex p-1 bg-slate-100 rounded-xl mb-6">
                    <button type="button" @click="claimMethod = 'pin'" :class="{'bg-white shadow-sm text-red-600': claimMethod === 'pin', 'text-slate-500 hover:text-slate-700': claimMethod !== 'pin'}" class="flex-1 py-2.5 text-sm font-bold rounded-lg transition-all">
                        পিন কোড (ইনস্ট্যান্ট)
                    </button>
                    <button type="button" @click="claimMethod = 'image'" :class="{'bg-white shadow-sm text-red-600': claimMethod === 'image', 'text-slate-500 hover:text-slate-700': claimMethod !== 'image'}" class="flex-1 py-2.5 text-sm font-bold rounded-lg transition-all">
                        ছবি আপলোড (রিভিউ)
                    </button>
                </div>

                <input type="hidden" name="claim_method" x-model="claimMethod">

                {{-- PIN Input Block --}}
                <div x-show="claimMethod === 'pin'" x-transition.opacity class="space-y-4">
                    <label class="block text-sm font-bold text-slate-700">রোগীর লোকের কাছ থেকে পাওয়া ৪-ডিজিটের পিনটি দিন:</label>
                    <input type="text" name="pin" placeholder="যেমন: 8492" class="w-full text-center tracking-[0.5em] text-2xl font-black rounded-xl border-slate-300 focus:ring-red-500 focus:border-red-500 py-4 placeholder:text-slate-300">
                    <p class="text-xs font-semibold text-slate-500 text-center">পিন মেলা মাত্রই আপনার প্রোফাইলে পয়েন্ট যুক্ত হবে।</p>
                    @error('pin') <p class="text-red-600 text-xs font-bold text-center mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Image Upload Block --}}
                <div x-show="claimMethod === 'image'" x-transition.opacity style="display: none;" class="space-y-4">
                    <label class="block text-sm font-bold text-slate-700">প্রমাণ আপলোড করুন:</label>
                    <div class="border-2 border-dashed border-slate-300 rounded-xl p-6 text-center hover:bg-slate-50 transition cursor-pointer relative">
                        <input type="file" name="proof_image" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                        <svg class="mx-auto h-10 w-10 text-slate-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <p class="text-sm font-bold text-slate-600">হাসপাতালের বেড স্লিপ বা ব্লাড ব্যাগের ছবি সিলেক্ট করুন</p>
                        <p class="text-xs text-slate-400 mt-1">JPG, PNG (সর্বোচ্চ ২ MB)</p>
                    </div>
                    @error('proof_image') <p class="text-red-600 text-xs font-bold text-center mt-1">{{ $message }}</p> @enderror
                </div>

                <button type="submit" class="w-full mt-6 bg-red-600 hover:bg-red-700 text-white font-black py-3.5 rounded-xl shadow-sm transition">
                    কনফার্ম করুন
                </button>
            </form>
        </div>
    @elseif($myResponse && strtolower($myResponse->status) === 'accepted' && $myResponse->verification_status !== 'pending')
        <div class="mt-6 p-4 bg-emerald-50 border border-emerald-200 rounded-2xl flex items-center justify-center gap-3">
            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span class="text-emerald-800 font-bold">
                @if($myResponse->verification_status === 'verified')
                    আপনার রক্তদান সফলভাবে ভেরিফাইড হয়েছে!
                @else
                    আপনার ক্লেইম রিভিউতে আছে। অনুগ্রহ করে অপেক্ষা করুন।
                @endif
            </span>
        </div>
    @endif

    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-5">
        {{-- রেসপন্স সামারি --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="text-lg font-extrabold">রেসপন্স সারাংশ</div>
            <div class="mt-3 text-sm font-semibold text-slate-700">
                Accepted: <span class="font-extrabold text-emerald-700">{{ $acceptedCount ?? 0 }}</span><br>
                Declined: <span class="font-extrabold text-slate-700">{{ $declinedCount ?? 0 }}</span>
            </div>
        </div>

        {{-- ডোনার লিস্ট --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="text-lg font-extrabold">Accepted ডোনার</div>

            @if(empty($canViewAcceptedDonors) || !$canViewAcceptedDonors)
                <div class="mt-3 text-sm text-slate-500 font-medium p-4 bg-slate-50 rounded-xl border border-slate-100">
                    প্রাইভেসির কারণে আপনি accepted ডোনারদের তালিকা দেখতে পারবেন না। শুধুমাত্র রিকোয়েস্টের মালিক এটি দেখতে পারবেন।
                </div>
            @else
                @if(empty($acceptedResponses) || $acceptedResponses->isEmpty())
                    <div class="mt-3 text-sm text-slate-500 font-medium p-4 bg-slate-50 rounded-xl border border-slate-100">
                        এখনো কেউ accept করেনি।
                    </div>
                @else
                    <div class="mt-4 space-y-4">
                        @foreach($acceptedResponses as $resp)
                            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="min-w-0">
                                        <div class="font-extrabold text-slate-900 truncate">
                                            {{ $resp->user?->name ?? 'Unknown' }}
                                        </div>
                                        <div class="text-xs text-emerald-600 font-bold mt-1">✓ Accepted</div>
                                    </div>
                                    <button type="button"
                                            class="reveal-btn shrink-0 px-4 py-2 rounded-lg bg-red-600 hover:bg-red-700 text-white font-extrabold text-sm transition-colors"
                                            data-url="{{ route('requests.donors.reveal_phone', ['bloodRequest' => $bloodRequest->id, 'donor' => $resp->user_id]) }}"
                                            data-target="phone-{{ $resp->user_id }}">
                                        Reveal Phone
                                    </button>
                                </div>

                                <div class="text-sm font-semibold text-slate-700 mb-3">
                                    ফোন: <span id="phone-{{ $resp->user_id }}" class="font-extrabold text-slate-900 tracking-wider">Hidden</span>
                                </div>

                                {{-- 🎯 রোগীর লোকের জন্য PIN Display Box --}}
                                <div class="p-3 bg-white rounded-lg border border-red-100 flex items-center justify-between shadow-sm">
                                    <div>
                                        <p class="text-[10px] font-bold text-red-500 uppercase tracking-wider">ভেরিফিকেশন পিন (ডোনারকে দিন)</p>
                                        <p class="text-2xl font-black text-red-600 tracking-widest mt-0.5">{{ $resp->verification_pin ?? '----' }}</p>
                                    </div>
                                    @if($resp->verification_status === 'verified')
                                        <span class="bg-emerald-100 text-emerald-700 text-[10px] font-black px-2 py-1 rounded uppercase tracking-wide">Verified</span>
                                    @elseif($resp->verification_status === 'claimed')
                                        <span class="bg-amber-100 text-amber-700 text-[10px] font-black px-2 py-1 rounded uppercase tracking-wide">Reviewing</span>
                                    @else
                                        <span class="bg-slate-100 text-slate-500 text-[10px] font-black px-2 py-1 rounded uppercase tracking-wide">Pending</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>

{{-- Reveal Phone Number Script --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
    document.addEventListener('click', async (e) => {
        const btn = e.target.closest('.reveal-btn');
        if (!btn) return;

        const url = btn.getAttribute('data-url');
        const targetId = btn.getAttribute('data-target');
        const targetEl = document.getElementById(targetId);

        if (!url || !targetEl) return;

        btn.disabled = true;
        const originalText = btn.textContent;
        btn.textContent = 'Revealing...';
        btn.classList.add('opacity-75', 'cursor-not-allowed');

        try {
            const res = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            });

            const data = await res.json();

            if (!res.ok) {
                throw new Error(data.message || 'নাম্বার দেখতে সমস্যা হচ্ছে। আবার চেষ্টা করুন।');
            }
            
            targetEl.textContent = data.phone ?? 'N/A';
            btn.textContent = 'Revealed';
            btn.classList.replace('bg-red-600', 'bg-slate-400');
            btn.classList.replace('hover:bg-red-700', 'hover:bg-slate-500');
            btn.classList.remove('opacity-75', 'cursor-not-allowed');
            
            btn.classList.remove('reveal-btn');

        } catch (err) {
            btn.disabled = false;
            btn.textContent = originalText;
            btn.classList.remove('opacity-75', 'cursor-not-allowed');
            alert(err.message);
        }
    });
});
</script>
@endsection