@extends('layouts.app')

@section('title', 'NID ভেরিফিকেশন রিভিউ কিউ | রক্তদূত অ্যাডমিন')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 flex items-center gap-2">
                <span class="w-9 h-9 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center text-lg">🪪</span>
                NID ভেরিফিকেশন রিভিউ
            </h1>
            <p class="text-slate-500 text-sm font-semibold mt-1">সিস্টেমের যেকোনো ডোনারের NID approve/reject করুন</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="inline-flex items-center gap-1.5 bg-blue-600 text-white text-sm font-black px-4 py-2 rounded-full shadow-sm">
                {{ $nidStats['total_pending'] }} টি পেন্ডিং
            </span>
            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-2 text-sm font-bold text-slate-600 hover:text-slate-900 transition">
                ← অ্যাডমিন ড্যাশবোর্ড
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
        <div class="bg-white border border-slate-200 rounded-xl p-4 text-center shadow-sm">
            <div class="text-xl mb-1">⏳</div>
            <div class="text-2xl font-extrabold text-slate-900">{{ $nidStats['total_pending'] }}</div>
            <div class="text-xs text-slate-500 font-semibold mt-0.5">মোট পেন্ডিং</div>
        </div>
        <div class="bg-white border border-slate-200 rounded-xl p-4 text-center shadow-sm">
            <div class="text-xl mb-1">✅</div>
            <div class="text-2xl font-extrabold text-slate-900">{{ $nidStats['approved'] }}</div>
            <div class="text-xs text-slate-500 font-semibold mt-0.5">মোট ভেরিফাইড</div>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 bg-emerald-50 border border-emerald-200 rounded-xl p-4 text-emerald-700 font-semibold text-sm flex items-center gap-2">
            ✅ {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 rounded-xl p-4 text-red-700 font-semibold text-sm flex items-center gap-2">
            ⚠️ {{ session('error') }}
        </div>
    @endif

    <div class="py-6 pb-16">
    <div class="mb-5 text-sm font-semibold text-slate-500 bg-blue-50 border border-blue-100 p-3 rounded-xl border-l-4 border-l-blue-500">
        ℹ️ সিস্টেম অ্যাডমিন হিসেবে আপনি যেকোনো সাধারণ ডোনার বা অর্গানাইজেশন সদস্যের NID অ্যাপ্রুভ বা রিজেক্ট করতে পারবেন।
    </div>

    @if($pendingNids->isEmpty())
        <div class="bg-white rounded-3xl border border-slate-200 p-12 text-center flex flex-col items-center">
            <div class="w-16 h-16 bg-blue-50 rounded-full flex items-center justify-center text-blue-300 mb-4 text-3xl">✅</div>
            <h3 class="text-xl font-extrabold text-slate-800">কোনো পেন্ডিং NID নেই</h3>
            <p class="font-medium text-slate-500 mt-2">সকল ডোনার ভেরিফাই করা হয়েছে।</p>
        </div>
    @else
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-xs font-extrabold text-slate-500 uppercase tracking-wider">
                            <th class="text-left px-6 py-4">ডোনার</th>
                            <th class="text-left px-6 py-4">জেলা</th>
                            <th class="text-center px-6 py-4">ডকুমেন্ট</th>
                            <th class="text-center px-6 py-4">অ্যাকশন</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($pendingNids as $donor)
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center font-black text-sm shrink-0">
                                            {{ mb_substr($donor->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="font-bold text-slate-900">{{ $donor->name }}</p>
                                            <p class="text-xs text-slate-400 font-medium">{{ $donor->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600 font-semibold">
                                    {{ $donor->district?->name ?? '—' }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <a href="{{ \Illuminate\Support\Facades\URL::temporarySignedRoute('admin.nid.image', now()->addMinutes(2), ['user' => $donor->id]) }}"
                                       target="_blank"
                                       class="inline-flex items-center justify-center gap-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold px-3 py-1.5 rounded-lg transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        ডকুমেন্ট দেখুন
                                    </a>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <form action="{{ route('admin.nid.verify', $donor) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="decision" value="approve">
                                            <button type="submit"
                                                    class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-extrabold px-4 py-2 rounded-xl transition shadow-sm">
                                                ✅ অ্যাপ্রুভ
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.nid.verify', $donor) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="decision" value="reject">
                                            <button type="submit"
                                                    onclick="return confirm('{{ $donor->name }}-এর NID বাতিল করবেন?')"
                                                    class="bg-white border text-slate-600 hover:bg-red-50 hover:text-red-600 hover:border-red-200 text-xs font-extrabold px-4 py-2 rounded-xl transition">
                                                ❌ বাতিল
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        @if($pendingNids->hasPages())
            <div class="mt-8">
                {{ $pendingNids->links() }}
            </div>
        @endif
    @endif
    </div>
</div>
@endsection
