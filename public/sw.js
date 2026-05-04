// রক্তদূত Service Worker v1.0
// ─────────────────────────────────────────────────────────────────

const CACHE_NAME = 'roktodut-v1';
const OFFLINE_URL = '/offline';

// ক্যাশ করার ফাইলের তালিকা (App Shell)
const APP_SHELL = [
    '/',
    '/offline',
    '/manifest.json',
    '/images/image_14.png',
];

// ─────────────────────────────────────────────────────────────────
// Install: App Shell ক্যাশ করা
// ─────────────────────────────────────────────────────────────────
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll(APP_SHELL);
        }).catch((err) => {
            console.warn('[SW] Cache addAll failed (some resources may not be available offline):', err);
        })
    );
    self.skipWaiting();
});

// ─────────────────────────────────────────────────────────────────
// Activate: পুরনো cache পরিষ্কার করা
// ─────────────────────────────────────────────────────────────────
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames
                    .filter((name) => name !== CACHE_NAME)
                    .map((name) => caches.delete(name))
            );
        })
    );
    self.clients.claim();
});

// ─────────────────────────────────────────────────────────────────
// Fetch: Network First, Offline Fallback
// Static assets: Cache First
// ─────────────────────────────────────────────────────────────────
self.addEventListener('fetch', (event) => {
    const url = new URL(event.request.url);

    // ১. শুধু GET রিকোয়েস্ট হ্যান্ডেল করব (POST/PATCH ছেড়ে দেব)
    if (event.request.method !== 'GET') return;

    // ২. Chrome Extensions এবং non-http বাদ দেওয়া
    if (!url.protocol.startsWith('http')) return;

    // ৩. Static Assets → Cache First
    if (
        url.pathname.startsWith('/build/') ||
        url.pathname.startsWith('/images/') ||
        url.pathname.endsWith('.png') ||
        url.pathname.endsWith('.jpg') ||
        url.pathname.endsWith('.ico') ||
        url.pathname.endsWith('.woff2')
    ) {
        event.respondWith(
            caches.match(event.request).then((cached) => {
                return cached || fetch(event.request).then((response) => {
                    if (response.ok) {
                        const clone = response.clone();
                        caches.open(CACHE_NAME).then((cache) => cache.put(event.request, clone));
                    }
                    return response;
                });
            })
        );
        return;
    }

    // ৪. Navigation Requests → Network First, Offline Page Fallback
    if (event.request.mode === 'navigate') {
        event.respondWith(
            fetch(event.request).catch(() => {
                return caches.match(OFFLINE_URL) || caches.match('/');
            })
        );
        return;
    }
});

// ─────────────────────────────────────────────────────────────────
// Push Notification: ব্যাকগ্রাউন্ড নোটিফিকেশন দেখানো
// ─────────────────────────────────────────────────────────────────
self.addEventListener('push', (event) => {
    let data = {
        title: '🩸 রক্তদূত',
        body: 'একটি জরুরি রক্তের অনুরোধ এসেছে!',
        icon: '/images/image_14.png',
        badge: '/images/image_14.png',
        url: '/',
        tag: 'blood-request',
    };

    if (event.data) {
        try {
            const received = event.data.json();
            data = { ...data, ...received };
        } catch (e) {
            data.body = event.data.text();
        }
    }

    event.waitUntil(
        self.registration.showNotification(data.title, {
            body: data.body,
            icon: data.icon,
            badge: data.badge,
            tag: data.tag,
            data: { url: data.url },
            requireInteraction: true,
            vibrate: [200, 100, 200, 100, 200],
            actions: [
                { action: 'open', title: '🩸 দেখুন', icon: '/images/image_14.png' },
                { action: 'close', title: '✕ বন্ধ করুন' },
            ],
        })
    );
});

// ─────────────────────────────────────────────────────────────────
// Notification Click: সংশ্লিষ্ট পেজে নিয়ে যাওয়া
// ─────────────────────────────────────────────────────────────────
self.addEventListener('notificationclick', (event) => {
    event.notification.close();

    if (event.action === 'close') return;

    const url = event.notification.data?.url || '/';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then((clientList) => {
            // ইতিমধ্যে ট্যাব খোলা থাকলে সেটিতে ফোকাস করা
            for (const client of clientList) {
                if (client.url === url && 'focus' in client) {
                    return client.focus();
                }
            }
            // নতুন ট্যাব খোলা
            if (clients.openWindow) {
                return clients.openWindow(url);
            }
        })
    );
});
