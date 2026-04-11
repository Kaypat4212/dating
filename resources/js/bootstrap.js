import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Laravel Echo setup for real-time broadcasting
 * Supports both Pusher (cloud) and Reverb (self-hosted)
 */
import Echo from 'laravel-echo';

const broadcaster = import.meta.env.VITE_BROADCAST_DRIVER || 'pusher';

if (broadcaster === 'reverb') {
    // Reverb (Laravel's WebSocket server)
    window.Echo = new Echo({
        broadcaster: 'reverb',
        key: import.meta.env.VITE_REVERB_APP_KEY,
        wsHost: import.meta.env.VITE_REVERB_HOST,
        wsPort: import.meta.env.VITE_REVERB_PORT ?? 443,
        wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
        forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
        enabledTransports: ['ws', 'wss'],
    });
    console.log('🟢 Laravel Echo initialized with Reverb');
} else {
    // Pusher (cloud service)
    import('pusher-js').then((Pusher) => {
        window.Pusher = Pusher.default;
        window.Echo = new Echo({
            broadcaster: 'pusher',
            key: import.meta.env.VITE_PUSHER_APP_KEY,
            cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER ?? 'us3',
            forceTLS: true,
            encrypted: true,
        });
        console.log('🔴 Laravel Echo initialized with Pusher');
    });
}
