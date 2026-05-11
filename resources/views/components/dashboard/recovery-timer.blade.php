@props([
    'items' => collect(),
])

@if($items->isNotEmpty())
    <section class="mt-6">
        <div class="mb-4 flex items-center justify-between">
            <div>
                <h3 class="text-base sm:text-lg font-black text-slate-900">ডাইনামিক রিকভারি টাইমার</h3>
                <p class="text-xs sm:text-sm text-slate-500 font-medium">প্রতিটি কম্পোনেন্টের মেডিকেল কুলডাউন অগ্রগতি</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            @foreach($items as $item)
                <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm hover:shadow-md transition-all duration-300">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wide text-slate-500">রিকভারি</p>
                            <h4 class="mt-1 text-base font-black text-slate-900">{{ $item['title'] }}</h4>
                        </div>
                        <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-bold text-slate-600">
                            {{ $item['max_cooldown_days'] }} দিন
                        </span>
                    </div>

                    <div class="mt-4 h-2.5 w-full overflow-hidden rounded-full bg-slate-100">
                        <div
                            class="h-full rounded-full {{ $item['bar_class'] }} transition-all duration-500 ease-out"
                            style="width: {{ $item['progress_percent'] }}%;"
                        ></div>
                    </div>

                    <div class="mt-4">
                        @if($item['is_ready'])
                            <p class="inline-flex items-center gap-1.5 text-sm font-black text-emerald-600">
                                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M16.704 5.29a1 1 0 010 1.42l-7.334 7.333a1 1 0 01-1.414 0L3.29 9.377a1 1 0 011.414-1.414l3.96 3.96 6.627-6.626a1 1 0 011.413-.007z" clip-rule="evenodd" />
                                </svg>
                                {{ $item['state_text'] }}
                            </p>
                        @else
                            <p class="text-sm font-black {{ $item['text_class'] }}">{{ $item['state_text'] }}</p>
                            <p class="mt-1 text-xs font-semibold text-slate-500">পরবর্তী তারিখ: {{ $item['eligible_on_formatted'] }}</p>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>
    </section>
@endif
