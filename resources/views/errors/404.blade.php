@extends('layouts.app')
@section('title', 'Page Not Found - RoktoDut')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center px-4">
    <div class="text-center max-w-md mx-auto">
        <div class="mb-8 flex justify-center">
            <div class="h-24 w-24 rounded-full bg-slate-100 flex items-center justify-center">
                <svg class="h-12 w-12 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
        </div>
        
        <h1 class="text-9xl font-black text-slate-200">404</h1>
        
        <h2 class="mt-4 text-2xl font-bold text-slate-900 tracking-tight sm:text-3xl">
            পেজটি খুঁজে পাওয়া যায়নি
        </h2>
        
        <p class="mt-4 text-base text-slate-600">
            আপনি যে পেজটি খুঁজছেন তা মুছে ফেলা হয়েছে অথবা লিংকটি ভুল। অনুগ্রহ করে সঠিক লিংক চেক করুন অথবা হোমপেজে ফিরে যান।
        </p>
        
        <div class="mt-8 flex justify-center gap-4">
            <a href="{{ route('home') }}" class="inline-flex items-center justify-center rounded-xl bg-red-600 px-6 py-3 text-sm font-bold text-white transition-all hover:bg-red-700 hover:shadow-lg hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                হোমপেজে ফিরে যান
            </a>
        </div>
    </div>
</div>
@endsection
