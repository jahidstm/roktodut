@extends('layouts.app')

@section('title', 'ডোনার ভেরিফিকেশন — রক্তদূত')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-extrabold text-slate-900">ডকুমেন্ট রিভিউ ও ভেরিফিকেশন</h1>
        <a href="{{ route('org.dashboard') }}" class="text-sm font-bold text-slate-500 hover:text-slate-800 transition">
            &larr; ড্যাশবোর্ডে ফিরে যান
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
                <div class="text-center mb-6">
                    <div class="w-24 h-24 bg-slate-100 rounded-full mx-auto flex items-center justify-center text-3xl font-black text-slate-400 border-4 border-white shadow-md">
                        {{ substr($donor->name, 0, 1) }}
                    </div>
                    <h2 class="text-xl font-extrabold text-slate-900 mt-4">{{ $donor->name }}</h2>
                    <span class="inline-flex mt-2 items-center px-3 py-1 rounded-full text-xs font-black bg-red-100 text-red-600">
                        {{ $donor->blood_group?->value ?? (string) $donor->blood_group }}
                    </span>
                </div>

                <div class="space-y-4 text-sm font-medium text-slate-600">
                    <div class="flex justify-between border-b border-slate-100 pb-2">
                        <span class="text-slate-400">ফোন নম্বর:</span>
                        <span class="font-bold text-slate-800">{{ $donor->phone ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between border-b border-slate-100 pb-2">
                        <span class="text-slate-400">জেলা:</span>
                        <span class="font-bold text-slate-800">{{ $donor->district ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between pb-2">
                        <span class="text-slate-400">রেজিস্ট্রেশন:</span>
                        <span class="font-bold text-slate-800">{{ $donor->created_at->format('d M, Y') }}</span>
                    </div>
                </div>
            </div>
            
            <div class="bg-slate-50 p-6 rounded-3xl border border-slate-200">
                <h3 class="text-sm font-bold text-slate-800 mb-4 uppercase tracking-wide text-center">আপনার সিদ্ধান্ত দিন</h3>
                
                <div class="flex flex-col gap-3">
                    <form action="{{ route('org.donor.approve', $donor->id) }}" method="POST">
                        @csrf
                        <button type="submit" onclick="return confirm('আপনি কি নিশ্চিত যে এই ডোনারের তথ্য সঠিক?')" class="w-full flex justify-center items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-3 rounded-xl font-extrabold transition-all shadow-lg shadow-emerald-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            অ্যাপ্রুভ করুন (ব্লু-ব্যাজ দিন)
                        </button>
                    </form>

                    <form action="{{ route('org.donor.reject', $donor->id) }}" method="POST">
                        @csrf
                        <button type="submit" onclick="return confirm('আপনি কি নিশ্চিত যে এই ডকুমেন্টটি বাতিল করতে চান?')" class="w-full flex justify-center items-center gap-2 bg-white hover:bg-red-50 border border-red-200 text-red-600 px-6 py-3 rounded-xl font-extrabold transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            রিজেক্ট করুন
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="lg:col-span-2">
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden h-full flex flex-col">
                <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
                    <h2 class="text-sm font-extrabold text-slate-800 uppercase tracking-widest">সাবমিট করা ডকুমেন্ট (NID/ID)</h2>
                </div>
                <div class="p-6 flex-1 flex items-center justify-center bg-slate-100/50">
                    @if($donor->nid_path)
                        @if(\Illuminate\Support\Str::endsWith($donor->nid_path, ['.pdf']))
                            <iframe src="{{ \Illuminate\Support\Facades\URL::temporarySignedRoute('org.nid.image', now()->addMinutes(2), ['user' => $donor->id]) }}"
                                    class="w-full h-[550px] rounded-xl border border-slate-200 shadow-inner"
                                    style="border: none;"></iframe>
                        @else
                            <img src="{{ \Illuminate\Support\Facades\URL::temporarySignedRoute('org.nid.image', now()->addMinutes(2), ['user' => $donor->id]) }}"
                                 alt="NID Document"
                                 class="max-w-full h-auto max-h-[600px] rounded-xl shadow-md border border-slate-200">
                        @endif
                    @else
                        <div class="text-center text-slate-400">
                            <svg class="w-16 h-16 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <p class="font-bold">এই ডোনার কোনো ছবি আপলোড করেননি।</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
    </div>
</div>
@endsection
