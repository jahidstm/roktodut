@if($requests->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
        @foreach($requests as $req)
            @php
                $urgencyVal  = $req->urgency?->value ?? $req->urgency ?? 'normal';
                $reqGroup    = $req->blood_group?->value ?? $req->blood_group ?? '?';
                $isEmergency = ($urgencyVal === 'emergency');
                $isUrgent    = ($urgencyVal === 'urgent');
                $neededAt    = $req->needed_at;
                $diffHours   = $neededAt ? (int) now()->diffInHours($neededAt, false) : null;
            @endphp

            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-lg flex flex-col relative overflow-hidden group">
                
                {{-- Top indicator --}}
                @if($isEmergency)
                    <div class="absolute top-0 left-0 right-0 h-1 bg-red-500"></div>
                @elseif($isUrgent)
                    <div class="absolute top-0 left-0 right-0 h-1 bg-amber-500"></div>
                @else
                    <div class="absolute top-0 left-0 right-0 h-1 bg-slate-200"></div>
                @endif

                <div class="p-6 flex-1 flex flex-col">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-lg font-black text-slate-800 leading-tight mb-1 truncate">{{ $req->patient_name ?? 'অজ্ঞাত রোগী' }}</h3>
                            
                            {{-- Urgency Badge --}}
                            <div class="mt-1">
                                @if($isEmergency)
                                    <span class="inline-flex items-center gap-1 text-[10px] font-black text-red-600 bg-red-50 px-2 py-0.5 rounded-md border border-red-100 uppercase tracking-wide">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse inline-block"></span>
                                        অতি জরুরি
                                    </span>
                                @elseif($isUrgent)
                                    <span class="inline-flex items-center gap-1 text-[10px] font-black text-amber-600 bg-amber-50 px-2 py-0.5 rounded-md border border-amber-100 uppercase tracking-wide">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 inline-block"></span>
                                        জরুরি
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-[10px] font-semibold text-slate-500 bg-slate-50 px-2 py-0.5 rounded-md border border-slate-100 uppercase tracking-wide">
                                        সাধারণ
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="shrink-0 w-12 h-12 rounded-xl flex items-center justify-center font-black text-lg {{ $isEmergency ? 'bg-red-50 text-red-600 border border-red-100' : 'bg-slate-50 text-slate-700 border border-slate-100' }}">
                            {{ $reqGroup }}
                        </div>
                    </div>
                    
                    <div class="space-y-2 mt-2 mb-6">
                        <div class="flex items-center gap-2 text-sm text-slate-600 font-medium">
                            <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                            <span class="truncate">{{ $req->hospital ?? 'হাসপাতাল উল্লেখ নেই' }}</span>
                        </div>
                        @if($req->district)
                        <div class="flex items-center gap-2 text-sm text-slate-600 font-medium">
                            <svg class="w-4 h-4 text-red-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            <span class="truncate">{{ $req->upazila?->name . ', ' ?? '' }}{{ $req->district->name }}</span>
                        </div>
                        @endif
                        <div class="flex items-center justify-between gap-2 text-sm text-slate-600 font-medium bg-slate-50 p-2 rounded-lg mt-2">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 {{ $isEmergency ? 'text-red-500' : 'text-slate-400' }} shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                @if($neededAt && $diffHours !== null)
                                    @if($diffHours < 0)
                                        <span class="text-red-500 font-bold">{{ abs($diffHours) }} ঘণ্টা আগে ছিল</span>
                                    @elseif($diffHours < 1)
                                        <span class="text-red-500 font-bold animate-pulse">এখনই প্রয়োজন!</span>
                                    @elseif($diffHours < 24)
                                        <span class="{{ $isEmergency ? 'text-red-600 font-bold' : 'text-amber-600 font-bold' }}">
                                            {{ $diffHours }} ঘণ্টার মধ্যে প্রয়োজন
                                        </span>
                                    @else
                                        <span class="text-slate-600">
                                            {{ $neededAt->format('d M, Y') }}-এর মধ্যে
                                        </span>
                                    @endif
                                @else
                                    <span class="text-slate-500">যত দ্রুত সম্ভব</span>
                                @endif
                            </div>
                            @if($req->bags_needed > 1)
                                <span class="text-xs font-bold bg-white text-slate-700 px-2 py-1 rounded shadow-sm border border-slate-100">{{ $req->bags_needed }} ব্যাগ</span>
                            @endif
                        </div>
                    </div>
                    
                    {{-- Action Button & Privacy Hook --}}
                    <div class="mt-auto">
                        @guest
                            <a href="{{ route('login') }}" onclick="alert('রোগীর বিস্তারিত দেখতে এবং রক্ত দিতে অনুগ্রহ করে লগ-ইন করুন।'); return true;"
                               class="flex items-center justify-center gap-2 w-full py-2.5 rounded-xl text-sm font-bold transition-all duration-300 border border-dashed border-slate-300 text-slate-600 hover:border-slate-400 hover:bg-slate-50">
                                <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                                নম্বর দেখতে ক্লিক করুন
                            </a>
                        @else
                            <a href="{{ route('requests.show', $req->id) }}"
                               class="flex items-center justify-center gap-2 w-full py-2.5 rounded-xl text-sm font-bold transition-all duration-300 bg-red-600 text-white hover:bg-red-700 shadow-sm shadow-red-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                                রক্ত দিতে চাই
                            </a>
                        @endguest
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-12">
        {{ $requests->links() }}
    </div>
@else
    {{-- Empty State --}}
    <div class="text-center py-20 bg-white rounded-3xl border border-slate-100 shadow-sm max-w-2xl mx-auto mt-10">
        <div class="w-24 h-24 bg-slate-50 text-slate-300 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-12 h-12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
        </div>
        <h3 class="text-xl font-bold text-slate-800 mb-2">এই মুহূর্তে কোনো জরুরি রক্তের অনুরোধ নেই</h3>
        <p class="text-slate-500 font-medium">নতুন কোনো রক্তের প্রয়োজন হলে এখানে দেখা যাবে।</p>
        <div class="mt-8">
            <a href="{{ route('home') }}" class="text-red-600 font-bold hover:underline">হোমে ফিরে যান</a>
        </div>
    </div>
@endif
