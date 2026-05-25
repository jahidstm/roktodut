@extends('layouts.app')

@section('title', 'হেলথ লেজার ড্যাশবোর্ড')

@push('head')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
@endpush

@section('content')
<div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8 py-10">
    <div class="flex flex-col gap-2 mb-8">
        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900">Predictive Health Ledger</h1>
        <p class="text-sm text-slate-500">আপনার সর্বশেষ হেলথ মেট্রিক্সের ট্রেন্ড ও রিস্ক সিগন্যাল।</p>
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

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Hemoglobin Trend (g/dL)</h2>
                    <p class="text-xs text-slate-500">শেষ ৬ মাসের ট্রেন্ড</p>
                </div>
                <span class="text-xs font-semibold text-slate-400">AI Trend</span>
            </div>
            <div id="hb-chart" class="min-h-[260px]"></div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Blood Pressure Trend (mmHg)</h2>
                    <p class="text-xs text-slate-500">সিস্টোলিক ও ডায়াস্টোলিক</p>
                </div>
                <span class="text-xs font-semibold text-slate-400">AI Trend</span>
            </div>
            <div id="bp-chart" class="min-h-[260px]"></div>
        </div>
    </div>

    <div class="mt-8 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <h3 class="text-base font-bold text-slate-900 mb-4">Recent Health Entries</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="text-slate-500">
                    <tr class="text-left">
                        <th class="py-2 pr-4">তারিখ</th>
                        <th class="py-2 pr-4">Hb</th>
                        <th class="py-2 pr-4">BP (S/D)</th>
                        <th class="py-2 pr-4">ওজন (কেজি)</th>
                        <th class="py-2 pr-4">সূত্র</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($records as $record)
                        <tr>
                            <td class="py-2 pr-4 text-slate-700">
                                {{ $record->recorded_at?->format('d M, Y') ?? '—' }}
                            </td>
                            <td class="py-2 pr-4 text-slate-700">{{ $record->hemoglobin_level ?? '—' }}</td>
                            <td class="py-2 pr-4 text-slate-700">
                                {{ $record->systolic_bp ?? '—' }}/{{ $record->diastolic_bp ?? '—' }}
                            </td>
                            <td class="py-2 pr-4 text-slate-700">{{ $record->weight_kg ?? '—' }}</td>
                            <td class="py-2 pr-4 text-slate-700">{{ $record->source ?? '—' }}</td>
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
</div>

<script>
    const labels = @json($charts['labels'] ?? []);
    const hbSeries = @json($charts['hemoglobin'] ?? []);
    const sysSeries = @json($charts['systolic'] ?? []);
    const diaSeries = @json($charts['diastolic'] ?? []);

    const hbChart = new ApexCharts(document.querySelector("#hb-chart"), {
        chart: {
            type: 'line',
            height: 260,
            toolbar: { show: false },
            zoom: { enabled: false }
        },
        stroke: { curve: 'smooth', width: 3 },
        series: [{ name: 'Hemoglobin', data: hbSeries }],
        xaxis: { categories: labels },
        colors: ['#dc2626'],
        grid: { borderColor: '#e2e8f0' },
        markers: { size: 3 },
        dataLabels: { enabled: false }
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
        xaxis: { categories: labels },
        colors: ['#2563eb', '#0f766e'],
        grid: { borderColor: '#e2e8f0' },
        markers: { size: 3 },
        dataLabels: { enabled: false }
    });

    bpChart.render();
</script>
@endsection
