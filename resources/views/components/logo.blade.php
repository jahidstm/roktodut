@props([
    'variant' => 'full',   {{-- 'full' | 'icon' --}}
    'size'    => 'md',     {{-- 'sm' | 'md' | 'lg' | 'xl' --}}
])

@php
    $s = match($size) {
        'sm'  => ['icon' => 'h-7 w-7',   'name' => 'text-sm',    'sub' => 'text-[8px]',  'gap' => 'gap-2'],
        'lg'  => ['icon' => 'h-12 w-12', 'name' => 'text-xl',    'sub' => 'text-[10px]', 'gap' => 'gap-3'],
        'xl'  => ['icon' => 'h-16 w-16', 'name' => 'text-2xl',   'sub' => 'text-xs',     'gap' => 'gap-4'],
        default => ['icon' => 'h-9 w-9 sm:h-10 sm:w-10', 'name' => 'text-base', 'sub' => 'text-[10px]', 'gap' => 'gap-2.5'],
    };
    /* Unique gradient ID so multiple logos on the same page don't conflict */
    static $logoIdx = 0;
    $uid = 'rdl-' . (++$logoIdx);
@endphp

<div {{ $attributes->merge(['class' => "inline-flex items-center {$s['gap']}"]) }}>

    {{-- ═══ SVG Icon Mark ═══ --}}
    {{-- Blood-drop + heartbeat pulse. Red fill stays red on both light/dark;
         text adapts automatically via currentColor / dark: variants. --}}
    <div class="{{ $s['icon'] }} shrink-0" aria-hidden="true">
        <svg viewBox="0 0 100 112" fill="none" xmlns="http://www.w3.org/2000/svg"
             class="w-full h-full" role="img">
            <defs>
                {{-- Vertical gradient: bright red → deep crimson --}}
                <linearGradient id="{{ $uid }}" x1="0" y1="0" x2="0" y2="112" gradientUnits="userSpaceOnUse">
                    <stop offset="0%"   stop-color="#f87171"/> {{-- red-400 --}}
                    <stop offset="100%" stop-color="#b91c1c"/> {{-- red-700 --}}
                </linearGradient>
            </defs>

            {{-- Blood-drop body --}}
            <path
                d="M50 5
                   C50 5, 91 48, 91 68
                   A41 41 0 0 1 9 68
                   C9 48, 50 5, 50 5 Z"
                fill="url(#{{ $uid }})"
            />

            {{-- ECG / Heartbeat line (white, inside the drop) --}}
            <polyline
                points="14,70 28,70 34,58 40,84 50,36 60,70 72,70 86,70"
                stroke="white"
                stroke-width="5"
                fill="none"
                stroke-linecap="round"
                stroke-linejoin="round"
                opacity="0.95"
            />
        </svg>
    </div>

    {{-- ═══ Text Block (hidden for icon-only variant) ═══ --}}
    @if($variant === 'full')
    <div class="leading-none select-none">
        {{-- Bengali brand name — inherits text color from parent (supports group-hover) --}}
        <div class="{{ $s['name'] }} font-extrabold tracking-tight leading-tight
                    text-slate-900 dark:text-white transition-colors">
            রক্তদূত
        </div>
        {{-- English subtitle — always muted, never changes on hover --}}
        <div class="{{ $s['sub'] }} font-bold uppercase tracking-[0.12em]
                    text-slate-400 dark:text-slate-500 mt-[2px]">
            Blood Donation Platform
        </div>
    </div>
    @endif

</div>
