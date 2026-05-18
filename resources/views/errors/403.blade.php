@extends('layouts.app')
@section('title', 'Access Denied - RoktoDut')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center px-4">
    <div class="text-center max-w-md mx-auto">
        <div class="mb-8 flex justify-center">
            <div class="h-24 w-24 rounded-full bg-red-50 flex items-center justify-center">
                <svg class="h-12 w-12 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
        </div>
        
        <h1 class="text-9xl font-black text-slate-200">403</h1>
        
        <h2 class="mt-4 text-2xl font-bold text-slate-900 tracking-tight sm:text-3xl">
            প্রবেশাধিকার সংরক্ষিত
        </h2>
        
        <p class="mt-4 text-base text-slate-600">
            দুঃখিত, এই পেজটি দেখার বা এই কাজটি করার অনুমতি আপনার নেই। আপনার যদি মনে হয় এটি একটি ভুল, তবে সাপোর্টে যোগাযোগ করুন।
        </p>
        
        <div class="mt-8 flex justify-center gap-4">
            <a href="{{ route('home') }}" class="inline-flex items-center justify-center rounded-xl bg-red-600 px-6 py-3 text-sm font-bold text-white transition-all hover:bg-red-700 hover:shadow-lg hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                হোমপেজে ফিরে যান
            </a>
            
            <a href="javascript:history.back()" class="inline-flex items-center justify-center rounded-xl bg-white border border-slate-300 px-6 py-3 text-sm font-bold text-slate-700 transition-all hover:bg-slate-50 hover:border-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2">
                আগের পেজে যান
            </a>
        </div>
    </div>
</div>
@endsection
