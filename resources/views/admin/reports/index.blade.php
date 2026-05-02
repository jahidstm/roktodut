@extends('layouts.app')

@section('title', 'রিপোর্ট ট্রায়াজ — অ্যাডমিন — রক্তদূত')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">
    <div class="mb-6">
        <h1 class="text-2xl font-extrabold text-slate-900">রিপোর্ট ট্রায়াজ</h1>
        <p class="text-sm text-slate-500 font-semibold mt-1">ইউজার ও গেস্ট রিপোর্ট রিভিউ করুন</p>
    </div>

    @if(session('success'))
        <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-800 font-bold">
            {{ session('success') }}
        </div>
    @endif

    @php
        $tabs = [
            null => ['label' => 'সব', 'count' => $counts['total']],
            'open' => ['label' => 'Open', 'count' => $counts['open']],
            'reviewing' => ['label' => 'Reviewing', 'count' => $counts['reviewing']],
            'resolved' => ['label' => 'Resolved', 'count' => $counts['resolved']],
            'dismissed' => ['label' => 'Dismissed', 'count' => $counts['dismissed']],
        ];
    @endphp

    <div class="mb-6 flex flex-wrap gap-2">
        @foreach($tabs as $key => $tab)
            @php $active = ($status === $key) || ($status === null && $key === null); @endphp
            <a href="{{ route('admin.reports.index', $key ? ['status' => $key] : []) }}"
               class="inline-flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-bold {{ $active ? 'bg-slate-900 text-white' : 'border border-slate-200 bg-white text-slate-700 hover:bg-slate-50' }}">
                <span>{{ $tab['label'] }}</span>
                <span class="rounded-full px-2 py-0.5 text-xs {{ $active ? 'bg-white/20' : 'bg-slate-100' }}">{{ $tab['count'] }}</span>
            </a>
        @endforeach
    </div>

    <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-sm">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-500 uppercase text-xs">
                <tr>
                    <th class="text-left px-4 py-3">#</th>
                    <th class="text-left px-4 py-3">টার্গেট</th>
                    <th class="text-left px-4 py-3">ক্যাটাগরি</th>
                    <th class="text-left px-4 py-3">রিপোর্টার</th>
                    <th class="text-left px-4 py-3">স্ট্যাটাস</th>
                    <th class="text-left px-4 py-3">তারিখ</th>
                    <th class="text-right px-4 py-3">অ্যাকশন</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($reports as $report)
                    <tr>
                        <td class="px-4 py-3 font-bold text-slate-500">#{{ $report->id }}</td>
                        <td class="px-4 py-3">
                            <div class="font-bold text-slate-800">{{ class_basename($report->reportable_type) }}</div>
                            <div class="text-xs text-slate-500">ID: {{ $report->reportable_id }}</div>
                        </td>
                        <td class="px-4 py-3 font-semibold text-slate-700">{{ str_replace('_', ' ', $report->category) }}</td>
                        <td class="px-4 py-3">
                            <div class="font-semibold text-slate-700">{{ $report->reporter_type }}</div>
                            @if($report->reporter)
                                <div class="text-xs text-slate-500">{{ $report->reporter->name }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex rounded-lg bg-slate-100 px-2.5 py-1 text-xs font-black text-slate-700">{{ $report->status }}</span>
                        </td>
                        <td class="px-4 py-3 text-xs font-semibold text-slate-500">{{ $report->created_at?->diffForHumans() }}</td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.reports.show', $report) }}" class="inline-flex rounded-lg bg-slate-800 px-3 py-2 text-xs font-bold text-white hover:bg-slate-700">খুলুন</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-10 text-center text-slate-500 font-semibold">কোনো রিপোর্ট নেই।</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($reports->hasPages())
        <div class="mt-6">{{ $reports->links() }}</div>
    @endif
</div>
@endsection
