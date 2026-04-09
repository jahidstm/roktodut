@extends('layouts.app')

@section('title', $user->name . ' — Gamification Governance')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-8 space-y-8">

    {{-- ── ব্র্যাডক্রাম + ব্যাক ── --}}
    <div class="flex items-center gap-2 text-sm text-slate-500 font-medium">
        <a href="{{ route('admin.gamification.index') }}" class="hover:text-slate-800 transition">Governance</a>
        <span>/</span>
        <span class="text-slate-800 font-bold">{{ $user->name }}</span>
    </div>

    {{-- ── Flash ── --}}
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl font-semibold flex items-center gap-2">
            {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div class="p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl font-semibold">
            @foreach($errors->all() as $error)
                <div>⚠️ {{ $error }}</div>
            @endforeach
        </div>
    @endif

    {{-- ════════════════════════════════════════════
         ১. ইউজার প্রোফাইল স্ন্যাপশট + শ্যাডোব্যান টগল
         ════════════════════════════════════════════ --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

        {{-- Shadowbanned Banner --}}
        @if($user->is_shadowbanned)
            <div class="bg-red-600 px-6 py-2.5 flex items-center gap-2 text-white text-sm font-bold">
                🚫 এই ইউজারটি শ্যাডোব্যান্ড — লিডারবোর্ডে দেখা যাচ্ছে না।
            </div>
        @endif

        <div class="p-6 flex flex-col sm:flex-row sm:items-center gap-6">
            {{-- অ্যাভাটার --}}
            <div class="w-16 h-16 rounded-2xl flex items-center justify-center font-black text-2xl shrink-0
                        {{ $user->is_shadowbanned ? 'bg-red-100 text-red-600' : 'bg-red-50 text-red-500' }}">
                {{ mb_substr($user->name, 0, 1) }}
            </div>

            {{-- তথ্য --}}
            <div class="flex-1">
                <h2 class="text-xl font-extrabold text-slate-900">{{ $user->name }}</h2>
                <p class="text-slate-500 text-sm">{{ $user->email }} · {{ $user->phone ?? 'ফোন নেই' }}</p>
                <div class="flex flex-wrap gap-2 mt-3">
                    <span class="bg-slate-100 text-slate-600 text-xs font-bold px-2.5 py-1 rounded-full">
                        🩸 {{ $user->blood_group?->value ?? 'N/A' }}
                    </span>
                    <span class="bg-blue-100 text-blue-700 text-xs font-bold px-2.5 py-1 rounded-full">
                        {{ number_format($user->points ?? 0) }} পয়েন্ট
                    </span>
                    <span class="bg-emerald-100 text-emerald-700 text-xs font-bold px-2.5 py-1 rounded-full">
                        {{ $user->total_verified_donations ?? 0 }}টি ভেরিফাইড ডোনেশন
                    </span>
                    @if($user->district)
                        <span class="bg-slate-100 text-slate-600 text-xs font-bold px-2.5 py-1 rounded-full">
                            📍 {{ $user->district->name }}
                        </span>
                    @endif
                </div>
            </div>

            {{-- শ্যাডোব্যান টগল বাটন --}}
            <form action="{{ route('admin.gamification.shadowban', $user) }}" method="POST">
                @csrf
                @if($user->is_shadowbanned)
                    <button type="submit"
                            onclick="return confirm('{{ $user->name }}-এর শ্যাডোব্যান তুলে নেবেন?')"
                            class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-extrabold px-5 py-2.5 rounded-xl transition shadow-sm">
                        ✅ আনব্যান করুন
                    </button>
                @else
                    <button type="submit"
                            onclick="return confirm('{{ $user->name }}-কে শ্যাডোব্যান করবেন? সে লিডারবোর্ড থেকে বাদ পড়বে।')"
                            class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white text-sm font-extrabold px-5 py-2.5 rounded-xl transition shadow-sm">
                        🚫 শ্যাডোব্যান করুন
                    </button>
                @endif
            </form>
        </div>
    </div>

    {{-- ════════════════════════════════════════════
         ২. Manual Point Adjustment
         ════════════════════════════════════════════ --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-2">
            <span class="text-lg">🔧</span>
            <h3 class="font-extrabold text-slate-800">ম্যানুয়াল পয়েন্ট অ্যাডজাস্টমেন্ট</h3>
        </div>
        <div class="p-6">
            <form action="{{ route('admin.gamification.points.adjust', $user) }}" method="POST"
                  class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                @csrf

                {{-- পয়েন্ট ইনপুট --}}
                <div>
                    <label class="block text-xs font-extrabold text-slate-500 uppercase tracking-wider mb-1.5">
                        পয়েন্ট (+ বা -)
                    </label>
                    <input
                        type="number"
                        name="points"
                        value="{{ old('points') }}"
                        placeholder="যেমন: -50 বা +100"
                        required
                        class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm font-bold text-slate-800
                               focus:outline-none focus:ring-2 focus:ring-red-300 @error('points') border-red-400 @enderror"
                    >
                    @error('points')
                        <p class="text-red-600 text-xs font-semibold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- কারণ --}}
                <div class="sm:col-span-2">
                    <label class="block text-xs font-extrabold text-slate-500 uppercase tracking-wider mb-1.5">
                        কারণ (Reason) <span class="text-red-500">*</span>
                    </label>
                    <div class="flex gap-3">
                        <input
                            type="text"
                            name="reason"
                            value="{{ old('reason') }}"
                            placeholder="যেমন: ফেক ডোনেশন ক্লেইম সনাক্ত, পেনাল্টি প্রযোজ্য"
                            required
                            class="flex-1 border border-slate-200 rounded-xl px-4 py-2.5 text-sm font-medium text-slate-800
                                   focus:outline-none focus:ring-2 focus:ring-red-300 @error('reason') border-red-400 @enderror"
                        >
                        <button type="submit"
                                class="bg-slate-800 hover:bg-slate-700 text-white text-sm font-extrabold px-6 py-2.5 rounded-xl transition shrink-0">
                            প্রয়োগ করুন
                        </button>
                    </div>
                    @error('reason')
                        <p class="text-red-600 text-xs font-semibold mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </form>

            <p class="text-xs text-slate-400 font-medium mt-3">
                💡 পজিটিভ মান (+) পয়েন্ট বোনাস, নেগেটিভ মান (-) পয়েন্ট কেটে নেবে। এই রেকর্ড <code>point_logs</code>-এ সেভ হবে।
            </p>
        </div>
    </div>

    {{-- ════════════════════════════════════════════
         ৩. Audit Trail — Point Log History
         ════════════════════════════════════════════ --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="text-lg">📋</span>
                <h3 class="font-extrabold text-slate-800">পয়েন্ট লগ (অডিট ট্রেইল)</h3>
            </div>
            <span class="text-xs text-slate-400 font-semibold">সর্বশেষ ১০টি রেকর্ড</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="text-left px-5 py-3 text-xs font-extrabold text-slate-500 uppercase tracking-wider">অ্যাকশন</th>
                        <th class="text-center px-4 py-3 text-xs font-extrabold text-slate-500 uppercase tracking-wider">পয়েন্ট</th>
                        <th class="text-left px-4 py-3 text-xs font-extrabold text-slate-500 uppercase tracking-wider">বিস্তারিত</th>
                        <th class="text-right px-5 py-3 text-xs font-extrabold text-slate-500 uppercase tracking-wider">তারিখ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($pointLogs as $log)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-5 py-3.5 font-semibold text-slate-700">
                                {{ $log->actionLabel() }}
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                <span class="font-extrabold {{ $log->points >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                                    {{ $log->points >= 0 ? '+' : '' }}{{ $log->points }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-slate-500 max-w-[240px] truncate">
                                @if($log->metadata)
                                    @if(isset($log->metadata['reason']))
                                        <span class="italic">{{ $log->metadata['reason'] }}</span>
                                    @elseif(isset($log->metadata['blood_request_id']))
                                        রিকোয়েস্ট #{{ $log->metadata['blood_request_id'] }}
                                    @else
                                        —
                                    @endif
                                @else
                                    —
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-right text-slate-400 whitespace-nowrap">
                                {{ $log->created_at->format('d M y • h:i A') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-10 text-center text-slate-400 font-semibold">
                                কোনো পয়েন্ট লগ পাওয়া যায়নি।
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ════════════════════════════════════════════
         ৪. Badge Assignment Panel
         ════════════════════════════════════════════ --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-2">
            <span class="text-lg">🏅</span>
            <h3 class="font-extrabold text-slate-800">ব্যাজ ম্যানেজমেন্ট</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($allBadges as $badge)
                    @php $owned = in_array($badge->id, $ownedBadgeIds); @endphp
                    <div class="flex items-center justify-between p-4 rounded-xl border
                                {{ $owned ? 'border-emerald-200 bg-emerald-50' : 'border-slate-200 bg-slate-50' }}">
                        <div class="flex items-center gap-3 min-w-0">
                            <span class="text-2xl shrink-0">
                                {{ \App\Services\GamificationService::getBadgeDisplayData($badge->name)['emoji'] ?? '🏅' }}
                            </span>
                            <div class="min-w-0">
                                <p class="text-sm font-extrabold text-slate-800 truncate">
                                    {{ $badge->bn_name ?? $badge->name }}
                                </p>
                                <p class="text-xs text-slate-500 font-medium">{{ $badge->name }}</p>
                            </div>
                        </div>

                        {{-- অ্যাসাইন / রিমুভ ফর্ম --}}
                        <form action="{{ route('admin.gamification.badges.assign', $user) }}" method="POST" class="ml-2 shrink-0">
                            @csrf
                            <input type="hidden" name="badge_id" value="{{ $badge->id }}">
                            <input type="hidden" name="action" value="{{ $owned ? 'detach' : 'attach' }}">
                            @if($owned)
                                <button type="submit"
                                        onclick="return confirm('এই ব্যাজটি সরিয়ে নেবেন?')"
                                        class="text-xs font-extrabold text-red-600 border border-red-200 bg-white hover:bg-red-50 px-3 py-1.5 rounded-lg transition">
                                    সরান
                                </button>
                            @else
                                <button type="submit"
                                        class="text-xs font-extrabold text-emerald-700 border border-emerald-200 bg-white hover:bg-emerald-50 px-3 py-1.5 rounded-lg transition">
                                    দিন
                                </button>
                            @endif
                        </form>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

</div>
@endsection
