/**
 * PWA Badge API Handler
 * Updates the app icon badge with unread message count
 * Works on: Chromium-based browsers (Chrome, Edge, Opera), Android, some Progressive Web Apps
 */

class PWABadgeManager {
    constructor() {
        this.isSupported = 'setAppBadge' in navigator;
        this.currentCount = 0;
    }

    /**
     * Set the badge count on the PWA icon
     * @param {number} count - Number to display (0 clears the badge)
     */
    async setBadge(count) {
        if (!this.isSupported) {
            console.log('Badge API not supported on this device');
            return false;
        }

        try {
            if (count > 0) {
                await navigator.setAppBadge(count);
                this.currentCount = count;
                console.log(`✅ PWA badge set to: ${count}`);
            } else {
                await navigator.clearAppBadge();
                this.currentCount = 0;
                console.log('✅ PWA badge cleared');
            }
            return true;
        } catch (error) {
            console.error('❌ Failed to set PWA badge:', error);
            return false;
        }
    }

    /**
     * Clear the badge
     */
    async clearBadge() {
        return await this.setBadge(0);
    }

    /**
     * Increment the badge count
     * @param {number} increment - Amount to add
     */
    async incrementBadge(increment = 1) {
        return await this.setBadge(this.currentCount + increment);
    }

    /**
     * Decrement the badge count
     * @param {number} decrement - Amount to subtract
     */
    async decrementBadge(decrement = 1) {
        const newCount = Math.max(0, this.currentCount - decrement);
        return await this.setBadge(newCount);
    }
}

// Initialize the badge manager
const pwaBadge = new PWABadgeManager();

/**
 * Fetch unread message count from server and update badge
 */
async function updateMessageBadge() {
    try {
        const response = await fetch('/api/unread-messages-count', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }

        const data = await response.json();
        const unreadCount = data.count || 0;

        // Update PWA badge
        await pwaBadge.setBadge(unreadCount);

        // Update DOM badge elements (if they exist)
        updateDOMBadges(unreadCount);

        return unreadCount;
    } catch (error) {
        console.error('❌ Failed to fetch unread message count:', error);
        return null;
    }
}

/**
 * Update badge elements in the DOM
 * @param {number} count - Unread message count
 */
function updateDOMBadges(count) {
    // Mobile badge
    const mobileBadge = document.getElementById('msgBadgeMobile');
    if (mobileBadge) {
        if (count > 0) {
            mobileBadge.textContent = count > 99 ? '99+' : count;
            mobileBadge.classList.remove('d-none');
        } else {
            mobileBadge.classList.add('d-none');
        }
    }

    // Desktop badge (adjust selector to match your layout)
    const desktopBadge = document.getElementById('msgBadgeDesktop');
    if (desktopBadge) {
        if (count > 0) {
            desktopBadge.textContent = count > 99 ? '99+' : count;
            desktopBadge.classList.remove('d-none');
        } else {
            desktopBadge.classList.add('d-none');
        }
    }

    // Update document title with count
    if (count > 0) {
        const baseTitle = document.title.replace(/^\(\d+\)\s*/, '');
        document.title = `(${count}) ${baseTitle}`;
    } else {
        document.title = document.title.replace(/^\(\d+\)\s*/, '');
    }
}

/**
 * Listen for new messages via Laravel Echo/Pusher
 */
function listenForMessages() {
    if (typeof Echo === 'undefined') {
        console.warn('Laravel Echo not loaded - real-time badge updates disabled');
        return;
    }

    // Listen to user's private channel for message notifications
    const userId = document.querySelector('meta[name="user-id"]')?.content;
    if (!userId) {
        console.log('User not authenticated - skipping message listener');
        return;
    }

    Echo.private(`App.Models.User.${userId}`)
        .notification((notification) => {
            if (notification.type === 'App\\Notifications\\NewMessage') {
                console.log('🔔 New message received!');
                // Increment badge immediately
                pwaBadge.incrementBadge();
                // Then fetch actual count from server to be sure
                setTimeout(updateMessageBadge, 500);
            }
        });

    console.log('✅ Listening for new messages on user channel');
}

/**
 * Initialize badge updates
 */
function initializeBadgeUpdates() {
    // Only run if user is authenticated
    if (!document.querySelector('meta[name="csrf-token"]')) {
        return;
    }

    // Initial badge update
    updateMessageBadge();

    // Update badge every 30 seconds (fallback for missed events)
    setInterval(updateMessageBadge, 30000);

    // Listen for real-time updates
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', listenForMessages);
    } else {
        listenForMessages();
    }

    // Clear badge when viewing messages page
    if (window.location.pathname.includes('/messages') || window.location.pathname.includes('/conversations')) {
        pwaBadge.clearBadge();
    }

    // Update when window regains focus
    window.addEventListener('focus', () => {
        updateMessageBadge();
    });

    // Clear badge when app is installed as PWA and opened
    window.addEventListener('appinstalled', () => {
        console.log('✅ PWA installed - badge support enabled');
        updateMessageBadge();
    });

    console.log('✅ PWA Badge Manager initialized');
}

// Auto-initialize on page load
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeBadgeUpdates);
} else {
    initializeBadgeUpdates();
}

// Export for manual control
window.pwaBadge = pwaBadge;
window.updateMessageBadge = updateMessageBadge;
