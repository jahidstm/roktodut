<!doctype html>
<html lang="bn">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="theme-color" content="#dc2626" />
    <meta name="mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="default" />
    <meta name="apple-mobile-web-app-title" content="রক্তদূত" />
    <title>@yield('title', 'রক্তদূত')</title>
    <link rel="manifest" href="/manifest.json" />
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="icon" href="{{ asset('images/image_14.png') }}" type="image/png" sizes="any">
    <link rel="apple-touch-icon" href="{{ asset('images/image_14.png') }}">
    {{-- JS globals for Alpine + Echo (auth'd users only) --}}
    @auth
    <script>
        window.__userId      = {{ auth()->id() }};
        window.__unreadCount = {{ auth()->user()->unreadNotifications()->count() }};
    </script>
    @endauth

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css','resources/js/app.js'])

    <style>
        :root { font-family: 'Hind Siliguri', ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Arial; }
    </style>

    {{-- Per-page head assets (CDN scripts, extra CSS pushed by child views) --}}
    @stack('head')
</head>

<body class="bg-slate-50 text-slate-900 antialiased">
    @include('partials.header')

<main class="min-h-screen">
    @if (session('success'))
        <div class="max-w-6xl mx-auto px-4 mt-6">
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800 font-semibold flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="max-w-6xl mx-auto px-4 mt-6">
            <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-red-800 font-semibold flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                {{ session('error') }}
            </div>
        </div>
    @endif

    @yield('content')
</main>

@include('layouts.footer')

    {{-- Toast container for real-time notification toasts --}}
    <div id="notif-toast-container"
         class="fixed bottom-6 right-6 z-[9999] flex flex-col gap-3 pointer-events-none"
         aria-live="polite"
         aria-label="নোটিফিকেশন">
    </div>

    {{-- Per-page scripts pushed by child views (modals, WYSIWYG init, etc.) --}}
    @stack('scripts')

    <style>
        .scroll-reveal {
            opacity: 0;
            --sr-x: 0px;
            --sr-y: 24px;
            transform: translate3d(var(--sr-x), var(--sr-y), 0);
            transition: opacity 0.45s ease-out, transform 0.45s ease-out;
            will-change: opacity, transform;
        }
        .scroll-reveal--left {
            --sr-x: -24px;
            --sr-y: 0px;
        }
        .scroll-reveal--right {
            --sr-x: 24px;
            --sr-y: 0px;
        }
        .scroll-reveal.is-visible,
        [data-scroll-reveal].is-visible {
            opacity: 1;
            --sr-x: 0px;
            --sr-y: 0px;
        }
        .hover-lift:hover {
            transform: translate3d(var(--sr-x, 0px), calc(var(--sr-y, 0px) - 2px), 0);
        }
        @media (prefers-reduced-motion: reduce) {
            .scroll-reveal,
            [data-scroll-reveal] {
                opacity: 1 !important;
                transform: none !important;
                transition: none !important;
            }
        }
    </style>

    <script>
        window.applyHeroButtonStyles = function(root = document) {
            const candidates = root.querySelectorAll('a, button');
            candidates.forEach((el) => {
                if (el.dataset.btnStyled === '1' || el.hasAttribute('data-btn-skip')) return;
                if (!el.closest('main') || el.closest('header, nav, [data-skip-button-style], [role="menu"], [role="dialog"]')) return;
                if (!el.textContent || !el.textContent.trim()) return;
                const classes = Array.from(el.classList);
                const hasPadding = classes.some(c => /^p[trblxy]?-\d/.test(c));
                const hasRounded = classes.some(c => c.startsWith('rounded'));
                const hasShadow = classes.some(c => c.startsWith('shadow'));
                const hasBorder = classes.some(c => c === 'border' || c.startsWith('border-'));
                const hasBg = classes.some(c => c.startsWith('bg-'));
                const hasTextWhite = classes.includes('text-white');
                const looksButton = hasPadding || hasRounded || hasShadow || hasBorder || hasBg;
                if (!looksButton) return;

                if (classes.some(c => ['bg-red-600', 'bg-red-700', 'bg-rose-600', 'bg-rose-700', 'bg-rose-500'].includes(c))) {
                    el.classList.add('btn-primary');
                } else if (hasBg && hasTextWhite) {
                    el.classList.add('btn-primary');
                } else if (classes.some(c => c.startsWith('border-white')) || (hasTextWhite && !hasBg)) {
                    el.classList.add('btn-outline');
                } else if (classes.includes('bg-white') && hasBorder) {
                    el.classList.add('btn-secondary');
                } else if (hasBorder && !hasBg) {
                    el.classList.add('btn-secondary');
                } else {
                    el.classList.add('btn-secondary');
                }

                el.classList.add('hover-lift');
                el.dataset.btnStyled = '1';
            });
        };

        window.initScrollReveal = function(root = document) {
            const autoTargets = root.querySelectorAll('section');
            autoTargets.forEach((el) => {
                if (!el.hasAttribute('data-scroll-reveal')) {
                    el.setAttribute('data-scroll-reveal', '');
                    el.classList.add('scroll-reveal');
                }
            });
            const revealItems = root.querySelectorAll('[data-scroll-reveal]');
            window.applyHeroButtonStyles(root);
            if (!revealItems.length) return;
            const revealObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        revealObserver.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.05, rootMargin: '0px 0px -10% 0px' });
            revealItems.forEach(item => revealObserver.observe(item));
        };

        document.addEventListener('DOMContentLoaded', () => {
            window.applyHeroButtonStyles();
            window.initScrollReveal();
        });
    </script>

    {{-- Lenis smooth scrolling --}}
    <script src="https://unpkg.com/@studio-freight/lenis@1.0.42/bundled/lenis.min.js"></script>
    <script>
        (() => {
            const lenis = new Lenis({
                smoothWheel: true,
                normalizeWheel: true,
                syncTouch: true,
            });

            lenis.on('scroll', () => {
                window.dispatchEvent(new Event('scroll'));
            });

            function raf(time) {
                lenis.raf(time);
                requestAnimationFrame(raf);
            }

            requestAnimationFrame(raf);
        })();
    </script>

    @include('layouts.chatbot-widget')

    @auth
    <script type="module">
    (() => {
        const firebaseConfig = {
            apiKey: "{{ config('services.firebase.api_key') }}",
            authDomain: "{{ config('services.firebase.auth_domain') }}",
            projectId: "{{ config('services.firebase.project_id') }}",
            storageBucket: "{{ config('services.firebase.storage_bucket') }}",
            messagingSenderId: "{{ config('services.firebase.messaging_sender_id') }}",
            appId: "{{ config('services.firebase.app_id') }}",
            measurementId: "{{ config('services.firebase.measurement_id') }}"
        };

        const vapidKey = "{{ config('services.firebase.vapid_key') }}";
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
        const updateTokenUrl = "{{ route('profile.fcm-token.update') }}";

        const requiredFirebaseKeys = ['apiKey', 'authDomain', 'projectId', 'storageBucket', 'messagingSenderId', 'appId'];
        const hasFirebaseConfig = requiredFirebaseKeys.every((key) => {
            const value = firebaseConfig[key];
            return typeof value === 'string' && value.length > 0;
        });
        if (!hasFirebaseConfig || !vapidKey || !('Notification' in window) || !('serviceWorker' in navigator)) {
            return;
        }

        const initFcm = async () => {
            const [{ initializeApp }, { getMessaging, getToken, isSupported }] = await Promise.all([
                import('https://www.gstatic.com/firebasejs/12.3.0/firebase-app.js'),
                import('https://www.gstatic.com/firebasejs/12.3.0/firebase-messaging.js'),
            ]);

            const supported = await isSupported();
            if (!supported) {
                return;
            }

            const permission = await Notification.requestPermission();
            if (permission !== 'granted') {
                return;
            }

            const serviceWorkerRegistration = await navigator.serviceWorker.register('{{ route('firebase.messaging.sw') }}');
            const firebaseApp = initializeApp(firebaseConfig);
            const messaging = getMessaging(firebaseApp);

            const token = await getToken(messaging, {
                vapidKey,
                serviceWorkerRegistration,
            });

            if (!token || localStorage.getItem('fcm_token') === token) {
                return;
            }

            const response = await fetch(updateTokenUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                credentials: 'same-origin',
                body: JSON.stringify({ fcm_token: token }),
            });

            if (!response.ok) {
                throw new Error('Unable to store FCM token.');
            }

            localStorage.setItem('fcm_token', token);
        };

        window.addEventListener('load', () => {
            initFcm().catch((error) => {
                console.warn('[FCM] Token initialization failed:', error);
            });
        });
    })();
    </script>
    @endauth

    {{-- 📱 PWA: Install Prompt Banner --}}
    <div id="pwa-install-banner"
         style="display:none;"
         class="fixed bottom-0 left-0 right-0 z-[9990] p-4 sm:p-0 sm:bottom-6 sm:right-6 sm:left-auto">
        <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 p-4 sm:p-5 sm:w-80 flex items-start gap-4">
            <div class="shrink-0 w-12 h-12 rounded-xl overflow-hidden border border-slate-100">
                <img src="/images/image_14.png" alt="রক্তদূত" class="w-full h-full object-contain p-1">
            </div>
            <div class="flex-1 min-w-0">
                <p class="font-black text-slate-900 text-sm leading-tight">রক্তদূত ইনস্টল করুন</p>
                <p class="text-xs text-slate-500 font-medium mt-0.5 leading-snug">হোম স্ক্রিনে যোগ করুন — অফলাইনেও কাজ করবে!</p>
                <div class="flex items-center gap-2 mt-3">
                    <button id="pwa-install-btn"
                            class="flex-1 bg-red-600 hover:bg-red-700 text-white text-xs font-black py-2 px-3 rounded-lg transition-colors">
                        ইনস্টল করুন
                    </button>
                    <button id="pwa-dismiss-btn"
                            class="text-xs text-slate-400 hover:text-slate-600 font-bold transition-colors">
                        পরে
                    </button>
                </div>
            </div>
            <button id="pwa-close-btn" class="shrink-0 text-slate-300 hover:text-slate-500 transition-colors -mt-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </div>

    {{-- 📱 PWA: Service Worker Registration + Install Prompt Logic --}}
    <script>
    (() => {
        // ১. Service Worker রেজিস্ট্রেশন
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js', { scope: '/' })
                    .then(reg => console.log('[SW] Registered:', reg.scope))
                    .catch(err => console.warn('[SW] Registration failed:', err));
            });
        }

        // ২. Install Prompt (beforeinstallprompt event)
        let deferredPrompt = null;
        const banner    = document.getElementById('pwa-install-banner');
        const installBtn = document.getElementById('pwa-install-btn');
        const dismissBtn = document.getElementById('pwa-dismiss-btn');
        const closeBtn  = document.getElementById('pwa-close-btn');

        // ইতিমধ্যে dismiss করা হয়েছে কিনা চেক করবো (localStorage এ ১৪ দিন সেভ থাকবে)
        const dismissedStr = localStorage.getItem('pwa_dismissed_at');
        let shouldShow = true;
        
        if (dismissedStr) {
            const dismissedAt = parseInt(dismissedStr, 10);
            const fourteenDays = 14 * 24 * 60 * 60 * 1000;
            // যদি ১৪ দিন পার না হয়ে থাকে, তাহলে দেখাবো না
            if (Date.now() - dismissedAt < fourteenDays) {
                shouldShow = false;
            }
        }

        const isStandalone = window.matchMedia('(display-mode: standalone)').matches
                          || window.navigator.standalone === true;

        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;

            if (shouldShow && !isStandalone) {
                setTimeout(() => {
                    if (banner) banner.style.display = 'block';
                }, 3000); // 3 সেকেন্ড পর দেখাবে
            }
        });

        if (installBtn) {
            installBtn.addEventListener('click', async () => {
                if (!deferredPrompt) return;
                deferredPrompt.prompt();
                const { outcome } = await deferredPrompt.userChoice;
                if (outcome === 'accepted') {
                    if (banner) banner.style.display = 'none';
                    // ইনস্টল হলে আর কখনোই দেখাবো না, তাই ফিউচার ডেট দিয়ে রাখলাম
                    localStorage.setItem('pwa_dismissed_at', (Date.now() + 3650*24*60*60*1000).toString());
                }
                deferredPrompt = null;
            });
        }

        const hideBanner = () => {
            if (banner) banner.style.display = 'none';
            // ডিসমিস করলে বর্তমান সময় সেভ করে রাখবো, যেন ১৪ দিন পর আবার দেখাতে পারে
            localStorage.setItem('pwa_dismissed_at', Date.now().toString());
        };

        if (dismissBtn) dismissBtn.addEventListener('click', hideBanner);
        if (closeBtn) closeBtn.addEventListener('click', hideBanner);

        // ৩. App install হলে banner hide করা
        window.addEventListener('appinstalled', () => {
            banner.style.display = 'none';
            console.log('[PWA] App installed successfully!');
        });
    })();
    </script>

{{-- ══════════════════════════════════════════════════════════════════════
     🆘 Emergency SOS Widget — শুধুমাত্র লগইন করা ইউজারদের জন্য
     Floating pulsating button + modal with GPS + blood group dropdown
══════════════════════════════════════════════════════════════════════ --}}
@auth
<div id="sos-widget"
     x-data="{
         open: false,
         loading: false,
         gpsStatus: 'idle',  {{-- idle | fetching | ready | denied --}}
         lat: null,
         lng: null,
         bloodGroup: '{{ auth()->user()->blood_group instanceof \App\Enums\BloodGroup ? auth()->user()->blood_group->value : (string) auth()->user()->blood_group }}',
         bloodGroups: ['A+','A-','B+','B-','AB+','AB-','O+','O-'],
         resultMsg: '',
         resultType: '',  {{-- '' | success | error --}}

         openModal() {
             this.open = true;
             this.resultMsg = '';
             this.resultType = '';
             this.gpsStatus = 'fetching';
             this.lat = null;
             this.lng = null;
             this.fetchGps();
         },

         fetchGps() {
             if (!navigator.geolocation) {
                 this.gpsStatus = 'denied';
                 return;
             }
             navigator.geolocation.getCurrentPosition(
                 (pos) => {
                     this.lat = pos.coords.latitude;
                     this.lng = pos.coords.longitude;
                     this.gpsStatus = 'ready';
                 },
                 () => { this.gpsStatus = 'denied'; },
                 { timeout: 8000, maximumAge: 60000 }
             );
         },

         async sendSos() {
             if (this.loading) return;
             this.loading = true;
             this.resultMsg = '';
             try {
                 const body = {
                     blood_group: this.bloodGroup,
                     _token: document.querySelector('meta[name=csrf-token]').content,
                 };
                 if (this.lat !== null) body.latitude  = this.lat;
                 if (this.lng !== null) body.longitude = this.lng;

                 const res  = await fetch('{{ route('sos.trigger') }}', {
                     method: 'POST',
                     headers: { 'Content-Type': 'application/json', 'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
                     body: JSON.stringify(body)
                 });
                 const data = await res.json();

                 if (data.success) {
                     this.resultType = 'success';
                     this.resultMsg  = data.message;
                     setTimeout(() => {
                         this.open = false;
                         window.location.href = data.redirect_url;
                     }, 1800);
                 } else {
                     this.resultType = 'error';
                     this.resultMsg  = data.message || 'কোনো সমস্যা হয়েছে। আবার চেষ্টা করুন।';
                 }
             } catch (e) {
                 this.resultType = 'error';
                 this.resultMsg  = 'নেটওয়ার্ক সমস্যা। অনুগ্রহ করে আবার চেষ্টা করুন।';
             } finally {
                 this.loading = false;
             }
         }
     }">

    {{-- ── Floating Pulsating Button ─────────────────────────────── --}}
    <button @click="openModal()"
            aria-label="জরুরি রক্ত চাই SOS"
            class="fixed bottom-6 left-6 z-[9990] flex items-center gap-2
                   bg-red-600 hover:bg-red-700 active:scale-95
                   text-white text-sm font-black
                   px-4 py-3 rounded-full shadow-2xl
                   transition-all duration-200
                   ring-4 ring-red-600/30 animate-[sos-pulse_2s_ease-in-out_infinite]">
        <span class="text-base leading-none">🆘</span>
        <span class="hidden sm:inline">জরুরি রক্ত চাই</span>
    </button>

    {{-- ── Modal Backdrop ───────────────────────────────────────── --}}
    <div x-show="open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click.self="open = false"
         class="fixed inset-0 z-[9991] bg-slate-900/60 backdrop-blur-sm flex items-end sm:items-center justify-center p-4"
         x-cloak>

        {{-- ── Modal Panel ──────────────────────────────────────── --}}
        <div x-show="open"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-4 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 scale-95"
             class="w-full max-w-sm bg-white rounded-3xl shadow-2xl overflow-hidden">

            {{-- Red header --}}
            <div class="bg-red-600 px-6 py-5 text-white">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="text-3xl">🆘</span>
                        <div>
                            <h2 class="text-lg font-black leading-tight">Emergency SOS</h2>
                            <p class="text-red-100 text-xs font-medium mt-0.5">কাছের ডোনারদের এখনই নোটিফাই করুন</p>
                        </div>
                    </div>
                    <button @click="open = false" class="text-red-200 hover:text-white transition p-1 rounded-full hover:bg-red-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>

            {{-- Body --}}
            <div class="px-6 py-5 space-y-4">

                {{-- Blood Group Dropdown (editable!) --}}
                <div>
                    <label class="block text-xs font-black text-slate-600 uppercase tracking-wider mb-2">কোন রক্তের গ্রুপ প্রয়োজন?</label>
                    <div class="grid grid-cols-4 gap-2">
                        <template x-for="bg in bloodGroups" :key="bg">
                            <button type="button"
                                    @click="bloodGroup = bg"
                                    :class="bloodGroup === bg
                                        ? 'bg-red-600 text-white border-red-600 font-black shadow-md scale-105'
                                        : 'bg-white text-slate-700 border-slate-200 hover:border-red-300 hover:text-red-600'"
                                    class="border-2 rounded-xl py-2 text-sm font-bold transition-all duration-150">
                                <span x-text="bg"></span>
                            </button>
                        </template>
                    </div>
                </div>

                {{-- GPS Status --}}
                <div class="flex items-center gap-2.5 rounded-xl px-3 py-2.5 text-xs font-semibold"
                     :class="{
                         'bg-amber-50 text-amber-700 border border-amber-200': gpsStatus === 'fetching',
                         'bg-emerald-50 text-emerald-700 border border-emerald-200': gpsStatus === 'ready',
                         'bg-slate-50 text-slate-500 border border-slate-200': gpsStatus === 'denied' || gpsStatus === 'idle',
                     }">
                    <span x-show="gpsStatus === 'fetching'" class="shrink-0">📡</span>
                    <span x-show="gpsStatus === 'ready'"    class="shrink-0">📍</span>
                    <span x-show="gpsStatus === 'denied'"   class="shrink-0">⚠️</span>
                    <span x-show="gpsStatus === 'idle'"     class="shrink-0">🗺️</span>
                    <span x-show="gpsStatus === 'fetching'">GPS লোকেশন নেওয়া হচ্ছে…</span>
                    <span x-show="gpsStatus === 'ready'">GPS প্রস্তুত — সঠিক লোকেশন পাওয়া গেছে</span>
                    <span x-show="gpsStatus === 'denied'">GPS পাওয়া যায়নি — আপনার নিবন্ধিত জেলা ব্যবহার হবে</span>
                    <span x-show="gpsStatus === 'idle'">লোকেশন চেক করা হচ্ছে…</span>
                </div>

                {{-- Result Message --}}
                <div x-show="resultMsg !== ''"
                     x-transition
                     :class="resultType === 'success' ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : 'bg-red-50 border-red-200 text-red-700'"
                     class="border rounded-xl px-4 py-3 text-sm font-semibold"
                     x-text="resultMsg">
                </div>

                {{-- Info --}}
                <p class="text-[11px] text-slate-400 font-medium leading-snug">
                    🔒 আপনার নাম ও নম্বর স্বয়ংক্রিয়ভাবে যুক্ত হবে। কাছের সর্বোচ্চ ২০ জন ডোনারকে এখনই নোটিফাই করা হবে।
                </p>
            </div>

            {{-- Footer --}}
            <div class="px-6 pb-5 flex gap-3">
                <button @click="open = false"
                        class="flex-1 py-3 rounded-xl border border-slate-200 text-slate-600 font-bold text-sm hover:bg-slate-50 transition">
                    বাতিল
                </button>
                <button @click="sendSos()"
                        :disabled="loading"
                        :class="loading ? 'opacity-60 cursor-not-allowed' : 'hover:bg-red-700 active:scale-95'"
                        class="flex-1 py-3 rounded-xl bg-red-600 text-white font-black text-sm shadow-lg shadow-red-200 transition-all duration-150 flex items-center justify-center gap-2">
                    <svg x-show="loading" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                    <span x-show="!loading">🩸 এখনই পাঠাও!</span>
                    <span x-show="loading">পাঠানো হচ্ছে…</span>
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes sos-pulse {
        0%, 100% { box-shadow: 0 0 0 0 rgba(220, 38, 38, 0.5); }
        50%       { box-shadow: 0 0 0 12px rgba(220, 38, 38, 0); }
    }
    [x-cloak] { display: none !important; }
</style>
@endauth

</body>
</html>
