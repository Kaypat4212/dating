// Firebase Cloud Messaging Service Worker
// This file handles background push notifications

importScripts('https://www.gstatic.com/firebasejs/10.7.1/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/10.7.1/firebase-messaging-compat.js');

// Initialize Firebase in the service worker
// ⚠️ UPDATE THESE VALUES:
// 1. Go to Firebase Console: https://console.firebase.google.com/project/fire-base-dojo-9/settings/general
// 2. Copy messagingSenderId and appId from "Your apps" section
// 3. Replace the values below
firebase.initializeApp({
    apiKey: "AIzaSyAFBjyUOQ1DIhcTMyqo46fP27eWfsU38_I",
    projectId: "fire-base-dojo-9",
    messagingSenderId: "767070636530",  // ⚠️ UPDATE: Get from Firebase Console → Cloud Messaging
    appId: "1:767070636530:web:e4f42e5..."  // ⚠️ UPDATE: Get COMPLETE App ID from Firebase Console → General
});

const messaging = firebase.messaging();

// Handle background messages
messaging.onBackgroundMessage((payload) => {
    console.log('Received background message:', payload);
    
    const notificationTitle = payload.notification.title || 'New Notification';
    const notificationOptions = {
        body: payload.notification.body || '',
        icon: payload.notification.icon || '/favicon.ico',
        badge: '/favicon.ico',
        data: payload.data,
        tag: payload.data?.type || 'general'
    };

    return self.registration.showNotification(notificationTitle, notificationOptions);
});

// Handle notification clicks
self.addEventListener('notificationclick', (event) => {
    console.log('Notification clicked:', event);
    event.notification.close();

    // Open the app or navigate to specific page
    const urlToOpen = event.notification.data?.url || '/';
    
    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true })
            .then((clientList) => {
                // Check if app is already open
                for (const client of clientList) {
                    if (client.url === urlToOpen && 'focus' in client) {
                        return client.focus();
                    }
                }
                // Open new window if app is not open
                if (clients.openWindow) {
                    return clients.openWindow(urlToOpen);
                }
            })
    );
});
