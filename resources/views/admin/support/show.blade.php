@extends('layouts.app')

@section('title', 'বার্তা #' . $message->id . ' — সাপোর্ট — রক্তদূত')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">

    {{-- ── ব্রেডক্রাম ──────────────────────────────────────────────────────── --}}
    <div class="flex items-center gap-2 text-sm font-semibold text-slate-500 mb-6">
        <a href="{{ route('admin.dashboard') }}" class="hover:text-red-600 transition-colors">অ্যাডমিন</a>
        <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <a href="{{ route('admin.support.messages.index') }}" class="hover:text-red-600 transition-colors">সাপোর্ট ইনবক্স</a>
        <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="text-slate-800 font-bold">বার্তা #{{ $message->id }}</span>
    </div>

    {{-- সাকসেস/এরর মেসেজ --}}
    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl font-bold flex items-center gap-2">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ── বাম: বার্তার বিষয়বস্তু ──────────────────────────────────────── --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- মেইন বার্তা কার্ড --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

                {{-- হেডার --}}
                <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h1 class="text-lg font-extrabold text-slate-900 leading-snug">{{ $message->subject }}</h1>
                            <p class="text-xs text-slate-400 font-semibold mt-1">
                                {{ $message->created_at->format('d M Y, h:i A') }}
                                • {{ $message->created_at->diffForHumans() }}
                            </p>
                        </div>
                        <span class="shrink-0 inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-extrabold {{ $message->status_color }}">
                            {{ $message->status_label }}
                        </span>
                    </div>
                </div>

                {{-- বার্তার মূল লেখা --}}
                <div class="px-6 py-6">
                    <div class="prose prose-sm max-w-none text-slate-700 leading-relaxed font-medium whitespace-pre-wrap">{{ $message->message }}</div>
                </div>

            </div>

            {{-- নিরাপত্তা তথ্য (IP/UA) --}}
            @if($message->ip_address || $message->user_agent)
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                    <h3 class="text-sm font-extrabold text-slate-600 flex items-center gap-2">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        নিরাপত্তা মেটাডেটা
                    </h3>
                </div>
                <div class="px-6 py-4 space-y-3">
                    @if($message->ip_address)
                    <div class="flex items-start gap-3">
                        <span class="text-xs font-extrabold text-slate-400 uppercase tracking-wider w-20 shrink-0 pt-0.5">IP ঠিকানা</span>
                        <code class="text-xs bg-slate-100 px-2.5 py-1 rounded-lg font-mono text-slate-700 font-semibold">{{ $message->ip_address }}</code>
                    </div>
                    @endif
                    @if($message->user_agent)
                    <div class="flex items-start gap-3">
                        <span class="text-xs font-extrabold text-slate-400 uppercase tracking-wider w-20 shrink-0 pt-0.5">User-Agent</span>
                        <p class="text-xs text-slate-500 font-medium break-all">{{ $message->user_agent }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif

        </div>

        {{-- ── ডান: প্রেরক তথ্য + স্ট্যাটাস আপডেট ──────────────────────────── --}}
        <div class="space-y-5">

            {{-- প্রেরকের তথ্য কার্ড --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/50">
                    <h3 class="text-sm font-extrabold text-slate-600">প্রেরকের তথ্য</h3>
                </div>
                <div class="px-5 py-5 space-y-4">

                    {{-- অ্যাভাটার --}}
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-full bg-gradient-to-br from-slate-100 to-slate-200 border border-slate-200 flex items-center justify-center font-black text-slate-600 text-base shrink-0">
                            {{ mb_substr($message->sender_name, 0, 1) }}
                        </div>
                        <div>
                            <p class="font-extrabold text-slate-900">{{ $message->sender_name }}</p>
                            @if($message->user)
                                <span class="text-[10px] font-bold bg-red-50 text-red-600 px-2 py-0.5 rounded border border-red-100">
                                    নিবন্ধিত সদস্য
                                </span>
                            @else
                                <span class="text-[10px] font-bold bg-slate-100 text-slate-500 px-2 py-0.5 rounded">
                                    গেস্ট ভিজিটর
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- তথ্য আইটেম --}}
                    <div class="space-y-2.5 text-sm">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-slate-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            <a href="mailto:{{ $message->email }}" class="text-blue-600 hover:underline font-semibold text-xs break-all">{{ $message->email }}</a>
                        </div>
                        @if($message->phone)
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-slate-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            <span class="font-semibold text-slate-700 text-xs">{{ $message->phone }}</span>
                        </div>
                        @endif
                        @if($message->user)
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-slate-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            <span class="font-semibold text-slate-500 text-xs">ইউজার ID: #{{ $message->user_id }}</span>
                        </div>
                        @endif
                    </div>

                    {{-- ইমেইল রিপ্লাই শর্টকাট --}}
                    <a
                        href="mailto:{{ $message->email }}?subject=Re: {{ $message->subject }}"
                        class="mt-2 w-full inline-flex items-center justify-center gap-2 bg-slate-800 hover:bg-slate-700 text-white text-xs font-extrabold px-4 py-2.5 rounded-xl transition-colors"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                        ইমেইলে উত্তর দিন
                    </a>
                </div>
            </div>

            {{-- স্ট্যাটাস আপডেট কার্ড --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/50">
                    <h3 class="text-sm font-extrabold text-slate-600">স্ট্যাটাস আপডেট করুন</h3>
                </div>
                <form
                    method="POST"
                    action="{{ route('admin.support.messages.status', $message->id) }}"
                    class="px-5 py-5 space-y-4"
                >
                    @csrf
                    <div>
                        <label for="msg-status" class="block text-xs font-bold text-slate-500 mb-2">নতুন স্ট্যাটাস</label>
                        <select
                            id="msg-status"
                            name="status"
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-red-300 focus:border-red-400 transition-colors"
                        >
                            <option value="new"          {{ $message->status === 'new'          ? 'selected' : '' }}>নতুন</option>
                            <option value="in_progress"  {{ $message->status === 'in_progress'  ? 'selected' : '' }}>প্রক্রিয়াধীন</option>
                            <option value="resolved"     {{ $message->status === 'resolved'     ? 'selected' : '' }}>সমাধান হয়েছে</option>
                            <option value="spam"         {{ $message->status === 'spam'         ? 'selected' : '' }}>স্প্যাম</option>
                        </select>
                        @error('status')
                            <p class="mt-1.5 text-xs text-red-600 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>
                    <button
                        type="submit"
                        class="w-full bg-red-600 hover:bg-red-700 text-white font-extrabold text-sm px-4 py-3 rounded-xl transition-colors shadow-sm"
                    >
                        স্ট্যাটাস সেভ করুন
                    </button>
                </form>
            </div>

            {{-- ফিরে যাওয়ার লিংক --}}
            <a
                href="{{ route('admin.support.messages.index') }}"
                class="flex items-center justify-center gap-2 w-full border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 font-bold text-sm px-4 py-3 rounded-xl transition-colors"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                ইনবক্সে ফিরে যান
            </a>
        </div>

    </div>
</div>
@endsection
