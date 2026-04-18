@extends('layouts.app')

@section('title', 'ক্যাম্প আপডেট - রক্তদূত')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
    <div class="mb-8 flex items-center gap-3">
        <a href="{{ route('org.camps.index') }}" class="p-2 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition text-slate-500">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        </a>
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">ক্যাম্প সম্পাদনা</h1>
            <p class="text-slate-500 font-medium">{{ $camp->name }} — প্রয়োজন অনুযায়ী তথ্য আপডেট করুন।</p>
        </div>
    </div>

    @include('org.camps.partials.form', [
        'formAction' => route('org.camps.update', $camp->id),
        'method' => 'PUT',
        'camp' => $camp,
        'districts' => $districts,
    ])
</div>
@endsection
