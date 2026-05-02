@php
    $focusAreas = (array) config('pilot.focus_areas', []);
    $focusAreasText = implode(', ', $focusAreas);
@endphp

<div id="pilot-banner" class="mb-6 rounded-xl border border-amber-200 bg-amber-50 p-4 text-amber-900 shadow-sm">
    <div class="flex items-start justify-between gap-3">
        <div>
            <p class="font-extrabold">
                ভেরিফাইড কভারেজ বর্তমানে ঢাকা ও সাভার-কেন্দ্রিক। অন্যান্য জেলায় ধীরে ধীরে সম্প্রসারণ হবে।
            </p>
            @if($focusAreasText !== '')
                <p class="mt-1 text-xs font-semibold text-amber-700">Focus area: {{ $focusAreasText }}</p>
            @endif
        </div>
        <button type="button"
                id="pilot-banner-dismiss"
                class="shrink-0 rounded-lg border border-amber-300 bg-white px-2.5 py-1 text-xs font-black text-amber-800 hover:bg-amber-100">
            ✕
        </button>
    </div>
</div>

<script>
(() => {
    const key = 'pilot_banner_dismissed_v1';
    const banner = document.getElementById('pilot-banner');
    const closeBtn = document.getElementById('pilot-banner-dismiss');

    if (!banner || !closeBtn) return;

    try {
        if (localStorage.getItem(key) === '1') {
            banner.remove();
            return;
        }
    } catch (e) {}

    closeBtn.addEventListener('click', () => {
        try { localStorage.setItem(key, '1'); } catch (e) {}
        banner.remove();
    });
})();
</script>
