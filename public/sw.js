// HeartsConnect Service Worker — network-first, offline-fallback
const CACHE_NAME = 'hc-app-v1';
const OFFLINE_URL = '/offline';

self.addEventListener('install', function (event) {
    self.skipWaiting();
});

self.addEventListener('activate', function (event) {
    event.waitUntil(clients.claim());
});

self.addEventListener('fetch', function (event) {
    // Only handle GET requests for same-origin navigation / assets
    if (event.request.method !== 'GET') return;
    if (!event.request.url.startsWith(self.location.origin)) return;

    event.respondWith(
        fetch(event.request)
            .then(function (response) {
                // Cache successful navigation responses briefly
                if (response.ok && event.request.mode === 'navigate') {
                    const cloned = response.clone();
                    caches.open(CACHE_NAME).then(function (cache) {
                        cache.put(event.request, cloned);
                    });
                }
                return response;
            })
            .catch(function () {
                return caches.match(event.request).then(function (cached) {
                    return cached || caches.match(OFFLINE_URL);
                });
            })
    );
});
