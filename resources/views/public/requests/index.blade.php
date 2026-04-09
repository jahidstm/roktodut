@extends('layouts.app')

@section('title', 'জরুরি রক্তের অনুরোধ | রক্তদূত')

@section('content')
<div class="min-h-screen bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- Page Title (matching Smart Donor Search style) --}}
        <div class="mb-8">
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 border-l-4 border-red-600 pl-4">
                জরুরি রক্তের অনুরোধ
            </h1>
        </div>

        {{-- Filter Section (matching Smart Donor Search filter card style) --}}
        <div class="bg-white border border-slate-200 rounded-2xl p-6 mb-8 shadow-sm"
             x-data="{
                loading: false,
                search: new URLSearchParams(location.search).get('search') || '',
                blood_group: new URLSearchParams(location.search).get('blood_group') || '',
                district: new URLSearchParams(location.search).get('district') || '',

                fetchResults() {
                    this.loading = true;
                    let params = new URLSearchParams();
                    if (this.search)      params.append('search', this.search);
                    if (this.blood_group) params.append('blood_group', this.blood_group);
                    if (this.district)    params.append('district', this.district);

                    const url = `{{ route('public.requests.index') }}?${params.toString()}`;
                    window.history.replaceState({}, '', url);

                    axios.get(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                        .then(res => {
                            document.getElementById('requests-grid').innerHTML = res.data;
                            this.loading = false;
                        })
                        .catch(() => { this.loading = false; });
                }
             }">

            <p class="text-sm font-bold text-slate-500 mb-5 uppercase tracking-wider">ফিল্টার করুন</p>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end">

                {{-- Live Search --}}
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1.5">রোগী / হাসপাতাল</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input type="text"
                               x-model.debounce.500ms="search"
                               @input="fetchResults()"
                               placeholder="নাম লিখুন..."
                               class="w-full pl-9 rounded-xl border border-slate-300 focus:border-red-500 focus:ring-1 focus:ring-red-500 text-sm font-medium text-slate-700 py-2.5 px-3 transition-all bg-white">
                    </div>
                </div>

                {{-- Blood Group --}}
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1.5">রক্তের গ্রুপ <span class="text-red-500">*</span></label>
                    <select x-model="blood_group" @change="fetchResults()"
                            class="w-full rounded-xl border border-slate-300 focus:border-red-500 focus:ring-1 focus:ring-red-500 text-sm font-medium text-slate-700 py-2.5 px-3 transition-all bg-white">
                        <option value="">সিলেক্ট করুন</option>
                        @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bg)
                            <option value="{{ $bg }}">{{ $bg }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- District --}}
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1.5">জেলা</label>
                    <select x-model="district" @change="fetchResults()"
                            class="w-full rounded-xl border border-slate-300 focus:border-red-500 focus:ring-1 focus:ring-red-500 text-sm font-medium text-slate-700 py-2.5 px-3 transition-all bg-white">
                        <option value="">সিলেক্ট করুন</option>
                        @foreach($districts as $dist)
                            <option value="{{ $dist->id }}">{{ $dist->name_bn ?? $dist->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Search Button --}}
                <div>
                    <button @click="fetchResults()"
                            class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-2.5 px-6 rounded-xl transition-all flex items-center justify-center gap-2 shadow-sm">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        খুঁজুন
                    </button>
                </div>
            </div>

            {{-- Loading overlay --}}
            <div x-show="loading" x-transition.opacity
                 class="fixed inset-0 z-50 flex items-center justify-center bg-white/50 backdrop-blur-sm"
                 style="display:none;">
                <div class="bg-white p-4 rounded-2xl shadow-xl flex items-center gap-3">
                    <div class="w-5 h-5 border-4 border-red-200 border-t-red-600 rounded-full animate-spin"></div>
                    <span class="font-bold text-slate-700 text-sm">লোড হচ্ছে...</span>
                </div>
            </div>
        </div>

        {{-- Flash message --}}
        @if(session('login_required'))
            <div class="mb-6 p-4 bg-amber-50 border border-amber-200 rounded-xl flex items-center gap-3">
                <svg class="w-5 h-5 text-amber-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <span class="text-amber-800 font-semibold text-sm">{{ session('login_required') }}</span>
            </div>
        @endif

        {{-- Results header --}}
        <div class="flex items-center justify-between mb-5">
            <h2 class="text-lg font-black text-slate-800">সার্চ ফলাফল</h2>
            <span class="text-sm font-bold text-slate-500 bg-slate-100 px-3 py-1 rounded-full">
                মোট: {{ $requests->total() }} টি অনুরোধ
            </span>
        </div>

        {{-- Requests Grid Container (Dynamic via Axios) --}}
        <div id="requests-grid">
            @include('public.requests.partials.list')
        </div>

    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
@endpush
@endsection
