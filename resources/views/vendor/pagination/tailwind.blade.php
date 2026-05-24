@if ($paginator->hasPages())
    @php
        $window = \Illuminate\Pagination\UrlWindow::make($paginator->onEachSide(1));
        if (is_array($window['first']) && count($window['first']) > 5 && $window['slider'] === null) {
            $window['first'] = array_slice($window['first'], 0, 5, true);
        }
        $elements = array_filter([
            $window['first'],
            is_array($window['slider']) ? '...' : null,
            $window['slider'],
            is_array($window['last']) ? '...' : null,
            $window['last'],
        ]);
        
        $from = $paginator->firstItem() ?? 0;
        $to = $paginator->lastItem() ?? 0;
        $total = $paginator->total();
    @endphp

    <div class="mt-4 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between pt-2">
        <p class="text-sm font-semibold text-slate-600">
            দেখানো হচ্ছে: {{ \App\Support\BanglaDate::digits((string) $from) }}–{{ \App\Support\BanglaDate::digits((string) $to) }} (মোট {{ \App\Support\BanglaDate::digits((string) $total) }} টি)
        </p>

        <nav class="inline-flex flex-wrap items-center gap-1" aria-label="Pagination">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span class="px-3 py-2 rounded-lg border border-slate-200 bg-slate-100 text-slate-400 text-sm font-bold">আগের</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="px-3 py-2 rounded-lg border border-slate-300 bg-white text-slate-700 text-sm font-bold hover:bg-slate-50">আগের</a>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="px-3 py-2 rounded-lg border border-slate-200 bg-slate-50 text-slate-400 text-sm font-bold">...</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="px-3 py-2 rounded-lg bg-red-600 text-white text-sm font-black">{{ \App\Support\BanglaDate::digits((string) $page) }}</span>
                        @else
                            <a href="{{ $url }}" class="px-3 py-2 rounded-lg border border-slate-300 bg-white text-slate-700 text-sm font-bold hover:bg-slate-50">{{ \App\Support\BanglaDate::digits((string) $page) }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="px-3 py-2 rounded-lg border border-slate-300 bg-white text-slate-700 text-sm font-bold hover:bg-slate-50">পরের</a>
            @else
                <span class="px-3 py-2 rounded-lg border border-slate-200 bg-slate-100 text-slate-400 text-sm font-bold">পরের</span>
            @endif
        </nav>
    </div>
@endif
