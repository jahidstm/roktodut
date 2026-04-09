@if($requests->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @foreach($requests as $req)
            @php
                $urgencyVal  = $req->urgency?->value ?? $req->urgency ?? 'normal';
                $reqGroup    = $req->blood_group?->value ?? $req->blood_group ?? '?';
                $isEmergency = ($urgencyVal === 'emergency');
                $isUrgent    = ($urgencyVal === 'urgent');
                $neededAt    = $req->needed_at;
                $diffHours   = $neededAt ? (int) now()->diffInHours($neededAt, false) : null;
            @endphp

            <div class="bg-white rounded-xl border border-slate-200 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 flex flex-col relative overflow-hidden">

                {{-- Top urgency bar --}}
                <div class="h-1 w-full {{ $isEmergency ? 'bg-red-500' : ($isUrgent ? 'bg-amber-500' : 'bg-slate-200') }}"></div>

                <div class="p-5 flex-1 flex flex-col">
                    {{-- Header Row --}}
                    <div class="flex justify-between items-start gap-3 mb-3">
                        <div class="min-w-0 flex-1">
                            <h3 class="text-base font-black text-slate-900 leading-tight truncate">
                                {{ $req->patient_name ?? 'অজ্ঞাত রোগী' }}
                            </h3>
                            {{-- Urgency badge --}}
                            <div class="mt-1">
                                @if($isEmergency)
                                    <span class="inline-flex items-center gap-1 text-[10px] font-black text-red-600 bg-red-50 px-2 py-0.5 rounded border border-red-100 uppercase tracking-wide">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse inline-block"></span>
                                        অতি জরুরি
                                    </span>
                                @elseif($isUrgent)
                                    <span class="inline-flex items-center gap-1 text-[10px] font-black text-amber-600 bg-amber-50 px-2 py-0.5 rounded border border-amber-100 uppercase tracking-wide">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 inline-block"></span>
                                        জরুরি
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-[10px] font-semibold text-slate-500 bg-slate-50 px-2 py-0.5 rounded border border-slate-200 uppercase tracking-wide">
                                        সাধারণ
                                    </span>
                                @endif
                            </div>
                        </div>
                        {{-- Blood Group Badge --}}
                        <div class="shrink-0 w-11 h-11 rounded-xl flex items-center justify-center font-black text-base {{ $isEmergency ? 'bg-red-50 text-red-600 border border-red-100' : 'bg-slate-50 text-slate-700 border border-slate-200' }}">
                            {{ $reqGroup }}
                        </div>
                    </div>

                    {{-- Info rows --}}
                    <div class="space-y-1.5 text-sm text-slate-600 mb-4">
                        <div class="flex items-center gap-2">
                            <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            <span class="truncate font-medium">{{ $req->hospital ?? 'হাসপাতাল উল্লেখ নেই' }}</span>
                        </div>

                        @if($req->district)
                        <div class="flex items-center gap-2">
                            <svg class="w-3.5 h-3.5 text-red-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span class="truncate font-medium">{{ $req->upazila?->name ? $req->upazila->name . ', ' : '' }}{{ $req->district->name }}</span>
                        </div>
                        @endif

                        {{-- Time + Bags --}}
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 {{ $isEmergency ? 'text-red-500' : 'text-slate-400' }} shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                @if($neededAt && $diffHours !== null)
                                    @if($diffHours < 0)
                                        <span class="text-red-500 font-bold text-xs">{{ abs($diffHours) }} ঘণ্টা আগে ছিল</span>
                                    @elseif($diffHours < 1)
                                        <span class="text-red-500 font-black text-xs animate-pulse">এখনই প্রয়োজন!</span>
                                    @elseif($diffHours < 24)
                                        <span class="font-bold text-xs {{ $isEmergency ? 'text-red-600' : 'text-amber-600' }}">{{ $diffHours }} ঘণ্টার মধ্যে</span>
                                    @else
                                        <span class="font-medium text-xs text-slate-500">{{ $neededAt->format('d M, Y') }}-এর মধ্যে</span>
                                    @endif
                                @else
                                    <span class="font-medium text-xs text-slate-500">যত দ্রুত সম্ভব</span>
                                @endif
                            </div>
                            @if($req->bags_needed > 1)
                                <span class="text-[11px] font-bold text-slate-600 bg-slate-100 px-2 py-0.5 rounded">
                                    {{ $req->bags_needed }} ব্যাগ
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Action Button —— মূল লজিক এখানে --}}
                    <div class="mt-auto">
                        @guest
                            {{-- Guest: login-এ redirect, flash message সহ --}}
                            <a href="{{ route('login') }}?redirect={{ urlencode(route('public.requests.index')) }}"
                               class="flex items-center justify-center gap-2 w-full py-2.5 rounded-xl text-sm font-bold transition-all border border-dashed border-slate-300 text-slate-500 hover:border-red-300 hover:text-red-600 hover:bg-red-50">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                                লগইন করে রক্ত দিন
                            </a>
                        @else
                            {{-- Authenticated: request detail page যেখানে Accept করা যায় --}}
                            <a href="{{ route('requests.show', $req->id) }}"
                               class="flex items-center justify-center gap-2 w-full py-2.5 rounded-xl text-sm font-bold transition-all bg-red-600 text-white hover:bg-red-700 shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                </svg>
                                রক্ত দিতে চাই
                            </a>
                        @endguest
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    <div class="mt-10">
        {{ $requests->links() }}
    </div>

@else
    {{-- Empty State --}}
    <div class="text-center py-20 bg-white rounded-xl border border-slate-100 shadow-sm max-w-xl mx-auto">
        <div class="w-16 h-16 bg-slate-50 text-slate-300 rounded-full flex items-center justify-center mx-auto mb-5">
            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <h3 class="text-lg font-black text-slate-800 mb-2">কোনো অনুরোধ পাওয়া যায়নি</h3>
        <p class="text-slate-500 font-medium text-sm">এই মুহূর্তে কোনো জরুরি রক্তের অনুরোধ নেই অথবা ফিল্টার পরিবর্তন করে দেখুন।</p>
        <a href="{{ route('home') }}" class="inline-block mt-6 text-red-600 font-bold hover:underline text-sm">হোমে ফিরে যান</a>
    </div>
@endif
