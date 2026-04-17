@props(['title', 'subtitle' => null, 'variant' => 'app', 'label' => null])

@if($variant === 'marketing')
<section class="relative bg-gradient-to-br from-red-700 via-red-600 to-rose-500 overflow-hidden text-center pt-20 pb-20 lg:pt-28 lg:pb-24">
    <div class="absolute inset-0 opacity-10" style="background-image: linear-gradient(rgba(255,255,255,.15) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.15) 1px, transparent 1px); background-size: 28px 28px;"></div>
    <div class="absolute -top-20 -right-20 w-80 h-80 bg-white/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-10 -left-10 w-60 h-60 bg-red-900/30 rounded-full blur-2xl pointer-events-none"></div>

    <div class="relative z-10 mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 flex flex-col items-center">
        @if($label ?? false)
        <span class="inline-flex items-center gap-2 bg-white/15 backdrop-blur-sm border border-white/20 text-white text-xs font-extrabold tracking-widest uppercase px-4 py-1.5 rounded-full mb-5">
            {{ $label }}
        </span>
        @endif
        <h1 class="text-4xl sm:text-5xl lg:text-[4rem] font-black text-white leading-[1.15] mb-6 tracking-tight drop-shadow-sm">
            {!! $title !!}
        </h1>
        @if($subtitle)
        <p class="text-lg text-white/90 mb-10 font-medium max-w-2xl mx-auto leading-relaxed">
            {{ $subtitle }}
        </p>
        @endif
        @if(isset($actions))
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4 w-full sm:w-auto">
            {{ $actions }}
        </div>
        @endif
    </div>
</section>@else
<section class="bg-white border-b border-slate-100 py-8 lg:py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">{{ $title }}</h1>
            @if($subtitle)
            <p class="mt-2 text-sm sm:text-base font-medium text-slate-500">{{ $subtitle }}</p>
            @endif
        </div>
        @if(isset($actions))
        <div class="flex items-center gap-3">
            {{ $actions }}
        </div>
        @endif
    </div>
</section>
@endif
