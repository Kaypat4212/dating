
<?php $__env->startSection('title', $topic->title); ?>
<?php $__env->startSection('content'); ?>
<div class="container py-4">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo e(route('forum.index')); ?>">Forum</a></li>
            <li class="breadcrumb-item"><a href="<?php echo e(route('forum.category', $category->slug)); ?>"><?php echo e($category->name); ?></a></li>
            <li class="breadcrumb-item active text-truncate" style="max-width:200px;"><?php echo e($topic->title); ?></li>
        </ol>
    </nav>

    <div class="row g-4">
        <div class="col-lg-8">
            
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <div class="d-flex align-items-start gap-3 mb-3">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center flex-shrink-0"
                             style="width:44px;height:44px;">
                            <?php echo e(strtoupper(substr($topic->author->name, 0, 1))); ?>

                        </div>
                        <div>
                            <h5 class="fw-bold mb-0"><?php echo e($topic->title); ?></h5>
                            <small class="text-muted">
                                by <?php echo e($topic->author->name); ?> &bull; <?php echo e($topic->created_at->format('M j, Y')); ?>

                                &bull; <i class="bi bi-eye me-1"></i><?php echo e($topic->views_count); ?> views
                            </small>
                        </div>
                    </div>
                    <p class="lh-lg"><?php echo e($topic->content); ?></p>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($topic->tags): ?>
                    <div class="mt-2">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $topic->tags; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <span class="badge bg-light text-dark border me-1"><?php echo e($tag); ?></span>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($replies->isNotEmpty()): ?>
            <h6 class="fw-semibold mb-3"><?php echo e($topic->replies_count); ?> <?php echo e(Str::plural('Reply', $topic->replies_count)); ?></h6>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $replies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $reply): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="card border-0 shadow-sm mb-3 <?php echo e($reply->is_best_answer ? 'border-success border' : ''); ?>" id="reply-<?php echo e($reply->id); ?>">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($reply->is_best_answer): ?>
                <div class="card-header bg-success text-white small fw-semibold py-1">
                    <i class="bi bi-check-circle me-1"></i>Best Answer
                </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <div class="card-body">
                    <div class="d-flex gap-3">
                        <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center flex-shrink-0"
                             style="width:36px;height:36px;font-size:0.8rem;">
                            <?php echo e(strtoupper(substr($reply->author->name, 0, 1))); ?>

                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between mb-1">
                                <strong class="small"><?php echo e($reply->author->name); ?></strong>
                                <small class="text-muted"><?php echo e($reply->created_at->diffForHumans()); ?></small>
                            </div>
                            <p class="mb-0"><?php echo e($reply->content); ?></p>
                        </div>
                    </div>
                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $reply->children; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="d-flex gap-3 mt-3 ms-5 ps-2 border-start">
                        <div class="rounded-circle bg-info text-white d-flex align-items-center justify-content-center flex-shrink-0"
                             style="width:30px;height:30px;font-size:0.75rem;">
                            <?php echo e(strtoupper(substr($child->author->name, 0, 1))); ?>

                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between mb-1">
                                <strong class="small"><?php echo e($child->author->name); ?></strong>
                                <small class="text-muted"><?php echo e($child->created_at->diffForHumans()); ?></small>
                            </div>
                            <p class="mb-0 small"><?php echo e($child->content); ?></p>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <div class="d-flex justify-content-center"><?php echo e($replies->links()); ?></div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$topic->is_locked): ?>
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header fw-semibold"><i class="bi bi-reply me-2"></i>Post a Reply</div>
                <div class="card-body">
                    <form action="<?php echo e(route('forum.reply', [$category->slug, $topic->slug])); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <div class="mb-3">
                            <textarea name="content" class="form-control <?php $__errorArgs = ['content'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                rows="5" placeholder="Share your thoughts..." required minlength="5" maxlength="5000"><?php echo e(old('content')); ?></textarea>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['content'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="bi bi-send me-1"></i>Post Reply
                        </button>
                    </form>
                </div>
            </div>
            <?php else: ?>
            <div class="alert alert-warning mt-4"><i class="bi bi-lock me-2"></i>This topic is locked and no longer accepts replies.</div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header fw-semibold">Topic Stats</div>
                <div class="card-body">
                    <div class="row g-2 text-center">
                        <div class="col-4">
                            <div class="fw-bold text-primary"><?php echo e($topic->views_count); ?></div>
                            <small class="text-muted">Views</small>
                        </div>
                        <div class="col-4">
                            <div class="fw-bold text-success"><?php echo e($topic->replies_count); ?></div>
                            <small class="text-muted">Replies</small>
                        </div>
                        <div class="col-4">
                            <div class="fw-bold text-warning"><?php echo e($topic->likes_count); ?></div>
                            <small class="text-muted">Likes</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card border-0 shadow-sm">
                <div class="card-header fw-semibold">Forum Rules</div>
                <div class="card-body small text-muted">
                    <ul class="mb-0 ps-3">
                        <li>Be respectful and kind</li>
                        <li>Stay on topic</li>
                        <li>No spam or self-promotion</li>
                        <li>Protect privacy (yours & others)</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\dating\resources\views\forum\topic.blade.php ENDPATH**/ ?>