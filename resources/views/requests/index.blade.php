<!doctype html>
<html lang="bn">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>রিকোয়েস্ট ফিড — রক্তদূত</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css','resources/js/app.js'])

    <style>
        :root { font-family: 'Hind Siliguri', ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Arial; }
    </style>
</head>
<body class="bg-slate-50 text-slate-900">
    <header class="bg-white border-b border-slate-100">
        <div class="mx-auto max-w-6xl px-4 py-5 flex items-center justify-between">
            <div>
                <h1 class="text-xl font-extrabold">রক্তের রিকোয়েস্ট ফিড</h1>
                <p class="text-sm text-slate-500 font-medium">সাম্প্রতিক পেন্ডিং রিকোয়েস্ট</p>
            </div>
            <a href="{{ route('home') }}" class="text-sm font-semibold text-red-600 hover:text-red-700">হোম</a>
        </div>
    </header>

    <main class="mx-auto max-w-6xl px-4 py-10">
        @if ($requests->isEmpty())
            <div class="rounded-2xl border border-slate-200 bg-white p-8 text-center">
                <div class="text-slate-800 font-semibold">কোনো পেন্ডিং রিকোয়েস্ট পাওয়া যায়নি।</div>
                <div class="text-slate-500 text-sm mt-2">নতুন রিকোয়েস্ট তৈরি হলে এখানে দেখাবে।</div>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                @foreach ($requests as $r)
                    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <div class="text-lg font-extrabold truncate">{{ $r->patient_name ?? 'রোগী' }}</div>
                                <div class="text-sm text-slate-500 font-medium truncate">
                                    {{ $r->hospital ?? 'হাসপাতাল উল্লেখ নেই' }}
                                </div>
                            </div>
                            <div class="shrink-0 px-3 py-1 rounded-lg bg-red-50 text-red-700 border border-red-100 font-extrabold">
                                {{ $r->blood_group?->value ?? (string) $r->blood_group }}
                            </div>
                        </div>

                        <div class="mt-4 text-sm text-slate-600 font-semibold">
                            লোকেশন: {{ $r->thana ?? '-' }}, {{ $r->district ?? '-' }}
                        </div>

                        <div class="mt-1 text-sm text-slate-500 font-semibold">
                            ব্যাগ: {{ $r->bags_needed ?? '-' }}
                            • জরুরিতা: {{ $r->urgency?->value ?? (string) $r->urgency }}
                        </div>

                        <div class="mt-4 text-xs text-slate-500 font-semibold">
                            দরকার: {{ $r->needed_at?->format('Y-m-d H:i') ?? 'ASAP' }}
                            • পোস্ট: {{ $r->created_at?->diffForHumans() }}
                        </div>

                        <div class="mt-5 flex items-center gap-3">
                            <button class="px-4 py-2 rounded-lg bg-red-600 hover:bg-red-700 text-white font-extrabold text-sm">
                                রেসপন্স করুন
                            </button>
                            <button class="px-4 py-2 rounded-lg border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 font-extrabold text-sm">
                                বিস্তারিত
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $requests->links() }}
            </div>
        @endif
    </main>
</body>
</html>