@extends('layouts.org-dashboard')

@section('title', 'আমাদের অ্যাম্বুলেন্স লিস্ট — রক্তদূত')

@section('content')
<div data-panel-id="ambulances">

    {{-- Info Banner --}}
    <div class="bg-blue-50 border border-blue-100 rounded-2xl p-5 mb-6 flex gap-4 items-start scroll-reveal" data-scroll-reveal>
        <div class="text-3xl">ℹ️</div>
        <div>
            <h4 class="font-black text-blue-900 mb-1">অটো-ভেরিফাইড ডিরেক্টরি</h4>
            <p class="text-sm text-blue-800 font-medium">
                যেহেতু আপনি একটি ভেরিফাইড অর্গানাইজেশন/হাসপাতাল, আপনার সাবমিট করা যেকোনো অ্যাম্বুলেন্স সাথে সাথে ভেরিফাইড হিসেবে পাবলিক ডিরেক্টরিতে প্রকাশিত হবে।
            </p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden scroll-reveal" data-scroll-reveal>
        <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
            <div>
                <h2 class="text-lg font-extrabold text-slate-900 flex items-center gap-2">🚑 অ্যাম্বুলেন্স লিস্ট</h2>
                <p class="text-xs text-slate-500 font-bold mt-1 uppercase tracking-tight">আপনার অর্গানাইজেশনের সকল অ্যাম্বুলেন্স</p>
            </div>
            <a href="{{ route('org.ambulances.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-xl font-bold text-sm shadow-sm transition">
                + নতুন যোগ করুন
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-slate-500 uppercase bg-slate-50 border-b border-slate-100 font-bold">
                    <tr>
                        <th scope="col" class="px-6 py-4">নাম ও ধরন</th>
                        <th scope="col" class="px-6 py-4">যোগাযোগ ও লোকেশন</th>
                        <th scope="col" class="px-6 py-4">স্ট্যাটাস</th>
                        <th scope="col" class="px-6 py-4 text-right">অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ambulances as $ambulance)
                        <tr class="bg-white border-b border-slate-50 hover:bg-slate-50/50 transition">
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900">{{ $ambulance->name }}</div>
                                <div class="text-xs font-medium text-slate-500 mt-1 uppercase tracking-wider">{{ $ambulance->type }}</div>
                                @if($ambulance->vehicle_number)
                                    <div class="text-xs font-medium text-slate-400 mt-1">Reg: {{ $ambulance->vehicle_number }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-700">{{ $ambulance->phone }}</div>
                                <div class="text-xs text-slate-500 mt-1 font-medium">
                                    {{ $ambulance->upazila?->name }}, {{ $ambulance->district?->name }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-bold border border-emerald-200 bg-emerald-50 text-emerald-700">
                                    ✅ ভেরিফাইড (অটো)
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <form action="{{ route('org.ambulances.destroy', $ambulance->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 font-bold text-xs" onclick="return confirm('আপনি কি নিশ্চিত? ডিলিট করলে পাবলিক ডিরেক্টরি থেকে এটি সরে যাবে।')">
                                        ডিলিট
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center">
                                <div class="text-slate-400 mb-2 text-4xl">🚑</div>
                                <div class="text-slate-500 font-medium text-sm">আপনার অর্গানাইজেশনের কোনো অ্যাম্বুলেন্স লিস্টেড নেই।</div>
                                <a href="{{ route('org.ambulances.create') }}" class="mt-4 inline-block text-indigo-600 font-bold hover:underline">নতুন যোগ করুন</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50">
            {{ $ambulances->links() }}
        </div>
    </div>

</div>
@endsection
