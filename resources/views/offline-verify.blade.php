@extends('layouts.app')

@section('title', 'অফলাইন রক্তদান যাচাই — রক্তদূত')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="rounded-3xl border border-red-100 bg-white shadow-sm overflow-hidden">
        <div class="bg-gradient-to-r from-red-600 to-rose-600 px-6 py-5">
            <h1 class="text-xl sm:text-2xl font-black text-white">রক্তদান যাচাই করুন</h1>
            <p class="text-red-100 text-sm font-medium mt-1">আপনার একটি নিশ্চিতকরণ ডোনারের অবদানকে বৈধতা দেবে।</p>
        </div>

        <div class="p-6 sm:p-8">
            @if($result === 'approved')
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5 text-emerald-800 font-semibold">
                    ✅ ধন্যবাদ! রক্তদান সফলভাবে যাচাই করা হয়েছে।
                </div>
            @elseif($result === 'rejected')
                <div class="rounded-2xl border border-red-200 bg-red-50 p-5 text-red-800 font-semibold">
                    ⚠️ ধন্যবাদ। ক্লেইমটি বাতিল করা হয়েছে এবং সিস্টেমে রিপোর্ট করা হয়েছে।
                </div>
            @elseif($claim->status !== 'pending')
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5 text-slate-700 font-semibold">
                    এই ক্লেইমটি ইতোমধ্যে {{ $claim->status }} অবস্থায় আছে।
                </div>
            @else
                <p class="text-lg sm:text-xl font-black text-slate-900 leading-relaxed">
                    Did <span class="text-red-600">{{ $claim->donor?->name ?? 'এই ডোনার' }}</span> donate blood to you on
                    <span class="text-slate-800">{{ $claim->donation_date?->format('d M, Y') }}</span>?
                </p>

                <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <form method="POST" action="{{ $confirmUrl }}">
                        @csrf
                        <input type="hidden" name="decision" value="yes">
                        <button type="submit" class="w-full inline-flex items-center justify-center rounded-xl bg-emerald-600 px-5 py-3 text-sm font-black text-white shadow-sm hover:bg-emerald-700 transition">
                            Yes
                        </button>
                    </form>
                    <form method="POST" action="{{ $confirmUrl }}">
                        @csrf
                        <input type="hidden" name="decision" value="no">
                        <button type="submit" class="w-full inline-flex items-center justify-center rounded-xl border border-red-200 bg-white px-5 py-3 text-sm font-black text-red-600 hover:bg-red-50 transition">
                            No
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
