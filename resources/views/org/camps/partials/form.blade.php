<div x-data="campForm()" x-init="init()" class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden p-6 md:p-8">
    <form action="{{ $formAction }}" method="POST" class="space-y-6">
        @csrf
        @if(($method ?? 'POST') !== 'POST')
            @method($method)
        @endif

        <div>
            <label for="name" class="block text-sm font-extrabold text-slate-900 mb-2">ক্যাম্পের নাম <span class="text-red-500">*</span></label>
            <input type="text" id="name" name="name" value="{{ old('name', $camp?->name) }}" required placeholder="যেমন: স্বাধীনতা দিবস রক্তদান ক্যাম্প" class="w-full rounded-xl border-slate-200 focus:border-teal-500 focus:ring-teal-500 bg-slate-50 text-slate-900 px-4 py-3 font-medium">
            @error('name') <p class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="start_at" class="block text-sm font-extrabold text-slate-900 mb-2">শুরুর সময় <span class="text-red-500">*</span></label>
                <input type="datetime-local" id="start_at" name="start_at" value="{{ old('start_at', optional($camp?->start_at)->format('Y-m-d\\TH:i')) }}" required class="w-full rounded-xl border-slate-200 focus:border-teal-500 focus:ring-teal-500 bg-slate-50 text-slate-900 px-4 py-3 font-medium">
                @error('start_at') <p class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="end_at" class="block text-sm font-extrabold text-slate-900 mb-2">শেষ সময় <span class="text-red-500">*</span></label>
                <input type="datetime-local" id="end_at" name="end_at" value="{{ old('end_at', optional($camp?->end_at)->format('Y-m-d\\TH:i')) }}" required class="w-full rounded-xl border-slate-200 focus:border-teal-500 focus:ring-teal-500 bg-slate-50 text-slate-900 px-4 py-3 font-medium">
                @error('end_at') <p class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="district_id" class="block text-sm font-extrabold text-slate-900 mb-2">জেলা <span class="text-red-500">*</span></label>
                <select id="district_id" name="district_id" x-model="districtId" @change="onDistrictChange" required class="w-full rounded-xl border-slate-200 focus:border-teal-500 focus:ring-teal-500 bg-slate-50 text-slate-900 px-4 py-3 font-medium">
                    <option value="">জেলা নির্বাচন করুন</option>
                    @foreach($districts as $district)
                        <option value="{{ $district->id }}" @selected((string) old('district_id', $camp?->district_id) === (string) $district->id)>{{ $district->bn_name ?? $district->name }}</option>
                    @endforeach
                </select>
                @error('district_id') <p class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="upazila_id" class="block text-sm font-extrabold text-slate-900 mb-2">উপজেলা <span class="text-red-500">*</span></label>
                <select id="upazila_id" name="upazila_id" x-model="upazilaId" required class="w-full rounded-xl border-slate-200 focus:border-teal-500 focus:ring-teal-500 bg-slate-50 text-slate-900 px-4 py-3 font-medium">
                    <option value="">উপজেলা নির্বাচন করুন</option>
                    <template x-for="item in upazilas" :key="item.id">
                        <option :value="item.id" x-text="item.name"></option>
                    </template>
                </select>
                @error('upazila_id') <p class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label for="address_line" class="block text-sm font-extrabold text-slate-900 mb-2">ঠিকানা (বিস্তারিত) <span class="text-red-500">*</span></label>
            <input type="text" id="address_line" name="address_line" value="{{ old('address_line', $camp?->address_line) }}" required placeholder="যেমন: পৌরসভা মিলনায়তন, সিভিল হাসপাতালের বিপরীত পাশে" class="w-full rounded-xl border-slate-200 focus:border-teal-500 focus:ring-teal-500 bg-slate-50 text-slate-900 px-4 py-3 font-medium">
            @error('address_line') <p class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="contact_name" class="block text-sm font-extrabold text-slate-900 mb-2">কন্টাক্ট পারসনের নাম <span class="text-red-500">*</span></label>
                <input type="text" id="contact_name" name="contact_name" value="{{ old('contact_name', $camp?->contact_name) }}" required class="w-full rounded-xl border-slate-200 focus:border-teal-500 focus:ring-teal-500 bg-slate-50 text-slate-900 px-4 py-3 font-medium">
                @error('contact_name') <p class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="contact_phone" class="block text-sm font-extrabold text-slate-900 mb-2">কন্টাক্ট ফোন <span class="text-red-500">*</span></label>
                <input type="text" id="contact_phone" name="contact_phone" value="{{ old('contact_phone', $camp?->contact_phone) }}" required placeholder="01XXXXXXXXX" class="w-full rounded-xl border-slate-200 focus:border-teal-500 focus:ring-teal-500 bg-slate-50 text-slate-900 px-4 py-3 font-medium">
                @error('contact_phone') <p class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="target_donors" class="block text-sm font-extrabold text-slate-900 mb-2">লক্ষ্যমাত্রা ডোনার (ঐচ্ছিক)</label>
                <input type="number" id="target_donors" name="target_donors" min="1" value="{{ old('target_donors', $camp?->target_donors) }}" class="w-full rounded-xl border-slate-200 focus:border-teal-500 focus:ring-teal-500 bg-slate-50 text-slate-900 px-4 py-3 font-medium">
                @error('target_donors') <p class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</p> @enderror
            </div>
            <div class="flex items-end">
                <label class="inline-flex items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 w-full">
                    <input type="checkbox" name="is_public" value="1" @checked(old('is_public', $camp?->is_public)) class="rounded border-slate-300 text-teal-600 focus:ring-teal-500">
                    <span class="text-sm font-bold text-slate-700">সবার জন্য দৃশ্যমান (Public)</span>
                </label>
            </div>
        </div>

        <div>
            <label for="notes" class="block text-sm font-extrabold text-slate-900 mb-2">অতিরিক্ত নোট (ঐচ্ছিক)</label>
            <textarea id="notes" name="notes" rows="3" class="w-full rounded-xl border-slate-200 focus:border-teal-500 focus:ring-teal-500 bg-slate-50 text-slate-900 px-4 py-3 font-medium">{{ old('notes', $camp?->notes) }}</textarea>
            @error('notes') <p class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</p> @enderror
        </div>

        <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row justify-end gap-3">
            <button type="submit" name="submit_action" value="draft" class="px-6 py-3 bg-slate-100 hover:bg-slate-200 text-slate-800 rounded-xl text-sm font-black transition">
                ড্রাফট সংরক্ষণ করুন
            </button>
            <button type="submit" name="submit_action" value="publish" class="px-6 py-3 bg-teal-600 hover:bg-teal-700 text-white rounded-xl text-sm font-black shadow-sm transition">
                পাবলিশ করুন
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
function campForm() {
    return {
        districtId: @json((string) old('district_id', $camp?->district_id)),
        upazilaId: @json((string) old('upazila_id', $camp?->upazila_id)),
        upazilas: [],

        init() {
            if (this.districtId) {
                this.fetchUpazilas(this.districtId).then(() => {
                    if (this.upazilaId) {
                        this.upazilaId = String(this.upazilaId);
                    }
                });
            }
        },

        async onDistrictChange() {
            this.upazilaId = '';
            if (!this.districtId) {
                this.upazilas = [];
                return;
            }
            await this.fetchUpazilas(this.districtId);
        },

        async fetchUpazilas(districtId) {
            const response = await fetch(`/ajax/upazilas/${districtId}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
            });
            if (!response.ok) {
                this.upazilas = [];
                return;
            }
            this.upazilas = await response.json();
        },
    }
}
</script>
@endpush
