@props(['paddings' => 'p-6'])

<div {{ $attributes->merge(['class' => 'bg-white rounded-2xl border border-slate-100 shadow-sm ' . $paddings]) }}>
    @if(isset($header))
        <div class="mb-4">
            {{ $header }}
        </div>
    @endif

    {{ $slot }}

    @if(isset($footer))
        <div class="mt-6 pt-4 border-t border-slate-100">
            {{ $footer }}
        </div>
    @endif
</div>
