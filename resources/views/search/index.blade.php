@extends('layouts.app')

@section('title', 'রক্তদাতা খুঁজুন — রক্তদূত')

@section('content')
<x-page-header variant="app" title="রক্তদাতা খুঁজুন" />

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    @include('partials.pilot-banner')

    @php
        $selectedDivision = $request['division_id'] ?? '';
        $selectedDistrict = $request['district_id'] ?? '';
        $selectedUpazila = $request['upazila_id'] ?? '';
        $showError = session('error') && !session('reveal_target');
    @endphp

    @if ($showError)
        <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-red-700 shadow-sm flex items-center gap-2">
            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
            </svg>
            <span class="font-semibold">{{ session('error') }}</span>
        </div>
    @endif

    <x-card class="mb-8 border-slate-200" paddings="p-5">
        <form method="GET" action="{{ route('search') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">রক্তের গ্রুপ</label>
                    <select name="blood_group" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-red-500 focus:ring-red-500 font-semibold text-slate-700">
                        <option value="">সব গ্রুপ</option>
                        @foreach ($bloodGroups as $bg)
                            <option value="{{ $bg->value }}" @selected(($request['blood_group'] ?? '') === $bg->value)>{{ $bg->value }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">রক্তের ধরন</label>
                    <select name="component_type" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-red-500 focus:ring-red-500 font-semibold text-slate-700">
                        <option value="">সব ধরন</option>
                        <option value="{{ \App\Enums\BloodComponentType::WHOLE_BLOOD->value }}" @selected(($request['component_type'] ?? '') === \App\Enums\BloodComponentType::WHOLE_BLOOD->value)>পূর্ণ রক্ত</option>
                        <option value="{{ \App\Enums\BloodComponentType::PACKED_RBC->value }}" @selected(($request['component_type'] ?? '') === \App\Enums\BloodComponentType::PACKED_RBC->value)>PRBC</option>
                        <option value="{{ \App\Enums\BloodComponentType::PLATELETS->value }}" @selected(($request['component_type'] ?? '') === \App\Enums\BloodComponentType::PLATELETS->value)>Platelet (Apheresis)</option>
                        <option value="{{ \App\Enums\BloodComponentType::PLASMA->value }}" @selected(($request['component_type'] ?? '') === \App\Enums\BloodComponentType::PLASMA->value)>Plasma</option>
                    </select>
                </div>

                <div>
                    <label for="filter_division" class="block text-sm font-bold text-slate-700 mb-1">বিভাগ</label>
                    <select name="division_id" id="filter_division" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-red-500 focus:ring-red-500 font-semibold text-slate-700">
                        <option value="">বিভাগ নির্বাচন</option>
                        @foreach(\App\Models\Division::orderBy('name', 'asc')->get() as $div)
                            <option value="{{ $div->id }}" {{ ($request['division_id'] ?? '') == $div->id ? 'selected' : '' }}>{{ $div->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="filter_district" class="block text-sm font-bold text-slate-700 mb-1">জেলা</label>
                    <select name="district_id" id="filter_district" disabled class="w-full rounded-lg border-slate-300 shadow-sm focus:border-red-500 focus:ring-red-500 font-semibold text-slate-700 disabled:bg-slate-100 disabled:text-slate-400 disabled:cursor-not-allowed">
                        <option value="">প্রথমে বিভাগ নির্বাচন করুন</option>
                    </select>
                </div>

                <div>
                    <label for="filter_upazila" class="block text-sm font-bold text-slate-700 mb-1">উপজেলা/থানা</label>
                    <select name="upazila_id" id="filter_upazila" disabled class="w-full rounded-lg border-slate-300 shadow-sm focus:border-red-500 focus:ring-red-500 font-semibold text-slate-700 disabled:bg-slate-100 disabled:text-slate-400 disabled:cursor-not-allowed">
                        <option value="">প্রথমে জেলা নির্বাচন করুন</option>
                    </select>
                </div>
                <div class="md:col-span-5 flex justify-end gap-2 mt-2">
                    @if(request()->hasAny(['blood_group', 'component_type', 'division_id', 'district_id', 'upazila_id']))
                        <a href="{{ route('search') }}" class="shrink-0 bg-slate-100 hover:bg-slate-200 text-slate-700 px-6 py-2.5 rounded-lg font-extrabold transition-colors flex items-center justify-center">
                            রিসেট
                        </a>
                    @endif
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-8 py-2.5 rounded-lg font-extrabold shadow-sm transition-colors">
                        খুঁজুন
                    </button>
                </div>

                <input type="hidden" id="selectedDivision" value="{{ $selectedDivision }}">
                <input type="hidden" id="selectedDistrict" value="{{ $selectedDistrict }}">
                <input type="hidden" id="selectedUpazila" value="{{ $selectedUpazila }}">
            </form>
    </x-card>

    <div class="mb-5">
        <div class="flex items-center justify-between gap-3">
            <h2 class="text-lg font-black text-slate-800">সার্চ ফলাফল</h2>
            <span class="text-sm font-bold text-slate-600 bg-slate-100 px-3 py-1 rounded-full">
                মোট: {{ \App\Support\BanglaDate::digits((string) $donors->total()) }} জন
            </span>
        </div>

        @if(!empty($selectedFilters))
            <div class="mt-3 flex flex-wrap gap-2">
                @foreach($selectedFilters as $chip)
                    <span class="inline-flex h-7 items-center rounded-full border border-slate-200 bg-white px-3 text-xs font-bold text-slate-700">
                        {{ $chip }}
                    </span>
                @endforeach
            </div>
        @endif
    </div>

    <div id="search-state-error" class="hidden mb-5 rounded-xl border border-red-200 bg-red-50 p-4 text-red-700 font-semibold">
        সার্ভারে সমস্যা হচ্ছে—আবার চেষ্টা করুন
    </div>

    @if($donors->isEmpty())
        <x-card paddings="p-10" class="text-center">
            <div class="mx-auto mb-4 h-14 w-14 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center">
                <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 15.75 18 18m-9.75-5.25h.008v.008H8.25V12.5Zm3.75 0h.008v.008H12V12.5Zm3.75 0h.008v.008h-.008V12.5ZM3 10.5h18m-1.5 9h-15A1.5 1.5 0 0 1 3 18V7.5A1.5 1.5 0 0 1 4.5 6h15A1.5 1.5 0 0 1 21 7.5V18a1.5 1.5 0 0 1-1.5 1.5Z"/>
                </svg>
            </div>
            <h3 class="text-lg font-black text-slate-800">এই ফিল্টারে কোনো ডোনার পাওয়া যায়নি</h3>
            <p class="mt-2 text-sm font-medium text-slate-500">অন্য লোকেশন বা ব্লাড গ্রুপ দিয়ে চেষ্টা করুন।</p>
            <div class="mt-5">
                <x-primary-button href="{{ route('search') }}">
                    ফিল্টার রিসেট করুন
                </x-primary-button>
            </div>
        </x-card>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($donors as $donor)
                @php
                    $donorId = $donor->id;
                    $isTarget = session('reveal_target') == $donorId;
                @endphp

                <x-donor-search-card :donor="$donor" :is-target="$isTarget" />
            @endforeach
        </div>

        @php
            $from = $donors->firstItem() ?? 0;
            $to = $donors->lastItem() ?? 0;
            $total = $donors->total();
        @endphp
        <div class="mt-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-sm font-semibold text-slate-600">
                দেখানো হচ্ছে: {{ \App\Support\BanglaDate::digits((string) $from) }}–{{ \App\Support\BanglaDate::digits((string) $to) }} (মোট {{ \App\Support\BanglaDate::digits((string) $total) }} জন)
            </p>

            @if($donors->hasPages())
                <nav class="inline-flex items-center gap-1" aria-label="Pagination">
                    @if($donors->onFirstPage())
                        <span class="px-3 py-2 rounded-lg border border-slate-200 bg-slate-100 text-slate-400 text-sm font-bold">আগের</span>
                    @else
                        <a href="{{ $donors->previousPageUrl() }}" class="px-3 py-2 rounded-lg border border-slate-300 bg-white text-slate-700 text-sm font-bold hover:bg-slate-50">আগের</a>
                    @endif

                    @foreach($donors->getUrlRange(1, $donors->lastPage()) as $page => $url)
                        @if($page == $donors->currentPage())
                            <span class="px-3 py-2 rounded-lg bg-red-600 text-white text-sm font-black">{{ \App\Support\BanglaDate::digits((string) $page) }}</span>
                        @else
                            <a href="{{ $url }}" class="px-3 py-2 rounded-lg border border-slate-300 bg-white text-slate-700 text-sm font-bold hover:bg-slate-50">{{ \App\Support\BanglaDate::digits((string) $page) }}</a>
                        @endif
                    @endforeach

                    @if($donors->hasMorePages())
                        <a href="{{ $donors->nextPageUrl() }}" class="px-3 py-2 rounded-lg border border-slate-300 bg-white text-slate-700 text-sm font-bold hover:bg-slate-50">পরের</a>
                    @else
                        <span class="px-3 py-2 rounded-lg border border-slate-200 bg-slate-100 text-slate-400 text-sm font-bold">পরের</span>
                    @endif
                </nav>
            @endif
        </div>
    @endif

    <template id="donor-loading-skeleton-template">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 mt-5">
            @for($i=0; $i<3; $i++)
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm animate-pulse">
                    <div class="h-5 w-2/3 bg-slate-200 rounded"></div>
                    <div class="mt-3 h-4 w-1/2 bg-slate-200 rounded"></div>
                    <div class="mt-4 space-y-2">
                        <div class="h-9 bg-slate-100 rounded-xl"></div>
                        <div class="h-9 bg-slate-100 rounded-xl"></div>
                    </div>
                </div>
            @endfor
        </div>
    </template>

    <script>
        document.querySelectorAll('form').forEach((form) => {
            form.addEventListener('submit', () => {
                sessionStorage.setItem('donorScrollPosition', window.scrollY);
            });
        });

        document.addEventListener('DOMContentLoaded', () => {
            const scrollPos = sessionStorage.getItem('donorScrollPosition');
            if (scrollPos) {
                window.scrollTo({ top: parseInt(scrollPos, 10), behavior: 'instant' });
                sessionStorage.removeItem('donorScrollPosition');
            }
        });
    </script>
</div>
@endsection
