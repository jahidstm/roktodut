@extends('layouts.app')

@section('title', 'হেলথ লেজার ড্যাশবোর্ড')

@push('head')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
@endpush

@section('content')
<div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8 py-10" x-data="{ showAddModal: false }" data-panel-id="health-ledger">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div class="flex flex-col gap-2">
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900">স্বাস্থ্য রেকর্ড</h1>
            <p class="text-sm text-slate-500">আপনার সর্বশেষ হেলথ মেট্রিক্সের ট্রেন্ড ও রিস্ক সিগন্যাল।</p>
        </div>
        <button @click="showAddModal = true" class="inline-flex items-center justify-center gap-2 bg-red-600 hover:bg-red-700 text-white font-bold py-2.5 px-5 rounded-xl transition-colors shadow-sm text-sm shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
            নতুন রেকর্ড যুক্ত করুন
        </button>
    </div>

    <div class="mb-6">
        @if (!empty($nudges))
            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4 sm:p-5">
                <div class="flex items-center gap-2 text-amber-700 font-semibold">
                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-amber-100">⚠️</span>
                    AI Risk Nudges
                </div>
                <ul class="mt-3 space-y-2 text-sm text-amber-800">
                    @foreach ($nudges as $nudge)
                        <li>• {{ $nudge }}</li>
                    @endforeach
                </ul>
            </div>
        @else
            <div class="rounded-2xl border border-emerald-100 bg-emerald-50 p-4 sm:p-5 text-emerald-700 font-semibold">
                ✅ কোনো রিস্ক সিগন্যাল নেই — আপনার হেলথ ট্রেন্ড স্থিতিশীল।
            </div>
        @endif
    </div>

    <div class="grid gap-6 lg:grid-cols-2 mb-6">
        <div class="rounded-2xl border border-sky-100 bg-sky-50 p-4 sm:p-5 flex items-start gap-3">
            <span class="text-2xl">🩸</span>
            <div>
                <h3 class="font-bold text-sky-900 text-sm">হিমোগ্লোবিনের আদর্শ মাত্রা</h3>
                <p class="text-xs text-sky-700 mt-1 leading-relaxed">রক্তদানের জন্য আপনার হিমোগ্লোবিন অন্তত ১২.৫ g/dL থাকা প্রয়োজন। চার্টের সবুজ অংশটি নিরাপদ জোন নির্দেশ করে।</p>
            </div>
        </div>
        <div class="rounded-2xl border border-teal-100 bg-teal-50 p-4 sm:p-5 flex items-start gap-3">
            <span class="text-2xl">🩺</span>
            <div>
                <h3 class="font-bold text-teal-900 text-sm">ব্লাড প্রেসারের আদর্শ মাত্রা</h3>
                <p class="text-xs text-teal-700 mt-1 leading-relaxed">স্বাভাবিক ব্লাড প্রেসার ১২০/৮০ mmHg এর আশেপাশে থাকা আদর্শ। চার্টের সবুজ লাইনটি এটি নির্দেশ করে।</p>
            </div>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">হিমোগ্লোবিন ট্রেন্ড (g/dL)</h2>
                    <p class="text-xs text-slate-500">শেষ ৬ মাসের ট্রেন্ড</p>
                </div>
                <span class="text-xs font-bold text-slate-400 bg-slate-100 px-2.5 py-1 rounded-md">আপনার স্বাস্থ্য বিশ্লেষণ</span>
            </div>
            <div id="hb-chart" class="min-h-[260px]"></div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">ব্লাড প্রেসার ট্রেন্ড (mmHg)</h2>
                    <p class="text-xs text-slate-500">সিস্টোলিক ও ডায়াস্টোলিক</p>
                </div>
                <span class="text-xs font-bold text-slate-400 bg-slate-100 px-2.5 py-1 rounded-md">আপনার স্বাস্থ্য বিশ্লেষণ</span>
            </div>
            <div id="bp-chart" class="min-h-[260px]"></div>
        </div>
    </div>

    <div class="mt-8 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <h3 class="text-base font-bold text-slate-900 mb-4">সাম্প্রতিক হেলথ রেকর্ড</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="text-slate-500">
                    <tr class="text-left border-b border-slate-100">
                        <th class="pb-3 pr-4 font-bold">তারিখ</th>
                        <th class="pb-3 pr-4 font-bold">Hb</th>
                        <th class="pb-3 pr-4 font-bold">BP (S/D)</th>
                        <th class="pb-3 pr-4 font-bold">ওজন (কেজি)</th>
                        <th class="pb-3 pr-4 font-bold">ডেটা সোর্স</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($records as $record)
                        <tr>
                            <td class="py-3 pr-4 text-slate-700 font-medium">
                                {{ $record->recorded_at?->format('d M, Y') ?? '—' }}
                            </td>
                            <td class="py-3 pr-4 text-slate-700 font-semibold">{{ $record->hemoglobin_level ?? '—' }}</td>
                            <td class="py-3 pr-4 text-slate-700 font-semibold">
                                {{ $record->systolic_bp ?? '—' }}/{{ $record->diastolic_bp ?? '—' }}
                            </td>
                            <td class="py-3 pr-4 text-slate-700 font-semibold">{{ $record->weight_kg ?? '—' }}</td>
                            <td class="py-3 pr-4 text-slate-700">
                                @if($record->source === 'verified_donation')
                                    <span class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2.5 py-1 rounded-md">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        ভেরিফাইড (হাসপাতাল)
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 text-xs font-bold text-blue-700 bg-blue-50 border border-blue-200 px-2.5 py-1 rounded-md">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        নিজস্ব ইনপুট
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-6 text-center text-slate-400">কোনো ডেটা পাওয়া যায়নি।</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Add Record Modal --}}
    <div x-show="showAddModal" 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-0"
         style="display: none;">
        <div x-show="showAddModal" x-transition.opacity class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" @click="showAddModal = false"></div>
        <div x-show="showAddModal" 
             x-transition.scale.95 
             class="relative bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden z-10">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-lg font-extrabold text-slate-900">নতুন হেলথ রেকর্ড যুক্ত করুন</h3>
                <button @click="showAddModal = false" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form action="{{ route('health-ledger.store') }}" method="POST" class="p-6">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">তারিখ <span class="text-red-500">*</span></label>
                        <input type="date" name="recorded_at" required value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}" class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm font-medium focus:bg-white focus:border-red-400 focus:ring-2 focus:ring-red-100">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">হিমোগ্লোবিন (g/dL) <span class="text-xs font-normal text-slate-400">(৫-২৫)</span></label>
                        <input type="number" step="0.01" min="5" max="25" name="hemoglobin_level" placeholder="যেমন: 14.5" class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm font-medium focus:bg-white focus:border-red-400 focus:ring-2 focus:ring-red-100">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-1.5">সিস্টোলিক (উপরে)</label>
                            <input type="number" min="70" max="200" name="systolic_bp" placeholder="120" class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm font-medium focus:bg-white focus:border-red-400 focus:ring-2 focus:ring-red-100">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-1.5">ডায়াস্টোলিক (নিচে)</label>
                            <input type="number" min="40" max="130" name="diastolic_bp" placeholder="80" class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm font-medium focus:bg-white focus:border-red-400 focus:ring-2 focus:ring-red-100">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">ওজন (কেজি)</label>
                        <input type="number" step="0.1" min="30" max="200" name="weight_kg" placeholder="যেমন: 65" class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm font-medium focus:bg-white focus:border-red-400 focus:ring-2 focus:ring-red-100">
                    </div>
                </div>
                <div class="mt-6 pt-5 border-t border-slate-100">
                    <button type="submit" class="w-full flex justify-center items-center bg-red-600 hover:bg-red-700 text-white font-bold py-2.5 px-4 rounded-xl transition-colors shadow-sm text-center">
                        সেভ করুন
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const labels = @json($charts['labels'] ?? []);
    const hbSeries = @json($charts['hemoglobin'] ?? []);
    const sysSeries = @json($charts['systolic'] ?? []);
    const diaSeries = @json($charts['diastolic'] ?? []);
    const hbMarkers = @json($charts['hb_markers'] ?? []);
    const sysMarkers = @json($charts['sys_markers'] ?? []);
    const diaMarkers = @json($charts['dia_markers'] ?? []);

    const hbChart = new ApexCharts(document.querySelector("#hb-chart"), {
        chart: {
            type: 'line',
            height: 260,
            toolbar: { show: false },
            zoom: { enabled: false }
        },
        stroke: { curve: 'smooth', width: 3 },
        series: [{ name: 'Hemoglobin', data: hbSeries }],
        xaxis: { categories: labels, tooltip: { enabled: false } },
        colors: ['#dc2626'],
        grid: { borderColor: '#e2e8f0' },
        markers: { size: 5, strokeWidth: 2, discrete: hbMarkers, hover: { sizeOffset: 2 } },
        dataLabels: { enabled: false },
        annotations: {
            yaxis: [
                {
                    y: 12.5,
                    y2: 18,
                    fillColor: '#10b981',
                    opacity: 0.1,
                    label: { text: 'Safe Zone (12.5+)', style: { color: '#047857', background: 'transparent', fontWeight: 800 } }
                }
            ]
        }
    });

    hbChart.render();

    const bpChart = new ApexCharts(document.querySelector("#bp-chart"), {
        chart: {
            type: 'line',
            height: 260,
            toolbar: { show: false },
            zoom: { enabled: false }
        },
        stroke: { curve: 'smooth', width: 3 },
        series: [
            { name: 'Systolic', data: sysSeries },
            { name: 'Diastolic', data: diaSeries }
        ],
        xaxis: { categories: labels, tooltip: { enabled: false } },
        colors: ['#2563eb', '#0f766e'],
        grid: { borderColor: '#e2e8f0' },
        markers: { size: 5, strokeWidth: 2, discrete: [...sysMarkers, ...diaMarkers], hover: { sizeOffset: 2 } },
        dataLabels: { enabled: false },
        annotations: {
            yaxis: [
                {
                    y: 120,
                    borderColor: '#3b82f6',
                    strokeDashArray: 4,
                    label: { text: 'Ideal Systolic (120)', style: { color: '#1d4ed8', background: 'transparent', fontWeight: 800 } }
                },
                {
                    y: 80,
                    borderColor: '#0f766e',
                    strokeDashArray: 4,
                    label: { text: 'Ideal Diastolic (80)', style: { color: '#0f766e', background: 'transparent', fontWeight: 800 } }
                }
            ]
        }
    });

    bpChart.render();
</script>
@endsection
