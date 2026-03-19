@extends('layouts.app')

@section('title', 'সিস্টেম অ্যাডমিন ড্যাশবোর্ড — রক্তদূত')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="mb-8">
        <h1 class="text-2xl font-extrabold text-slate-900">অ্যাডমিন ওভারভিউ</h1>
        <p class="text-slate-500 font-medium mt-1">পুরো সিস্টেমের রিয়েল-টাইম ডেটা অ্যানালিটিক্স এবং ট্রেন্ড।</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
            <div class="text-slate-500 text-sm font-bold uppercase tracking-wider">মোট ইউজার</div>
            <div class="mt-2 text-4xl font-black text-slate-900">{{ $totalUsers }}</div>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
            <div class="text-blue-600 text-sm font-bold uppercase tracking-wider">মোট ডোনার</div>
            <div class="mt-2 text-4xl font-black text-blue-600">{{ $totalDonors }}</div>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
            <div class="text-red-600 text-sm font-bold uppercase tracking-wider">সফল রিকোয়েস্ট</div>
            <div class="mt-2 text-4xl font-black text-red-600">{{ $fulfilledRequests }} / {{ $totalRequests }}</div>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
            <div class="text-emerald-600 text-sm font-bold uppercase tracking-wider">সাকসেস রেট</div>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-4xl font-black text-emerald-600">{{ $successRate }}</span>
                <span class="text-emerald-400 font-bold text-sm">%</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-6 bg-slate-50 rounded-2xl border-2 border-dashed border-slate-300">
        <div>
            <h3 class="font-extrabold text-slate-800 mb-2">@Alif: ব্লাড গ্রুপ ডিমান্ড (Pie Chart)</h3>
            <p class="text-sm text-slate-500 mb-4">Chart.js ব্যবহার করে নিচের JSON ডেটা দিয়ে পাই-চার্ট রেন্ডার করো।</p>
            <pre class="bg-slate-900 text-emerald-400 p-4 rounded-xl text-xs overflow-auto">
const bloodGroupData = @json($bloodGroupDemand);
            </pre>
        </div>
        <div>
            <h3 class="font-extrabold text-slate-800 mb-2">@Alif: ইমার্জেন্সি জোন (Bar Chart)</h3>
            <p class="text-sm text-slate-500 mb-4">টপ ৫ জেলার ডেটা দিয়ে একটি বার-চার্ট তৈরি করো।</p>
            <pre class="bg-slate-900 text-blue-400 p-4 rounded-xl text-xs overflow-auto">
const districtData = @json($districtDemand);
            </pre>
        </div>
    </div>
</div>
@endsection