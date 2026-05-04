@extends('layouts.app')

@section('title', 'হাসপাতালের নাম যাচাই — অ্যাডমিন')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">

    {{-- Header --}}
    <div class="mb-8 border-b border-slate-200 pb-5 flex items-center justify-between gap-4 flex-wrap">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <a href="{{ route('admin.dashboard') }}"
                   class="text-slate-400 hover:text-slate-700 transition text-sm font-bold flex items-center gap-1">
                    ← অ্যাডমিন ড্যাশবোর্ড
                </a>
            </div>
            <h1 class="text-3xl font-extrabold text-slate-900 flex items-center gap-3">
                🏗️ হাসপাতালের নাম যাচাই
            </h1>
            <p class="text-slate-500 font-medium mt-1">
                ইউজারের টাইপ করা আনভেরিফাইড নামগুলো Merge, Approve বা Reject করুন।
            </p>
        </div>
        <div class="flex items-center gap-3">
            <span class="bg-amber-100 text-amber-700 font-extrabold text-sm px-4 py-2 rounded-xl border border-amber-200">
                🟡 {{ $hospitals->total() }} টি পেন্ডিং
            </span>
        </div>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl font-bold flex items-center gap-2">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl font-bold flex items-center gap-2">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('error') }}
        </div>
    @endif

    {{-- Legend --}}
    <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 flex gap-3">
            <span class="text-2xl shrink-0">🔀</span>
            <div>
                <p class="font-extrabold text-blue-800 text-sm">Merge with Existing</p>
                <p class="text-xs text-blue-600 font-medium mt-0.5">টাইপো হলে অরিজিনালের সাথে মার্জ করুন। রিকোয়েস্টগুলো অটো-মাইগ্রেট হবে। টাইপোটি aliases-এ সেভ হয়ে সার্চ আরও স্মার্ট হবে।</p>
            </div>
        </div>
        <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 flex gap-3">
            <span class="text-2xl shrink-0">✅</span>
            <div>
                <p class="font-extrabold text-emerald-800 text-sm">Approve as New</p>
                <p class="text-xs text-emerald-600 font-medium mt-0.5">সত্যিকারের নতুন ক্লিনিক বা হসপিটাল হলে স্ট্যান্ডার্ড নাম দিয়ে ভেরিফাই করুন।</p>
            </div>
        </div>
        <div class="bg-red-50 border border-red-200 rounded-xl p-4 flex gap-3">
            <span class="text-2xl shrink-0">🗑️</span>
            <div>
                <p class="font-extrabold text-red-800 text-sm">Reject & Nullify</p>
                <p class="text-xs text-red-600 font-medium mt-0.5">স্প্যাম বা অশালীন নাম হলে নিরাপদে ডিলিট করুন। সংশ্লিষ্ট রিকোয়েস্টগুলো অক্ষত থাকবে।</p>
            </div>
        </div>
    </div>

    {{-- Empty state --}}
    @if($hospitals->isEmpty())
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-16 text-center">
            <div class="w-16 h-16 bg-emerald-50 rounded-full flex items-center justify-center mx-auto mb-4 text-3xl">✅</div>
            <h3 class="text-xl font-extrabold text-slate-900">সব ক্লিয়ার!</h3>
            <p class="text-slate-500 font-medium mt-2">কোনো আনভেরিফাইড হাসপাতালের নাম পেন্ডিং নেই।</p>
        </div>
    @else

    {{-- Table --}}
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/80">
                        <th class="text-left px-6 py-4 font-extrabold text-slate-700 text-xs uppercase tracking-wider">#</th>
                        <th class="text-left px-6 py-4 font-extrabold text-slate-700 text-xs uppercase tracking-wider">ইউজারের ইনপুট (অযাচাইকৃত)</th>
                        <th class="text-center px-6 py-4 font-extrabold text-slate-700 text-xs uppercase tracking-wider">রিকোয়েস্ট</th>
                        <th class="text-left px-6 py-4 font-extrabold text-slate-700 text-xs uppercase tracking-wider">যোগ হয়েছে</th>
                        <th class="text-right px-6 py-4 font-extrabold text-slate-700 text-xs uppercase tracking-wider">অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($hospitals as $h)
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <td class="px-6 py-4 text-slate-400 font-bold text-xs">{{ $h->id }}</td>

                        {{-- Hospital name --}}
                        <td class="px-6 py-4">
                            <div class="font-extrabold text-slate-900">{{ $h->name }}</div>
                            @if($h->district)
                                <div class="text-xs text-slate-400 font-medium mt-0.5">📍 {{ $h->district->name }}</div>
                            @endif
                        </td>

                        {{-- Request count --}}
                        <td class="px-6 py-4 text-center">
                            @if($h->blood_requests_count > 0)
                                <span class="bg-red-100 text-red-700 font-extrabold text-xs px-2.5 py-1 rounded-full">
                                    {{ $h->blood_requests_count }} রিকোয়েস্ট
                                </span>
                            @else
                                <span class="text-slate-300 font-bold text-xs">শূন্য</span>
                            @endif
                        </td>

                        {{-- Created at --}}
                        <td class="px-6 py-4 text-xs text-slate-500 font-semibold whitespace-nowrap">
                            {{ $h->created_at->diffForHumans() }}
                        </td>

                        {{-- Actions --}}
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-2 flex-wrap" x-data="{ openModal: null }">

                                {{-- ── FLOW 1: Merge --}}
                                <button @click="openModal = 'merge-{{ $h->id }}'"
                                        class="inline-flex items-center gap-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-extrabold px-3 py-2 rounded-lg transition">
                                    🔀 Merge
                                </button>

                                {{-- ── FLOW 2: Approve --}}
                                <button @click="openModal = 'approve-{{ $h->id }}'"
                                        class="inline-flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-extrabold px-3 py-2 rounded-lg transition">
                                    ✅ Approve
                                </button>

                                {{-- ── FLOW 3: Reject --}}
                                <button @click="openModal = 'reject-{{ $h->id }}'"
                                        class="inline-flex items-center gap-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-extrabold px-3 py-2 rounded-lg transition">
                                    🗑️ Reject
                                </button>

                                {{-- ══════════════════════════════════════════════
                                     MODAL: Merge with Existing
                                ══════════════════════════════════════════════ --}}
                                <div x-show="openModal === 'merge-{{ $h->id }}'"
                                     x-transition
                                     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
                                     style="display:none;"
                                     @click.self="openModal = null">
                                    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6" @click.stop>
                                        <div class="flex items-center justify-between mb-5">
                                            <h3 class="font-extrabold text-slate-900 text-lg flex items-center gap-2">
                                                🔀 Merge করুন
                                            </h3>
                                            <button @click="openModal = null" class="text-slate-400 hover:text-slate-700">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                            </button>
                                        </div>

                                        <div class="bg-amber-50 border border-amber-200 rounded-xl p-3 mb-4">
                                            <p class="text-xs font-bold text-amber-700">আনভেরিফাইড নাম:</p>
                                            <p class="font-extrabold text-amber-900 mt-0.5">{{ $h->name }}</p>
                                        </div>

                                        <form action="{{ route('admin.hospitals.merge', $h) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <label class="block text-sm font-extrabold text-slate-700 mb-2">
                                                অরিজিনাল হাসপাতাল নির্বাচন করুন <span class="text-red-500">*</span>
                                            </label>
                                            <select name="target_hospital_id"
                                                    required
                                                    class="w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 font-semibold text-sm mb-4">
                                                <option value="">— সিলেক্ট করুন —</option>
                                                @foreach($verified as $v)
                                                    <option value="{{ $v->id }}">{{ $v->name }}{{ $v->name_bn ? ' / ' . $v->name_bn : '' }}</option>
                                                @endforeach
                                            </select>
                                            <p class="text-xs text-slate-500 font-medium mb-4">
                                                মার্জ হলে সমস্ত রিকোয়েস্ট অরিজিনালে মাইগ্রেট হবে এবং "{{ $h->name }}" ওই হসপিটালের aliases-এ যুক্ত হবে।
                                            </p>
                                            <div class="flex gap-3">
                                                <button type="submit"
                                                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-extrabold py-2.5 rounded-xl text-sm transition">
                                                    🔀 মার্জ কনফার্ম করুন
                                                </button>
                                                <button type="button" @click="openModal = null"
                                                        class="px-5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold py-2.5 rounded-xl text-sm transition">
                                                    বাতিল
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                {{-- ══════════════════════════════════════════════
                                     MODAL: Approve as New
                                ══════════════════════════════════════════════ --}}
                                <div x-show="openModal === 'approve-{{ $h->id }}'"
                                     x-transition
                                     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
                                     style="display:none;"
                                     @click.self="openModal = null">
                                    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6" @click.stop>
                                        <div class="flex items-center justify-between mb-5">
                                            <h3 class="font-extrabold text-slate-900 text-lg">✅ নতুন হিসেবে Approve</h3>
                                            <button @click="openModal = null" class="text-slate-400 hover:text-slate-700">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                            </button>
                                        </div>

                                        <form action="{{ route('admin.hospitals.verify', $h) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <div class="space-y-4 mb-4">
                                                <div>
                                                    <label class="block text-sm font-extrabold text-slate-700 mb-1.5">
                                                        স্ট্যান্ডার্ড ইংরেজি নাম <span class="text-red-500">*</span>
                                                    </label>
                                                    <input type="text" name="name" required
                                                           value="{{ $h->name }}"
                                                           class="w-full rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 font-semibold text-sm">
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-extrabold text-slate-700 mb-1.5">
                                                        বাংলা নাম <span class="text-slate-400 font-medium">(ঐচ্ছিক)</span>
                                                    </label>
                                                    <input type="text" name="name_bn"
                                                           value="{{ $h->name_bn }}"
                                                           placeholder="যেমন: ঢাকা মেডিকেল কলেজ হাসপাতাল"
                                                           class="w-full rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 font-semibold text-sm">
                                                </div>
                                            </div>
                                            <div class="flex gap-3">
                                                <button type="submit"
                                                        class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold py-2.5 rounded-xl text-sm transition">
                                                    ✅ ভেরিফাই কনফার্ম
                                                </button>
                                                <button type="button" @click="openModal = null"
                                                        class="px-5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold py-2.5 rounded-xl text-sm transition">
                                                    বাতিল
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                {{-- ══════════════════════════════════════════════
                                     MODAL: Reject & Nullify
                                ══════════════════════════════════════════════ --}}
                                <div x-show="openModal === 'reject-{{ $h->id }}'"
                                     x-transition
                                     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
                                     style="display:none;"
                                     @click.self="openModal = null">
                                    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6" @click.stop>
                                        <div class="flex items-center justify-between mb-5">
                                            <h3 class="font-extrabold text-red-700 text-lg">🗑️ Reject & Nullify</h3>
                                            <button @click="openModal = null" class="text-slate-400 hover:text-slate-700">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                            </button>
                                        </div>

                                        <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-5">
                                            <p class="text-sm font-bold text-red-700">মুছতে যাওয়া নাম:</p>
                                            <p class="font-extrabold text-red-900 mt-1">{{ $h->name }}</p>
                                            @if($h->blood_requests_count > 0)
                                                <p class="text-xs text-red-600 font-semibold mt-2 bg-red-100 p-2 rounded-lg">
                                                    ⚠️ {{ $h->blood_requests_count }}টি রিকোয়েস্টের hospital_id null হবে। রিকোয়েস্টগুলো ডিলিট হবে না।
                                                </p>
                                            @else
                                                <p class="text-xs text-slate-500 font-medium mt-2">এই নামে কোনো রিকোয়েস্ট নেই।</p>
                                            @endif
                                        </div>

                                        <form action="{{ route('admin.hospitals.destroy', $h) }}" method="POST">
                                            @csrf @method('DELETE')
                                            <div class="flex gap-3">
                                                <button type="submit"
                                                        class="flex-1 bg-red-600 hover:bg-red-700 text-white font-extrabold py-2.5 rounded-xl text-sm transition">
                                                    🗑️ হ্যাঁ, Reject করুন
                                                </button>
                                                <button type="button" @click="openModal = null"
                                                        class="px-5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold py-2.5 rounded-xl text-sm transition">
                                                    বাতিল
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                            </div>{{-- /x-data --}}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($hospitals->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $hospitals->links() }}
            </div>
        @endif
    </div>

    @endif

</div>
@endsection
