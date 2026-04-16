@extends('layouts.app')

@section('title', 'অ্যানালিটিক্স ড্যাশবোর্ড — রক্তদূত অ্যাডমিন')

@push('head')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

@section('content')
@php
    $summary = $analytics['summary'] ?? [];
    $bloodGroupData = $analytics['blood_group_distribution'] ?? ['labels' => [], 'values' => []];
    $monthlyTrend = $analytics['monthly_success_trend'] ?? ['labels' => [], 'values' => []];

    $bn = fn ($value) => \App\Support\BanglaDate::digits((string) ($value ?? 0));
@endphp

<div class="max-w-7xl mx-auto px-4 py-8"
     x-data="analyticsDashboard(@js($bloodGroupData), @js($monthlyTrend))"
     x-init="initCharts()">

    <div class="mb-8 flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900">অ্যানালিটিক্স ড্যাশবোর্ড</h1>
            <p class="mt-2 text-slate-500 font-medium">রক্তদূতের রিয়েল ডেটা ট্রেন্ড, ডোনার প্রোফাইল এবং রিকোয়েস্ট পারফরম্যান্স দেখুন।</p>
        </div>
        <a href="{{ route('admin.analytics.export') }}"
           class="inline-flex items-center justify-center gap-2 bg-red-600 hover:bg-red-700 text-white font-extrabold px-5 py-3 rounded-xl shadow-sm shadow-red-200 transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-red-500 focus-visible:ring-offset-2">
            <span>⬇</span>
            রিপোর্ট ডাউনলোড করুন (CSV)
        </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-8">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-extrabold uppercase tracking-wider text-slate-500">মোট ডোনার</p>
            <p class="mt-2 text-3xl font-black text-slate-900">{{ $bn($summary['total_donors'] ?? 0) }}</p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-extrabold uppercase tracking-wider text-slate-500">মোট রিসিপিয়েন্ট</p>
            <p class="mt-2 text-3xl font-black text-slate-900">{{ $bn($summary['total_recipients'] ?? 0) }}</p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-extrabold uppercase tracking-wider text-slate-500">চলমান রিকোয়েস্ট</p>
            <p class="mt-2 text-3xl font-black text-red-600">{{ $bn($summary['active_requests'] ?? 0) }}</p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-extrabold uppercase tracking-wider text-slate-500">আনুমানিক বাঁচানো জীবন</p>
            <p class="mt-2 text-3xl font-black text-emerald-600">{{ $bn($summary['lives_saved_estimate'] ?? 0) }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-extrabold text-slate-900">ডোনারের রক্তের গ্রুপ বণ্টন</h2>
            <p class="text-sm text-slate-500 font-medium mt-1">বর্তমান রেজিস্টার্ড ডোনার ডেটা ভিত্তিক</p>
            <div class="mt-5 h-[320px]">
                <canvas x-ref="bloodGroupChart"></canvas>
            </div>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-extrabold text-slate-900">মাসভিত্তিক সফল রিকোয়েস্ট ট্রেন্ড</h2>
            <p class="text-sm text-slate-500 font-medium mt-1">শেষ ১২ মাসের ভেরিফায়েড সফল রিকোয়েস্ট</p>
            <div class="mt-5 h-[320px]">
                <canvas x-ref="monthlyTrendChart"></canvas>
            </div>
        </div>
    </div>

    <div class="mt-6">
        <a href="{{ route('admin.dashboard') }}"
           class="inline-flex items-center gap-2 text-sm font-bold text-slate-600 hover:text-red-600 transition">
            ← অ্যাডমিন ড্যাশবোর্ডে ফিরে যান
        </a>
    </div>
</div>
@endsection

@push('scripts')
<script>
function analyticsDashboard(bloodGroupData, monthlyTrendData) {
    return {
        bloodGroupData,
        monthlyTrendData,
        bloodGroupChartInstance: null,
        monthlyTrendChartInstance: null,

        initCharts() {
            if (typeof Chart === 'undefined') return;
            this.renderBloodGroupChart();
            this.renderMonthlyTrendChart();
        },

        renderBloodGroupChart() {
            if (!this.$refs.bloodGroupChart) return;

            this.bloodGroupChartInstance = new Chart(this.$refs.bloodGroupChart, {
                type: 'doughnut',
                data: {
                    labels: this.bloodGroupData.labels || [],
                    datasets: [{
                        data: this.bloodGroupData.values || [],
                        backgroundColor: [
                            '#ef4444', '#f97316', '#eab308', '#22c55e',
                            '#06b6d4', '#3b82f6', '#6366f1', '#a855f7'
                        ],
                        borderColor: '#ffffff',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                boxWidth: 10,
                                font: { family: 'Hind Siliguri' }
                            }
                        }
                    }
                }
            });
        },

        renderMonthlyTrendChart() {
            if (!this.$refs.monthlyTrendChart) return;

            this.monthlyTrendChartInstance = new Chart(this.$refs.monthlyTrendChart, {
                type: 'bar',
                data: {
                    labels: this.monthlyTrendData.labels || [],
                    datasets: [{
                        label: 'সফল রিকোয়েস্ট',
                        data: this.monthlyTrendData.values || [],
                        backgroundColor: '#dc2626',
                        borderRadius: 10,
                        maxBarThickness: 36
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0,
                                font: { family: 'Hind Siliguri' }
                            },
                            grid: { color: '#f1f5f9' }
                        },
                        x: {
                            ticks: { font: { family: 'Hind Siliguri' } },
                            grid: { display: false }
                        }
                    },
                    plugins: {
                        legend: {
                            labels: { font: { family: 'Hind Siliguri' } }
                        }
                    }
                }
            });
        }
    };
}
</script>
@endpush
