
<?php $__env->startSection('title', 'Stories'); ?>
<?php $__env->startSection('content'); ?>
<div class="container py-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="fw-bold mb-0"><i class="bi bi-camera-video text-warning me-2"></i>Stories</h4>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addStoryModal">
            <i class="bi bi-plus-circle me-1"></i>Add Story
        </button>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?><div class="alert alert-success alert-dismissible fade show"><?php echo e(session('success')); ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('error')): ?><div class="alert alert-danger alert-dismissible fade show"><?php echo e(session('error')); ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($stories->isEmpty()): ?>
    <div class="card border-0 shadow-sm text-center py-5">
        <div class="display-1 mb-3">📸</div>
        <h5>No stories yet</h5>
        <p class="text-muted">Your matches' stories will appear here. Add your own story above!</p>
    </div>
    <?php else: ?>
    <div class="row g-4">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $stories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $userId => $userStories): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php $storyUser = $userStories->first()->user; $first = $userStories->first(); ?>
        <div class="col-auto">
            <div class="story-circle text-center" style="cursor:pointer" data-bs-toggle="modal" data-bs-target="#storyModal<?php echo e($userId); ?>">
                <div class="story-ring <?php echo e($storyUser->id === auth()->id() ? 'story-ring-mine' : ''); ?> mx-auto mb-1"
                     style="width:68px;height:68px;border-radius:50%;padding:3px;background:linear-gradient(135deg,#f09433 0%,#e6683c 25%,#dc2743 50%,#cc2366 75%,#bc1888 100%)">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($storyUser->primaryPhoto): ?>
                    <img src="<?php echo e($storyUser->primaryPhoto->thumbnail_url); ?>"
                         class="rounded-circle object-fit-cover" width="62" height="62"
                         style="border:3px solid var(--bs-body-bg)"
                         alt="<?php echo e($storyUser->name); ?>">
                    <?php else: ?>
                    <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center" style="width:62px;height:62px;border:3px solid var(--bs-body-bg)">
                        <i class="bi bi-person-fill text-white fs-4"></i>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <div style="font-size:.72rem;max-width:70px" class="text-truncate"><?php echo e($storyUser->name); ?></div>
            </div>

            
            <div class="modal fade" id="storyModal<?php echo e($userId); ?>" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered" style="max-width:400px">
                    <div class="modal-content border-0 bg-black">
                        <div class="modal-body p-0 position-relative" style="height:600px">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $userStories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $story): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="story-slide <?php echo e($i === 0 ? 'd-flex' : 'd-none'); ?> align-items-center justify-content-center h-100 flex-column"
                                 data-index="<?php echo e($i); ?>" data-total="<?php echo e($userStories->count()); ?>">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($story->media_type === 'image'): ?>
                                <img src="<?php echo e(asset('storage/'.$story->media_path)); ?>" class="mw-100 mh-100 object-fit-contain">
                                <?php else: ?>
                                <video src="<?php echo e(asset('storage/'.$story->media_path)); ?>" class="mw-100 mh-100 object-fit-contain" autoplay muted loop></video>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($story->caption): ?>
                                <div class="position-absolute bottom-0 start-0 end-0 p-3 text-white fw-semibold" style="background:linear-gradient(transparent,rgba(0,0,0,.7))">
                                    <?php echo e($story->caption); ?>

                                </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                
                                <div class="position-absolute top-0 start-0 end-0 p-3 d-flex align-items-center gap-2" style="background:linear-gradient(rgba(0,0,0,.5),transparent)">
                                    <span class="text-white fw-semibold small"><?php echo e($storyUser->name); ?></span>
                                    <span class="text-white-50" style="font-size:.7rem"><?php echo e($story->created_at->diffForHumans()); ?></span>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($story->user_id === auth()->id()): ?>
                                    <form method="POST" action="<?php echo e(route('stories.destroy', $story->id)); ?>" class="ms-auto" onsubmit="return confirm('Delete this story?')"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                        <button class="btn btn-sm btn-outline-light py-0"><i class="bi bi-trash"></i></button>
                                    </form>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                                
                                <div class="position-absolute" style="top:8px;left:12px;right:12px;display:flex;gap:4px">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php for($j = 0; $j < $userStories->count(); $j++): ?>
                                    <div style="flex:1;height:3px;border-radius:2px;background:<?php echo e($j <= $i ? 'white' : 'rgba(255,255,255,.4)'); ?>"></div>
                                    <?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            
                            <div class="position-absolute top-0 start-50 end-0 h-100" style="cursor:pointer" onclick="advanceStory(this.closest('.modal'))"></div>
                            <button class="btn-close btn-close-white position-absolute top-0 end-0 m-3" data-bs-dismiss="modal"></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>


<div class="modal fade" id="addStoryModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold"><i class="bi bi-camera-video me-2"></i>Add a Story</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="<?php echo e(route('stories.store')); ?>" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Photo or Video <span class="text-danger">*</span></label>
                        <input type="file" name="media" class="form-control" accept="image/*,video/mp4" required>
                        <div class="form-text">Max 20MB · JPG, PNG, GIF, WebP, MP4 · Disappears in 24h</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Caption <span class="text-muted">(optional)</span></label>
                        <input type="text" name="caption" class="form-control" maxlength="120" placeholder="Say something…">
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-upload me-1"></i>Post Story</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function advanceStory(modal) {
    const slides = modal.querySelectorAll('.story-slide');
    let current  = [...slides].findIndex(s => s.classList.contains('d-flex'));
    if (current < slides.length - 1) {
        slides[current].classList.replace('d-flex','d-none');
        slides[current + 1].classList.replace('d-none','d-flex');
    } else {
        bootstrap.Modal.getInstance(modal).hide();
    }
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\dating\resources\views\stories\index.blade.php ENDPATH**/ ?>