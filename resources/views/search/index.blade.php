<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('ডোনার সার্চ') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Alerts --}}
            @if (session('error'))
                <div class="mb-4 rounded-md bg-red-50 p-4 text-red-700 border border-red-200">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Search Form --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('search') }}">
                        <div class="grid grid-cols-1 md:grid-cols-5 gap-3 items-end">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">বিভাগ</label>
                                <select name="division" id="division" class="w-full rounded-md border-gray-300">
                                    <option value="">সিলেক্ট করুন</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    জেলা <span class="text-red-600">*</span>
                                </label>
                                <select name="district" id="district" class="w-full rounded-md border-gray-300" required>
                                    <option value="">সিলেক্ট করুন</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">উপজেলা/এরিয়া</label>
                                <select name="upazila" id="upazila" class="w-full rounded-md border-gray-300">
                                    <option value="">সব এলাকা</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    রক্তের গ্রুপ <span class="text-red-600">*</span>
                                </label>
                                <select name="blood_group" class="w-full rounded-md border-gray-300" required>
                                    <option value="">সিলেক্ট করুন</option>
                                    @foreach ($bloodGroups as $bg)
                                        <option value="{{ $bg->value }}" @selected(($query['blood_group'] ?? '') === $bg->value)>
                                            {{ $bg->value }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <button type="submit"
                                    class="w-full inline-flex justify-center items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">
                                    খুঁজুন
                                </button>
                            </div>
                        </div>

                        {{-- Keep selected values --}}
                        <input type="hidden" id="selectedDivision" value="{{ $query['division'] ?? '' }}">
                        <input type="hidden" id="selectedDistrict" value="{{ $query['district'] ?? '' }}">
                        <input type="hidden" id="selectedUpazila" value="{{ $query['upazila'] ?? '' }}">
                    </form>
                </div>
            </div>

            {{-- Results --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">ফলাফল</h3>
                        @if(isset($donors) && $donors->count() > 0)
                            <span class="text-sm text-gray-600">মোট: {{ $donors->count() }}</span>
                        @endif
                    </div>

                    @if (!isset($donors) || $donors->count() === 0)
                        <p class="text-gray-600">ডোনার দেখতে উপরের ফর্মে সার্চ করুন।</p>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach ($donors as $donor)
                                @php
                                    $donorId = $donor->id;
                                    $challenge = session("reveal_challenge.$donorId");
                                    $revealedPhone = session("revealed_phone.$donorId");
                                    $target = session('reveal_target');
                                    $masked = substr($donor->phone, 0, 2) . '******' . substr($donor->phone, -3);
                                @endphp

                                <div class="border rounded-lg p-4">
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <div class="font-semibold text-gray-900">{{ $donor->name }}</div>
                                            <div class="text-sm text-gray-600">
                                                {{ $donor->district }}{{ $donor->upazila ? ', '.$donor->upazila : '' }}
                                            </div>
                                            <div class="text-sm text-gray-600">
                                                গ্রুপ: <span class="font-semibold">{{ $donor->blood_group }}</span>
                                            </div>

                                            <div class="mt-2 flex flex-wrap gap-2 text-xs">
                                                @if($donor->is_ready_now)
                                                    <span class="px-2 py-1 rounded bg-green-100 text-green-800">Ready Now</span>
                                                @endif
                                                @if($donor->verified_badge)
                                                    <span class="px-2 py-1 rounded bg-blue-100 text-blue-800">Verified</span>
                                                @endif
                                                @if($donor->nid_status === 'approved')
                                                    <span class="px-2 py-1 rounded bg-purple-100 text-purple-800">NID Approved</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="text-right">
                                            <div class="text-sm text-gray-500">ফোন</div>
                                            <div class="font-semibold text-gray-900">
                                                {{ $revealedPhone ? $revealedPhone : $masked }}
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Reveal Flow --}}
                                    @if(!$revealedPhone)
                                        <div class="mt-3">
                                            @if($target == $donorId && is_array($challenge))
                                                {{-- Challenge Verify Form --}}
                                                <form method="POST"
                                                      data-reveal-verify
                                                      action="{{ route('donors.reveal.verify', $donorId) }}"
                                                      class="flex flex-col sm:flex-row gap-2 items-end">
                                                    @csrf
                                                    <div class="flex-1">
                                                        <label class="block text-sm text-gray-700 mb-1">
                                                            প্রশ্ন: {{ $challenge['question'] }}
                                                        </label>
                                                        <input type="number" name="answer" required
                                                            class="w-full rounded-md border-gray-300"
                                                            placeholder="উত্তর লিখুন">
                                                    </div>
                                                    <button type="submit"
                                                        class="px-4 py-2 bg-gray-900 text-white rounded-md hover:bg-black">
                                                        সাবমিট
                                                    </button>
                                                </form>
                                            @else
                                                {{-- Reveal Start Button --}}
                                                <button type="button"
                                                    data-reveal-start="{{ route('donors.reveal.start', $donorId) }}"
                                                    class="px-4 py-2 border border-red-600 text-red-600 rounded-md hover:bg-red-50">
                                                    ফোন দেখুন
                                                </button>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>