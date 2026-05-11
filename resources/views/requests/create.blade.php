@extends('layouts.app')

@section('title', 'নতুন রক্তের রিকোয়েস্ট — রক্তদূত')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<style>
    #hospital-map { height: 280px; border-radius: 1rem; border: 1px solid #e2e8f0; z-index: 0; }
    .leaflet-container { font-family: inherit; }
</style>
@endpush

@section('content')
{{-- 🎯 THE FIX: ফর্মটি মাঝখানে রাখতে এবং উপরে-নিচে পর্যাপ্ত শ্বাস নেওয়ার জায়গা (Breathing Space) দিতে px-4 sm:px-6 lg:px-8 py-10 যোগ করা হলো --}}
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    @include('partials.pilot-banner')

    <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">নতুন রক্তের রিকোয়েস্ট</h1>
    <p class="text-slate-500 font-medium mt-1">সঠিক তথ্য দিলে দ্রুত ডোনার রেসপন্স পাবে।</p>

    @if(session('existing_request_url'))
        <div class="mt-4">
            <a href="{{ session('existing_request_url') }}"
               class="inline-flex items-center gap-2 rounded-xl border border-amber-200 bg-amber-50 px-4 py-2.5 text-sm font-extrabold text-amber-700 hover:bg-amber-100 transition">
                আগের অনুরোধ দেখুন
            </a>
        </div>
    @endif

    <div class="mt-8 rounded-3xl border border-slate-200 bg-white p-6 md:p-8 shadow-sm relative overflow-hidden">
        <div class="absolute top-0 left-0 w-2 h-full bg-red-600"></div>
        
        {{-- 🎯 ফর্মের সাথে Alpine.js x-data যোগ করা হলো --}}
        <form method="POST" action="{{ route('requests.store') }}" class="space-y-6" x-data="{ isSubmitting: false }" @submit="if(isSubmitting) { $event.preventDefault(); return false; } isSubmitting = true;">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="text-sm font-extrabold text-slate-800">রোগীর নাম</label>
                    <input name="patient_name" value="{{ old('patient_name') }}"
                           class="mt-2 w-full rounded-xl border-slate-200 focus:border-red-500 focus:ring-red-500 font-medium px-4 py-3" />
                    @error('patient_name') <div class="text-sm text-red-600 font-bold mt-1">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="text-sm font-extrabold text-slate-800">রক্তের গ্রুপ <span class="text-red-500">*</span></label>
                    <select name="blood_group" class="mt-2 w-full rounded-xl border-slate-200 bg-white focus:border-red-500 focus:ring-red-500 font-bold px-4 py-3">
                        <option value="">সিলেক্ট করুন</option>
                        @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $group)
                            <option value="{{ $group }}" @selected(old('blood_group') === $group)>{{ $group }}</option>
                        @endforeach
                    </select>
                    @error('blood_group') <div class="text-sm text-red-600 font-bold mt-1">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="text-sm font-extrabold text-slate-800">রক্তের ধরন <span class="text-red-500">*</span></label>
                    <select name="component_type" class="mt-2 w-full rounded-xl border-slate-200 bg-white focus:border-red-500 focus:ring-red-500 font-bold px-4 py-3">
                        @foreach(\App\Enums\BloodComponentType::cases() as $component)
                            <option value="{{ $component->value }}" @selected(old('component_type', \App\Enums\BloodComponentType::WHOLE_BLOOD->value) === $component->value)>
                                {{ $component->label() }}
                            </option>
                        @endforeach
                    </select>
                    @error('component_type') <div class="text-sm text-red-600 font-bold mt-1">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- 🏥 Hospital Autocomplete (Alpine.js) --}}
                <div
                    x-data="hospitalAutocomplete({{ old('hospital_id') ?? 'null' }}, '{{ old('hospital_display', '') }}')"
                    class="relative"
                >
                    <label class="text-sm font-extrabold text-slate-800">
                        হাসপাতাল
                        <span x-show="selectedId && isVerified" class="ml-1 text-[10px] font-black text-emerald-600 bg-emerald-50 border border-emerald-200 px-1.5 py-0.5 rounded-full">✓ ভেরিফাইড</span>
                        <span x-show="selectedId && !isVerified" class="ml-1 text-[10px] font-black text-amber-600 bg-amber-50 border border-amber-200 px-1.5 py-0.5 rounded-full">নতুন — রিভিউ পেন্ডিং</span>
                    </label>

                    {{-- Hidden FK input (form submit হলে এটিই যাবে) --}}
                    <input type="hidden" name="hospital_id" x-model="selectedId">

                    {{-- Visible Search Input --}}
                    <input
                        type="text"
                        id="hospital-search"
                        autocomplete="off"
                        placeholder="হাসপাতালের নাম লিখুন..."
                        x-model="query"
                        @input.debounce.300ms="search()"
                        @focus="if(query.length >= 2) open = true"
                        @keydown.arrow-down.prevent="focusNext()"
                        @keydown.arrow-up.prevent="focusPrev()"
                        @keydown.enter.prevent="selectFocused()"
                        @keydown.escape="open = false"
                        @blur="handleBlur()"
                        class="mt-2 w-full rounded-xl border-slate-200 focus:border-red-500 focus:ring-red-500 font-medium px-4 py-3"
                    />

                    {{-- Dropdown --}}
                    <div
                        x-show="open && (results.length > 0 || canCreateNew)"
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        class="absolute z-50 mt-1 w-full bg-white rounded-xl shadow-xl border border-slate-200 overflow-hidden"
                        style="display:none;"
                    >
                        {{-- Search results --}}
                        <template x-for="(item, idx) in results" :key="item.id">
                            <button
                                type="button"
                                @mousedown.prevent="select(item)"
                                :class="focusedIndex === idx ? 'bg-red-50 text-red-700' : 'text-slate-700 hover:bg-slate-50'"
                                class="w-full text-left px-4 py-3 text-sm font-semibold flex items-center justify-between border-b border-slate-50 last:border-0 transition-colors"
                            >
                                <span x-text="item.display || item.name"></span>
                                <span x-show="item.is_verified" class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded-full shrink-0">✓</span>
                            </button>
                        </template>

                        {{-- "Add new" option --}}
                        <button
                            x-show="canCreateNew"
                            type="button"
                            @mousedown.prevent="createNew()"
                            class="w-full text-left px-4 py-3 text-sm font-bold text-blue-600 hover:bg-blue-50 flex items-center gap-2 transition-colors"
                        >
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            "<span x-text="query"></span>" — নতুন হিসেবে যোগ করুন
                        </button>
                    </div>

                    {{-- Loading spinner --}}
                    <div x-show="loading" class="absolute right-4 top-[3.1rem]">
                        <svg class="w-4 h-4 animate-spin text-slate-400" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                    </div>
                </div>
                @error('hospital_id') <div class="text-sm text-red-600 font-bold mt-1">{{ $message }}</div> @enderror


                <div>
                    <label class="text-sm font-extrabold text-slate-800">ব্যাগ প্রয়োজন <span class="text-red-500">*</span></label>
                    <input type="number" min="1" name="bags_needed" value="{{ old('bags_needed', 1) }}"
                           class="mt-2 w-full rounded-xl border-slate-200 focus:border-red-500 focus:ring-red-500 font-bold px-4 py-3" />
                    @error('bags_needed') <div class="text-sm text-red-600 font-bold mt-1">{{ $message }}</div> @enderror
                </div>
            </div>

            {{-- 📍 Dynamic Location Dropdowns --}}
            <div class="p-6 bg-slate-50 rounded-2xl border border-slate-100">
                <x-location-selector 
                    :selected-division="old('division_id')"
                    :selected-district="old('district_id')"
                    :selected-upazila="old('upazila_id')"
                />
                
                {{-- Error states for location --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-3">
                    @error('division_id') <div class="text-sm text-red-600 font-bold">{{ $message }}</div> @enderror
                    @error('district_id') <div class="text-sm text-red-600 font-bold">{{ $message }}</div> @enderror
                    @error('upazila_id') <div class="text-sm text-red-600 font-bold">{{ $message }}</div> @enderror
                </div>
            </div>

            <div>
                <label class="text-sm font-extrabold text-slate-800">ঠিকানা</label>
                <input name="address" value="{{ old('address') }}" placeholder="যেমন: ওয়ার্ড নং ৩, সদর হাসপাতাল রোড"
                       class="mt-2 w-full rounded-xl border-slate-200 focus:border-red-500 focus:ring-red-500 font-medium px-4 py-3" />
                @error('address') <div class="text-sm text-red-600 font-bold mt-1">{{ $message }}</div> @enderror
            </div>

            {{-- 📍 Geospatial Map Picker --}}
            <div class="rounded-2xl border border-blue-100 bg-blue-50/40 p-4">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <p class="text-sm font-extrabold text-blue-900">📍 হাসপাতালের অবস্থান পিন করুন <span class="text-blue-400 font-semibold text-xs">(ঐচ্ছিক কিন্তু অত্যন্ত জরুরি)</span></p>
                        <p class="text-xs text-blue-600 font-medium mt-0.5">মানচিত্রে ক্লিক করুন বা নিচের বাটন দিয়ে সরাসরি আপনার অবস্থান নিন — ডোনার ম্যাচিং আরও নির্ভুল হবে।</p>
                    </div>
                    <button type="button" id="use-my-location"
                            class="shrink-0 flex items-center gap-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold px-3 py-2 rounded-lg transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        আমার লোকেশন
                    </button>
                </div>

                <div id="hospital-map"></div>

                <div id="map-coords-display" class="mt-2 hidden">
                    <p class="text-xs text-emerald-700 font-semibold bg-emerald-50 border border-emerald-200 rounded-lg px-3 py-1.5">
                        ✅ পিন সেট: <span id="map-lat-display"></span>, <span id="map-lng-display"></span>
                    </p>
                </div>

                {{-- Hidden inputs — submitted with the form --}}
                <input type="hidden" name="latitude" id="input-latitude" value="{{ old('latitude') }}">
                <input type="hidden" name="longitude" id="input-longitude" value="{{ old('longitude') }}">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="text-sm font-extrabold text-slate-800">কন্টাক্ট নাম</label>
                    <input name="contact_name" value="{{ old('contact_name') }}"
                           class="mt-2 w-full rounded-xl border-slate-200 focus:border-red-500 focus:ring-red-500 font-medium px-4 py-3" />
                    @error('contact_name') <div class="text-sm text-red-600 font-bold mt-1">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="text-sm font-extrabold text-slate-800">কন্টাক্ট নাম্বার <span class="text-red-500">*</span></label>
                    <input name="contact_number" value="{{ old('contact_number') }}" placeholder="01XXXXXXXXX"
                           class="mt-2 w-full rounded-xl border-slate-200 focus:border-red-500 focus:ring-red-500 font-bold px-4 py-3" />
                    @error('contact_number') <div class="text-sm text-red-600 font-bold mt-1">{{ $message }}</div> @enderror
                </div>
            </div>

            {{-- 🛡️ ওয়ান-ওয়ে হ্যান্ডশেক: প্রাইভেসি অপশন --}}
            <div class="rounded-2xl border border-purple-200 bg-purple-50 p-4 flex items-start gap-4">
                <div class="pt-0.5 shrink-0">
                    <input type="checkbox" name="is_phone_hidden" id="is_phone_hidden" value="1"
                           class="h-5 w-5 rounded border-purple-300 text-purple-600 focus:ring-purple-500 cursor-pointer"
                           {{ old('is_phone_hidden') ? 'checked' : '' }}>
                </div>
                <label for="is_phone_hidden" class="cursor-pointer">
                    <p class="font-extrabold text-purple-900 text-sm">🛡️ আমার নম্বর ফিডে গোপন রাখুন</p>
                    <p class="text-xs text-purple-700 font-medium mt-1">
                        চালু করলে ফিডে শুধু "রক্ত দিতে চাই" বাটন দেখাবে। ডোনার ক্লিক করার সাথে সাথে সার্ভার সরাসরি আপনার Telegram-এ ডোনারের ফোন নম্বর পাঠিয়ে দেবে — আপনাকে অ্যাপ খুলতে হবে না।
                    </p>
                    @if(!auth()->user()->telegram_chat_id)
                        <p class="text-xs text-amber-700 font-bold mt-1.5 bg-amber-50 border border-amber-200 rounded-lg px-2 py-1">
                            ⚠️ এই ফিচার ব্যবহার করতে প্রথমে <a href="{{ route('profile.edit') }}" class="underline">প্রোফাইলে</a> Telegram কানেক্ট করুন।
                        </p>
                    @endif
                </label>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="text-sm font-extrabold text-slate-800">জরুরিতা <span class="text-red-500">*</span></label>
                    <select id="urgency" name="urgency" class="mt-2 w-full rounded-xl border-slate-200 bg-white focus:border-red-500 focus:ring-red-500 font-bold px-4 py-3">
                        <option value="">সিলেক্ট করুন</option>
                        @foreach (\App\Enums\UrgencyLevel::cases() as $case)
                            <option value="{{ $case->value }}" @selected(old('urgency') === $case->value)>{{ $case->label() }}</option>
                        @endforeach
                    </select>
                    @error('urgency') <div class="text-sm text-red-600 font-bold mt-1">{{ $message }}</div> @enderror
                    <div id="urgency-threshold-note" class="mt-1 text-xs font-semibold text-amber-700 hidden"></div>
                </div>

                <div>
                    <label class="text-sm font-extrabold text-slate-800">কবে রক্ত লাগবে <span class="text-red-500">*</span></label>
                    <input id="needed_at" type="datetime-local" name="needed_at" value="{{ old('needed_at') }}"
                           class="mt-2 w-full rounded-xl border-slate-200 focus:border-red-500 focus:ring-red-500 font-bold px-4 py-3" />
                    @error('needed_at') <div class="text-sm text-red-600 font-bold mt-1">{{ $message }}</div> @enderror
                </div>
            </div>

            <div>
                <label class="text-sm font-extrabold text-slate-800">অতিরিক্ত নোট</label>
                <textarea name="notes" rows="3" placeholder="রোগীর বর্তমান অবস্থা বা অন্য কোনো তথ্য..."
                          class="mt-2 w-full rounded-xl border-slate-200 focus:border-red-500 focus:ring-red-500 font-medium px-4 py-3">{{ old('notes') }}</textarea>
                @error('notes') <div class="text-sm text-red-600 font-bold mt-1">{{ $message }}</div> @enderror
            </div>

            <x-captcha-field :captcha-question="$captchaQuestion" />

            <div class="flex items-center gap-4 pt-4 border-t border-slate-100">
                {{-- 🎯 ডাইনামিক সাবমিট বাটন --}}
                <button type="submit" 
                        :disabled="isSubmitting"
                        class="bg-red-600 hover:bg-red-700 disabled:bg-slate-400 disabled:cursor-not-allowed text-white px-8 py-3.5 rounded-xl text-sm font-black transition-all shadow-sm shadow-red-200 flex items-center justify-center min-w-[200px]">
                    
                    <span x-show="!isSubmitting" class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                        রিকোয়েস্ট সাবমিট করুন
                    </span>

                    <span x-show="isSubmitting" style="display: none;" class="flex items-center gap-2">
                        <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        প্রসেসিং হচ্ছে...
                    </span>
                </button>
                
                <a href="{{ route('requests.index') }}" class="px-6 py-3.5 rounded-xl font-bold text-slate-500 hover:bg-slate-100 transition">
                    বাতিল
                </a>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV/XN/WLs=" crossorigin=""></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const urgencySelect = document.getElementById('urgency');
    const neededAtInput = document.getElementById('needed_at');
    const note = document.getElementById('urgency-threshold-note');

    function updateUrgencyAvailability() {
        if (!neededAtInput.value) return;

        const neededAt = new Date(neededAtInput.value);
        const now = new Date();
        const diffHours = (neededAt - now) / (1000 * 60 * 60);

        const normalOption = Array.from(urgencySelect.options).find(opt => opt.value === 'normal');
        const disableEmergency = diffHours > 24;
        const disableUrgent = diffHours > 72;

        Array.from(urgencySelect.options).forEach(opt => {
            if (opt.value === 'emergency') opt.disabled = disableEmergency;
            if (opt.value === 'urgent') opt.disabled = disableUrgent;
        });

        if (urgencySelect.value === 'emergency' && disableEmergency) {
            urgencySelect.value = normalOption ? 'normal' : '';
        }

        if (urgencySelect.value === 'urgent' && disableUrgent) {
            urgencySelect.value = normalOption ? 'normal' : '';
        }

        if (disableUrgent) {
            note.textContent = 'নির্বাচিত সময় ৭২ ঘণ্টার বেশি — Emergency ও Urgent অপশন নিষ্ক্রিয়।';
            note.classList.remove('hidden');
            return;
        }

        if (disableEmergency) {
            note.textContent = 'নির্বাচিত সময় ২৪ ঘণ্টার বেশি — Emergency অপশন নিষ্ক্রিয়।';
            note.classList.remove('hidden');
            return;
        }

        note.classList.add('hidden');
        note.textContent = '';
    }

    neededAtInput.addEventListener('change', updateUrgencyAvailability);
    neededAtInput.addEventListener('input', updateUrgencyAvailability);
    updateUrgencyAvailability();

    // 📍 Leaflet Map Initialization
    let map = L.map('hospital-map').setView([23.8103, 90.4125], 7);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    let marker = null;
    const latInput = document.getElementById('input-latitude');
    const lngInput = document.getElementById('input-longitude');
    const displayDiv = document.getElementById('map-coords-display');
    const latDisplay = document.getElementById('map-lat-display');
    const lngDisplay = document.getElementById('map-lng-display');

    function setMarker(lat, lng) {
        if (marker) map.removeLayer(marker);
        marker = L.marker([lat, lng]).addTo(map);
        latInput.value = lat.toFixed(6);
        lngInput.value = lng.toFixed(6);
        latDisplay.textContent = lat.toFixed(6);
        lngDisplay.textContent = lng.toFixed(6);
        displayDiv.classList.remove('hidden');
    }

    // If old values exist
    if (latInput.value && lngInput.value) {
        let oldLat = parseFloat(latInput.value);
        let oldLng = parseFloat(lngInput.value);
        setMarker(oldLat, oldLng);
        map.setView([oldLat, oldLng], 15);
    }

    map.on('click', function(e) {
        setMarker(e.latlng.lat, e.latlng.lng);
    });

    document.getElementById('use-my-location').addEventListener('click', function() {
        if (!navigator.geolocation) {
            alert('আপনার ব্রাউজার লোকেশন সাপোর্ট করে না।');
            return;
        }
        const originalText = this.innerHTML;
        this.innerHTML = 'খুঁজছি...';
        navigator.geolocation.getCurrentPosition(
            position => {
                setMarker(position.coords.latitude, position.coords.longitude);
                map.setView([position.coords.latitude, position.coords.longitude], 15);
                this.innerHTML = originalText;
            },
            () => {
                alert('লোকেশন পাওয়া যায়নি। দয়া করে ম্যাপে ক্লিক করে পিন বসান।');
                this.innerHTML = originalText;
            }
        );
    });
});
</script>

{{-- 🏥 Hospital Autocomplete Alpine Component --}}
<script>
function hospitalAutocomplete(initialId = null, initialDisplay = '') {
    return {
        query:        initialDisplay || '',
        selectedId:   initialId,
        isVerified:   false,
        results:      [],
        open:         false,
        loading:      false,
        focusedIndex: -1,

        get canCreateNew() {
            return this.query.trim().length >= 2
                && !this.results.some(r => r.display?.toLowerCase() === this.query.toLowerCase() || r.name?.toLowerCase() === this.query.toLowerCase())
                && !this.loading;
        },

        async search() {
            const q = this.query.trim();
            if (q.length < 2) {
                this.results = [];
                this.open = false;
                this.selectedId = null;
                this.isVerified = false;
                return;
            }

            this.loading = true;
            this.open = true;
            this.focusedIndex = -1;

            try {
                const districtEl = document.querySelector('[name="district_id"]');
                const districtId = districtEl ? districtEl.value : '';
                const url = `/api/hospitals/search?q=${encodeURIComponent(q)}${districtId ? '&district_id=' + districtId : ''}`;
                const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
                this.results = await res.json();
            } catch (e) {
                this.results = [];
            } finally {
                this.loading = false;
            }
        },

        select(item) {
            this.query      = item.display || item.name;
            this.selectedId = item.id;
            this.isVerified = item.is_verified;
            this.open       = false;
            this.results    = [];
        },

        async createNew() {
            const name = this.query.trim();
            if (!name) return;

            this.loading = true;

            try {
                const res = await fetch('/api/hospitals', {
                    method:  'POST',
                    headers: {
                        'Content-Type':  'application/json',
                        'Accept':        'application/json',
                        'X-CSRF-TOKEN':  document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ name }),
                });

                const data = await res.json();

                if (data.id) {
                    this.selectedId = data.id;
                    this.isVerified = false;
                    this.open = false;
                    this.results = [];
                }
            } catch (e) {
                console.error('[HospitalAutocomplete] createNew failed:', e);
            } finally {
                this.loading = false;
            }
        },

        handleBlur() {
            setTimeout(() => { this.open = false; }, 200);
        },

        focusNext() {
            if (this.focusedIndex < this.results.length - 1) this.focusedIndex++;
        },

        focusPrev() {
            if (this.focusedIndex > 0) this.focusedIndex--;
        },

        selectFocused() {
            if (this.focusedIndex >= 0 && this.results[this.focusedIndex]) {
                this.select(this.results[this.focusedIndex]);
            } else if (this.canCreateNew) {
                this.createNew();
            }
        },
    };
}
</script>
@endpush
