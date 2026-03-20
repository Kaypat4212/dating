// ============================================================
//  HeartsConnect – Main JavaScript Entry Point
// ============================================================

import './bootstrap';

// Bootstrap 5 JS bundle (includes Popper)
import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;

// Alpine.js
import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();

// Laravel Echo + Reverb (WebSockets)
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
window.Pusher = Pusher;

if (import.meta.env.VITE_REVERB_APP_KEY) {
    window.Echo = new Echo({
        broadcaster: 'reverb',
        key: import.meta.env.VITE_REVERB_APP_KEY,
        wsHost: import.meta.env.VITE_REVERB_HOST ?? 'localhost',
        wsPort: import.meta.env.VITE_REVERB_PORT ?? 8080,
        wssPort: import.meta.env.VITE_REVERB_PORT ?? 8080,
        forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'http') === 'https',
        enabledTransports: ['ws', 'wss'],
    });
}

// ---- Dark mode toggle ----
const stored = localStorage.getItem('theme');
if (stored) document.documentElement.setAttribute('data-bs-theme', stored);

document.addEventListener('DOMContentLoaded', () => {
    const toggle = document.getElementById('darkModeToggle');
    if (toggle) {
        toggle.addEventListener('click', () => {
            const current = document.documentElement.getAttribute('data-bs-theme') ?? 'light';
            const next = current === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-bs-theme', next);
            localStorage.setItem('theme', next);
            toggle.innerHTML = next === 'dark'
                ? '<i class="bi bi-sun-fill"></i>'
                : '<i class="bi bi-moon-stars-fill"></i>';
        });
    }

    // ---- Real-time notifications (if user is logged in) ----
    const userId = document.querySelector('meta[name="user-id"]')?.content;
    if (userId && window.Echo) {
        window.Echo.private(`App.Models.User.${userId}`)
            .notification((notification) => {
                showNotificationToast(notification);
                incrementNotificationBadge();
            });
    }

    // ---- Tooltips ----
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
        new bootstrap.Tooltip(el);
    });

    // ---- Popovers ----
    document.querySelectorAll('[data-bs-toggle="popover"]').forEach(el => {
        new bootstrap.Popover(el);
    });
});

// ---- Notification toast helper ----
function showNotificationToast(notification) {
    const container = document.getElementById('toast-container');
    if (!container) return;

    const icons = {
        'App\\Notifications\\NewMatchNotification': 'bi-hearts text-danger',
        'App\\Notifications\\NewMessageNotification': 'bi-chat-heart text-primary',
        'App\\Notifications\\ProfileLikedNotification': 'bi-hand-thumbs-up text-warning',
        'App\\Notifications\\ProfileViewedNotification': 'bi-eye text-info',
    };
    const icon = icons[notification.type] ?? 'bi-bell';

    const wrapper = document.createElement('div');
    wrapper.innerHTML = `
        <div class="toast align-items-center border-0 show shadow" role="alert">
            <div class="d-flex">
                <div class="toast-body d-flex align-items-center gap-2">
                    <i class="bi ${icon} fs-5"></i>
                    <span>${notification.message ?? 'You have a new notification!'}</span>
                </div>
                <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>`;
    container.appendChild(wrapper);

    const toastEl = wrapper.querySelector('.toast');
    const toast = new bootstrap.Toast(toastEl, { delay: 5000 });
    toast.show();
    toastEl.addEventListener('hidden.bs.toast', () => wrapper.remove());
}

function incrementNotificationBadge() {
    const badge = document.getElementById('notif-badge');
    if (!badge) return;
    const current = parseInt(badge.textContent) || 0;
    badge.textContent = current + 1;
    badge.classList.remove('d-none');
}

// ---- Match celebration confetti ----
export async function celebrateMatch() {
    const { default: confetti } = await import('canvas-confetti');
    confetti({
        particleCount: 150,
        spread: 90,
        colors: ['#c2185b', '#7b1fa2', '#f48fb1', '#ce93d8', '#fff'],
        origin: { y: 0.6 },
    });
    setTimeout(() => confetti({
        particleCount: 80,
        angle: 60,
        spread: 55,
        origin: { x: 0 },
        colors: ['#c2185b', '#fff'],
    }), 400);
    setTimeout(() => confetti({
        particleCount: 80,
        angle: 120,
        spread: 55,
        origin: { x: 1 },
        colors: ['#7b1fa2', '#fff'],
    }), 700);
}

window.celebrateMatch = celebrateMatch;

