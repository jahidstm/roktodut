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

        <nav class="flex items-center gap-4">
            <a href="{{ route('requests.index') }}" class="text-sm font-semibold text-slate-700 hover:text-red-600">রিকোয়েস্ট ফিড</a>
            
            @auth
            <div class="relative" x-data="{ open: false }" @click.outside="open = false" @close.stop="open = false">
                <button @click="open = ! open" class="relative p-2 text-slate-500 hover:text-slate-800 transition rounded-full hover:bg-slate-100 focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                    @if(auth()->user()->unreadNotifications->count() > 0)
                        <span class="absolute top-1 right-1 flex h-4 w-4 items-center justify-center rounded-full bg-red-600 text-[10px] font-extrabold text-white shadow-sm">
                            {{ auth()->user()->unreadNotifications->count() }}
                        </span>
                    @endif
                </button>

                <div x-show="open"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="absolute right-0 z-50 mt-2 w-80 rounded-2xl bg-white shadow-lg ring-1 ring-slate-200"
                     style="display: none;">
                    <div class="px-4 py-3 border-b border-slate-100 flex justify-between items-center bg-slate-50 rounded-t-2xl">
                        <h3 class="text-sm font-extrabold text-slate-800">নোটিফিকেশন</h3>
                    </div>
                    <div class="max-h-80 overflow-y-auto">
                        @forelse(auth()->user()->unreadNotifications as $notification)
                            <form action="{{ route('notifications.read', $notification->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full text-left p-4 border-b border-slate-50 hover:bg-slate-50 transition block">
                                    <div class="text-sm font-semibold text-slate-800">
                                        {{ $notification->data['message'] ?? 'নতুন নোটিফিকেশন' }}
                                    </div>
                                    <div class="text-xs text-emerald-600 mt-1 font-bold">
                                        {{ $notification->data['patient_name'] ?? '' }} • {{ $notification->created_at->diffForHumans() }}
                                    </div>
                                </button>
                            </form>
                        @empty
                            <div class="p-6 text-center text-sm text-slate-500 font-medium">
                                কোনো নতুন নোটিফিকেশন নেই।
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
            @endauth

            <a href="{{ route('requests.create') }}"
               class="text-sm font-extrabold bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg shadow-sm shadow-red-200">
                রিকোয়েস্ট করুন
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