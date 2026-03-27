
<?php $__env->startSection('title', $chatRoom->name); ?>
<?php $__env->startSection('content'); ?>
<div class="container-fluid py-3" style="max-width:1200px;">
    <div class="row g-3" style="height:calc(100vh - 120px);">

        
        <div class="col-lg-9 d-flex flex-column h-100">
            <div class="card border-0 shadow-sm d-flex flex-column h-100">
                
                <div class="card-header d-flex align-items-center justify-content-between py-2">
                    <div class="d-flex align-items-center gap-2">
                        <a href="<?php echo e(route('chat-rooms.index')); ?>" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i>
                        </a>
                        <div>
                            <div class="fw-bold"><?php echo e($chatRoom->name); ?></div>
                            <small class="text-muted"><i class="bi bi-people me-1"></i><?php echo e($chatRoom->members_count); ?> members</small>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($member->role !== 'admin'): ?>
                        <form action="<?php echo e(route('chat-rooms.leave', $chatRoom->slug)); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Leave this room?')">
                                <i class="bi bi-box-arrow-right"></i> Leave
                            </button>
                        </form>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>

                
                <div class="card-body overflow-auto flex-grow-1 p-3" id="messagesContainer" style="background:#f8f9fa;">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $messages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $message): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="d-flex gap-2 mb-3 <?php echo e($message->user_id === auth()->id() ? 'flex-row-reverse' : ''); ?>">
                        <div class="rounded-circle bg-<?php echo e($message->user_id === auth()->id() ? 'primary' : 'secondary'); ?> text-white d-flex align-items-center justify-content-center flex-shrink-0"
                             style="width:32px;height:32px;font-size:0.75rem;align-self:flex-end;">
                            <?php echo e(strtoupper(substr($message->author->name, 0, 1))); ?>

                        </div>
                        <div style="max-width:70%;">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($message->user_id !== auth()->id()): ?>
                            <div class="small text-muted mb-1"><?php echo e($message->author->name); ?></div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <div class="p-2 rounded-3 <?php echo e($message->user_id === auth()->id() ? 'bg-primary text-white' : 'bg-white border'); ?>"
                                 style="word-break:break-word;">
                                <?php echo e($message->content); ?>

                            </div>
                            <div class="small text-muted mt-1 <?php echo e($message->user_id === auth()->id() ? 'text-end' : ''); ?>">
                                <?php echo e($message->created_at->format('H:i')); ?>

                            </div>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <div id="messagesEnd"></div>
                </div>

                
                <div class="card-footer p-2">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$member->is_muted): ?>
                    <form id="sendForm" class="d-flex gap-2">
                        <?php echo csrf_field(); ?>
                        <input type="text" id="messageInput" class="form-control form-control-sm"
                               placeholder="Type a message..." maxlength="1000" autocomplete="off">
                        <button type="submit" class="btn btn-primary btn-sm px-3">
                            <i class="bi bi-send"></i>
                        </button>
                    </form>
                    <?php else: ?>
                    <div class="text-muted small text-center py-1"><i class="bi bi-mic-mute me-1"></i>You are muted in this room.</div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>

        
        <div class="col-lg-3 d-none d-lg-flex flex-column h-100">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header fw-semibold py-2">
                    <i class="bi bi-people me-2"></i>Members (<?php echo e($chatRoom->members_count); ?>)
                </div>
                <div class="card-body overflow-auto p-2">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $chatRoom->members()->with('user')->limit(50)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="d-flex align-items-center gap-2 py-1">
                        <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center flex-shrink-0"
                             style="width:28px;height:28px;font-size:0.7rem;">
                            <?php echo e(strtoupper(substr($m->user->name, 0, 1))); ?>

                        </div>
                        <span class="small text-truncate"><?php echo e($m->user->name); ?></span>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($m->role !== 'member'): ?>
                        <span class="badge bg-warning text-dark ms-auto" style="font-size:0.6rem;"><?php echo e($m->role); ?></span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
(function() {
    const roomSlug = <?php echo json_encode($chatRoom->slug, 15, 512) ?>;
    const messagesUrl = '/chat-rooms/' + roomSlug + '/messages';
    const sendUrl = '/chat-rooms/' + roomSlug + '/send';
    const currentUserId = <?php echo json_encode(auth()->id(), 15, 512) ?>;
    const container = document.getElementById('messagesContainer');
    const form = document.getElementById('sendForm');
    const input = document.getElementById('messageInput');
    let lastId = <?php echo json_encode($messages->last()?->id ?? 0, 15, 512) ?>;

    // Scroll to bottom
    function scrollBottom() {
        container.scrollTop = container.scrollHeight;
    }
    scrollBottom();

    function appendMessage(msg) {
        const isMe = msg.user_id === currentUserId;
        const html = `
        <div class="d-flex gap-2 mb-3 ${isMe ? 'flex-row-reverse' : ''}">
            <div class="rounded-circle bg-${isMe ? 'primary' : 'secondary'} text-white d-flex align-items-center justify-content-center flex-shrink-0"
                 style="width:32px;height:32px;font-size:.75rem;align-self:flex-end;">
                ${msg.user_name.charAt(0).toUpperCase()}
            </div>
            <div style="max-width:70%;">
                ${!isMe ? `<div class="small text-muted mb-1">${msg.user_name}</div>` : ''}
                <div class="p-2 rounded-3 ${isMe ? 'bg-primary text-white' : 'bg-white border'}" style="word-break:break-word;">
                    ${msg.content}
                </div>
                <div class="small text-muted mt-1 ${isMe ? 'text-end' : ''}">${msg.created_at}</div>
            </div>
        </div>`;
        const end = document.getElementById('messagesEnd');
        end.insertAdjacentHTML('beforebegin', html);
        scrollBottom();
    }

    // Send message
    if (form) {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            const content = input.value.trim();
            if (!content) return;
            input.value = '';
            input.disabled = true;
            try {
                const resp = await fetch(sendUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ content })
                });
                const data = await resp.json();
                if (data.id) {
                    lastId = data.id;
                    appendMessage({ ...data, user_id: currentUserId });
                }
            } catch(err) { console.error(err); }
            input.disabled = false;
            input.focus();
        });
    }

    // Poll for new messages every 3 seconds
    setInterval(async function() {
        try {
            const resp = await fetch(`${messagesUrl}?after=${lastId}`, {
                headers: { 'Accept': 'application/json' }
            });
            const msgs = await resp.json();
            msgs.forEach(msg => {
                if (msg.user_id !== currentUserId) {
                    appendMessage(msg);
                }
                lastId = Math.max(lastId, msg.id);
            });
        } catch(e) {}
    }, 3000);
})();
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\dating\resources\views\chat-rooms\show.blade.php ENDPATH**/ ?>