@extends('layouts.donor-dashboard')

@section('title', 'আমার সার্টিফিকেট — রক্তদূত')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 py-6" data-panel-id="certificates">

    {{-- ── Page Header ── --}}
    <div class="mb-6 scroll-reveal" data-scroll-reveal>
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center shadow-md shadow-amber-200">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div>
                <h1 class="text-xl font-black text-slate-900">আমার সার্টিফিকেট</h1>
                <p class="text-sm text-slate-500 font-medium mt-0.5">আপনার প্রতিটি রক্তদানের অফিসিয়াল স্বীকৃতিপত্র</p>
            </div>
            @if($totalCertificates > 0)
            <div class="ml-auto">
                <span class="inline-flex items-center gap-1.5 bg-amber-50 text-amber-700 border border-amber-200 text-sm font-black px-3 py-1.5 rounded-xl">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    {{ $totalCertificates }}টি সার্টিফিকেট
                </span>
            </div>
            @endif
        </div>
    </div>

    {{-- ── Empty State ── --}}
    @if($donations->isEmpty())
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-12 text-center scroll-reveal" data-scroll-reveal>
        <div class="w-20 h-20 rounded-2xl bg-amber-50 flex items-center justify-center mx-auto mb-5">
            <svg class="w-10 h-10 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
        </div>
        <h3 class="text-lg font-black text-slate-800 mb-2">এখনো কোনো সার্টিফিকেট নেই</h3>
        <p class="text-sm text-slate-500 font-medium max-w-sm mx-auto leading-relaxed">
            আপনার রক্তদান ভেরিফাই হলে স্বয়ংক্রিয়ভাবে এখানে সার্টিফিকেট তৈরি হবে।
        </p>
        <a href="{{ route('donor.blood_history') }}"
           class="mt-6 inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white font-bold text-sm px-5 py-2.5 rounded-xl transition shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            রক্তদান হিস্ট্রি দেখুন
        </a>
    </div>

    @else

    {{-- ── Certificate Cards Grid ── --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        @foreach($donations as $index => $donation)
        @php
            $certId     = 'RKDT-' . now()->format('Y') . '-' . str_pad($donation->id, 5, '0', STR_PAD_LEFT);
            $bgColors   = ['from-red-900 to-red-800','from-slate-800 to-slate-700','from-rose-900 to-rose-800'];
            $bg         = $bgColors[$index % count($bgColors)];
            $donateDate = $donation->donation_date?->format('d M, Y') ?? ($donation->created_at?->format('d M, Y') ?? '—');
            $hospital   = $donation->bloodRequest?->hospital?->display_name ?? 'সরাসরি দান';
            $district   = $donation->bloodRequest?->district?->name ?? '';
        @endphp

        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 scroll-reveal" data-scroll-reveal>

            {{-- Certificate Preview Banner --}}
            <div class="bg-gradient-to-br {{ $bg }} relative h-32 flex items-center justify-center overflow-hidden">
                {{-- Background pattern --}}
                <div class="absolute inset-0 opacity-10">
                    <svg width="100%" height="100%" viewBox="0 0 200 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="20" cy="20" r="40" stroke="white" stroke-width="0.5" fill="none"/>
                        <circle cx="180" cy="80" r="40" stroke="white" stroke-width="0.5" fill="none"/>
                        <circle cx="100" cy="50" r="60" stroke="white" stroke-width="0.3" fill="none"/>
                    </svg>
                </div>
                {{-- Certificate Icon & Title --}}
                <div class="relative text-center z-10 px-4">
                    <div class="flex items-center justify-center gap-2 mb-1">
                        <svg class="w-4 h-4 text-amber-300 opacity-80" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        <span class="text-xs font-bold text-amber-200 uppercase tracking-widest">Certificate of Appreciation</span>
                        <svg class="w-4 h-4 text-amber-300 opacity-80" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    </div>
                    <p class="text-2xl font-black text-white tracking-tight">{{ strtoupper(auth()->user()->name) }}</p>
                    <p class="text-xs text-white/60 font-semibold mt-1">has successfully donated blood</p>
                </div>
                {{-- Cert ID badge --}}
                <div class="absolute bottom-2 right-3">
                    <span class="text-[9px] font-bold text-white/40 font-mono">{{ $certId }}</span>
                </div>
            </div>

            {{-- Card Body --}}
            <div class="px-5 py-4">
                {{-- Donation Info --}}
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-9 h-9 rounded-xl bg-red-50 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-black text-slate-900">{{ $donateDate }}</p>
                        <p class="text-xs text-slate-500 font-medium truncate">{{ $hospital }}{{ $district ? ' · ' . $district : '' }}</p>
                    </div>
                    <div class="ml-auto shrink-0">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-700 uppercase tracking-widest">✓ ভেরিফাইড</span>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex items-center gap-2 pt-3 border-t border-slate-100">
                    {{-- View/Share --}}
                    <a href="{{ route('certificate.show', $donation->certificate_token) }}"
                       target="_blank"
                       class="flex-1 flex items-center justify-center gap-1.5 bg-slate-900 hover:bg-slate-700 text-white text-xs font-bold py-2.5 px-3 rounded-xl transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        দেখুন ও শেয়ার করুন
                    </a>

                    {{-- Download --}}
                    <a href="{{ route('certificate.download', $donation->certificate_token) }}?v={{ time() }}"
                       title="PNG ডাউনলোড করুন"
                       class="flex items-center justify-center gap-1.5 bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold py-2.5 px-3.5 rounded-xl transition shadow-sm shadow-amber-200">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        ডাউনলোড
                    </a>

                    {{-- Copy Link --}}
                    <button
                        onclick="navigator.clipboard.writeText('{{ route('certificate.show', $donation->certificate_token) }}').then(() => { this.classList.add('bg-emerald-100','text-emerald-700'); this.title='কপি হয়েছে!'; setTimeout(() => this.classList.remove('bg-emerald-100','text-emerald-700'), 2000) })"
                        title="লিংক কপি করুন"
                        class="flex items-center justify-center w-9 h-9 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    </button>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- ── Info Footer ── --}}
    <div class="mt-6 bg-amber-50 border border-amber-200 rounded-2xl px-5 py-4 flex items-start gap-3 scroll-reveal" data-scroll-reveal>
        <svg class="w-5 h-5 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div>
            <p class="text-sm font-bold text-amber-800">সার্টিফিকেট সম্পর্কে জানুন</p>
            <p class="text-xs text-amber-700 font-medium mt-0.5 leading-relaxed">
                প্রতিটি রক্তদান ভেরিফাই হওয়ার পর স্বয়ংক্রিয়ভাবে একটি অনন্য সার্টিফিকেট তৈরি হয়।
                এই সার্টিফিকেট সোশ্যাল মিডিয়ায় শেয়ার করে অন্যদের অনুপ্রাণিত করুন!
            </p>
        </div>
    </div>

    @endif

</div>
@endsection
