@extends('layouts.app')

@section('title', 'Gamification Governance — রক্তদূত অ্যাডমিন')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">

    {{-- ── হেডার ── --}}
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 flex items-center gap-2">
                <span class="w-9 h-9 rounded-xl bg-red-100 text-red-600 flex items-center justify-center text-lg">
                    🎮
                </span>
                Gamification Governance
            </h1>
            <p class="text-slate-500 text-sm font-semibold mt-1">ডোনারদের পয়েন্ট, ব্যাজ ও লিডারবোর্ড স্ট্যাটাস কন্ট্রোল করুন।</p>
        </div>
        <a href="{{ route('admin.dashboard') }}"
           class="inline-flex items-center gap-2 text-sm font-bold text-slate-600 hover:text-slate-900 transition">
            ← অ্যাডমিন ড্যাশবোর্ড
        </a>
    </div>

    {{-- ── Flash Message ── --}}
    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl font-semibold flex items-center gap-2">
            ✅ {{ session('success') }}
        </div>
    @endif

    {{-- ── ফিল্টার / সার্চ বার ── --}}
    <form method="GET" action="{{ route('admin.gamification.index') }}"
          class="mb-6 flex flex-col sm:flex-row gap-3">
        <input
            type="text"
            name="search"
            value="{{ request('search') }}"
            placeholder="নাম, ইমেইল বা ফোন দিয়ে সার্চ করুন…"
            class="flex-1 border border-slate-200 rounded-xl px-4 py-2.5 text-sm font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-red-300"
        >
        <label class="flex items-center gap-2 text-sm font-semibold text-slate-700 cursor-pointer
                       border border-slate-200 rounded-xl px-4 py-2.5 bg-white hover:bg-slate-50 transition">
            <input type="checkbox" name="banned_only" value="1"
                   {{ request()->boolean('banned_only') ? 'checked' : '' }}
                   class="accent-red-600">
            শুধু শ্যাডোব্যান্ড
        </label>
        <button type="submit"
                class="bg-slate-800 text-white text-sm font-bold px-6 py-2.5 rounded-xl hover:bg-slate-700 transition">
            ফিল্টার
        </button>
        @if(request()->hasAny(['search','banned_only']))
            <a href="{{ route('admin.gamification.index') }}"
               class="border border-slate-200 text-slate-600 text-sm font-bold px-5 py-2.5 rounded-xl hover:bg-slate-50 transition">
               রিসেট
            </a>
        @endif
    </form>

    {{-- ── ডোনার টেবিল ── --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200">
                    <th class="text-left px-5 py-3.5 font-extrabold text-slate-600 uppercase tracking-wider text-xs">ডোনার</th>
                    <th class="text-center px-4 py-3.5 font-extrabold text-slate-600 uppercase tracking-wider text-xs">পয়েন্ট</th>
                    <th class="text-center px-4 py-3.5 font-extrabold text-slate-600 uppercase tracking-wider text-xs">ডোনেশন</th>
                    <th class="text-center px-4 py-3.5 font-extrabold text-slate-600 uppercase tracking-wider text-xs">স্ট্যাটাস</th>
                    <th class="text-center px-4 py-3.5 font-extrabold text-slate-600 uppercase tracking-wider text-xs">অ্যাকশন</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($users as $user)
                    <tr class="hover:bg-slate-50/60 transition {{ $user->is_shadowbanned ? 'bg-red-50/40' : '' }}">
                        {{-- নাম + ইমেইল --}}
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full flex items-center justify-center font-black text-sm shrink-0
                                            {{ $user->is_shadowbanned ? 'bg-red-100 text-red-600' : 'bg-red-50 text-red-500' }}">
                                    {{ mb_substr($user->name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="font-bold text-slate-900">{{ $user->name }}</p>
                                    <p class="text-xs text-slate-400">{{ $user->email }}</p>
                                </div>
                            </div>
                        </td>

                        {{-- পয়েন্ট --}}
                        <td class="px-4 py-4 text-center">
                            <span class="font-extrabold text-slate-800">{{ number_format($user->points ?? 0) }}</span>
                            <span class="text-slate-400 text-xs ml-0.5">pts</span>
                        </td>

                        {{-- ডোনেশন কাউন্ট --}}
                        <td class="px-4 py-4 text-center">
                            <span class="font-bold text-slate-700">{{ $user->total_verified_donations ?? 0 }}</span>
                        </td>

                        {{-- স্ট্যাটাস ব্যাজ --}}
                        <td class="px-4 py-4 text-center">
                            @if($user->is_shadowbanned)
                                <span class="inline-flex items-center gap-1 bg-red-100 text-red-700 text-[11px] font-extrabold uppercase tracking-wider px-2.5 py-1 rounded-full">
                                    🚫 Shadowbanned
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 bg-emerald-100 text-emerald-700 text-[11px] font-extrabold uppercase tracking-wider px-2.5 py-1 rounded-full">
                                    ✓ Active
                                </span>
                            @endif
                        </td>

                        {{-- অ্যাকশন --}}
                        <td class="px-4 py-4 text-center">
                            <a href="{{ route('admin.gamification.show', $user) }}"
                               class="inline-block bg-slate-800 text-white text-xs font-bold px-4 py-2 rounded-lg hover:bg-slate-700 transition">
                                ম্যানেজ করুন →
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-16 text-center text-slate-400 font-semibold">
                            কোনো ডোনার পাওয়া যায়নি।
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Pagination --}}
        @if($users->hasPages())
            <div class="px-5 py-4 border-t border-slate-100">
                {{ $users->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
