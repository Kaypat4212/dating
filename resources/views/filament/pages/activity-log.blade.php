<x-filament-panels::page>

@pushOnce('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
@endPushOnce

<div>

<style>
:root {
    --primary-gradient: linear-gradient(135deg, #f43f5e 0%, #a855f7 100%);
    --card-bg: #1a1625;
    --success-color: #10b981;
    --warning-color: #f59e0b;
    --danger-color: #ef4444;
}

body {
    background: #0f0a1a;
}

/* Stat Cards */
.stat-card {
    background: linear-gradient(135deg, rgba(30, 10, 46, 0.9), rgba(45, 16, 80, 0.7));
    border: 1px solid rgba(244, 63, 94, 0.15);
    border-radius: 16px;
    padding: 1.5rem;
    transition: all 0.3s ease;
    height: 100%;
}

.stat-card:hover {
    transform: translateY(-4px);
    border-color: rgba(244, 63, 94, 0.4);
    box-shadow: 0 8px 24px rgba(244, 63, 94, 0.15);
}

/* Filter Bar */
.filter-bar {
    background: linear-gradient(145deg, rgba(30, 10, 46, 0.8), rgba(45, 16, 80, 0.6));
    border: 1px solid rgba(244, 63, 94, 0.2);
    border-radius: 16px;
    padding: 1.5rem;
}

/* Table Styling */
.activity-table {
    background: linear-gradient(145deg, rgba(30, 10, 46, 0.95), rgba(45, 16, 80, 0.85));
    border: 1px solid rgba(244, 63, 94, 0.2);
    border-radius: 20px;
    overflow: hidden;
}

.table-header {
    background: rgba(244, 63, 94, 0.1);
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.activity-row {
    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    transition: all 0.2s ease;
}

.activity-row:hover {
    background: rgba(255, 255, 255, 0.03);
}

/* Action Badges */
.action-badge {
    display: inline-flex;
    align-items-center;
    gap: 0.4rem;
    padding: 0.35rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
}

.action-badge-login {
    background: rgba(59, 130, 246, 0.15);
    border: 1px solid rgba(59, 130, 246, 0.3);
    color: #93c5fd;
}

.action-badge-like {
    background: rgba(244, 63, 94, 0.15);
    border: 1px solid rgba(244, 63, 94, 0.3);
    color: #fda4af;
}

.action-badge-message {
    background: rgba(168, 85, 247, 0.15);
    border: 1px solid rgba(168, 85, 247, 0.3);
    color: #d8b4fe;
}

.action-badge-match {
    background: rgba(236, 72, 153, 0.15);
    border: 1px solid rgba(236, 72, 153, 0.3);
    color: #f9a8d4;
}

.action-badge-report {
    background: rgba(239, 68, 68, 0.18);
    border: 1px solid rgba(239, 68, 68, 0.35);
    color: #fca5a5;
}

.action-badge-photo {
    background: rgba(20, 184, 166, 0.15);
    border: 1px solid rgba(20, 184, 166, 0.3);
    color: #5eead4;
}

.action-badge-profile {
    background: rgba(250, 204, 21, 0.12);
    border: 1px solid rgba(250, 204, 21, 0.25);
    color: #fde68a;
}

.action-badge-premium {
    background: rgba(245, 158, 11, 0.18);
    border: 1px solid rgba(245, 158, 11, 0.35);
    color: #fcd34d;
}

.action-badge-block {
    background: rgba(107, 114, 128, 0.2);
    border: 1px solid rgba(107, 114, 128, 0.35);
    color: #d1d5db;
}

.action-badge-other {
    background: rgba(255, 255, 255, 0.07);
    border: 1px solid rgba(255, 255, 255, 0.12);
    color: rgba(255, 255, 255, 0.5);
}

/* Flag Badges */
.flag-badge {
    padding: 0.25rem 0.6rem;
    border-radius: 12px;
    font-size: 0.7rem;
    font-weight: 600;
}

.flag-suspicious {
    background: rgba(239, 68, 68, 0.18);
    border: 1px solid rgba(239, 68, 68, 0.35);
    color: #fca5a5;
}

.flag-spam {
    background: rgba(234, 179, 8, 0.18);
    border: 1px solid rgba(234, 179, 8, 0.35);
    color: #fde047;
}

/* User Avatar */
.user-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
}

.user-avatar-fallback {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content-center;
    font-weight: 700;
    font-size: 0.9rem;
    color: white;
}

/* Custom Scrollbar */
.table-scroll {
    max-height: 70vh;
    overflow-y: auto;
    scrollbar-width: thin;
    scrollbar-color: rgba(244, 63, 94, 0.3) transparent;
}

.table-scroll::-webkit-scrollbar {
    width: 8px;
}

.table-scroll::-webkit-scrollbar-track {
    background: transparent;
}

.table-scroll::-webkit-scrollbar-thumb {
    background: rgba(244, 63, 94, 0.3);
    border-radius: 10px;
}

/* Live Update Animation */
@keyframes newRowFlash {
    0%, 100% { background: rgba(16, 185, 129, 0); }
    50% { background: rgba(16, 185, 129, 0.2); }
}

.new-row-flash {
    animation: newRowFlash 1.5s ease-in-out;
}
</style>

@php
    $stats = $this->getStats();
    $activities = $this->getActivities();
    $actionTypes = $this->getActionTypes();
@endphp

{{-- Real-time Toast Container --}}
<div id="toast-container" class="position-fixed top-0 end-0 p-3" style="z-index: 9999;"></div>

<div class="container-fluid px-4 py-4">
    
    {{-- Page Header --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="display-6 fw-bold text-white mb-2">
                        <i class="bi bi-clipboard-data me-2" style="color: #f43f5e;"></i>
                        Activity Log
                    </h1>
                    <p class="text-white-50 mb-0">Real-time platform activity monitoring and moderation</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-light btn-sm" wire:loading.attr="disabled">
                        <i class="bi bi-arrow-clockwise"></i> Refresh
                    </button>
                    <div class="badge bg-success px-3 py-2">
                        <i class="bi bi-broadcast"></i> Live Updates
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Stats Dashboard --}}
    <div class="row g-3 mb-4">
        <div class="col-md-2">
            <div class="stat-card">
                <div class="text-center">
                    <div class="mb-2">
                        <i class="bi bi-activity fs-1" style="color: #a855f7;"></i>
                    </div>
                    <p class="text-white-50 text-uppercase small mb-1 fw-semibold">Actions Today</p>
                    <h3 class="text-white fw-bold mb-0">{{ number_format($stats['total_today']) }}</h3>
                </div>
            </div>
        </div>
        
        <div class="col-md-2">
            <div class="stat-card">
                <div class="text-center">
                    <div class="mb-2">
                        <i class="bi bi-key fs-1" style="color: #3b82f6;"></i>
                    </div>
                    <p class="text-white-50 text-uppercase small mb-1 fw-semibold">Logins Today</p>
                    <h3 class="text-white fw-bold mb-0">{{ number_format($stats['logins_today']) }}</h3>
                </div>
            </div>
        </div>
        
        <div class="col-md-2">
            <div class="stat-card">
                <div class="text-center">
                    <div class="mb-2">
                        <i class="bi bi-chat-dots fs-1" style="color: #d946ef;"></i>
                    </div>
                    <p class="text-white-50 text-uppercase small mb-1 fw-semibold">Messages Today</p>
                    <h3 class="text-white fw-bold mb-0">{{ number_format($stats['messages_today']) }}</h3>
                </div>
            </div>
        </div>
        
        <div class="col-md-2">
            <div class="stat-card">
                <div class="text-center">
                    <div class="mb-2">
                        <i class="bi bi-flag fs-1" style="color: #ef4444;"></i>
                    </div>
                    <p class="text-white-50 text-uppercase small mb-1 fw-semibold">Reports Today</p>
                    <h3 class="text-white fw-bold mb-0">{{ number_format($stats['reports_today']) }}</h3>
                </div>
            </div>
        </div>
        
        <div class="col-md-2">
            <div class="stat-card">
                <div class="text-center">
                    <div class="mb-2">
                        <i class="bi bi-exclamation-triangle fs-1" style="color: #f59e0b;"></i>
                    </div>
                    <p class="text-white-50 text-uppercase small mb-1 fw-semibold">Suspicious</p>
                    <h3 class="text-white fw-bold mb-0">{{ number_format($stats['suspicious']) }}</h3>
                </div>
            </div>
        </div>
        
        <div class="col-md-2">
            <div class="stat-card">
                <div class="text-center">
                    <div class="mb-2">
                        <i class="bi bi-shield-exclamation fs-1" style="color: #ec4899;"></i>
                    </div>
                    <p class="text-white-50 text-uppercase small mb-1 fw-semibold">Flagged Users</p>
                    <h3 class="text-white fw-bold mb-0">{{ number_format($stats['flagged_users']) }}</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="filter-bar mb-4">
        <div class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label text-white-50 small fw-semibold mb-2">
                    <i class="bi bi-search"></i> Search User
                </label>
                <div class="input-group">
                    <span class="input-group-text bg-dark border-0" style="background: rgba(255, 255, 255, 0.05) !important;">
                        <i class="bi bi-search text-white-50"></i>
                    </span>
                    <input type="text" 
                           class="form-control bg-dark border-0 text-white"
                           style="background: rgba(255, 255, 255, 0.05) !important;"
                           placeholder="Search by name or email..."
                           wire:model.live.debounce.400ms="search">
                </div>
            </div>
            
            <div class="col-md-2">
                <label class="form-label text-white-50 small fw-semibold mb-2">
                    <i class="bi bi-filter"></i> Action Type
                </label>
                <select class="form-select bg-dark border-0 text-white" 
                        style="background: rgba(255, 255, 255, 0.07) !important;"
                        wire:model.live="filterAction">
                    <option value="">All Actions</option>
                    @foreach($actionTypes as $type)
                        <option value="{{ $type }}">{{ ucwords(str_replace('_', ' ', $type)) }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-md-2">
                <label class="form-label text-white-50 small fw-semibold mb-2">
                    <i class="bi bi-flag"></i> Flag Status
                </label>
                <select class="form-select bg-dark border-0 text-white" 
                        style="background: rgba(255, 255, 255, 0.07) !important;"
                        wire:model.live="filterFlag">
                    <option value="">All Flags</option>
                    <option value="suspicious">⚠️ Suspicious</option>
                    <option value="spam">🔴 Spam</option>
                </select>
            </div>
            
            <div class="col-md-2">
                <label class="form-label text-white-50 small fw-semibold mb-2">
                    <i class="bi bi-list-ol"></i> Per Page
                </label>
                <select class="form-select bg-dark border-0 text-white" 
                        style="background: rgba(255, 255, 255, 0.07) !important;"
                        wire:model.live="perPage">
                    <option value="25">25 / page</option>
                    <option value="50">50 / page</option>
                    <option value="100">100 / page</option>
                </select>
            </div>
            
            <div class="col-md-2">
                @if($search || $filterAction || $filterFlag)
                    <button class="btn btn-outline-danger w-100" 
                            wire:click="$set('search', ''); $set('filterAction', ''); $set('filterFlag', '')">
                        <i class="bi bi-x-circle"></i> Clear Filters
                    </button>
                @endif
            </div>
        </div>
    </div>

    {{-- Activity Table --}}
    <div class="activity-table">
        {{-- Table Header --}}
        <div class="table-header p-3">
            <div class="row text-white-50 fw-semibold small text-uppercase">
                <div class="col-md-3">
                    <i class="bi bi-person"></i> User
                </div>
                <div class="col-md-2">
                    <i class="bi bi-lightning"></i> Action
                </div>
                <div class="col-md-2">
                    <i class="bi bi-router"></i> IP Address
                </div>
                <div class="col-md-1">
                    <i class="bi bi-flag"></i> Flag
                </div>
                <div class="col-md-3">
                    <i class="bi bi-info-circle"></i> Meta Data
                </div>
                <div class="col-md-1 text-end">
                    <i class="bi bi-clock"></i> Time
                </div>
            </div>
        </div>

        {{-- Table Body with Scroll --}}
        <div class="table-scroll">
            @forelse($activities as $log)
                @php
                    $user = $log->user;
                    $meta = $log->meta ?? [];
                    $metaStr = '';
                    if (!empty($meta)) {
                        $parts = [];
                        foreach(array_slice($meta, 0, 3, true) as $k => $v) {
                            if (is_scalar($v)) $parts[] = str_replace('_', ' ', $k) . ': ' . $v;
                        }
                        $metaStr = implode(' · ', $parts);
                    }
                    
                    // Determine badge class
                    $actionLower = strtolower($log->action);
                    $badgeClass = 'action-badge-other';
                    $icon = '⚡';
                    
                    if (str_contains($actionLower, 'login')) {
                        $badgeClass = 'action-badge-login';
                        $icon = '🔑';
                    } elseif (str_contains($actionLower, 'like')) {
                        $badgeClass = 'action-badge-like';
                        $icon = '❤️';
                    } elseif (str_contains($actionLower, 'message')) {
                        $badgeClass = 'action-badge-message';
                        $icon = '💬';
                    } elseif (str_contains($actionLower, 'match')) {
                        $badgeClass = 'action-badge-match';
                        $icon = '💞';
                    } elseif (str_contains($actionLower, 'report')) {
                        $badgeClass = 'action-badge-report';
                        $icon = '🚩';
                    } elseif (str_contains($actionLower, 'photo')) {
                        $badgeClass = 'action-badge-photo';
                        $icon = '📷';
                    } elseif (str_contains($actionLower, 'profile')) {
                        $badgeClass = 'action-badge-profile';
                        $icon = '👁️';
                    } elseif (str_contains($actionLower, 'premium')) {
                        $badgeClass = 'action-badge-premium';
                        $icon = '⭐';
                    } elseif (str_contains($actionLower, 'block')) {
                        $badgeClass = 'action-badge-block';
                        $icon = '🚫';
                    }
                @endphp
                
                <div class="activity-row p-3" data-log-id="{{ $log->id }}">
                    <div class="row align-items-center">
                        {{-- User --}}
                        <div class="col-md-3">
                            @if($user)
                                <div class="d-flex align-items-center gap-2">
                                    <div class="user-avatar-fallback flex-shrink-0" 
                                         style="background: linear-gradient(135deg, {{ $user->is_suspicious ? '#ef4444' : '#7c3aed' }}, {{ $user->is_suspicious ? '#b91c1c' : '#a855f7' }});">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div class="flex-grow-1 min-w-0">
                                        <a href="{{ route('filament.admin.resources.users.edit', $user->id) }}" 
                                           class="text-white text-decoration-none fw-semibold d-block text-truncate">
                                            {{ $user->name }}
                                            @if($user->is_suspicious)
                                                <i class="bi bi-exclamation-triangle-fill text-warning ms-1"></i>
                                            @endif
                                        </a>
                                        <small class="text-white-50 d-block text-truncate">{{ $user->email }}</small>
                                    </div>
                                </div>
                            @else
                                <span class="text-white-50 fst-italic small">
                                    <i class="bi bi-person-x"></i> Deleted user
                                </span>
                            @endif
                        </div>

                        {{-- Action --}}
                        <div class="col-md-2">
                            <span class="action-badge {{ $badgeClass }}">
                                <span>{{ $icon }}</span>
                                {{ ucwords(str_replace('_', ' ', $log->action)) }}
                            </span>
                        </div>

                        {{-- IP Address --}}
                        <div class="col-md-2">
                            <code class="text-white-50 small">{{ $log->ip_address ?? 'N/A' }}</code>
                        </div>

                        {{-- Flag --}}
                        <div class="col-md-1">
                            @if($log->flag)
                                <span class="flag-badge {{ $log->flag === 'suspicious' ? 'flag-suspicious' : 'flag-spam' }}">
                                    {{ $log->flag === 'suspicious' ? '⚠️' : '🔴' }}
                                    {{ ucfirst($log->flag) }}
                                </span>
                            @else
                                <span class="text-white-50 small">—</span>
                            @endif
                        </div>

                        {{-- Meta --}}
                        <div class="col-md-3">
                            @if($metaStr)
                                <small class="text-white-50">{{ $metaStr }}</small>
                            @else
                                <small class="text-white-50 fst-italic">No metadata</small>
                            @endif
                        </div>

                        {{-- Time --}}
                        <div class="col-md-1 text-end">
                            <small class="text-white-50" title="{{ $log->created_at->format('Y-m-d H:i:s') }}">
                                {{ $log->created_at->diffForHumans(null, true) }}
                            </small>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-5">
                    <i class="bi bi-inbox fs-1 text-white-50 mb-3 d-block"></i>
                    <p class="text-white-50 mb-0">No activity logs found</p>
                    <small class="text-white-50">Try adjusting your filters</small>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($activities->hasPages())
            <div class="p-3" style="border-top: 1px solid rgba(255, 255, 255, 0.1);">
                {{ $activities->links() }}
            </div>
        @endif
    </div>
</div>

@pushOnce('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endPushOnce
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('✅ Activity Log real-time monitoring initialized');
    
    // Toast notification system
    function showToast(title, message, type = 'info') {
        const toastId = 'toast-' + Date.now();
        const bgClass = type === 'success' ? 'bg-success' : 
                       type === 'danger' ? 'bg-danger' : 
                       type === 'warning' ? 'bg-warning' : 'bg-primary';
        
        const toastHtml = `
            <div id="${toastId}" class="toast align-items-center ${bgClass} text-white border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body">
                        <strong>${title}</strong><br>${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `;
        
        document.getElementById('toast-container').insertAdjacentHTML('beforeend', toastHtml);
        const toastElement = document.getElementById(toastId);
        const toast = new bootstrap.Toast(toastElement, { delay: 5000 });
        toast.show();
        
        toastElement.addEventListener('hidden.bs.toast', () => toastElement.remove());
    }
    
    // Listen for Livewire updates
    Livewire.hook('morph.updated', ({ el, component }) => {
        // Flash animation for new rows
        setTimeout(() => {
            document.querySelectorAll('.activity-row').forEach((row, index) => {
                if (index === 0) {
                    row.classList.add('new-row-flash');
                    setTimeout(() => row.classList.remove('new-row-flash'), 1500);
                }
            });
        }, 100);
    });
    
    // Auto-refresh every 30 seconds (configurable)
    const autoRefreshInterval = 30000; // 30 seconds
    let refreshTimer;
    
    function startAutoRefresh() {
        refreshTimer = setInterval(() => {
            Livewire.emit('refreshComponent');
            console.log('Auto-refreshed activity log');
        }, autoRefreshInterval);
    }
    
    // Start auto-refresh
    // startAutoRefresh(); // Uncomment to enable auto-refresh
    
    // Cleanup on page unload
    window.addEventListener('beforeunload', () => {
        if (refreshTimer) clearInterval(refreshTimer);
    });
    
    // Laravel Echo real-time broadcasting with Reverb
    if (window.Echo) {
        console.log('🔴 Connecting to activity log channels...');
        
        // Listen for new activity events
        window.Echo.channel('activity-log')
            .listen('.new.activity', (e) => {
                console.log('New activity:', e);
                showToast('📊 New Activity', e.message || 'New user action recorded', 'info');
                // Refresh the activity log
                Livewire.emit('refreshComponent');
            });
        
        // Listen for moderation alerts
        window.Echo.channel('moderation')
            .listen('.suspicious.activity', (e) => {
                console.log('Suspicious activity detected:', e);
                showToast('⚠️ Suspicious Activity', e.message || 'Flagged activity detected', 'warning');
                // Refresh to show flagged activity
                Livewire.emit('refreshComponent');
            });
        
        console.log('✅ Activity log real-time channels connected');
    } else {
        console.warn('⚠️ Laravel Echo not available - real-time features disabled');
    }
});
</script>
</div>
</x-filament-panels::page>
