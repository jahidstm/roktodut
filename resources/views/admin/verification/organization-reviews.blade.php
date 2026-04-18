@extends('layouts.app')

@section('title', 'অর্গানাইজেশন/হাসপাতাল যাচাই কিউ | রক্তদূত অ্যাডমিন')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 flex items-center gap-2">
                <span class="w-9 h-9 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center text-lg">🏥</span>
                অর্গানাইজেশন/হাসপাতাল যাচাই
            </h1>
            <p class="text-slate-500 text-sm font-semibold mt-1">অফিশিয়াল ডকুমেন্ট দেখে organization approve/reject করুন</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="inline-flex items-center gap-1.5 bg-indigo-600 text-white text-sm font-black px-4 py-2 rounded-full shadow-sm">
                {{ $orgStats['total_pending'] }} টি পেন্ডিং
            </span>
            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-2 text-sm font-bold text-slate-600 hover:text-slate-900 transition">
                ← অ্যাডমিন ড্যাশবোর্ড
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-white border border-slate-200 rounded-xl p-4 text-center shadow-sm">
            <div class="text-xl mb-1">⏳</div>
            <div class="text-2xl font-extrabold text-slate-900">{{ $orgStats['total_pending'] }}</div>
            <div class="text-xs text-slate-500 font-semibold mt-0.5">মোট পেন্ডিং</div>
        </div>
        <div class="bg-white border border-slate-200 rounded-xl p-4 text-center shadow-sm">
            <div class="text-xl mb-1">✅</div>
            <div class="text-2xl font-extrabold text-slate-900">{{ $orgStats['approved'] }}</div>
            <div class="text-xs text-slate-500 font-semibold mt-0.5">মোট অ্যাপ্রুভড</div>
        </div>
        <div class="bg-white border border-slate-200 rounded-xl p-4 text-center shadow-sm">
            <div class="text-xl mb-1">❌</div>
            <div class="text-2xl font-extrabold text-slate-900">{{ $orgStats['rejected'] }}</div>
            <div class="text-xs text-slate-500 font-semibold mt-0.5">মোট বাতিল</div>
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

    <div class="py-6 pb-16" x-data="{ rejectModalOpen: false, currentOrgId: null, currentOrgName: '' }">
    @if($pendingOrgs->isEmpty())
        <div class="bg-white rounded-3xl border border-slate-200 p-12 text-center flex flex-col items-center">
            <div class="w-16 h-16 bg-indigo-50 rounded-full flex items-center justify-center text-indigo-300 mb-4 text-3xl">🎉</div>
            <h3 class="text-xl font-extrabold text-slate-800">কোনো পেন্ডিং অর্গানাইজেশন নেই</h3>
            <p class="font-medium text-slate-500 mt-2">সকল অ্যাপলিকেশন প্রসেসড।</p>
        </div>
    @else
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-xs font-extrabold text-slate-500 uppercase tracking-wider">
                            <th class="text-left px-6 py-4">নাম ও ধরন</th>
                            <th class="text-left px-6 py-4">ঠিকানা</th>
                            <th class="text-left px-6 py-4">যোগাযোগ</th>
                            <th class="text-center px-6 py-4">অফিশিয়াল ডকুমেন্ট</th>
                            <th class="text-center px-6 py-4">অ্যাকশন</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($pendingOrgs as $org)
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        @if($org->logo)
                                            <img src="{{ asset('storage/' . $org->logo) }}" alt="{{ $org->name }}" class="w-10 h-10 rounded-full object-cover shadow-sm bg-white p-0.5 border border-slate-200">
                                        @else
                                            <div class="w-10 h-10 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center font-black text-sm border border-indigo-100 shrink-0">
                                                {{ mb_substr($org->name, 0, 1) }}
                                            </div>
                                        @endif
                                        <div>
                                            <p class="font-bold text-slate-900">{{ $org->name }}</p>
                                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 border border-slate-200">
                                                {{ ucfirst($org->type) }}
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm font-medium text-slate-600">
                                    {{ $org->address }}, {{ $org->locationUpazila?->name ?? 'N/A' }}, {{ $org->locationDistrict?->name ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 text-sm font-medium text-slate-600">
                                    <div class="flex flex-col gap-0.5">
                                        <span class="flex items-center gap-1.5"><svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg> {{ $org->phone }}</span>
                                        @if($org->email)
                                            <span class="flex items-center gap-1.5"><svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg> {{ $org->email }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <a href="{{ route('admin.org.document', $org->id) }}" target="_blank"
                                       class="inline-flex items-center justify-center gap-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold px-3 py-1.5 rounded-lg transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        ডকুমেন্ট দেখুন
                                    </a>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <form action="{{ route('admin.org.verify', $org->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="status" value="approved">
                                            <button type="submit"
                                                    class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-extrabold px-3 py-1.5 rounded-lg transition shadow-sm">
                                                অ্যাপ্রুভ
                                            </button>
                                        </form>
                                        <button type="button"
                                                @click="currentOrgId = {{ $org->id }}; currentOrgName = '{{ addslashes($org->name) }}'; rejectModalOpen = true;"
                                                class="bg-white border text-slate-600 hover:bg-red-50 hover:text-red-600 hover:border-red-200 text-xs font-extrabold px-3 py-1.5 rounded-lg transition">
                                            বাতিল
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        @if($pendingOrgs->hasPages())
            <div class="mt-8">
                {{ $pendingOrgs->links() }}
            </div>
        @endif
    @endif

    <div x-show="rejectModalOpen"
         class="fixed inset-0 z-50 overflow-y-auto"
         aria-labelledby="modal-title" role="dialog" aria-modal="true" style="display: none;">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="rejectModalOpen"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-slate-900 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="rejectModalOpen"
                 @click.away="rejectModalOpen = false"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                <form :action="'{{ url('/admin/orgs') }}/' + currentOrgId + '/verify'" method="POST">
                    @csrf
                    <input type="hidden" name="status" value="rejected">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-extrabold text-slate-900" id="modal-title">
                                    অর্গানাইজেশন বাতিল করুন
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm font-medium text-slate-500 mb-3">আপনি <span x-text="currentOrgName" class="font-bold text-slate-800"></span> এর আবেদন বাতিল করতে যাচ্ছেন। অনুগ্রহ করে কারণ উল্লেখ করুন:</p>
                                    <textarea name="rejection_reason" rows="3" required
                                              class="w-full border-slate-200 rounded-xl focus:ring-red-500 focus:border-red-500 text-sm font-medium"
                                              placeholder="উদাঃ অফিশিয়াল ডকুমেন্ট স্পষ্ট নয়..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-slate-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-slate-100">
                        <button type="submit" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-extrabold text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm transition">
                            নিশ্চিত করুন
                        </button>
                        <button type="button" @click="rejectModalOpen = false" class="mt-3 w-full inline-flex justify-center rounded-xl border border-slate-300 shadow-sm px-4 py-2 bg-white text-base font-bold text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition">
                            ক্যান্সেল
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    </div>
</div>
@endsection
