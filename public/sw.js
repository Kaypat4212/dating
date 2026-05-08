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
                // Only cache successful responses
                if (!response || response.status !== 200 || response.type === 'error') {
                    return response;
                }
                
                // Cache successful navigation responses
                if (event.request.mode === 'navigate') {
                    const cloned = response.clone();
                    caches.open(CACHE_NAME).then(function (cache) {
                        cache.put(event.request, cloned);
                    });
                }
                return response;
            })
            .catch(function () {
                // Try to return cached version
                return caches.match(event.request).then(function (cached) {
                    if (cached) return cached;
                    
                    // Fallback to offline page for navigation requests
                    if (event.request.mode === 'navigate') {
                        return caches.match(OFFLINE_URL).then(function (offlinePage) {
                            return offlinePage || new Response('Service unavailable', { status: 503 });
                        });
                    }
                    
                    // For other requests, return error response
                    return new Response('Network request failed', { status: 503 });
                });
            })
    );
});

// Handle badge updates from the main thread
self.addEventListener('message', function (event) {
    if (event.data && event.data.type === 'UPDATE_BADGE') {
        const count = event.data.count || 0;
        
        try {
            if (count > 0 && typeof self.registration.badge !== 'undefined') {
                self.registration.badge = count.toString();
            }
        } catch (err) {
            // Badge API not supported, silently fail
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
            try {
                event.waitUntil(
                    fetch('/api/unread-messages-count')
                        .then(response => response.json())
                        .then(result => {
                            const count = result.count || 0;
                            if (count > 0 && typeof self.registration.badge !== 'undefined') {
                                self.registration.badge = count.toString();
                            }
                        })
                        .catch(err => console.error('Failed to update badge:', err))
                );
            } catch (err) {
                // Badge API not supported
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
