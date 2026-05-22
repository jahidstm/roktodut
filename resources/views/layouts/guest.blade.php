<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="icon" href="{{ asset('images/image_14.png') }}" type="image/png">
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
                transform: translateY(24px);
                transition: opacity 0.8s ease, transform 0.8s ease;
                will-change: opacity, transform;
            }
            .scroll-reveal--left {
                transform: translateX(-24px);
            }
            .scroll-reveal--right {
                transform: translateX(24px);
            }
            .scroll-reveal.is-visible,
            [data-scroll-reveal].is-visible {
                opacity: 1;
                transform: translateY(0);
            }
            .scroll-reveal--left.is-visible,
            .scroll-reveal--right.is-visible {
                transform: translateX(0);
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
            window.initScrollReveal = function(root = document) {
                const autoTargets = root.querySelectorAll('section');
                autoTargets.forEach((el) => {
                    if (!el.hasAttribute('data-scroll-reveal')) {
                        el.setAttribute('data-scroll-reveal', '');
                        el.classList.add('scroll-reveal');
                    }
                });
                const revealItems = root.querySelectorAll('[data-scroll-reveal]');
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
