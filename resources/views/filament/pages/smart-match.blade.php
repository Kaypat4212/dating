<x-filament-panels::page>

{{-- Include Bootstrap CSS --}}
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

<style>
:root {
    --primary-gradient: linear-gradient(135deg, #f43f5e 0%, #a855f7 100%);
    --card-bg: #1a1625;
    --card-border: rgba(244, 63, 94, 0.2);
    --hover-bg: rgba(255, 255, 255, 0.05);
}

body {
    background: #0f0a1a;
}

/* Real-time Online Status Indicator */
.online-status {
    width: 12px;
    height: 12px;
    background: #22c55e;
    border-radius: 50%;
    border: 2px solid #1a1625;
    animation: pulse-glow 2s infinite;
}

@keyframes pulse-glow {
    0%, 100% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7); }
    50% { box-shadow: 0 0 0 6px rgba(34, 197, 94, 0); }
}

/* Typing Indicator */
.typing-indicator {
    display: inline-flex;
    align-items: center;
    gap: 3px;
}

.typing-dot {
    width: 6px;
    height: 6px;
    background: #a855f7;
    border-radius: 50%;
    animation: typing-bounce 1.4s infinite;
}

.typing-dot:nth-child(2) { animation-delay: 0.2s; }
.typing-dot:nth-child(3) { animation-delay: 0.4s; }

@keyframes typing-bounce {
    0%, 60%, 100% { transform: translate Y(0); }
    30% { transform: translateY(-10px); }
}

/* Stat Cards */
.stat-card {
    background: linear-gradient(135deg, rgba(30, 10, 46, 0.9), rgba(45, 16, 80, 0.7));
    border: 1px solid rgba(244, 63, 94, 0.15);
    border-radius: 16px;
    padding: 1.5rem;
    transition: all 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-4px);
    border-color: rgba(244, 63, 94, 0.4);
    box-shadow: 0 8px 24px rgba(244, 63, 94, 0.15);
}

/* Member Sidebar */
.member-list {
    max-height: calc(100vh - 300px);
    overflow-y: auto;
    scrollbar-width: thin;
    scrollbar-color: rgba(244, 63, 94, 0.3) transparent;
}

.member-list::-webkit-scrollbar {
    width: 6px;
}

.member-list::-webkit-scrollbar-track {
    background: transparent;
}

.member-list::-webkit-scrollbar-thumb {
    background: rgba(244, 63, 94, 0.3);
    border-radius: 10px;
}

.member-item {
    background: transparent;
    border: none;
    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    padding: 1rem;
    transition: all 0.2s ease;
    cursor: pointer;
    width: 100%;
    text-align: left;
}

.member-item:hover {
    background: rgba(255, 255, 255, 0.05);
}

.member-item.active {
    background: linear-gradient(135deg, rgba(244, 63, 94, 0.15), rgba(168, 85, 247, 0.1));
    border-left: 4px solid #f43f5e;
}

/* Match Cards */
.match-card {
    background: linear-gradient(145deg, rgba(30, 10, 46, 0.95), rgba(45, 16, 80, 0.85));
    border: 1px solid rgba(244, 63, 94, 0.2);
    border-radius: 20px;
    overflow: hidden;
    transition: all 0.3s ease;
}

.match-card:hover {
    transform: translateY(-8px) scale(1.02);
    box-shadow: 0 20px 40px rgba(244, 63, 94, 0.3);
    border-color: rgba(244, 63, 94, 0.5);
}

/* Score Ring */
.score-ring {
    stroke-dasharray: 283;
    stroke-linecap: round;
    transform: rotate(-90deg);
    transform-origin: center;
    transition: stroke-dashoffset 1s ease;
}

/* Match Button */
.match-btn {
    background: linear-gradient(135deg, #f43f5e, #a855f7);
    border: none;
    padding: 12px 24px;
    border-radius: 12px;
    color: white;
    font-weight: 600;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.match-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    transition: left 0.5s;
}

.match-btn:hover::before {
    left: 100%;
}

.match-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(244, 63, 94, 0.5);
}

/* Toast Notifications */
.toast-notification {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
    min-width: 300px;
    animation: slideIn 0.3s ease;
}

@keyframes slideIn {
    from { transform: translateX(400px); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}

/* Empty State */
.empty-state {
    background: linear-gradient(135deg, rgba(30, 10, 46, 0.6), rgba(45, 16, 80, 0.4));
    border: 2px dashed rgba(244, 63, 94, 0.3);
    border-radius: 20px;
    padding: 4rem 2rem;
    text-align: center;
}

/* Badge Styles */
.badge-premium {
    background: linear-gradient(90deg, #f59e0b, #f97316);
    color: white;
    font-weight: 700;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
}

.badge-verified {
    background: rgba(14, 165, 233, 0.2);
    border: 1px solid rgba(14, 165, 233, 0.4);
    color: #7dd3fc;
    font-weight: 600;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
}
</style>

@php $newUsers = $this->getNewUsers(); @endphp

{{-- Real-time Toast Container --}}
<div id="toast-container" class="position-fixed top-0 end-0 p-3" style="z-index: 9999;"></div>

<div class="container-fluid px-4 py-4">
    
    {{-- Page Header --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="display-6 fw-bold text-white mb-2">
                        <i class="bi bi-stars me-2" style="color: #f43f5e;"></i>
                        Smart Match
                    </h1>
                    <p class="text-white-50 mb-0">AI-powered compatibility matching for new members</p>
                </div>
                <div class="badge bg-success px-3 py-2">
                    <i class="bi bi-broadcast"></i> Real-time Enabled
                </div>
            </div>
        </div>
    </div>

    {{-- Stats Bar --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="stat-card">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="rounded-circle d-flex align-items-center justify-content-center" 
                             style="width: 60px; height: 60px; background: linear-gradient(135deg, #f43f5e, #a855f7);">
                            <i class="bi bi-people-fill text-white fs-4"></i>
                        </div>
                    </div>
                    <div class="ms-3">
                        <p class="text-white-50 text-uppercase small mb-1 fw-semibold">New Members</p>
                        <h3 class="text-white fw-bold mb-0">{{ $newUsers->count() }}</h3>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="stat-card">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="rounded-circle d-flex align-items-center justify-content-center" 
                             style="width: 60px; height: 60px; background: linear-gradient(135deg, #10b981, #0d9488);">
                            <i class="bi bi-stars text-white fs-4"></i>
                        </div>
                    </div>
                    <div class="ms-3">
                        <p class="text-white-50 text-uppercase small mb-1 fw-semibold">AI Scoring</p>
                        <h3 class="text-white fw-bold mb-0">Top 10</h3>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="stat-card">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="rounded-circle d-flex align-items-center justify-content-center" 
                             style="width: 60px; height: 60px; background: linear-gradient(135deg, #3b82f6, #2563eb);">
                            <i class="bi bi-heart-fill text-white fs-4"></i>
                        </div>
                    </div>
                    <div class="ms-3">
                        <p class="text-white-50 text-uppercase small mb-1 fw-semibold">Admin Match</p>
                        <h3 class="text-white fw-bold mb-0">Force & Notify</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content Area --}}
    <div class="row g-4">
        
        {{-- Left Sidebar: Member List --}}
        <div class="col-lg-3">
            <div class="card border-0" style="background: linear-gradient(145deg, rgba(30, 10, 46, 0.95), rgba(45, 16, 80, 0.85)); border: 1px solid rgba(244, 63, 94, 0.25) !important; border-radius: 20px;">
                <div class="card-header border-0 py-3" style="background: rgba(244, 63, 94, 0.1); border-bottom: 1px solid rgba(255, 255, 255, 0.1) !important;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white fw-bold mb-0">New Members</h6>
                            <small class="text-white-50">Last 7 days</small>
                        </div>
                        <span class="badge rounded-pill" style="background: linear-gradient(135deg, #f43f5e, #a855f7);">
                            {{ $newUsers->count() }}
                        </span>
                    </div>
                </div>
                
                <div class="member-list">
                    @forelse($newUsers as $nu)
                        @php
                            $photoUrl = $nu->primaryPhoto?->thumbnail_url;
                            $active = $focusUserId === $nu->id;
                            $isOnline = isset($nu->last_active_at) && \Carbon\Carbon::parse($nu->last_active_at)->diffInMinutes() < 30;
                        @endphp
                        
                        <button
                            wire:click="selectUser({{ $nu->id }})"
                            wire:loading.class="opacity-50"
                            class="member-item {{ $active ? 'active' : '' }}"
                            data-user-id="{{ $nu->id }}"
                        >
                            <div class="d-flex align-items-center">
                                <div class="position-relative flex-shrink-0">
                                    @if($photoUrl)
                                        <img src="{{ $photoUrl }}" alt="{{ $nu->name }}" 
                                             class="rounded-circle" 
                                             style="width: 48px; height: 48px; object-fit: cover; border: 2px solid {{ $active ? '#f43f5e' : 'rgba(255,255,255,0.2)' }};">
                                    @else
                                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold"
                                             style="width: 48px; height: 48px; background: linear-gradient(135deg, #2d1050, #4a0e6e); border: 2px solid {{ $active ? '#f43f5e' : 'rgba(255,255,255,0.2)' }};">
                                            {{ strtoupper(substr($nu->name, 0, 1)) }}
                                        </div>
                                    @endif
                                    @if($isOnline)
                                        <span class="online-status position-absolute bottom-0 end-0"></span>
                                    @endif
                                </div>
                                
                                <div class="ms-3 flex-grow-1 text-start">
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <span class="text-white fw-semibold" style="font-size: 0.9rem; {{ $active ? 'color: #fb7185 !important;' : '' }}">
                                            {{ $nu->name }}
                                        </span>
                                        @if($nu->is_premium)
                                            <span class="badge badge-premium" style="font-size: 0.65rem;">★</span>
                                        @endif
                                        @if($nu->is_verified)
                                            <span class="badge-verified" style="font-size: 0.65rem;">✓</span>
                                        @endif
                                    </div>
                                    <small class="text-white-50">
                                        {{ ucfirst($nu->gender ?? '—') }}{{ $nu->age ? ' · ' . $nu->age . ' yrs' : '' }}
                                    </small>
                                    <br>
                                    <small class="text-white-50" style="font-size: 0.75rem;">
                                        {{ $nu->created_at->diffForHumans() }}
                                    </small>
                                </div>
                                
                                @if($active)
                                    <i class="bi bi-chevron-right text-danger"></i>
                                @endif
                            </div>
                        </button>
                    @empty
                        <div class="text-center py-5">
                            <i class="bi bi-people fs-1 text-white-50 mb-3"></i>
                            <p class="text-white-50 mb-0">No members found</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Right Content: Selected User & Matches --}}
        <div class="col-lg-9">
            @if(!$focusUserId)
                {{-- Empty State --}}
                <div class="empty-state">
                    <div class="mb-4">
                        <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                             style="width: 100px; height: 100px; background: radial-gradient(circle, rgba(244, 63, 94, 0.2), rgba(168, 85, 247, 0.1)); border: 2px solid rgba(244, 63, 94, 0.3);">
                            <i class="bi bi-stars" style="font-size: 3rem; color: rgba(244, 63, 94, 0.6);"></i>
                        </div>
                    </div>
                    <h3 class="text-white fw-bold mb-3">Select a member to begin</h3>
                    <p class="text-white-50 mb-4">Pick any new member from the sidebar to see their AI-powered compatibility suggestions.</p>
                    <div class="d-flex justify-content-center gap-4 mt-4">
                        <div class="text-center">
                            <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-2"
                                 style="width: 50px; height: 50px; background: rgba(244, 63, 94, 0.15);">
                                <span style="font-size: 1.5rem;">🧠</span>
                            </div>
                            <p class="text-white-50 small mb-0">AI Scoring</p>
                        </div>
                        <div class="text-center">
                            <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-2"
                                 style="width: 50px; height: 50px; background: rgba(168, 85, 247, 0.15);">
                                <span style="font-size: 1.5rem;">💞</span>
                            </div>
                            <p class="text-white-50 small mb-0">Auto-match</p>
                        </div>
                        <div class="text-center">
                            <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-2"
                                 style="width: 50px; height: 50px; background: rgba(16, 185, 129, 0.15);">
                                <span style="font-size: 1.5rem;">⚡</span>
                            </div>
                            <p class="text-white-50 small mb-0">Instant notifications</p>
                        </div>
                    </div>
                </div>
            @else
                @php
                    $focusUser = \App\Models\User::with(['profile.interests', 'primaryPhoto'])->find($focusUserId);
                    $focusPhoto = $focusUser?->primaryPhoto;
                @endphp

                @if($focusUser)
                    {{-- Focus User Hero Card --}}
                    <div class="card border-0 mb-4" style="background: linear-gradient(145deg, rgba(30, 10, 46, 0.98), rgba(45, 16, 80, 0.95)); border: 1px solid rgba(244, 63, 94, 0.4) !important; border-radius: 20px; box-shadow: 0 10px 40px rgba(244, 63, 94, 0.2);">
                        <div style="height: 4px; background: linear-gradient(90deg, #f43f5e, #a855f7, #3b82f6);"></div>
                        <div class="card-body p-4">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    @if($focusPhoto)
                                        <img src="{{ $focusPhoto->thumbnail_url }}" alt="{{ $focusUser->name }}"
                                             class="rounded-3"
                                             style="width: 100px; height: 100px; object-fit: cover; border: 3px solid #f43f5e; box-shadow: 0 8px 24px rgba(244, 63, 94, 0.4);">
                                    @else
                                        <div class="rounded-3 d-flex align-items-center justify-content-center text-white fw-bold"
                                             style="width: 100px; height: 100px; font-size: 2.5rem; background: linear-gradient(135deg, #f43f5e, #a855f7); box-shadow: 0 8px 24px rgba(244, 63, 94, 0.4);">
                                            {{ strtoupper(substr($focusUser->name, 0, 1)) }}
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="col">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <h2 class="text-white fw-bold mb-0">{{ $focusUser->name }}</h2>
                                        @if($focusUser->is_premium)
                                            <span class="badge-premium">★ PREMIUM</span>
                                        @endif
                                        @if($focusUser->is_verified)
                                            <span class="badge-verified">✓ VERIFIED</span>
                                        @endif
                                    </div>
                                    <div class="text-white-50 mb-2">
                                        <span>{{ ucfirst($focusUser->gender ?? 'Unknown') }}</span>
                                        @if($focusUser->age)<span> · {{ $focusUser->age }} years old</span>@endif
                                        <span> · Seeking <strong class="text-white">{{ ucfirst($focusUser->seeking ?? 'everyone') }}</strong></span>
                                        @if($focusUser->profile?->city)
                                            <span> · <i class="bi bi-geo-alt"></i> {{ $focusUser->profile->city }}{{ $focusUser->profile->country ? ', ' . $focusUser->profile->country : '' }}</span>
                                        @endif
                                    </div>
                                    @if($focusUser->profile?->headline)
                                        <p class="text-white-50 fst-italic mb-0 small">"{{ Str::limit($focusUser->profile->headline, 100) }}"</p>
                                    @endif
                                </div>
                                
                                <div class="col-auto text-end">
                                    <p class="text-white-50 text-uppercase small fw-semibold mb-1" style="font-size: 0.7rem;">Joined</p>
                                    <p class="text-white fw-bold mb-1">{{ $focusUser->created_at->format('M d, Y') }}</p>
                                    <p class="text-white-50 small mb-0">{{ $focusUser->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Compatibility Matches --}}
                    @if($suggestions->isEmpty())
                        <div class="empty-state">
                            <span style="font-size: 4rem;">🤷</span>
                            <h4 class="text-white fw-bold mt-3 mb-2">No compatible candidates found</h4>
                            <p class="text-white-50 mb-0">All suitable users may already be matched, or this user's profile needs more data.</p>
                        </div>
                    @else
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h5 class="text-white fw-bold mb-0">
                                    <span class="fs-4">{{ $suggestions->count() }}</span> compatibility {{ Str::plural('match', $suggestions->count()) }} found
                                </h5>
                            </div>
                            <div class="d-flex gap-3 small text-white-50">
                                <span><span class="badge bg-success"></span> ≥70% excellent</span>
                                <span><span class="badge bg-warning"></span> ≥40% good</span>
                                <span><span class="badge bg-secondary"></span> <40% fair</span>
                            </div>
                        </div>

                        <div class="row g-4">
                            @foreach($suggestions as $rank => $item)
                                @php
                                    $candidate = $item['user'];
                                    $score = $item['score'];
                                    $candPhoto = $candidate->primaryPhoto;
                                    $interests = $candidate->profile?->interests ?? collect();
                                    
                                    [$barColor, $badgeBg] = match(true) {
                                        $score >= 70 => ['#10b981', 'success'],
                                        $score >= 40 => ['#f59e0b', 'warning'],
                                        default => ['#6b7280', 'secondary'],
                                    };
                                    
                                    $sharedInterests = array_intersect(
                                        $focusUser->profile?->interests->pluck('name')->toArray() ?? [],
                                        $interests->pluck('name')->toArray()
                                    );
                                @endphp

                                <div class="col-md-6 col-xl-4">
                                    <div class="match-card h-100 d-flex flex-column">
                                        {{-- Card Image/Header --}}
                                        @if($candPhoto)
                                            <div class="position-relative" style="height: 200px;">
                                                <img src="{{ $candPhoto->thumbnail_url }}" alt="{{ $candidate->name }}"
                                                     class="w-100 h-100" style="object-fit: cover; filter: brightness(0.7);">
                                                <div class="position-absolute w-100 h-100 top-0 start-0"
                                                     style="background: linear-gradient(to top, rgba(0, 0, 0, 0.9) 0%, transparent 60%);"></div>
                                                
                                                {{-- Rank Badge --}}
                                                <span class="position-absolute top-0 start-0 m-3 badge rounded-pill bg-dark fw-bold">
                                                    #{{ $rank + 1 }}
                                                </span>
                                                
                                                {{-- Score Ring --}}
                                                <div class="position-absolute top-0 end-0 m-3">
                                                    <svg width="60" height="60" class="position-relative" style="transform: rotate(-90deg);">
                                                        <circle cx="30" cy="30" r="26" fill="rgba(0, 0, 0, 0.5)" stroke="rgba(255, 255, 255, 0.2)" stroke-width="4"/>
                                                        <circle cx="30" cy="30" r="26" fill="none" stroke="{{ $barColor }}" stroke-width="4"
                                                                stroke-dasharray="163" stroke-dashoffset="{{ 163 - ($score / 100 * 163) }}"
                                                                stroke-linecap="round" class="score-ring"/>
                                                        <text x="30" y="35" text-anchor="middle" font-size="14" font-weight="900" fill="{{ $barColor }}"
                                                              style="transform: rotate(90deg); transform-origin: 30px 30px;">{{ $score }}%</text>
                                                    </svg>
                                                </div>
                                                
                                                {{-- Name Overlay --}}
                                                <div class="position-absolute bottom-0 start-0 p-3 w-100">
                                                    <h5 class="text-white fw-bold mb-1">{{ $candidate->name }}</h5>
                                                    <p class="text-white-50 mb-0 small">
                                                        {{ ucfirst($candidate->gender ?? '?') }}{{ $candidate->age ? ' · ' . $candidate->age . ' yrs' : '' }}
                                                    </p>
                                                </div>
                                            </div>
                                        @else
                                            <div class="position-relative d-flex align-items-center justify-content-center" 
                                                 style="height: 150px; background: linear-gradient(135deg, #2d1050, #4a0e6e);">
                                                <span class="display-1 text-white opacity-25 fw-bold">{{ strtoupper(substr($candidate->name, 0, 1)) }}</span>
                                                <span class="position-absolute top-0 start-0 m-3 badge rounded-pill bg-dark fw-bold">#{{ $rank + 1 }}</span>
                                            </div>
                                        @endif

                                        {{-- Card Body --}}
                                        <div class="card-body flex-grow-1 d-flex flex-column">
                                            @if(!$candPhoto)
                                                <div class="mb-2">
                                                    <div class="d-flex align-items-center gap-2">
                                                        <h5 class="text-white fw-bold mb-0">{{ $candidate->name }}</h5>
                                                        @if($candidate->is_premium)
                                                            <span class="badge-premium">★</span>
                                                        @endif
                                                    </div>
                                                    <small class="text-white-50">
                                                        {{ ucfirst($candidate->gender ?? '?') }}{{ $candidate->age ? ' · ' . $candidate->age . ' yrs' : '' }}
                                                    </small>
                                                </div>
                                            @else
                                                @if($candidate->is_premium)
                                                    <span class="badge-premium align-self-start mb-2">★ PREMIUM</span>
                                                @endif
                                            @endif

                                            @if($candidate->profile?->city)
                                                <p class="text-white-50 small mb-2">
                                                    <i class="bi bi-geo-alt"></i> {{ $candidate->profile->city }}{{ $candidate->profile->country ? ', ' . $candidate->profile->country : '' }}
                                                </p>
                                            @endif

                                            @if($candidate->profile?->headline)
                                                <p class="text-white-50 fst-italic small mb-3">"{{ Str::limit($candidate->profile->headline, 60) }}"</p>
                                            @endif

                                            @if(count($sharedInterests) > 0)
                                                <div class="mb-3">
                                                    <p class="text-white-50 text-uppercase fw-semibold mb-2" style="font-size: 0.7rem;">
                                                        <i class="bi bi-heart-fill text-danger"></i> Shared Interests
                                                    </p>
                                                    <div class="d-flex flex-wrap gap-1">
                                                        @foreach(array_slice($sharedInterests, 0, 4) as $int)
                                                            <span class="badge bg-info bg-opacity-25 text-info">{{ $int }}</span>
                                                        @endforeach
                                                        @if(count($sharedInterests) > 4)
                                                            <span class="badge bg-secondary bg-opacity-25 text-secondary">+{{ count($sharedInterests) - 4 }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endif

                                            <div class="mt-auto">
                                                {{-- Compatibility Bar --}}
                                                <div class="mb-3">
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <small class="text-white-50">Compatibility</small>
                                                        <small class="fw-bold" style="color: {{ $barColor }};">{{ $score }}%</small>
                                                    </div>
                                                    <div class="progress" style="height: 8px; background: rgba(255, 255, 255, 0.1);">
                                                        <div class="progress-bar" role="progressbar" 
                                                             style="width: {{ $score }}%; background: {{ $barColor }};"
                                                             aria-valuenow="{{ $score }}" aria-valuemin="0" aria-valuemax="100"></div>
                                                    </div>
                                                </div>

                                                {{-- Match Button --}}
                                                <button
                                                    wire:click="forceMatch({{ $focusUserId }}, {{ $candidate->id }})"
                                                    wire:loading.attr="disabled"
                                                    class="match-btn w-100"
                                                >
                                                    <span wire:loading.remove wire:target="forceMatch({{ $focusUserId }}, {{ $candidate->id }})">
                                                        💞 Force Match
                                                    </span>
                                                    <span wire:loading wire:target="forceMatch({{ $focusUserId }}, {{ $candidate->id }})"
                                                          class="d-flex align-items-center justify-content-center gap-2">
                                                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                                        Matching…
                                                    </span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                @endif
            @endif
        </div>
    </div>
</div>

{{-- Real-time JavaScript Integration --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize online status tracking
    const onlineUsers = new Set();
    
    // Toast notification system
    function showToast(title, message, type = 'info') {
        const toastId = 'toast-' + Date.now();
        const bgClass = type === 'success' ? 'bg-success' : 
                       type === 'danger' ? 'bg-danger' : 
                       type === 'warning' ? 'bg-warning' : 'bg-primary';
        
        const toastHtml = `
            <div id="${toastId}" class="toast align-items-center ${bgClass} text-white border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        <strong>${title}</strong><br>${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `;
        
        document.getElementById('toast-container').insertAdjacentHTML('beforeend', toastHtml);
        const toastElement = document.getElementById(toastId);
        const toast = new bootstrap.Toast(toastElement, { delay: 5000 });
        toast.show();
        
        toastElement.addEventListener('hidden.bs.toast', () => toastElement.remove());
    }
    
    // Livewire event listeners
    Livewire.on('match-created', (event) => {
        showToast('New Match!', 'A new match has been created successfully.', 'success');
        
        // Play notification sound if available
        if ('Audio' in window) {
            const audio = new Audio('/sounds/notification.mp3');
            audio.play().catch(() => {}); // Fail silently if no sound file
        }
        
        // Request browser notification permission
        if ('Notification' in window && Notification.permission === 'granted') {
            new Notification('New Match Created!', {
                body: 'You successfully created a new match.',
                icon: '/images/icon-match.png'
            });
        }
    });
    
    // Simulate real-time online status updates (replace with actual broadcasting)
    setInterval(() => {
        // This would be replaced with actual Echo/Pusher integration
        document.querySelectorAll('[data-user-id]').forEach(el => {
            const userId = el.getAttribute('data-user-id');
            // Randomly simulate online status for demo
            if (Math.random() > 0.7) {
                const statusEl = el.querySelector('.online-status');
                if (statusEl) {
                    statusEl.style.display = 'block';
                }
            }
        });
    }, 10000);
    
    // Request notification permission on load
    if ('Notification' in window && Notification.permission === 'default') {
        Notification.requestPermission();
    }
    
    console.log('✅ Real-time features initialized');
    
    // Laravel Echo real-time broadcasting with Reverb
    if (window.Echo) {
        console.log('🔴 Connecting to real-time channels...');
        
        // Listen for user online status changes
        window.Echo.channel('user-status')
            .listen('.user.status.changed', (e) => {
                console.log('User status changed:', e);
                updateUserOnlineStatus(e.userId, e.isOnline);
            });
        
        // Listen for private notifications
        window.Echo.private('user.{{ auth()->id() }}')
            .listen('.match.created', (e) => {
                console.log('New match created:', e);
                showToast('💞 New Match!', `You matched with ${e.otherUser.name}!`, 'success');
                // Refresh component to show new match
                Livewire.emit('refreshComponent');
            })
            .listen('.new.notification', (e) => {
                console.log('New notification:', e);
                showToast(e.title, e.message, e.type || 'info');
            });
        
        console.log('✅ Real-time channels connected');
    } else {
        console.warn('⚠️ Laravel Echo not available - real-time features disabled');
    }
});
</script>

</x-filament-panels::page>

