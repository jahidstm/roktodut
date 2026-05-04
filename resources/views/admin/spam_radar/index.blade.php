@extends('layouts.app')

@section('title', 'Spam Radar - System Command Center')

@section('content')
<div class="py-8 bg-slate-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <h1 class="text-3xl font-black text-slate-900 tracking-tight flex items-center gap-3">
                    <span class="text-red-500">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </span>
                    Spam Radar
                </h1>
                <p class="text-slate-500 font-medium mt-1">Pending fake/spam reports waiting for your review.</p>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl font-medium">
                {{ session('success') }}
            </div>
        @endif

        <div class="space-y-6">
            @forelse($suspectedRequests as $request)
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="p-6">
                        <div class="flex flex-col lg:flex-row gap-6 justify-between">
                            {{-- Request Details --}}
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="px-2.5 py-1 text-xs font-bold rounded-lg bg-red-100 text-red-700">
                                        {{ $request->pending_reports_count }} Pending Reports
                                    </span>
                                    <span class="text-sm font-semibold text-slate-500">
                                        Req ID: #{{ $request->id }}
                                    </span>
                                    @if($request->is_hidden)
                                        <span class="px-2.5 py-1 text-xs font-bold rounded-lg bg-slate-100 text-slate-700">
                                            Currently Auto-Hidden
                                        </span>
                                    @endif
                                </div>
                                <h2 class="text-xl font-bold text-slate-900 mb-1">
                                    Requester: {{ optional($request->requester)->name ?? 'Unknown' }}
                                </h2>
                                <p class="text-sm text-slate-600 mb-2">
                                    Phone: <span class="font-bold">{{ optional($request->requester)->phone ?? '-' }}</span> 
                                    (Req. Phone: <span class="font-bold">{{ $request->contact_number }}</span>)
                                </p>
                                <p class="text-sm text-slate-600 mb-4">
                                    Requester Strikes: 
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full {{ optional($request->requester)->spam_strikes >= 2 ? 'bg-red-600 text-white' : 'bg-red-100 text-red-600' }} font-bold text-xs ml-1">
                                        {{ optional($request->requester)->spam_strikes ?? 0 }}
                                    </span>
                                    @if(optional($request->requester)->is_shadowbanned)
                                        <span class="ml-2 text-xs font-bold text-red-600 uppercase">Shadowbanned</span>
                                    @endif
                                </p>

                                <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
                                    <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Report Details</h3>
                                    <ul class="space-y-3">
                                        @foreach($request->spamReports as $report)
                                            <li class="flex items-start gap-3 text-sm">
                                                <div class="mt-0.5">
                                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                </div>
                                                <div>
                                                    <span class="font-semibold text-slate-800">{{ optional($report->reporter)->name ?? 'Unknown' }}</span>
                                                    <span class="text-slate-500"> reported for </span>
                                                    <span class="font-bold text-red-600">{{ str_replace('_', ' ', $report->reason) }}</span>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div class="w-full lg:w-72 flex flex-col gap-3 justify-center border-t lg:border-t-0 lg:border-l border-slate-100 pt-6 lg:pt-0 lg:pl-6">
                                <a href="{{ route('requests.show', $request) }}" target="_blank" class="w-full text-center px-4 py-2.5 rounded-xl border border-slate-200 text-slate-700 font-bold hover:bg-slate-50 transition">
                                    View Request
                                </a>

                                <form method="POST" action="{{ route('admin.spam-radar.approve', $request) }}">
                                    @csrf
                                    <button class="w-full px-4 py-2.5 rounded-xl bg-red-600 text-white font-black hover:bg-red-700 shadow-sm transition">
                                        Approve Strike
                                    </button>
                                </form>

                                <form method="POST" action="{{ route('admin.spam-radar.reject', $request) }}">
                                    @csrf
                                    <button class="w-full px-4 py-2.5 rounded-xl bg-slate-800 text-white font-bold hover:bg-slate-900 shadow-sm transition">
                                        Reject Reports (False Alarm)
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-20 bg-white rounded-3xl border border-slate-200 border-dashed">
                    <div class="w-16 h-16 bg-green-100 text-green-500 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900">All clear!</h3>
                    <p class="text-slate-500 mt-2 font-medium">There are no pending spam reports to review.</p>
                </div>
            @endforelse

            <div class="mt-6">
                {{ $suspectedRequests->links() }}
            </div>
        </div>

    </div>
</div>
@endsection
