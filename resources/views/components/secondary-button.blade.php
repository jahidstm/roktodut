@if ($attributes->has('href'))
    <a {{ $attributes->merge(['class' => 'inline-flex items-center justify-center gap-2 bg-white border-2 border-slate-200 text-slate-700 hover:bg-slate-900 hover:text-white hover:border-slate-900 font-black text-sm px-6 py-3 rounded-full shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 disabled:opacity-50 disabled:pointer-events-none']) }}>
        {{ $slot }}
    </a>
@else
    <button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center justify-center gap-2 bg-white border-2 border-slate-200 text-slate-700 hover:bg-slate-900 hover:text-white hover:border-slate-900 font-black text-sm px-6 py-3 rounded-full shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 disabled:opacity-50 disabled:pointer-events-none']) }}>
        {{ $slot }}
    </button>
@endif
