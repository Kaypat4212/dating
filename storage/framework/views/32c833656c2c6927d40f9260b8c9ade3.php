
<?php $__env->startSection('title', 'Chat Rooms'); ?>
<?php $__env->startSection('content'); ?>
<div class="container py-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h2 class="fw-bold mb-0"><i class="bi bi-chat-dots me-2 text-primary"></i>Chat Rooms</h2>
            <p class="text-muted small mb-0">Join public rooms or create your own</p>
        </div>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createRoomModal">
            <i class="bi bi-plus-lg me-1"></i>Create Room
        </button>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($myRooms->isNotEmpty()): ?>
    <h6 class="text-muted fw-semibold mb-2 text-uppercase small">My Rooms</h6>
    <div class="row g-3 mb-4">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $myRooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $room): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="col-md-4 col-lg-3">
            <a href="<?php echo e(route('chat-rooms.show', $room->slug)); ?>" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100 border-start border-primary border-3">
                    <div class="card-body py-2">
                        <div class="fw-semibold text-dark text-truncate"><?php echo e($room->name); ?></div>
                        <small class="text-muted"><i class="bi bi-people me-1"></i><?php echo e($room->members_count); ?> members</small>
                    </div>
                </div>
            </a>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <h6 class="text-muted fw-semibold mb-2 text-uppercase small">All Public Rooms</h6>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($rooms->isEmpty()): ?>
    <div class="text-center py-5 text-muted">
        <i class="bi bi-chat-square fs-1"></i>
        <p class="mt-2">No rooms yet. Create the first one!</p>
    </div>
    <?php else: ?>
    <div class="row g-3">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $rooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $room): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="col-sm-6 col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex flex-column">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center flex-shrink-0"
                             style="width:40px;height:40px;">
                            <i class="bi bi-chat-dots"></i>
                        </div>
                        <div class="min-w-0">
                            <div class="fw-semibold text-truncate"><?php echo e($room->name); ?></div>
                            <small class="badge bg-secondary"><?php echo e($room->type); ?></small>
                        </div>
                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($room->description): ?>
                    <p class="text-muted small flex-grow-1 mb-2"><?php echo e(Str::limit($room->description, 80)); ?></p>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <div class="d-flex justify-content-between align-items-center mt-auto">
                        <small class="text-muted"><i class="bi bi-people me-1"></i><?php echo e($room->members_count); ?></small>
                        <a href="<?php echo e(route('chat-rooms.show', $room->slug)); ?>" class="btn btn-outline-primary btn-sm">
                            Join <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
    <div class="mt-4 d-flex justify-content-center"><?php echo e($rooms->links()); ?></div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>


<div class="modal fade" id="createRoomModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Create Chat Room</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?php echo e(route('chat-rooms.store')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Room Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required minlength="3" maxlength="80"
                               placeholder="e.g. Singles in NYC, Dog Lovers...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea name="description" class="form-control" rows="3" maxlength="500"
                                  placeholder="What's this room about?"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Type</label>
                        <select name="type" class="form-select">
                            <option value="public">Public - Anyone can join</option>
                            <option value="interest">Interest-based</option>
                            <option value="location">Location-based</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Create Room</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\dating\resources\views\chat-rooms\index.blade.php ENDPATH**/ ?>