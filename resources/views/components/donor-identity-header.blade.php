@props(['user', 'totalContributions' => 0])

<div class="group relative bg-white rounded-3xl border border-slate-200/80 shadow-sm hover:shadow-md hover:border-slate-300 transition-all duration-300 p-5 z-10 overflow-hidden">
    
    {{-- Decorative Background Glow (Subtle) --}}
    <div class="absolute -top-24 -left-12 w-48 h-48 bg-red-50 rounded-full blur-3xl opacity-60 pointer-events-none transition-opacity duration-500 group-hover:opacity-100"></div>

    <div class="relative z-10 flex flex-row items-center sm:items-center gap-4 sm:gap-6 w-full">
        
        {{-- Left: Blood Group Target --}}
        <div class="shrink-0 self-start sm:self-center">
            <div class="relative w-16 h-16 sm:w-20 sm:h-20 rounded-[1rem] sm:rounded-[1.25rem] bg-gradient-to-br from-red-50 to-red-100/80 border border-white shadow-[0_4px_12px_-4px_rgba(220,38,38,0.2)] sm:shadow-[0_8px_16px_-6px_rgba(220,38,38,0.2)] ring-[3px] sm:ring-4 ring-red-50 flex items-center justify-center transform transition-transform duration-300 group-hover:scale-[1.03]">
                <span class="absolute -top-1.5 -right-1.5 sm:-top-2 sm:-right-2 text-lg sm:text-xl drop-shadow-sm">🩸</span>
                <span class="text-2xl sm:text-3xl font-black bg-clip-text text-transparent bg-gradient-to-br from-red-600 to-red-800 drop-shadow-sm">
                    {{ $user->blood_group?->value ?? $user->blood_group ?? '?' }}
                </span>
            </div>
        </div>

        {{-- Center: Name, Location, Badges --}}
        <div class="flex-1 min-w-0 flex flex-col justify-center py-1">
            
            {{-- Name & Verified Status --}}
            <h2 class="text-xl sm:text-2xl font-black text-slate-900 leading-none flex items-center gap-1.5 flex-wrap mb-1">
                <span class="truncate">{{ $user->name }}</span>
                @if($user->nid_status === 'verified' || $user->nid_status === 'approved' || $user->verified_badge)
                    <div class="relative flex items-center group/tooltip cursor-help mb-0.5">
                        <svg class="w-5 h-5 text-blue-500 drop-shadow-sm" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1.5 w-max px-2.5 py-1 bg-slate-800 text-white text-[10px] font-bold rounded-lg shadow-lg opacity-0 group-hover/tooltip:opacity-100 transition-opacity pointer-events-none z-20">ভেরিফাইড ডোনার</span>
                    </div>
                @endif
            </h2>
            
            {{-- Location --}}
            <p class="text-slate-500 font-semibold mb-2 flex items-center gap-1 text-[13px] sm:text-sm">
                <svg class="w-3.5 h-3.5 text-red-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" /></svg>
                <span class="truncate">{{ $user->upazila?->name ?? 'উপজেলা দেওয়া নেই' }}, {{ $user->district?->name ?? 'জেলা দেওয়া নেই' }}</span>
            </p>

            {{-- Trust Badges Row --}}
            <div class="flex flex-wrap items-center gap-2">
                {{-- Dynamic NID Badge --}}
                @if($user->nid_status === 'verified' || $user->nid_status === 'approved' || $user->verified_badge)
                    <div class="group/badge relative inline-flex items-center gap-1 px-2.5 py-1 bg-blue-50/80 border border-blue-200 text-blue-700 text-[11px] font-bold rounded-md hover:bg-blue-100 transition-colors cursor-default">
                        <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <span>এনআইডি যাচাইকৃত</span>
                        <span class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1.5 w-max px-2.5 py-1 bg-slate-800 text-white text-[10px] font-bold rounded shadow-lg opacity-0 group-hover/badge:opacity-100 transition-opacity pointer-events-none z-20">পরিচয়পত্র সফলভাবে যাচাই করা হয়েছে।</span>
                    </div>
                @elseif($user->nid_status === 'pending')
                    <div class="group/badge relative inline-flex items-center gap-1 px-2.5 py-1 bg-amber-50/80 border border-amber-200 text-amber-700 text-[11px] font-bold rounded-md hover:bg-amber-100 transition-colors cursor-default">
                        <svg class="w-3.5 h-3.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>এনআইডি রিভিউতে</span>
                        <span class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1.5 w-max px-2.5 py-1 bg-slate-800 text-white text-[10px] font-bold rounded shadow-lg opacity-0 group-hover/badge:opacity-100 transition-opacity pointer-events-none z-20">অ্যাডমিন আপনার তথ্য যাচাই করছেন।</span>
                    </div>
                @elseif($user->nid_status === 'rejected')
                    <div class="group/badge relative inline-flex items-center gap-1 px-2.5 py-1 bg-rose-50 border border-rose-200 text-rose-700 text-[11px] font-bold rounded-md hover:bg-rose-100 transition-colors cursor-help">
                        <svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span>যাচাই বাতিল</span>
                        <span class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1.5 w-max px-2.5 py-1 bg-slate-800 text-white text-[10px] font-bold rounded shadow-lg opacity-0 group-hover/badge:opacity-100 transition-opacity pointer-events-none z-20">তথ্য সঠিক নয়। দয়া করে পুনরায় আপলোড করুন।</span>
                    </div>
                @else
                    <div class="group/badge relative inline-flex items-center gap-1 px-2.5 py-1 bg-slate-50 border border-slate-200 text-slate-600 text-[11px] font-bold rounded-md hover:bg-slate-100 transition-colors cursor-default">
                        <span>এনআইডি যাচাইকৃত নয়</span>
                    </div>
                @endif

                {{-- Donation Badge --}}
                <div class="inline-flex items-center gap-1 px-2.5 py-1 bg-red-50/50 border border-red-200/70 text-red-700 text-[11px] font-bold rounded-md cursor-default">
                    <span>মোট ডোনেশন <span class="font-black">{{ $totalContributions }}</span> বার</span>
                </div>
            </div>
        </div>

        {{-- Right: Segemented Availability Toggle --}}
        <div class="shrink-0 self-start sm:self-center ml-auto">
            <div class="relative group/toggle" x-data="{ saving: false }">
                <form action="{{ route('donor_profile.is_available_now') }}" method="POST" @submit="saving = true; setTimeout(() => saving=false, 2500)">
                    @csrf
                    <button type="submit" class="relative overflow-hidden flex items-center justify-between p-1 rounded-full border shadow-inner transition-all w-[100px] sm:w-[120px] h-[34px] bg-slate-100 border-slate-200 hover:ring-2 hover:ring-slate-200">
                        
                        {{-- The sliding pill (50% block) --}}
                        <div class="absolute top-1 bottom-1 w-[calc(50%-4px)] rounded-full transition-transform duration-300 ease-out z-0 {{ $user->is_available ? 'translate-x-0 bg-emerald-500 shadow-sm' : 'translate-x-[100%] bg-white shadow' }}"></div>
                        
                        {{-- Labels --}}
                        <span class="relative z-10 w-1/2 text-center text-[10px] sm:text-[11px] font-black transition-colors duration-200 {{ $user->is_available ? 'text-white' : 'text-slate-500 hover:text-slate-700' }}">
                            Available
                        </span>
                        <span class="relative z-10 w-1/2 text-center text-[10px] sm:text-[11px] font-black transition-colors duration-200 {{ !$user->is_available ? 'text-slate-700' : 'text-slate-400 hover:text-slate-600' }}">
                            Busy
                        </span>

                        {{-- Loading Toast Overlay --}}
                        <div x-show="saving" x-transition.opacity style="display: none;" class="absolute inset-0 bg-white/90 backdrop-blur-[2px] z-20 flex items-center justify-center rounded-full border border-emerald-100">
                            <span class="text-[10px] font-bold text-emerald-600 flex items-center gap-1.5">
                                <svg class="animate-spin h-3.5 w-3.5 text-emerald-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                <span class="sm:inline hidden">আপডেট হচ্ছে...</span>
                            </span>
                        </div>
                    </button>
                </form>

                {{-- Tooltip for priority --}}
                <div class="absolute right-0 sm:left-1/2 sm:-translate-x-1/2 top-full mt-2 w-max px-2.5 py-1.5 bg-slate-800 text-white text-[10px] font-bold rounded-lg shadow-xl opacity-0 group-hover/toggle:opacity-100 transition-opacity duration-200 pointer-events-none z-30">
                    <div class="absolute -top-1 right-3 sm:right-auto sm:left-1/2 sm:-translate-x-1/2 w-2 h-2 bg-slate-800 rotate-45"></div>
                    চালু থাকলে জরুরি অনুরোধে অগ্রাধিকার পাবেন
                </div>
            </div>
        </div>

    </div>
</div>
