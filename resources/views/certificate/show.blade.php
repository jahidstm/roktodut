@php
    $appName = config('app.name', 'Roktodut');
    $appUrl  = config('app.url', 'https://roktodut.com');
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blood Donation Certificate — {{ $donorName }} | {{ $appName }}</title>
    <meta name="description" content="{{ $shareDesc }}">

    {{-- Open Graph (Facebook / WhatsApp) --}}
    <meta property="og:type"        content="website">
    <meta property="og:title"       content="{{ $shareTitle }}">
    <meta property="og:description" content="{{ $shareDesc }}">
    <meta property="og:image"       content="{{ $imageUrl }}">
    <meta property="og:image:width"  content="1200">
    <meta property="og:image:height" content="848">
    <meta property="og:url"         content="{{ $shareUrl }}">
    <meta property="og:site_name"   content="{{ $appName }}">

    {{-- Twitter Card --}}
    <meta name="twitter:card"        content="summary_large_image">
    <meta name="twitter:title"       content="{{ $shareTitle }}">
    <meta name="twitter:description" content="{{ $shareDesc }}">
    <meta name="twitter:image"       content="{{ $imageUrl }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-950 min-h-screen text-white">

    {{-- ── Navigation Strip ── --}}
    <div class="bg-slate-900 border-b border-slate-800 px-4 py-3">
        <div class="max-w-4xl mx-auto flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-2 text-red-400 font-black text-lg hover:text-red-300 transition">
                🩸 <span>রক্তদূত</span>
            </a>
            <a href="{{ route('home') }}"
               class="text-xs font-bold text-slate-400 hover:text-white transition border border-slate-700 px-3 py-1.5 rounded-lg">
                যোগ দিন →
            </a>
        </div>
    </div>

    {{-- ── Main Content ── --}}
    <div class="max-w-4xl mx-auto px-4 py-10">

        {{-- Certificate Image Preview --}}
        <div class="rounded-2xl overflow-hidden shadow-2xl shadow-red-900/30 border border-red-900/40 mb-8">
            <img src="{{ $imageUrl }}"
                 alt="Blood Donation Certificate for {{ $donorName }}"
                 class="w-full h-auto"
                 loading="eager">
        </div>

        {{-- Donor Info Card --}}
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 mb-6">
            <div class="flex items-start gap-4">
                <div class="w-14 h-14 rounded-2xl bg-red-900/50 border border-red-700 flex items-center justify-center text-2xl shrink-0">
                    🩸
                </div>
                <div class="flex-1 min-w-0">
                    <h1 class="text-xl font-black text-white truncate">{{ $donorName }}</h1>
                    <div class="flex flex-wrap gap-2 mt-2">
                        <span class="inline-flex items-center gap-1 bg-red-900/50 border border-red-700 text-red-300 px-3 py-1 rounded-full text-sm font-bold">
                            🩸 {{ $bloodGroup }}
                        </span>
                        <span class="inline-flex items-center gap-1 bg-slate-800 border border-slate-700 text-slate-300 px-3 py-1 rounded-full text-sm font-semibold">
                            📅 {{ $donationDate }}
                        </span>
                        <span class="inline-flex items-center gap-1 bg-slate-800 border border-slate-700 text-slate-300 px-3 py-1 rounded-full text-sm font-semibold">
                            📍 {{ $districtName }}
                        </span>
                    </div>
                    <p class="text-sm text-slate-400 mt-2">
                        Total Donations: <span class="font-bold text-white">{{ $totalCount }}</span> time(s) &nbsp;|&nbsp;
                        Certificate ID: <span class="font-mono font-bold text-amber-400">{{ $certId }}</span>
                    </p>
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">
            {{-- Download --}}
            <a href="{{ route('certificate.download', $token) }}?v={{ time() }}"
               id="btn-download-certificate"
               class="flex items-center justify-center gap-3 bg-red-600 hover:bg-red-700 text-white font-black py-4 px-6 rounded-2xl transition shadow-lg hover:shadow-red-700/30">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                </svg>
                Download Certificate (PNG)
            </a>

            {{-- Copy Link --}}
            <button onclick="copyLink()"
                    id="btn-copy-certificate-link"
                    class="flex items-center justify-center gap-3 bg-slate-800 hover:bg-slate-700 text-white font-bold py-4 px-6 rounded-2xl transition border border-slate-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <span id="copy-label">লিংক কপি করুন</span>
            </button>
        </div>

        {{-- Social Share Buttons --}}
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 mb-8">
            <p class="text-sm font-bold text-slate-400 mb-4 uppercase tracking-wider">শেয়ার করুন</p>
            <div class="flex flex-wrap gap-3">
                {{-- Facebook --}}
                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($shareUrl) }}"
                   id="btn-share-facebook"
                   target="_blank" rel="noopener noreferrer"
                   class="flex items-center gap-2 bg-blue-700 hover:bg-blue-600 text-white font-bold py-2.5 px-5 rounded-xl transition text-sm">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                    </svg>
                    Facebook
                </a>

                {{-- WhatsApp --}}
                <a href="https://wa.me/?text={{ urlencode($shareTitle . ' ' . $shareUrl) }}"
                   id="btn-share-whatsapp"
                   target="_blank" rel="noopener noreferrer"
                   class="flex items-center gap-2 bg-green-600 hover:bg-green-500 text-white font-bold py-2.5 px-5 rounded-xl transition text-sm">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                    </svg>
                    WhatsApp
                </a>

                {{-- Twitter/X --}}
                <a href="https://twitter.com/intent/tweet?text={{ urlencode($shareTitle) }}&url={{ urlencode($shareUrl) }}"
                   id="btn-share-twitter"
                   target="_blank" rel="noopener noreferrer"
                   class="flex items-center gap-2 bg-slate-700 hover:bg-slate-600 text-white font-bold py-2.5 px-5 rounded-xl transition text-sm">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.748l7.73-8.835L1.254 2.25H8.08l4.259 5.63 5.905-5.63zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                    </svg>
                    X / Twitter
                </a>
            </div>
        </div>

        {{-- CTA — Join Platform --}}
        <div class="bg-gradient-to-r from-red-900/50 to-red-800/30 border border-red-800/50 rounded-2xl p-6 text-center">
            <p class="text-2xl font-black text-white mb-2">রক্ত দিন, জীবন বাঁচান 🩸</p>
            <p class="text-slate-400 text-sm mb-4">রক্তদূতে যোগ দিন এবং হাজারো মানুষের পাশে দাঁড়ান।</p>
            <a href="{{ route('register') }}"
               id="btn-join-platform"
               class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-500 text-white font-black py-3 px-8 rounded-xl transition">
                বিনামূল্যে নিবন্ধন করুন →
            </a>
        </div>

    </div>

    {{-- Footer --}}
    <div class="border-t border-slate-800 mt-10 py-6 text-center text-xs text-slate-600">
        © {{ date('Y') }} {{ $appName }} — Blood Donation Platform | {{ $appUrl }}
    </div>

    <script>
        function copyLink() {
            const url = '{{ $shareUrl }}';
            navigator.clipboard.writeText(url).then(() => {
                const label = document.getElementById('copy-label');
                label.textContent = '✓ কপি হয়েছে!';
                setTimeout(() => { label.textContent = 'লিংক কপি করুন'; }, 2000);
            }).catch(() => {
                prompt('এই লিংকটি কপি করুন:', url);
            });
        }
    </script>
</body>
</html>
