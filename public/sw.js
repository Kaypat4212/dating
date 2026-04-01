// HeartsConnect Service Worker — network-first, offline-fallback + Badge API
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

// Handle badge updates from the main thread
self.addEventListener('message', function (event) {
    if (event.data && event.data.type === 'UPDATE_BADGE') {
        const count = event.data.count || 0;
        
        if ('setAppBadge' in self.navigator) {
            if (count > 0) {
                self.navigator.setAppBadge(count);
            } else {
                self.navigator.clearAppBadge();
            }
        }
    }
});

// Update badge when push notification received
self.addEventListener('push', function (event) {
    if (!event.data) return;

    try {
        const data = event.data.json();
        
        // If it's a message notification, increment badge
        if (data.type === 'message' || data.notification_type === 'new_message') {
            if ('setAppBadge' in self.navigator) {
                // Fetch current unread count from server
                event.waitUntil(
                    fetch('/api/unread-messages-count')
                        .then(response => response.json())
                        .then(result => {
                            const count = result.count || 0;
                            if (count > 0) {
                                self.navigator.setAppBadge(count);
                            }
                        })
                        .catch(err => console.error('Failed to update badge:', err))
                );
            }
        }

        // Show notification
        const title = data.title || 'New Notification';
        const options = {
            body: data.body || '',
            icon: data.icon || '/favicon.svg',
            badge: data.badge || '/favicon.svg',
            tag: data.tag || 'notification',
            requireInteraction: data.requireInteraction || false,
            data: data
        };

        event.waitUntil(
            self.registration.showNotification(title, options)
        );
    } catch (error) {
        console.error('Push notification error:', error);
    }
});

// Handle notification click
self.addEventListener('notificationclick', function (event) {
    event.notification.close();

    const urlToOpen = event.notification.data?.url || '/dashboard';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true })
            .then(function (clientList) {
                // Check if app is already open
                for (const client of clientList) {
                    if (client.url === urlToOpen && 'focus' in client) {
                        return client.focus();
                    }
                }
                // Open new window if not found
                if (clients.openWindow) {
                    return clients.openWindow(urlToOpen);
                }
            })
    );
});
