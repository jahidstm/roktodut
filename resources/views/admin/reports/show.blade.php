@extends('layouts.app')

@section('title', 'রিপোর্ট #' . $report->id . ' — অ্যাডমিন — রক্তদূত')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="mb-6 flex items-center justify-between gap-3">
        <h1 class="text-2xl font-extrabold text-slate-900">রিপোর্ট #{{ $report->id }}</h1>
        <a href="{{ route('admin.reports.index') }}" class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-bold text-slate-700 hover:bg-slate-50">সব রিপোর্ট</a>
    </div>



    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-5">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-sm font-extrabold text-slate-500 uppercase">রিপোর্ট ডিটেইলস</h2>
                <dl class="mt-4 space-y-3 text-sm">
                    <div>
                        <dt class="text-slate-500 font-semibold">টার্গেট</dt>
                        <dd class="font-bold text-slate-800">
                            {{ class_basename($report->reportable_type) }} #{{ $report->reportable_id }}
                            @if(class_basename($report->reportable_type) === 'User')
                                <a href="{{ route('admin.gamification.show', $report->reportable_id) }}" class="ml-2 inline-flex rounded bg-red-100 px-2 py-0.5 text-[10px] font-bold text-red-700 hover:bg-red-200 uppercase tracking-wide">
                                    অ্যাকশন নিন (ব্যান)
                                </a>
                            @endif
                        </dd>
                    </div>
                    <div><dt class="text-slate-500 font-semibold">ক্যাটাগরি</dt><dd class="font-bold text-slate-800">{{ str_replace('_', ' ', $report->category) }}</dd></div>
                    <div><dt class="text-slate-500 font-semibold">স্ট্যাটাস</dt><dd class="font-bold text-slate-800">{{ $report->status }}</dd></div>
                    <div><dt class="text-slate-500 font-semibold">রিপোর্টার টাইপ</dt><dd class="font-bold text-slate-800">{{ $report->reporter_type }}</dd></div>
                    @if($report->reporter)
                        <div><dt class="text-slate-500 font-semibold">রিপোর্টার ইউজার</dt><dd class="font-bold text-slate-800">{{ $report->reporter->name }} ({{ $report->reporter->email }})</dd></div>
                    @endif
                    <div><dt class="text-slate-500 font-semibold">IP Hash</dt><dd class="font-mono text-xs text-slate-700 break-all">{{ $report->reporter_ip_hash }}</dd></div>
                </dl>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-sm font-extrabold text-slate-500 uppercase">রিপোর্ট মেসেজ</h2>
                <p class="mt-3 text-sm font-medium text-slate-700 whitespace-pre-wrap">{{ $report->message ?: 'কোনো অতিরিক্ত বার্তা নেই।' }}</p>
            </div>
        </div>

        <div class="space-y-5">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-sm font-extrabold text-slate-500 uppercase">ট্রায়াজ আপডেট</h2>
                <form method="POST" action="{{ route('admin.reports.status', $report) }}" class="mt-4 space-y-4">
                    @csrf
                    <div>
                        <label for="status" class="block text-xs font-bold text-slate-500 mb-2">স্ট্যাটাস</label>
                        <select id="status" name="status" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-800">
                            @foreach(['open', 'reviewing', 'resolved', 'dismissed'] as $state)
                                <option value="{{ $state }}" @selected($report->status === $state)>{{ ucfirst($state) }}</option>
                            @endforeach
                        </select>
                        @error('status')<p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="admin_note" class="block text-xs font-bold text-slate-500 mb-2">অ্যাডমিন নোট</label>
                        <textarea id="admin_note" name="admin_note" rows="5" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-800">{{ old('admin_note', $report->admin_note) }}</textarea>
                        @error('admin_note')<p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <button type="submit" class="w-full rounded-xl bg-red-600 px-4 py-2.5 text-sm font-black text-white hover:bg-red-700">সেভ করুন</button>
                </form>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm text-xs text-slate-500">
                <p>রিপোর্ট সময়: {{ $report->created_at?->format('d M Y, h:i A') }}</p>
                <p class="mt-2">শেষ আপডেট: {{ $report->updated_at?->diffForHumans() }}</p>
                @if($report->resolver)
                    <p class="mt-2">শেষে সমাধান করেছেন: {{ $report->resolver->name }}</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
