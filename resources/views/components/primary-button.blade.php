@if ($attributes->has('href'))
    <a {{ $attributes->merge(['class' => 'inline-flex items-center justify-center gap-2 bg-[#D32F2F] hover:bg-[#B71C1C] text-white font-black text-sm px-6 py-3 rounded-full shadow-md hover:shadow-xl hover:-translate-y-1 transition-all duration-300 disabled:opacity-50 disabled:pointer-events-none']) }}>
        {{ $slot }}
    </a>
@else
    <button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center gap-2 bg-[#D32F2F] hover:bg-[#B71C1C] text-white font-black text-sm px-6 py-3 rounded-full shadow-md hover:shadow-xl hover:-translate-y-1 transition-all duration-300 disabled:opacity-50 disabled:pointer-events-none']) }}>
        {{ $slot }}
    </button>
@endif
