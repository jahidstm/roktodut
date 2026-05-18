@extends('layouts.app')
@section('title', 'Server Error - RoktoDut')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center px-4">
    <div class="text-center max-w-md mx-auto">
        <div class="mb-8 flex justify-center">
            <div class="h-24 w-24 rounded-full bg-orange-50 flex items-center justify-center">
                <svg class="h-12 w-12 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>
        
        <h1 class="text-9xl font-black text-slate-200">500</h1>
        
        <h2 class="mt-4 text-2xl font-bold text-slate-900 tracking-tight sm:text-3xl">
            সার্ভারে ত্রুটি হয়েছে
        </h2>
        
        <p class="mt-4 text-base text-slate-600">
            আমাদের সার্ভারে অপ্রত্যাশিত কোনো সমস্যা হয়েছে। আমরা সমস্যাটি সমাধানের চেষ্টা করছি। কিছুক্ষণ পর আবার চেষ্টা করুন।
        </p>
        
        <div class="mt-8 flex justify-center gap-4">
            <a href="javascript:location.reload()" class="inline-flex items-center justify-center rounded-xl bg-red-600 px-6 py-3 text-sm font-bold text-white transition-all hover:bg-red-700 hover:shadow-lg hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                রিফ্রেশ করুন
            </a>
            
            <a href="{{ route('home') }}" class="inline-flex items-center justify-center rounded-xl bg-white border border-slate-300 px-6 py-3 text-sm font-bold text-slate-700 transition-all hover:bg-slate-50 hover:border-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2">
                হোমপেজে ফিরে যান
            </a>
        </div>
    </div>
</div>
@endsection
