/**
 * Centralized Toast Notification System
 * Prevents duplicate toasts and uses admin-configured settings
 */

window.ToastNotification = (function() {
    'use strict';

    // Active toasts tracker to prevent duplicates
    const activeToasts = new Set();
    
    // Default settings (will be overridden by admin settings)
    let settings = {
        position: 'top-right',
        duration: 3000,
        animation: 'slide',
        showIcon: true,
        closeButton: true,
        successColor: '#198754',
        errorColor: '#dc3545',
        warningColor: '#ffc107',
        infoColor: '#0dcaf0',
        primaryColor: '#0d6efd'
    };

    /**
     * Initialize with admin settings from backend
     */
    function init(adminSettings = {}) {
        settings = { ...settings, ...adminSettings };
    }

    /**
     * Get position CSS classes
     */
    function getPositionClasses(position) {
        const positions = {
            'top-left': 'top-0 start-0',
            'top-center': 'top-0 start-50 translate-middle-x',
            'top-right': 'top-0 end-0',
            'bottom-left': 'bottom-0 start-0',
            'bottom-center': 'bottom-0 start-50 translate-middle-x',
            'bottom-right': 'bottom-0 end-0',
            'center': 'top-50 start-50 translate-middle'
        };
        return positions[position] || positions['top-right'];
    }

    /**
     * Get icon for toast type
     */
    function getIcon(type) {
        if (!settings.showIcon) return '';
        
        const icons = {
            'success': 'bi-check-circle-fill',
            'error': 'bi-exclamation-circle-fill',
            'danger': 'bi-exclamation-circle-fill',
            'warning': 'bi-exclamation-triangle-fill',
            'info': 'bi-info-circle-fill',
            'primary': 'bi-bell-fill'
        };
        
        const iconClass = icons[type] || icons['info'];
        return `<i class="bi ${iconClass} me-2"></i>`;
    }

    /**
     * Get background color for type
     */
    function getColor(type) {
        const colors = {
            'success': settings.successColor,
            'error': settings.errorColor,
            'danger': settings.errorColor,
            'warning': settings.warningColor,
            'info': settings.infoColor,
            'primary': settings.primaryColor
        };
        return colors[type] || colors['info'];
    }

    /**
     * Show a toast notification
     * @param {string} message - The message to display
     * @param {string} type - Type: success, error, warning, info, primary
     * @param {number} duration - Override default duration (optional)
     */
    function show(message, type = 'success', duration = null) {
        // Prevent duplicate toasts
        const toastKey = `${type}:${message}`;
        if (activeToasts.has(toastKey)) {
            return null;
        }

        activeToasts.add(toastKey);

        // Create toast element
        const toastId = 'toast-' + Date.now() + '-' + Math.random().toString(36).substr(2, 9);
        const toastDuration = duration || settings.duration;
        const positionClasses = getPositionClasses(settings.position);
        const backgroundColor = getColor(type);
        const icon = getIcon(type);
        const closeBtn = settings.closeButton 
            ? '<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>'
            : '';

        const toastHtml = `
            <div id="${toastId}" class="toast align-items-center text-white border-0 shadow-lg" 
                 role="alert" aria-live="assertive" aria-atomic="true"
                 style="position:fixed;z-index:9999;${settings.position.includes('bottom') ? 'margin-bottom:1rem' : 'margin-top:1rem'};margin-left:1rem;margin-right:1rem;background-color:${backgroundColor}">
                <div class="d-flex">
                    <div class="toast-body fw-semibold">
                        ${icon}${message}
                    </div>
                    ${closeBtn}
                </div>
            </div>
        `;

        // Create container div
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = toastHtml.trim();
        const toastElement = tempDiv.firstElementChild;

        // Add animation class
        if (settings.animation === 'fade') {
            toastElement.classList.add('fade');
        }

        // Append to body
        document.body.appendChild(toastElement);

        // Initialize Bootstrap toast
        const bsToast = new bootstrap.Toast(toastElement, {
            delay: toastDuration,
            autohide: true
        });

        // Show toast
        bsToast.show();

        // Clean up after toast is hidden
        toastElement.addEventListener('hidden.bs.toast', function() {
            activeToasts.delete(toastKey);
            toastElement.remove();
        });

        return bsToast;
    }

    /**
     * Convenience methods
     */
    function success(message, duration) {
        return show(message, 'success', duration);
    }

    function error(message, duration) {
        return show(message, 'error', duration);
    }

    function warning(message, duration) {
        return show(message, 'warning', duration);
    }

    function info(message, duration) {
        return show(message, 'info', duration);
    }

    function primary(message, duration) {
        return show(message, 'primary', duration);
    }

    /**
     * Clear all active toasts
     */
    function clearAll() {
        document.querySelectorAll('[id^="toast-"]').forEach(el => {
            const toast = bootstrap.Toast.getInstance(el);
            if (toast) {
                toast.hide();
            }
        });
        activeToasts.clear();
    }

    // Public API
    return {
        init,
        show,
        success,
        error,
        warning,
        info,
        primary,
        clearAll
    };
})();

// Backward compatibility - keep old showToast function
window.showToast = function(message, type = 'success', duration) {
    return ToastNotification.show(message, type, duration);
};
