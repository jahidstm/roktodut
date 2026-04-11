<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    {{-- বিভাগ --}}
    <div>
        <label for="comp_division" class="text-sm font-extrabold text-slate-800">বিভাগ <span class="text-red-500">*</span></label>
        <div class="relative">
            <select id="comp_division" name="division_id" class="mt-2 w-full appearance-none bg-none rounded-xl border-slate-200 bg-white cursor-pointer focus:border-red-500 focus:ring-red-500 font-medium px-4 py-3 pr-10" required>
                <option value="">বিভাগ নির্বাচন</option>
                @foreach($divisions as $division)
                    <option value="{{ $division->id }}" {{ $selectedDivision == $division->id ? 'selected' : '' }}>
                        {{ $division->name }}
                    </option>
                @endforeach
            </select>
            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-500">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </div>
        </div>
    </div>

    {{-- জেলা --}}
    <div>
        <label for="comp_district" class="text-sm font-extrabold text-slate-800">জেলা <span class="text-red-500">*</span></label>
        <div class="relative">
            <select id="comp_district" name="district_id" class="mt-2 w-full appearance-none bg-none rounded-xl border-slate-200 bg-white cursor-pointer focus:border-red-500 focus:ring-red-500 font-medium px-4 py-3 pr-10" required {{ $selectedDivision ? '' : 'disabled' }}>
                <option value="">{{ $selectedDivision ? 'লোড হচ্ছে...' : 'জেলা নির্বাচন' }}</option>
            </select>
            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-500">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </div>
        </div>
    </div>

    {{-- উপজেলা --}}
    <div>
        <label for="comp_upazila" class="text-sm font-extrabold text-slate-800">উপজেলা/থানা <span class="text-red-500">*</span></label>
        <div class="relative">
            <select id="comp_upazila" name="upazila_id" class="mt-2 w-full appearance-none bg-none rounded-xl border-slate-200 bg-white cursor-pointer focus:border-red-500 focus:ring-red-500 font-medium px-4 py-3 pr-10" required disabled>
                <option value="">উপজেলা/এরিয়া</option>
            </select>
            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-500">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </div>
        </div>
    </div>
</div>

{{-- 🚀 Isolated AJAX Script (Protected against DOM Conflicts) --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const divSelect = document.getElementById('comp_division');
        const distSelect = document.getElementById('comp_district');
        const upzSelect = document.getElementById('comp_upazila');

        const oldDist = "{{ $selectedDistrict ?? '' }}";
        const oldUpz = "{{ $selectedUpazila ?? '' }}";

        function loadDistricts(divId, preSelectedDist = null) {
            distSelect.innerHTML = '<option value="">লোড হচ্ছে...</option>';
            distSelect.disabled = true;
            upzSelect.innerHTML = '<option value="">উপজেলা/এরিয়া</option>';
            upzSelect.disabled = true;

            if(divId) {
                fetch(`/ajax/districts/${divId}`)
                    .then(res => {
                        if(!res.ok) throw new Error('API Error');
                        return res.json();
                    })
                    .then(data => {
                        distSelect.innerHTML = '<option value="">জেলা নির্বাচন</option>';
                        data.forEach(d => {
                            let selected = (preSelectedDist == d.id) ? 'selected' : '';
                            distSelect.innerHTML += `<option value="${d.id}" ${selected}>${d.name}</option>`;
                        });
                        distSelect.disabled = false;

                        if(preSelectedDist) {
                            loadUpazilas(preSelectedDist, oldUpz);
                        }
                    })
                    .catch(err => {
                        console.error("Districts API Failed:", err);
                        distSelect.innerHTML = '<option value="">সার্ভার এরর!</option>';
                    });
            } else {
                distSelect.innerHTML = '<option value="">জেলা নির্বাচন</option>';
            }
        }

        function loadUpazilas(distId, preSelectedUpz = null) {
            upzSelect.innerHTML = '<option value="">লোড হচ্ছে...</option>';
            upzSelect.disabled = true;

            if(distId) {
                fetch(`/ajax/upazilas/${distId}`)
                    .then(res => {
                        if(!res.ok) throw new Error('API Error');
                        return res.json();
                    })
                    .then(data => {
                        upzSelect.innerHTML = '<option value="">উপজেলা/এরিয়া</option>';
                        data.forEach(u => {
                            let selected = (preSelectedUpz == u.id) ? 'selected' : '';
                            upzSelect.innerHTML += `<option value="${u.id}" ${selected}>${u.name}</option>`;
                        });
                        upzSelect.disabled = false;
                    })
                    .catch(err => {
                        console.error("Upazilas API Failed:", err);
                        upzSelect.innerHTML = '<option value="">সার্ভার এরর!</option>';
                    });
            } else {
                upzSelect.innerHTML = '<option value="">উপজেলা/এরিয়া</option>';
            }
        }

        divSelect.addEventListener('change', function() { loadDistricts(this.value); });
        distSelect.addEventListener('change', function() { loadUpazilas(this.value); });

        // পেজ লোড বা ভ্যালিডেশন ফেইল হলে ডেটা রি-পপুলেট করা
        if(divSelect.value) {
            loadDistricts(divSelect.value, oldDist);
        }
    });
</script>