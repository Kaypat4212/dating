/**
 * Real-time In-App Notifications System
 * 
 * Listens to Laravel Echo broadcasts and displays toast notifications
 * Updates unread badges in real-time
 */

export class RealtimeNotifications {
    constructor() {
        this.userId = null;
        this.toastContainer = null;
        this.unreadBadges = [];
        this.initialized = false;
    }

    /**
     * Initialize real-time notifications for authenticated user
     */
    init() {
        if (this.initialized) return;

        // Get user ID from meta tag
        const userIdMeta = document.querySelector('meta[name="user-id"]');
        if (!userIdMeta) {
            console.log('No user-id meta tag found - user not authenticated');
            return;
        }

        this.userId = userIdMeta.content;
        
        // Setup toast container
        this.setupToastContainer();
        
        // Find all unread count badge elements
        this.findUnreadBadges();

        // Start listening for notifications
        if (window.Echo) {
            this.listenForNotifications();
            this.initialized = true;
            console.log('✅ Real-time notifications initialized for user', this.userId);
        } else {
            console.warn('Laravel Echo not available - real-time notifications disabled');
        }
    }

    /**
     * Setup or find the toast container
     */
    setupToastContainer() {
        this.toastContainer = document.getElementById('toastContainer');
        if (!this.toastContainer) {
            this.toastContainer = document.createElement('div');
            this.toastContainer.id = 'toastContainer';
            this.toastContainer.className = 'position-fixed bottom-0 end-0 p-3';
            this.toastContainer.style.zIndex = '1040';
            this.toastContainer.style.paddingBottom = 'calc(.75rem + 62px)';
            document.body.appendChild(this.toastContainer);
        }
    }

    /**
     * Find all unread notification badge elements
     */
    findUnreadBadges() {
        this.unreadBadges = [
            document.querySelector('a[href*="notifications"] .badge'),
            document.querySelector('#navBellBadge'),
            document.querySelector('#mobileNavBellBadge'),
        ].filter(el => el); // Remove nulls
    }

    /**
     * Listen for real-time notifications via Laravel Echo
     */
    listenForNotifications() {
        // Listen on private user channel
        window.Echo.private(`App.Models.User.${this.userId}`)
            .notification((notification) => {
                console.log('🔔 Received notification:', notification);
                this.handleNotification(notification);
            });

        console.log(`🎧 Listening on channel: App.Models.User.${this.userId}`);
    }

    /**
     * Handle incoming notification
     */
    handleNotification(notification) {
        // Show toast
        this.showToast(notification);
        
        // Update badge
        this.incrementUnreadBadge();
        
        // Play sound (optional)
        this.playNotificationSound();
    }

    /**
     * Display toast notification
     */
    showToast(notification) {
        const config = this.getNotificationConfig(notification);
        
        const toastHtml = `
            <div class="toast align-items-center text-bg-${config.variant} border-0 shadow-lg" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body d-flex align-items-center gap-2 fw-semibold">
                        <i class="bi ${config.icon} fs-5"></i>
                        <div>
                            <div class="fw-bold">${config.title}</div>
                            <small>${config.message}</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `;

        const wrapper = document.createElement('div');
        wrapper.innerHTML = toastHtml;
        this.toastContainer.appendChild(wrapper);

        const toastElement = wrapper.querySelector('.toast');
        const toast = new window.bootstrap.Toast(toastElement, {
            autohide: true,
            delay: 5000
        });

        toast.show();

        // Click to navigate
        if (config.url) {
            toastElement.style.cursor = 'pointer';
            toastElement.addEventListener('click', (e) => {
                if (!e.target.closest('.btn-close')) {
                    window.location.href = config.url;
                }
            });
        }

        // Remove from DOM after hidden
        toastElement.addEventListener('hidden.bs.toast', () => {
            wrapper.remove();
        });
    }

    /**
     * Get notification configuration (icon, color, message)
     */
    getNotificationConfig(notification) {
        const type = notification.type || '';
        const data = notification.data || notification;

        // Configuration map
        const configs = {
            'new_match': {
                icon: 'bi-hearts',
                variant: 'danger',
                title: "It's a Match! 💕",
                message: data.message || 'You have a new match!',
                url: data.url
            },
            'new_message': {
                icon: 'bi-chat-heart-fill',
                variant: 'primary',
                title: 'New Message 💬',
                message: data.message || `${data.sender_name || 'Someone'} sent you a message`,
                url: data.url
            },
            'profile_liked': {
                icon: 'bi-hand-thumbs-up-fill',
                variant: 'warning',
                title: 'Profile Liked! 😍',
                message: data.message || 'Someone liked your profile',
                url: data.url
            },
            'profile_viewed': {
                icon: 'bi-eye-fill',
                variant: 'info',
                title: 'Profile View 👀',
                message: data.message || `${data.viewer_name || 'Someone'} viewed your profile`,
                url: data.url
            },
            'wave_received': {
                icon: 'bi-hand-wave',
                variant: 'warning',
                title: 'Wave Received! 👋',
                message: data.message || `${data.sender_name || 'Someone'} sent you a wave`,
                url: data.url
            },
            'premium_purchased': {
                icon: 'bi-star-fill',
                variant: 'success',
                title: 'Premium Active! ⭐',
                message: data.message || 'Your premium subscription is now active',
                url: data.url
            },
            'tip_received': {
                icon: 'bi-coin',
                variant: 'success',
                title: 'Tip Received! 💰',
                message: data.message || 'You received a tip',
                url: data.url
            }
        };

        const notifType = data.type || type.split('\\').pop().replace('Notification', '').toLowerCase();
        
        return configs[notifType] || {
            icon: 'bi-bell-fill',
            variant: 'info',
            title: 'New Notification',
            message: data.message || 'You have a new notification',
            url: data.url
        };
    }

    /**
     * Increment unread notification badge
     */
    incrementUnreadBadge() {
        this.unreadBadges.forEach(badge => {
            if (!badge) return;
            
            const current = parseInt(badge.textContent) || 0;
            const newCount = current + 1;
            
            badge.textContent = newCount > 99 ? '99+' : newCount;
            badge.classList.remove('d-none');
        });
    }

    /**
     * Update unread badge to specific count (useful after marking as read)
     */
    updateUnreadBadge(count) {
        this.unreadBadges.forEach(badge => {
            if (!badge) return;
            
            if (count <= 0) {
                badge.classList.add('d-none');
            } else {
                badge.textContent = count > 99 ? '99+' : count;
                badge.classList.remove('d-none');
            }
        });
    }

    /**
     * Play notification sound (optional, can be toggled in settings)
     */
    playNotificationSound() {
        // Check if user has sounds enabled (you can add this preference later)
        const soundEnabled = localStorage.getItem('notification_sound') !== 'false';
        
        if (soundEnabled) {
            // Use a subtle notification sound
            const audio = new Audio('/sounds/notification.mp3');
            audio.volume = 0.3;
            audio.play().catch(() => {
                // Ignore errors (browser might block autoplay)
            });
        }
    }

    /**
     * Manually show a notification (useful for testing)
     */
    testNotification() {
        this.showToast({
            type: 'new_match',
            data: {
                message: 'This is a test notification',
                url: '/notifications'
            }
        });
    }
}

// Export singleton instance
export const notifications = new RealtimeNotifications();

// Auto-initialize on DOM ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => notifications.init());
} else {
    notifications.init();
}
