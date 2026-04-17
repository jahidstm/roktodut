@props(['title', 'subtitle' => null, 'label' => null, 'icon' => null, 'alignment' => 'left'])

<div class="mb-10 {{ $alignment === 'center' ? 'text-center mx-auto max-w-2xl' : '' }}">
    @if($label)
    <p class="text-sm font-extrabold text-[#D32F2F] uppercase tracking-widest mb-3 flex items-center {{ $alignment === 'center' ? 'justify-center' : '' }} gap-2">
        @if($icon)
            <span class="w-6 h-6 rounded-full bg-red-50 flex items-center justify-center text-[#D32F2F]">
                {!! $icon !!}
            </span>
        @endif
        {{ $label }}
    </p>
    @endif
    <h2 class="text-3xl lg:text-4xl font-black text-slate-900 mb-4 tracking-tight">{{ $title }}</h2>
    @if($subtitle)
    <p class="text-base font-medium text-slate-500">{{ $subtitle }}</p>
    @endif
</div>
