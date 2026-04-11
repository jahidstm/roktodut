@extends('layouts.app')

@section('title', 'নতুন ক্যাম্প তৈরি - রক্তদূত')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
    
    <div class="mb-8 flex items-center gap-3">
        <a href="{{ route('org.camps.index') }}" class="p-2 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition text-slate-500">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        </a>
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">নতুন ক্যাম্প তৈরি</h1>
            <p class="text-slate-500 font-medium">ক্লাবের রক্তদান ক্যাম্পের তথ্য রেকর্ড করুন।</p>
        </div>
    </div>

    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden p-6 md:p-8">
        <form action="{{ route('org.camps.store') }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <label for="name" class="block text-sm font-extrabold text-slate-900 mb-2">ক্যাম্পের নাম <span class="text-red-500">*</span></label>
                <input type="text" id="name" name="name" required placeholder="যেমন: বিজয় দিবস রক্তদান ক্যাম্প ২০২৬" class="w-full rounded-xl border-slate-200 focus:border-teal-500 focus:ring-teal-500 bg-slate-50 text-slate-900 px-4 py-3 font-medium">
                @error('name') <p class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="camp_date" class="block text-sm font-extrabold text-slate-900 mb-2">তারিখ <span class="text-red-500">*</span></label>
                    <input type="date" id="camp_date" name="camp_date" required class="w-full rounded-xl border-slate-200 focus:border-teal-500 focus:ring-teal-500 bg-slate-50 text-slate-900 px-4 py-3 font-medium">
                    @error('camp_date') <p class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</p> @enderror
                </div>
                
                <div>
                    <label for="location" class="block text-sm font-extrabold text-slate-900 mb-2">স্থান/লোকেশন <span class="text-red-500">*</span></label>
                    <input type="text" id="location" name="location" required placeholder="যেমন: ঢাকা বিশ্ববিদ্যালয় টিএসসি" class="w-full rounded-xl border-slate-200 focus:border-teal-500 focus:ring-teal-500 bg-slate-50 text-slate-900 px-4 py-3 font-medium">
                    @error('location') <p class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label for="notes" class="block text-sm font-extrabold text-slate-900 mb-2">অতিরিক্ত নোট (ঐচ্ছিক)</label>
                <textarea id="notes" name="notes" rows="3" placeholder="ক্যাম্প সম্পর্কে কোনো বিশেষ তথ্য..." class="w-full rounded-xl border-slate-200 focus:border-teal-500 focus:ring-teal-500 bg-slate-50 text-slate-900 px-4 py-3 font-medium"></textarea>
                @error('notes') <p class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</p> @enderror
            </div>

            <div class="pt-4 border-t border-slate-100 flex justify-end">
                <button type="submit" class="px-8 py-3.5 bg-teal-600 hover:bg-teal-700 text-white rounded-xl text-sm font-black shadow-sm transition-all focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 w-full md:w-auto">
                    ক্যাম্প সংরক্ষণ করুন
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
