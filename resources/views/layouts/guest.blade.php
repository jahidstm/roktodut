<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
        <link rel="icon" href="{{ asset('images/image_14.png') }}" type="image/png" sizes="any">
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
            <div>
                <a href="/">
                    <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
                {{ $slot }}
            </div>
        </div>

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
                }, { threshold: 0.2, rootMargin: '0px 0px -10% 0px' });
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
    </body>
</html>
