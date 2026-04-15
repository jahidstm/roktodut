@if($requests->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @foreach($requests as $req)
            <x-request-feed-card :request="$req" :is-public="true" />
        @endforeach
    </div>

    {{-- Pagination --}}
    <div class="mt-10">
        {{ $requests->links() }}
    </div>

@else
    {{-- Empty State --}}
    <div class="text-center py-20 bg-white rounded-xl border border-slate-100 shadow-sm max-w-xl mx-auto">
        <div class="w-16 h-16 bg-slate-50 text-slate-300 rounded-full flex items-center justify-center mx-auto mb-5">
            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <h3 class="text-lg font-black text-slate-800 mb-2">কোনো অনুরোধ পাওয়া যায়নি</h3>
        <p class="text-slate-500 font-medium text-sm">এই মুহূর্তে কোনো জরুরি রক্তের অনুরোধ নেই অথবা ফিল্টার পরিবর্তন করে দেখুন।</p>
        <a href="{{ route('home') }}" class="inline-block mt-6 text-red-600 font-bold hover:underline text-sm">হোমে ফিরে যান</a>
    </div>
@endif
