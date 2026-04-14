// Firebase Cloud Messaging Setup for Push Notifications
// ⚠️ IMPORTANT: Update these values from Firebase Console
// Visit: https://console.firebase.google.com/project/fire-base-dojo-9/settings/general

const firebaseConfig = {
    apiKey: "AIzaSyAFBjyUOQ1DIhcTMyqo46fP27eWfsU38_I",
    projectId: "fire-base-dojo-9",
    messagingSenderId: "767070636530",  // ⚠️ UPDATE: From Firebase Console → Cloud Messaging tab
    appId: "1:767070636530:web:e4f42e5..."  // ⚠️ UPDATE: COMPLETE App ID from Firebase Console → General tab
};

// ⚠️ NOTE: This file is a reference/template
// The actual Firebase config is loaded from Admin Panel settings via app.blade.php
// Update your Firebase credentials in: Admin Panel → Firebase & Analytics

// Initialize Firebase
if ('serviceWorker' in navigator) {
    // Request notification permission
    window.requestNotificationPermission = async function() {
        try {
            const permission = await Notification.requestPermission();
            
            if (permission === 'granted') {
                console.log('Notification permission granted');
                
                // Register service worker for FCM
                const registration = await navigator.serviceWorker.register('/firebase-messaging-sw.js');
                console.log('Service Worker registered:', registration);
                
                // Get FCM token (requires Firebase SDK)
                // NOTE: Install Firebase SDK first: npm install firebase
                // import { initializeApp } from 'firebase/app';
                // import { getMessaging, getToken } from 'firebase/messaging';
                // const app = initializeApp(firebaseConfig);
                // const messaging = getMessaging(app);
                // const token = await getToken(messaging, { vapidKey: 'YOUR_VAPID_KEY' });
                
                // Send token to server
                // await saveFCMToken(token);
                
            } else {
                console.log('Notification permission denied');
            }
        } catch (error) {
            console.error('Error requesting notification permission:', error);
        }
    };
}

// Function to save FCM token to your server
async function saveFCMToken(token) {
    try {
        const response = await fetch('/api/save-fcm-token', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ fcm_token: token })
        });
        
        if (response.ok) {
            console.log('FCM token saved successfully');
        }
    } catch (error) {
        console.error('Error saving FCM token:', error);
    }
}

// Call this when user logs in or on page load
// window.requestNotificationPermission();
