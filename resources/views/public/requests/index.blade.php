@extends('layouts.app')

@section('title', 'জরুরি রক্তের অনুরোধ - পাবলিক ফিড | রক্তদূত')

@section('content')
<div class="bg-slate-50 min-h-screen py-10 sm:py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Header Section --}}
        <div class="text-center max-w-3xl mx-auto mb-10">
            <span class="inline-flex items-center gap-2 bg-red-50 text-red-700 px-4 py-1.5 rounded-full text-sm font-extrabold tracking-widest uppercase border border-red-100 mb-4">
                <span class="w-2 h-2 rounded-full bg-red-600 animate-pulse"></span>
                পাবলিক ফিড
            </span>
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-slate-900 mb-6 leading-tight">
                জরুরি <span class="text-red-600">রক্তের অনুরোধ</span>
            </h1>
            <p class="text-lg text-slate-600 font-medium">
                দেশের বিভিন্ন প্রান্তে এই মুহূর্তে রক্তের প্রয়োজন। নিচে দেওয়া তালিকা থেকে রোগীদের সাহায্য করতে এগিয়ে আসুন।
            </p>
        </div>

        {{-- Quick Filter & Live Search --}}
        <div class="bg-white p-4 sm:p-6 rounded-2xl border border-slate-200 shadow-sm mb-10 max-w-5xl mx-auto"
             x-data="{
                loading: false,
                search: new URLSearchParams(location.search).get('search') || '',
                blood_group: new URLSearchParams(location.search).get('blood_group') || '',
                district: new URLSearchParams(location.search).get('district') || '',
                
                fetchResults() {
                    this.loading = true;
                    let params = new URLSearchParams();
                    if(this.search) params.append('search', this.search);
                    if(this.blood_group) params.append('blood_group', this.blood_group);
                    if(this.district) params.append('district', this.district);
                    
                    const url = `{{ route('public.requests.index') }}?${params.toString()}`;
                    window.history.replaceState({}, '', url);

                    axios.get(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                        .then(res => {
                            document.getElementById('requests-grid').innerHTML = res.data;
                            this.loading = false;
                        })
                        .catch(err => {
                            console.error(err);
                            this.loading = false;
                        });
                }
             }">
             
            <div class="flex flex-col md:flex-row items-end gap-4">
                {{-- Live Search Input --}}
                <div class="w-full md:w-1/3">
                    <label class="block text-sm font-bold text-slate-700 mb-2">রোগী বা হাসপাতালের নাম</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                        </div>
                        <input type="text" x-model.debounce.500ms="search" @input="fetchResults()" placeholder="খুঁজুন..." class="w-full pl-10 rounded-xl border-slate-300 focus:border-red-500 focus:ring-red-500 font-medium text-slate-700 shadow-sm py-3 px-4 transition-all">
                    </div>
                </div>

                {{-- Blood Group Filter --}}
                <div class="w-full md:w-1/3">
                    <label class="block text-sm font-bold text-slate-700 mb-2">রক্তের গ্রুপ</label>
                    <select x-model="blood_group" @change="fetchResults()" class="w-full rounded-xl border-slate-300 focus:border-red-500 focus:ring-red-500 font-medium text-slate-700 shadow-sm py-3 px-4 transition-all">
                        <option value="">সব রক্তের গ্রুপ</option>
                        @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bg)
                            <option value="{{ $bg }}">{{ $bg }}</option>
                        @endforeach
                    </select>
                </div>
                
                {{-- District Filter --}}
                <div class="w-full md:w-1/3">
                    <label class="block text-sm font-bold text-slate-700 mb-2">জেলা</label>
                    <select x-model="district" @change="fetchResults()" class="w-full rounded-xl border-slate-300 focus:border-red-500 focus:ring-red-500 font-medium text-slate-700 shadow-sm py-3 px-4 transition-all">
                        <option value="">সব জেলা</option>
                        @foreach($districts as $dist)
                            <option value="{{ $dist->id }}">{{ $dist->name_bn ?? $dist->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Loading Indicator overlay --}}
            <div x-show="loading" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center bg-white/40 backdrop-blur-sm" style="display: none;">
                <div class="bg-white p-5 rounded-2xl shadow-xl flex items-center gap-3">
                    <div class="w-6 h-6 border-4 border-red-200 border-t-red-600 rounded-full animate-spin"></div>
                    <span class="font-bold text-slate-700">ডেটা লোড হচ্ছে...</span>
                </div>
            </div>
        </div>

        {{-- Requests Grid Container (Dynamic) --}}
        <div id="requests-grid">
            @include('public.requests.partials.list')
        </div>
        
        {{-- Pagination intercept to use AlpineJS form? If they paginate, standard laravel pagination links reload the page or we need Vue/Livewire. For this task, standard Laravel links reloading is acceptable for pagination, or they preserve query via `withQueryString()`. --}}
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
@endpush
@endsection
