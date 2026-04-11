@props(['maxWidth' => 'max-w-[28rem]'])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'RoktoDut') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Bengali:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body {
            font-family: 'Noto Sans Bengali', 'Inter', sans-serif;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.5);
        }
        
        .input-modern {
            background-color: rgb(248 250 252); /* slate-50 */
            border: 1px solid rgb(226 232 240); /* slate-200 */
            border-radius: 0.75rem;
            padding: 0.75rem 1rem;
            width: 100%;
            color: rgb(30 41 59); /* slate-800 */
            transition: all 0.2s ease-in-out;
        }
        .input-modern:focus {
            outline: none;
            border-color: rgb(220 38 38); /* red-600 */
            box-shadow: 0 0 0 4px rgba(220, 38, 38, 0.1);
            background-color: white;
        }
        .input-modern::placeholder {
            color: rgb(148 163 184); /* slate-400 */
        }
    </style>
</head>
<body class="font-sans text-slate-800 antialiased bg-slate-50 min-h-screen selection:bg-red-200 selection:text-red-900">
    <div class="min-h-screen flex w-full">
        <!-- Left Side: Graphic / Gradient -->
        <div class="hidden lg:flex lg:w-1/2 relative bg-gradient-to-br from-red-50 to-red-100 items-center justify-center overflow-hidden">
            <!-- Decorative Elements -->
            <div class="absolute top-0 left-0 w-full h-full bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-red-200/40 via-transparent to-transparent"></div>
            <div class="absolute bottom-0 right-0 w-full h-full bg-[radial-gradient(ellipse_at_bottom_left,_var(--tw-gradient-stops))] from-white/80 via-transparent to-transparent"></div>
            
            <!-- Abstract Blood Drop shapes -->
            <div class="absolute w-96 h-96 bg-red-600/10 rounded-full blur-3xl -top-20 -left-20"></div>
            <div class="absolute w-80 h-80 bg-red-500/10 rounded-full blur-2xl bottom-10 right-10"></div>
            <div class="absolute w-64 h-64 bg-red-600/5 rounded-full blur-3xl top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2"></div>
            
            <div class="relative z-10 text-center px-12">
                <div class="flex justify-center mb-8 hover:scale-105 transition-transform duration-500 cursor-default">
                    <!-- RoktoDut Logo/Icon representation -->
                    <div class="w-24 h-24 bg-gradient-to-br from-red-500 to-red-600 rounded-3xl rotate-12 flex items-center justify-center shadow-2xl shadow-red-600/30">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-white -rotate-12" fill="currentColor" viewBox="0 0 24 24">
                          <path d="M12 2.5C12 2.5 4.5 9 4.5 15.5C4.5 19.6421 7.85786 23 12 23C16.1421 23 19.5 19.6421 19.5 15.5C19.5 9 12 2.5 12 2.5Z"/>
                        </svg>
                    </div>
                </div>
                <h1 class="text-4xl lg:text-5xl font-extrabold text-slate-800 mb-5 tracking-tight">রক্তদানে জীবন বাঁচে</h1>
                <p class="text-lg text-slate-600 max-w-md mx-auto leading-relaxed">
                    রক্তদূত প্ল্যাটফর্মে আপনাকে স্বাগতম। আপনার এক ফোঁটা রক্ত হতে পারে একজন মুমূর্ষু রোগীর জীবন বাঁচানোর একমাত্র উপায়।
                </p>
                <div class="mt-12 flex gap-6 justify-center">
                    <div class="bg-white/70 backdrop-blur-md px-6 py-4 rounded-2xl border border-white shadow-sm hover:-translate-y-1 transition-transform">
                        <span class="block text-3xl font-bold text-red-600">১০k+</span>
                        <span class="text-sm font-medium text-slate-600">সক্রিয় রক্তদাতা</span>
                    </div>
                    <div class="bg-white/70 backdrop-blur-md px-6 py-4 rounded-2xl border border-white shadow-sm hover:-translate-y-1 transition-transform">
                        <span class="block text-3xl font-bold text-red-600">৫k+</span>
                        <span class="text-sm font-medium text-slate-600">জীবন বাঁচানো</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side: Form Content -->
        <div class="w-full lg:w-1/2 flex flex-col items-center justify-center px-6 py-12 sm:px-12 relative overflow-y-auto overflow-x-hidden bg-slate-50">
             <!-- Mobile decorative elements -->
            <div class="lg:hidden absolute top-0 left-0 w-full h-1/2 bg-gradient-to-b from-red-100/50 to-transparent"></div>
            <div class="lg:hidden absolute w-72 h-72 bg-red-600/10 rounded-full blur-3xl -top-20 -right-10"></div>
            
            <!-- Mobile Logo -->
            <div class="lg:hidden mb-8 relative z-10 flex flex-col items-center">
                <div class="w-16 h-16 bg-gradient-to-br from-red-500 to-red-600 rounded-2xl flex items-center justify-center shadow-lg shadow-red-600/20 mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2.5C12 2.5 4.5 9 4.5 15.5C4.5 19.6421 7.85786 23 12 23C16.1421 23 19.5 19.6421 19.5 15.5C19.5 9 12 2.5 12 2.5Z"/>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-slate-800">রক্তদূত</h2>
            </div>
            
            <div class="w-full {{ $maxWidth ?? 'max-w-[28rem]' }} relative z-10 glass-card rounded-3xl p-8 sm:p-10 shadow-xl shadow-slate-200">
                {{ $slot }}
            </div>
        </div>
    </div>
</body>
</html>
