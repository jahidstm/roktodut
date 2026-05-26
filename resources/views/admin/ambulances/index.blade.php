@extends('layouts.app')

@section('title', 'অ্যাম্বুলেন্স ম্যানেজমেন্ট — রক্তদূত')

@section('content')
    <div class="bg-white border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <h2 class="font-black text-2xl text-slate-900 leading-tight flex items-center gap-2">
                    <span class="text-3xl">🚑</span> অ্যাম্বুলেন্স ম্যানেজমেন্ট
                </h2>
            </div>
        </div>
    </div>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Tabs --}}
            <div class="flex space-x-1 bg-slate-100/50 p-1 rounded-xl mb-8 w-max border border-slate-200">
                <a href="{{ route('admin.ambulances.index', ['status' => 'pending']) }}" class="px-6 py-2.5 rounded-lg text-sm font-bold transition-all {{ $status === 'pending' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-200/50' }}">
                    পেন্ডিং অ্যাম্বুলেন্স
                </a>
                <a href="{{ route('admin.ambulances.index', ['status' => 'verified']) }}" class="px-6 py-2.5 rounded-lg text-sm font-bold transition-all {{ $status === 'verified' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-200/50' }}">
                    ভেরিফাইড অ্যাম্বুলেন্স
                </a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-100">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs text-slate-500 uppercase bg-slate-50 border-b border-slate-100 font-bold">
                            <tr>
                                <th scope="col" class="px-6 py-4">নাম ও ধরন</th>
                                <th scope="col" class="px-6 py-4">যোগাযোগ ও লোকেশন</th>
                                <th scope="col" class="px-6 py-4">যুক্ত করেছেন</th>
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
                                        @if($ambulance->adder)
                                            <a href="{{ route('admin.gamification.show', $ambulance->adder->id) }}" class="font-bold text-blue-600 hover:underline">
                                                {{ $ambulance->adder->name }}
                                            </a>
                                            <div class="text-xs text-slate-500 font-medium">
                                                {{ $ambulance->created_at->format('d M Y, h:i A') }}
                                            </div>
                                        @else
                                            <span class="text-slate-400 font-medium">সিস্টেম/অজ্ঞাত</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right space-x-2">
                                        @if(!$ambulance->is_verified)
                                            <form action="{{ route('admin.ambulances.verify', $ambulance->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white px-3 py-1.5 rounded-lg font-bold transition border border-emerald-100 hover:border-emerald-600 text-xs" onclick="return confirm('আপনি কি নিশ্চিত যে এই তথ্যটি সঠিক?')">
                                                    ভেরিফাই করুন
                                                </button>
                                            </form>
                                        @endif
                                        <form action="{{ route('admin.ambulances.destroy', $ambulance->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="bg-red-50 text-red-600 hover:bg-red-600 hover:text-white px-3 py-1.5 rounded-lg font-bold transition border border-red-100 hover:border-red-600 text-xs" onclick="return confirm('আপনি কি নিশ্চিত? ডিলিট করলে আর ফেরত পাওয়া যাবে না।')">
                                                ডিলিট
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center">
                                        <div class="text-slate-400 mb-2 text-4xl">📭</div>
                                        <div class="text-slate-500 font-medium text-sm">কোনো অ্যাম্বুলেন্স ডেটা পাওয়া যায়নি।</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-6">
                {{ $ambulances->links() }}
            </div>

        </div>
    </div>
@endsection
