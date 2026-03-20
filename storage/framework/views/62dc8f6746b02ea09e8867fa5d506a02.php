
<?php $__env->startSection('title', 'Messages'); ?>
<?php $__env->startSection('content'); ?>
<div class="container py-4">
    <?php $totalUnread = $conversations->sum(fn($c) => $c->unreadCountFor(auth()->id())); ?>

    
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="fw-bold mb-0">
            <i class="bi bi-chat-heart text-primary me-2"></i>Messages
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($totalUnread > 0): ?>
                <span class="badge bg-primary rounded-pill ms-1" style="font-size:.7rem;vertical-align:middle"><?php echo e($totalUnread); ?></span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </h4>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($conversations->isEmpty()): ?>
    
    <div class="card border-0 shadow-sm text-center py-5 px-3">
        <div class="display-4 mb-3">💬</div>
        <h5 class="fw-semibold mb-2">No conversations yet</h5>
        <p class="text-muted small mb-4">Match with someone and start chatting!</p>
        <a href="<?php echo e(route('matches.index')); ?>" class="btn btn-primary mx-auto px-4" style="width:fit-content">
            <i class="bi bi-hearts me-2"></i>View Matches
        </a>
    </div>
    <?php else: ?>

    
    <div class="position-relative mb-3" style="max-width:480px">
        <i class="bi bi-search position-absolute text-muted" style="left:.9rem;top:50%;transform:translateY(-50%);font-size:.9rem;pointer-events:none"></i>
        <input id="inboxSearch" type="search" class="form-control ps-5 rounded-pill" placeholder="Search conversations…">
    </div>

    <div class="card border-0 shadow-sm overflow-hidden" id="convList">
        
        <a href="<?php echo e(route('ai.chat')); ?>" class="conv-item">
            <div class="conv-avatar d-flex align-items-center justify-content-center"
                 style="background:linear-gradient(135deg,#667eea,#764ba2);min-width:52px;height:52px;border-radius:50%;flex-shrink:0">
                <span style="font-size:1.4rem;line-height:1">🤖</span>
            </div>
            <div class="conv-body ms-3 overflow-hidden">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="conv-name fw-semibold">AI Dating Assistant</span>
                    <span class="badge rounded-pill text-bg-primary" style="font-size:.65rem">AI</span>
                </div>
                <div class="conv-preview text-muted small text-truncate">Ask for dating advice, bio help, and more…</div>
            </div>
        </a>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $conversations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $conv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
            $other   = $conv->match->user1_id === auth()->id() ? $conv->match->user2 : $conv->match->user1;
            $lastMsg = $conv->messages->first();
            $unread  = $conv->unreadCountFor(auth()->id());
            $online  = $other?->last_active_at && $other->last_active_at->gt(now()->subMinutes(10));
        ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$other): ?> <?php continue; ?> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <a href="<?php echo e(route('conversations.show', $conv->id)); ?>"
           class="conv-item <?php echo e($unread > 0 ? 'unread' : ''); ?>"
           data-name="<?php echo e(strtolower($other->name)); ?>">

            
            <div class="conv-avatar">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($other->primaryPhoto): ?>
                    <img src="<?php echo e($other->primaryPhoto->thumbnail_url); ?>" alt="<?php echo e($other->name); ?>">
                <?php else: ?>
                    <div class="conv-avatar-ph"><?php echo e(strtoupper(mb_substr($other->name, 0, 1))); ?></div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($online): ?><div class="conv-online" title="Online now"></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            
            <div class="conv-body">
                <div class="conv-name">
                    <?php echo e($other->name); ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($other->is_verified ?? false): ?><i class="bi bi-patch-check-fill text-info ms-1" style="font-size:.8rem"></i><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <div class="conv-preview">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lastMsg): ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lastMsg->sender_id === auth()->id()): ?><span class="text-muted">You: </span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?><?php echo e(Str::limit($lastMsg->body, 55)); ?>

                    <?php else: ?>
                        <em>Start the conversation…</em>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            
            <div class="conv-meta">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lastMsg): ?>
                <div class="conv-time">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lastMsg->created_at->isToday()): ?>
                        <?php echo e($lastMsg->created_at->format('g:i A')); ?>

                    <?php elseif($lastMsg->created_at->isYesterday()): ?>
                        Yesterday
                    <?php else: ?>
                        <?php echo e($lastMsg->created_at->format('M j')); ?>

                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($unread > 0): ?>
                    <div class="conv-unread-badge"><?php echo e($unread > 9 ? '9+' : $unread); ?></div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.getElementById('inboxSearch')?.addEventListener('input', function () {
    const q = this.value.toLowerCase();
    document.querySelectorAll('#convList .conv-item').forEach(el => {
        el.style.display = el.dataset.name.includes(q) ? '' : 'none';
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\dating\resources\views\conversations\index.blade.php ENDPATH**/ ?>