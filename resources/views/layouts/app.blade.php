<!doctype html>
<html lang="bn">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>@yield('title', 'রক্তদূত')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css','resources/js/app.js'])

    <style>
        :root { font-family: 'Hind Siliguri', ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Arial; }
    </style>
</head>

<body class="bg-slate-50 text-slate-900 antialiased">
<header class="bg-white border-b border-slate-100">
    <div class="mx-auto max-w-6xl px-4 py-5 flex items-center justify-between">
        <a href="{{ route('home') }}" class="flex items-center gap-3">
            <div class="h-9 w-9 rounded-xl bg-red-50 border border-red-100 flex items-center justify-center">
                <span class="text-red-600 font-extrabold tracking-tight">RD</span>
            </div>
            <div class="leading-tight">
                <div class="font-extrabold tracking-tight">রক্তদূত</div>
                <div class="text-xs text-slate-500 font-semibold">Blood Donation Platform</div>
            </div>
        </a>

        <nav class="flex items-center gap-3">
            <a href="{{ route('requests.index') }}" class="text-sm font-semibold text-slate-700 hover:text-red-600">রিকোয়েস্ট ফিড</a>
            <a href="{{ route('requests.create') }}"
               class="text-sm font-extrabold bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg shadow-sm shadow-red-200">
                রিকোয়েস্ট করুন
            </a>
        </nav>
    </div>
</header>

<main class="mx-auto max-w-6xl px-4 py-10">
    @if (session('success'))
        <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800 font-semibold">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-red-800 font-semibold">
            {{ session('error') }}
        </div>
    @endif

    @yield('content')
</main>

<footer class="border-t border-slate-100 bg-white">
    <div class="mx-auto max-w-6xl px-4 py-6 text-xs text-slate-500 flex justify-between">
        <span>© {{ date('Y') }} রক্তদূত</span>
        <span>Built with Laravel</span>
    </div>
</footer>
</body>
</html>