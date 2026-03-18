<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @php
                $user = auth()->user();
                $isEligible = $user->is_eligible_to_donate;
                $nextDate = $user->next_eligible_date;
            @endphp

            <div class="mb-10 bg-white p-6 rounded-3xl border {{ $isEligible ? 'border-emerald-200 shadow-emerald-50' : 'border-amber-200 shadow-amber-50' }} shadow-lg flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="flex items-center gap-4">
                    <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-full {{ $isEligible ? 'bg-emerald-100 text-emerald-600' : 'bg-amber-100 text-amber-600' }}">
                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-extrabold {{ $isEligible ? 'text-emerald-700' : 'text-amber-700' }}">
                            {{ $isEligible ? 'আপনি রক্তদানের জন্য যোগ্য (Eligible)' : 'আপনি আপাতত রক্তদানের জন্য যোগ্য নন' }}
                        </h3>
                        <p class="text-sm font-semibold text-slate-500 mt-1">
                            @if(!$user->last_donated_at)
                                আমাদের সিস্টেমে আপনার পূর্বের রক্তদানের কোনো রেকর্ড নেই।
                            @elseif($isEligible)
                                আপনার সর্বশেষ রক্তদানের পর ৯০ দিন পার হয়ে গেছে।
                            @else
                                পরবর্তী রক্তদানের তারিখ: <span class="text-slate-800 font-extrabold">{{ $nextDate->format('d M, Y') }}</span> 
                                (আর মাত্র <span class="text-red-600 font-extrabold">{{ now()->diffInDays($nextDate) }} দিন</span> বাকি)
                            @endif
                        </p>
                    </div>
                </div>

                <form action="{{ route('donation.record.update') }}" method="POST" class="flex items-end gap-3 w-full md:w-auto">
                    @csrf
                    <div class="flex-1 md:w-48">
                        <label class="block text-xs font-bold text-slate-500 mb-1">শেষ রক্তদানের তারিখ আপডেট করুন</label>
                        <input type="date" name="last_donated_at" value="{{ $user->last_donated_at?->format('Y-m-d') }}" max="{{ date('Y-m-d') }}" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-red-500 focus:ring-red-500 font-semibold text-slate-700">
                    </div>
                    <button type="submit" class="bg-slate-800 hover:bg-slate-900 text-white px-4 py-2.5 rounded-lg font-extrabold shadow-sm transition-colors">
                        সেভ
                    </button>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>