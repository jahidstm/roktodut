@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-3">ডোনার সার্চ</h1>

    {{-- Search Form --}}
    <form method="GET" action="{{ route('search') }}" class="card p-3 mb-4">
        <div class="row g-2">
            <div class="col-md-3">
                <label class="form-label">বিভাগ</label>
                <select name="division" id="division" class="form-select">
                    <option value="">সিলেক্ট করুন</option>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">জেলা <span class="text-danger">*</span></label>
                <select name="district" id="district" class="form-select" required>
                    <option value="">সিলেক্ট করুন</option>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">উপজেলা/এরিয়া</label>
                <select name="upazila" id="upazila" class="form-select">
                    <option value="">সব এলাকা</option>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">রক্তের গ্রুপ <span class="text-danger">*</span></label>
                <select name="blood_group" class="form-select" required>
                    <option value="">সিলেক্ট করুন</option>
                    @foreach($bloodGroups as $bg)
                        <option value="{{ $bg->value }}" @selected(($query['blood_group'] ?? '') === $bg->value)>
                            {{ $bg->value }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-12 mt-2">
                <button class="btn btn-danger" type="submit">খুঁজুন</button>
            </div>
        </div>
    </form>

    {{-- Messages --}}
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- Results --}}
    <div class="card p-3">
        <h2 class="h5">ফলাফল</h2>

        @if(($donors?->count() ?? 0) === 0)
            <p class="text-muted mb-0">সার্চ করে ডোনার দেখুন।</p>
        @else
            <div class="list-group">
                @foreach($donors as $donor)
                    @php
                        $donorId = $donor->id;
                        $challenge = session("reveal_challenge.$donorId");
                        $revealed = session("revealed_phone.$donorId");
                        $target = session('reveal_target');
                    @endphp

                    <div class="list-group-item">
                        <div class="d-flex justify-content-between">
                            <div>
                                <strong>{{ $donor->name }}</strong>
                                <div class="text-muted">
                                    {{ $donor->district }}{{ $donor->upazila ? ', '.$donor->upazila : '' }}
                                    | {{ $donor->blood_group }}
                                </div>
                            </div>

                            <div class="text-end">
                                @if($revealed)
                                    <div><strong>{{ $revealed }}</strong></div>
                                @else
                                    {{-- masked phone --}}
                                    <div><strong>{{ substr($donor->phone,0,2) }}******{{ substr($donor->phone,-3) }}</strong></div>
                                @endif
                            </div>
                        </div>

                        {{-- Flow 1: start -> challenge -> verify --}}
                        @if(!$revealed)
                            @if($target == $donorId && is_array($challenge))
                                <form method="POST" action="{{ route('donors.reveal.verify', $donorId) }}" class="mt-2">
                                    @csrf
                                    <div class="row g-2 align-items-end">
                                        <div class="col-md-4">
                                            <label class="form-label">প্রশ্ন: {{ $challenge['question'] }}</label>
                                            <input type="number" name="answer" class="form-control" required>
                                        </div>
                                        <div class="col-md-2">
                                            <button class="btn btn-primary" type="submit">সাবমিট</button>
                                        </div>
                                    </div>
                                </form>
                            @else
                                <form method="POST" action="{{ route('donors.reveal.start', $donorId) }}" class="mt-2">
                                    @csrf
                                    <button class="btn btn-outline-danger" type="submit">ফোন দেখুন</button>
                                </form>
                            @endif
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

<script>
(async function () {
    // Load JSON locations for cascading dropdowns
    try {
        const res = await fetch('/data/bd_locations.json');
        const data = await res.json();

        const divisionEl = document.getElementById('division');
        const districtEl = document.getElementById('district');
        const upazilaEl = document.getElementById('upazila');

        const selectedDivision = @json($query['division'] ?? '');
        const selectedDistrict = @json($query['district'] ?? '');
        const selectedUpazila  = @json($query['upazila'] ?? '');

        // Populate divisions
        data.divisions.forEach(d => {
            const opt = document.createElement('option');
            opt.value = d.name;
            opt.textContent = d.name;
            if (d.name === selectedDivision) opt.selected = true;
            divisionEl.appendChild(opt);
        });

        function populateDistricts() {
            districtEl.innerHTML = '<option value="">সিলেক্ট করুন</option>';
            upazilaEl.innerHTML = '<option value="">সব এলাকা</option>';

            const div = data.divisions.find(x => x.name === divisionEl.value);
            const districts = div ? div.districts : [];
            districts.forEach(dd => {
                const opt = document.createElement('option');
                opt.value = dd.name;
                opt.textContent = dd.name;
                if (dd.name === selectedDistrict) opt.selected = true;
                districtEl.appendChild(opt);
            });
        }

        function populateUpazilas() {
            upazilaEl.innerHTML = '<option value="">সব এলাকা</option>';

            const div = data.divisions.find(x => x.name === divisionEl.value);
            const dist = div?.districts?.find(x => x.name === districtEl.value);
            const upazilas = dist ? dist.upazilas : [];

            upazilas.forEach(u => {
                const opt = document.createElement('option');
                opt.value = u;
                opt.textContent = u;
                if (u === selectedUpazila) opt.selected = true;
                upazilaEl.appendChild(opt);
            });
        }

        divisionEl.addEventListener('change', () => {
            populateDistricts();
            populateUpazilas();
        });

        districtEl.addEventListener('change', () => {
            populateUpazilas();
        });

        // Initial fill
        if (!divisionEl.value && data.divisions.length) {
            divisionEl.value = selectedDivision || data.divisions[0].name;
        }
        populateDistricts();
        if (selectedDistrict) districtEl.value = selectedDistrict;
        populateUpazilas();
        if (selectedUpazila) upazilaEl.value = selectedUpazila;

    } catch (e) {
        console.error('Failed to load locations JSON', e);
    }
})();
</script>
@endsection