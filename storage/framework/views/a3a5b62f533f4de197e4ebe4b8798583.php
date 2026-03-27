<?php if (isset($component)) { $__componentOriginal166a02a7c5ef5a9331faf66fa665c256 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal166a02a7c5ef5a9331faf66fa665c256 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament-panels::components.page.index','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament-panels::page'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<div>

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

<?php $newUsers = $this->getNewUsers(); ?>


<div id="toast-container" class="position-fixed top-0 end-0 p-3" style="z-index: 9999;"></div>

<div class="container-fluid px-4 py-4">
    
    
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
            </div>
        </div>
    </div>

    
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
                        <h3 class="text-white fw-bold mb-0"><?php echo e($newUsers->count()); ?></h3>
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

    
    <div class="row g-4">
        
        
        <div class="col-lg-3">
            <div class="card border-0" style="background: linear-gradient(145deg, rgba(30, 10, 46, 0.95), rgba(45, 16, 80, 0.85)); border: 1px solid rgba(244, 63, 94, 0.25) !important; border-radius: 20px;">
                <div class="card-header border-0 py-3" style="background: rgba(244, 63, 94, 0.1); border-bottom: 1px solid rgba(255, 255, 255, 0.1) !important;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white fw-bold mb-0">New Members</h6>
                            <small class="text-white-50">Last 7 days</small>
                        </div>
                        <span class="badge rounded-pill" style="background: linear-gradient(135deg, #f43f5e, #a855f7);">
                            <?php echo e($newUsers->count()); ?>

                        </span>
                    </div>
                </div>
                
                <div class="member-list">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $newUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $nu): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $photoUrl = $nu->primaryPhoto?->thumbnail_url;
                            $active = $focusUserId === $nu->id;
                        ?>
                        
                        <button
                            wire:click="selectUser(<?php echo e($nu->id); ?>)"
                            wire:loading.class="opacity-50"
                            class="member-item <?php echo e($active ? 'active' : ''); ?>"
                        >
                            <div class="d-flex align-items-center">
                                <div class="position-relative flex-shrink-0">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($photoUrl): ?>
                                        <img src="<?php echo e($photoUrl); ?>" alt="<?php echo e($nu->name); ?>" 
                                             class="rounded-circle" 
                                             style="width: 48px; height: 48px; object-fit: cover; border: 2px solid <?php echo e($active ? '#f43f5e' : 'rgba(255,255,255,0.2)'); ?>;">
                                    <?php else: ?>
                                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold"
                                             style="width: 48px; height: 48px; background: linear-gradient(135deg, #2d1050, #4a0e6e); border: 2px solid <?php echo e($active ? '#f43f5e' : 'rgba(255,255,255,0.2)'); ?>;">
                                            <?php echo e(strtoupper(substr($nu->name, 0, 1))); ?>

                                        </div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                                
                                <div class="ms-3 flex-grow-1 text-start">
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <span class="text-white fw-semibold" style="font-size: 0.9rem; <?php echo e($active ? 'color: #fb7185 !important;' : ''); ?>">
                                            <?php echo e($nu->name); ?>

                                        </span>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($nu->is_premium): ?>
                                            <span class="badge badge-premium" style="font-size: 0.65rem;">★</span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($nu->is_verified): ?>
                                            <span class="badge-verified" style="font-size: 0.65rem;">✓</span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                    <small class="text-white-50">
                                        <?php echo e(ucfirst($nu->gender ?? '—')); ?><?php echo e($nu->age ? ' · ' . $nu->age . ' yrs' : ''); ?>

                                    </small>
                                    <br>
                                    <small class="text-white-50" style="font-size: 0.75rem;">
                                        <?php echo e($nu->created_at->diffForHumans()); ?>

                                    </small>
                                </div>
                                
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($active): ?>
                                    <i class="bi bi-chevron-right text-danger"></i>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </button>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="text-center py-5">
                            <i class="bi bi-people fs-1 text-white-50 mb-3"></i>
                            <p class="text-white-50 mb-0">No members found</p>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>

        
        <div class="col-lg-9">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$focusUserId): ?>
                
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
                                <span style="font-size: 1.5rem;">📊</span>
                            </div>
                            <p class="text-white-50 small mb-0">Compatibility score</p>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <?php
                    $focusUser = \App\Models\User::with(['profile.interests', 'primaryPhoto'])->find($focusUserId);
                    $focusPhoto = $focusUser?->primaryPhoto;
                ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($focusUser): ?>
                    
                    <div class="card border-0 mb-4" style="background: linear-gradient(145deg, rgba(30, 10, 46, 0.98), rgba(45, 16, 80, 0.95)); border: 1px solid rgba(244, 63, 94, 0.4) !important; border-radius: 20px; box-shadow: 0 10px 40px rgba(244, 63, 94, 0.2);">
                        <div style="height: 4px; background: linear-gradient(90deg, #f43f5e, #a855f7, #3b82f6);"></div>
                        <div class="card-body p-4">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($focusPhoto): ?>
                                        <img src="<?php echo e($focusPhoto->thumbnail_url); ?>" alt="<?php echo e($focusUser->name); ?>"
                                             class="rounded-3"
                                             style="width: 100px; height: 100px; object-fit: cover; border: 3px solid #f43f5e; box-shadow: 0 8px 24px rgba(244, 63, 94, 0.4);">
                                    <?php else: ?>
                                        <div class="rounded-3 d-flex align-items-center justify-content-center text-white fw-bold"
                                             style="width: 100px; height: 100px; font-size: 2.5rem; background: linear-gradient(135deg, #f43f5e, #a855f7); box-shadow: 0 8px 24px rgba(244, 63, 94, 0.4);">
                                            <?php echo e(strtoupper(substr($focusUser->name, 0, 1))); ?>

                                        </div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                                
                                <div class="col">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <h2 class="text-white fw-bold mb-0"><?php echo e($focusUser->name); ?></h2>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($focusUser->is_premium): ?>
                                            <span class="badge-premium">★ PREMIUM</span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($focusUser->is_verified): ?>
                                            <span class="badge-verified">✓ VERIFIED</span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                    <div class="text-white-50 mb-2">
                                        <span><?php echo e(ucfirst($focusUser->gender ?? 'Unknown')); ?></span>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($focusUser->age): ?><span> · <?php echo e($focusUser->age); ?> years old</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        <span> · Seeking <strong class="text-white"><?php echo e(ucfirst($focusUser->seeking ?? 'everyone')); ?></strong></span>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($focusUser->profile?->city): ?>
                                            <span> · <i class="bi bi-geo-alt"></i> <?php echo e($focusUser->profile->city); ?><?php echo e($focusUser->profile->country ? ', ' . $focusUser->profile->country : ''); ?></span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($focusUser->profile?->headline): ?>
                                        <p class="text-white-50 fst-italic mb-0 small">"<?php echo e(Str::limit($focusUser->profile->headline, 100)); ?>"</p>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                                
                                <div class="col-auto text-end">
                                    <p class="text-white-50 text-uppercase small fw-semibold mb-1" style="font-size: 0.7rem;">Joined</p>
                                    <p class="text-white fw-bold mb-1"><?php echo e($focusUser->created_at->format('M d, Y')); ?></p>
                                    <p class="text-white-50 small mb-0"><?php echo e($focusUser->created_at->diffForHumans()); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($suggestions->isEmpty()): ?>
                        <div class="empty-state">
                            <span style="font-size: 4rem;">🤷</span>
                            <h4 class="text-white fw-bold mt-3 mb-2">No compatible candidates found</h4>
                            <p class="text-white-50 mb-0">All suitable users may already be matched, or this user's profile needs more data.</p>
                        </div>
                    <?php else: ?>
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h5 class="text-white fw-bold mb-0">
                                    <span class="fs-4"><?php echo e($suggestions->count()); ?></span> compatibility <?php echo e(Str::plural('match', $suggestions->count())); ?> found
                                </h5>
                            </div>
                            <div class="d-flex gap-3 small text-white-50">
                                <span><span class="badge bg-success"></span> ≥70% excellent</span>
                                <span><span class="badge bg-warning"></span> ≥40% good</span>
                                <span><span class="badge bg-secondary"></span> <40% fair</span>
                            </div>
                        </div>

                        <div class="row g-4">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $suggestions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rank => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
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
                                ?>

                                <div class="col-md-6 col-xl-4">
                                    <div class="match-card h-100 d-flex flex-column">
                                        
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($candPhoto): ?>
                                            <div class="position-relative" style="height: 200px;">
                                                <img src="<?php echo e($candPhoto->thumbnail_url); ?>" alt="<?php echo e($candidate->name); ?>"
                                                     class="w-100 h-100" style="object-fit: cover; filter: brightness(0.7);">
                                                <div class="position-absolute w-100 h-100 top-0 start-0"
                                                     style="background: linear-gradient(to top, rgba(0, 0, 0, 0.9) 0%, transparent 60%);"></div>
                                                
                                                
                                                <span class="position-absolute top-0 start-0 m-3 badge rounded-pill bg-dark fw-bold">
                                                    #<?php echo e($rank + 1); ?>

                                                </span>
                                                
                                                
                                                <div class="position-absolute top-0 end-0 m-3">
                                                    <svg width="60" height="60" class="position-relative" style="transform: rotate(-90deg);">
                                                        <circle cx="30" cy="30" r="26" fill="rgba(0, 0, 0, 0.5)" stroke="rgba(255, 255, 255, 0.2)" stroke-width="4"/>
                                                        <circle cx="30" cy="30" r="26" fill="none" stroke="<?php echo e($barColor); ?>" stroke-width="4"
                                                                stroke-dasharray="163" stroke-dashoffset="<?php echo e(163 - ($score / 100 * 163)); ?>"
                                                                stroke-linecap="round" class="score-ring"/>
                                                        <text x="30" y="35" text-anchor="middle" font-size="14" font-weight="900" fill="<?php echo e($barColor); ?>"
                                                              style="transform: rotate(90deg); transform-origin: 30px 30px;"><?php echo e($score); ?>%</text>
                                                    </svg>
                                                </div>
                                                
                                                
                                                <div class="position-absolute bottom-0 start-0 p-3 w-100">
                                                    <h5 class="text-white fw-bold mb-1"><?php echo e($candidate->name); ?></h5>
                                                    <p class="text-white-50 mb-0 small">
                                                        <?php echo e(ucfirst($candidate->gender ?? '?')); ?><?php echo e($candidate->age ? ' · ' . $candidate->age . ' yrs' : ''); ?>

                                                    </p>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <div class="position-relative d-flex align-items-center justify-content-center" 
                                                 style="height: 150px; background: linear-gradient(135deg, #2d1050, #4a0e6e);">
                                                <span class="display-1 text-white opacity-25 fw-bold"><?php echo e(strtoupper(substr($candidate->name, 0, 1))); ?></span>
                                                <span class="position-absolute top-0 start-0 m-3 badge rounded-pill bg-dark fw-bold">#<?php echo e($rank + 1); ?></span>
                                            </div>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                        
                                        <div class="card-body flex-grow-1 d-flex flex-column">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$candPhoto): ?>
                                                <div class="mb-2">
                                                    <div class="d-flex align-items-center gap-2">
                                                        <h5 class="text-white fw-bold mb-0"><?php echo e($candidate->name); ?></h5>
                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($candidate->is_premium): ?>
                                                            <span class="badge-premium">★</span>
                                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                    </div>
                                                    <small class="text-white-50">
                                                        <?php echo e(ucfirst($candidate->gender ?? '?')); ?><?php echo e($candidate->age ? ' · ' . $candidate->age . ' yrs' : ''); ?>

                                                    </small>
                                                </div>
                                            <?php else: ?>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($candidate->is_premium): ?>
                                                    <span class="badge-premium align-self-start mb-2">★ PREMIUM</span>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($candidate->profile?->city): ?>
                                                <p class="text-white-50 small mb-2">
                                                    <i class="bi bi-geo-alt"></i> <?php echo e($candidate->profile->city); ?><?php echo e($candidate->profile->country ? ', ' . $candidate->profile->country : ''); ?>

                                                </p>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($candidate->profile?->headline): ?>
                                                <p class="text-white-50 fst-italic small mb-3">"<?php echo e(Str::limit($candidate->profile->headline, 60)); ?>"</p>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($sharedInterests) > 0): ?>
                                                <div class="mb-3">
                                                    <p class="text-white-50 text-uppercase fw-semibold mb-2" style="font-size: 0.7rem;">
                                                        <i class="bi bi-heart-fill text-danger"></i> Shared Interests
                                                    </p>
                                                    <div class="d-flex flex-wrap gap-1">
                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = array_slice($sharedInterests, 0, 4); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $int): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <span class="badge bg-info bg-opacity-25 text-info"><?php echo e($int); ?></span>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($sharedInterests) > 4): ?>
                                                            <span class="badge bg-secondary bg-opacity-25 text-secondary">+<?php echo e(count($sharedInterests) - 4); ?></span>
                                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                    </div>
                                                </div>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                            <div class="mt-auto">
                                                
                                                <div class="mb-3">
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <small class="text-white-50">Compatibility</small>
                                                        <small class="fw-bold" style="color: <?php echo e($barColor); ?>;"><?php echo e($score); ?>%</small>
                                                    </div>
                                                    <div class="progress" style="height: 8px; background: rgba(255, 255, 255, 0.1);">
                                                        <div class="progress-bar" role="progressbar" 
                                                             style="width: <?php echo e($score); ?>%; background: <?php echo e($barColor); ?>;"
                                                             aria-valuenow="<?php echo e($score); ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                                    </div>
                                                </div>

                                                
                                                <button
                                                    wire:click="forceMatch(<?php echo e($focusUserId); ?>, <?php echo e($candidate->id); ?>)"
                                                    wire:loading.attr="disabled"
                                                    class="match-btn w-100"
                                                >
                                                    <span wire:loading.remove wire:target="forceMatch(<?php echo e($focusUserId); ?>, <?php echo e($candidate->id); ?>)">
                                                        💞 Force Match
                                                    </span>
                                                    <span wire:loading wire:target="forceMatch(<?php echo e($focusUserId); ?>, <?php echo e($candidate->id); ?>)"
                                                          class="d-flex align-items-center justify-content-center gap-2">
                                                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                                        Matching…
                                                    </span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
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
    
    // Livewire event listener for match creation
    Livewire.on('match-created', (event) => {
        showToast('New Match!', 'A new match has been created successfully.', 'success');
    });
    
    console.log('✅ SmartMatch features initialized');
});
</script>
</div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal166a02a7c5ef5a9331faf66fa665c256)): ?>
<?php $attributes = $__attributesOriginal166a02a7c5ef5a9331faf66fa665c256; ?>
<?php unset($__attributesOriginal166a02a7c5ef5a9331faf66fa665c256); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal166a02a7c5ef5a9331faf66fa665c256)): ?>
<?php $component = $__componentOriginal166a02a7c5ef5a9331faf66fa665c256; ?>
<?php unset($__componentOriginal166a02a7c5ef5a9331faf66fa665c256); ?>
<?php endif; ?>

<?php /**PATH C:\xampp\htdocs\dating\resources\views\filament\pages\smart-match.blade.php ENDPATH**/ ?>