@extends('layouts.app')

@section('title', 'সাপোর্ট ইনবক্স — অ্যাডমিন — রক্তদূত')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">

    {{-- ── পেজ হেডার ──────────────────────────────────────────────────────── --}}
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 flex items-center gap-2">
                <span class="w-9 h-9 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center text-lg">📬</span>
                সাপোর্ট ইনবক্স
            </h1>
            <p class="text-slate-500 text-sm font-semibold mt-1">
                ভিজিটর ও সদস্যদের পাঠানো সকল যোগাযোগ বার্তা
            </p>
        </div>

        {{-- নতুন বার্তার ব্যাজ --}}
        @if($counts['new'] > 0)
            <span class="inline-flex items-center gap-1.5 bg-red-600 text-white text-sm font-black px-4 py-2 rounded-full shadow-sm animate-pulse">
                <span class="w-2 h-2 rounded-full bg-white"></span>
                {{ $counts['new'] }} টি নতুন বার্তা
            </span>
        @endif
    </div>

    {{-- সাকসেস/এরর মেসেজ --}}
    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl font-bold flex items-center gap-2">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- ── স্ট্যাটাস ফিল্টার ট্যাব ─────────────────────────────────────────── --}}
    <div class="mb-6 flex flex-wrap gap-2">
        @php
            $tabs = [
                null         => ['label' => 'সব বার্তা', 'count' => $counts['total'],       'color' => 'slate'],
                'new'        => ['label' => 'নতুন',       'count' => $counts['new'],         'color' => 'blue'],
                'in_progress'=> ['label' => 'প্রক্রিয়াধীন', 'count' => $counts['in_progress'], 'color' => 'amber'],
                'resolved'   => ['label' => 'সমাধান হয়েছে', 'count' => $counts['resolved'],    'color' => 'emerald'],
                'spam'       => ['label' => 'স্প্যাম',    'count' => $counts['spam'],        'color' => 'red'],
            ];
            $colorMap = [
                'slate'   => ['active' => 'bg-slate-800 text-white',    'inactive' => 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50'],
                'blue'    => ['active' => 'bg-blue-600 text-white',     'inactive' => 'bg-white text-blue-600  border border-blue-200  hover:bg-blue-50'],
                'amber'   => ['active' => 'bg-amber-500 text-white',    'inactive' => 'bg-white text-amber-600 border border-amber-200 hover:bg-amber-50'],
                'emerald' => ['active' => 'bg-emerald-600 text-white',  'inactive' => 'bg-white text-emerald-600 border border-emerald-200 hover:bg-emerald-50'],
                'red'     => ['active' => 'bg-red-600 text-white',      'inactive' => 'bg-white text-red-600   border border-red-200   hover:bg-red-50'],
            ];
        @endphp

        @foreach($tabs as $key => $tab)
            @php
                $isActive = $status === $key || ($status === null && $key === null);
                $classes  = $isActive ? $colorMap[$tab['color']]['active'] : $colorMap[$tab['color']]['inactive'];
            @endphp
            <a
                href="{{ route('admin.support.messages.index', $key ? ['status' => $key] : []) }}"
                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-bold transition-all {{ $classes }}"
            >
                {{ $tab['label'] }}
                <span class="text-[11px] font-black {{ $isActive ? 'bg-white/20' : 'bg-slate-100' }} px-1.5 py-0.5 rounded-full min-w-[20px] text-center leading-none py-1">
                    {{ $tab['count'] }}
                </span>
            </a>
        @endforeach
    </div>

    {{-- ── বার্তার তালিকা ──────────────────────────────────────────────────── --}}
    @if($messages->isEmpty())
        <div class="bg-white rounded-2xl border border-slate-200 p-16 text-center flex flex-col items-center">
            <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center text-slate-300 text-3xl mb-4">📭</div>
            <h3 class="text-lg font-extrabold text-slate-700">কোনো বার্তা নেই</h3>
            <p class="text-slate-400 font-semibold text-sm mt-1">এই ক্যাটাগরিতে এখনো কোনো বার্তা আসেনি।</p>
        </div>
    @else
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-xs font-extrabold text-slate-500 uppercase tracking-wider">
                            <th class="text-left px-5 py-4 w-8">#</th>
                            <th class="text-left px-5 py-4">প্রেরক</th>
                            <th class="text-left px-5 py-4">বিষয়</th>
                            <th class="text-left px-5 py-4 hidden md:table-cell">বার্তার সারসংক্ষেপ</th>
                            <th class="text-center px-5 py-4">স্ট্যাটাস</th>
                            <th class="text-right px-5 py-4 hidden sm:table-cell">তারিখ</th>
                            <th class="text-center px-5 py-4">দেখুন</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($messages as $msg)
                            <tr class="hover:bg-slate-50/60 transition {{ $msg->status === 'new' ? 'bg-blue-50/30' : 'bg-white' }}">
                                {{-- # ID --}}
                                <td class="px-5 py-4 text-xs font-bold text-slate-400">#{{ $msg->id }}</td>

                                {{-- প্রেরক --}}
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-2.5">
                                        <div class="shrink-0 w-8 h-8 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center font-black text-xs text-slate-600">
                                            {{ mb_substr($msg->sender_name, 0, 1) }}
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-extrabold text-slate-900 truncate max-w-[120px]">{{ $msg->sender_name }}</p>
                                            <p class="text-xs text-slate-400 font-medium truncate max-w-[120px]">{{ $msg->email }}</p>
                                        </div>
                                    </div>
                                </td>

                                {{-- বিষয় --}}
                                <td class="px-5 py-4">
                                    <p class="font-bold text-slate-800 line-clamp-1 max-w-[160px]">{{ $msg->subject }}</p>
                                    @if($msg->status === 'new')
                                        <span class="text-[10px] bg-blue-100 text-blue-700 px-1.5 py-0.5 rounded font-bold">NEW</span>
                                    @endif
                                </td>

                                {{-- প্রিভিউ --}}
                                <td class="px-5 py-4 hidden md:table-cell">
                                    <p class="text-slate-500 text-xs font-medium line-clamp-2 max-w-[200px]">{{ $msg->preview }}</p>
                                </td>

                                {{-- স্ট্যাটাস --}}
                                <td class="px-5 py-4 text-center">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-extrabold {{ $msg->status_color }}">
                                        {{ $msg->status_label }}
                                    </span>
                                </td>

                                {{-- তারিখ --}}
                                <td class="px-5 py-4 text-right hidden sm:table-cell">
                                    <p class="text-xs font-semibold text-slate-500" title="{{ $msg->created_at->format('d M Y, h:i A') }}">
                                        {{ $msg->created_at->diffForHumans() }}
                                    </p>
                                </td>

                                {{-- দেখুন --}}
                                <td class="px-5 py-4 text-center">
                                    <a
                                        href="{{ route('admin.support.messages.show', $msg->id) }}"
                                        class="inline-flex items-center gap-1 bg-slate-800 hover:bg-slate-700 text-white text-xs font-bold px-3 py-2 rounded-lg transition shadow-sm"
                                    >
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        খুলুন
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ── Pagination ─────────────────────────────────────────────────── --}}
        @if($messages->hasPages())
            <div class="mt-6">
                {{ $messages->links() }}
            </div>
        @endif
    @endif

</div>
@endsection
