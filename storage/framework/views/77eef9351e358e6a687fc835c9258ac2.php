
<?php $__env->startSection('title', $post->title); ?>
<?php $__env->startSection('content'); ?>
<div class="container py-4">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo e(route('blog.index')); ?>">Blog</a></li>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($post->category): ?>
            <li class="breadcrumb-item"><a href="<?php echo e(route('blog.category', $post->category->slug)); ?>"><?php echo e($post->category->name); ?></a></li>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <li class="breadcrumb-item active text-truncate" style="max-width:200px;"><?php echo e($post->title); ?></li>
        </ol>
    </nav>

    <div class="row g-4">
        <div class="col-lg-8">
            
            <article class="card border-0 shadow-sm mb-4">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($post->featured_image): ?>
                <img src="<?php echo e(Storage::url($post->featured_image)); ?>" class="card-img-top rounded-top" style="max-height:400px;object-fit:cover;" alt="">
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <div class="card-body">
                    <h1 class="fw-bold h3 mb-2"><?php echo e($post->title); ?></h1>
                    <div class="d-flex align-items-center gap-3 mb-3 text-muted small flex-wrap">
                        <span><i class="bi bi-person me-1"></i><?php echo e($post->author->name); ?></span>
                        <span><i class="bi bi-calendar me-1"></i><?php echo e($post->published_at->format('M j, Y')); ?></span>
                        <span><i class="bi bi-eye me-1"></i><?php echo e(number_format($post->views_count)); ?> views</span>
                        <span><i class="bi bi-chat me-1"></i><?php echo e($post->comments_count); ?> comments</span>
                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($post->tags): ?>
                    <div class="mb-3">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $post->tags; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <span class="badge bg-light text-dark border me-1"><?php echo e($tag); ?></span>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <div class="blog-content lh-lg">
                        <?php echo nl2br(e($post->content)); ?>

                    </div>
                </div>
            </article>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($post->allow_comments): ?>
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header fw-semibold">
                    <i class="bi bi-chat-square-text me-2"></i>Comments (<?php echo e($post->comments_count); ?>)
                </div>
                <div class="card-body">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->check()): ?>
                    <form action="<?php echo e(route('blog.comment.store', $post->slug)); ?>" method="POST" class="mb-4">
                        <?php echo csrf_field(); ?>
                        <div class="mb-2">
                            <textarea name="content" class="form-control <?php $__errorArgs = ['content'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                rows="3" placeholder="Write a comment..." required maxlength="2000"></textarea>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['content'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm">Post Comment</button>
                    </form>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $comments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $comment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="d-flex gap-3 mb-3" id="comment-<?php echo e($comment->id); ?>">
                        <div class="flex-shrink-0">
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center"
                                 style="width:40px;height:40px;font-size:0.875rem;">
                                <?php echo e(strtoupper(substr($comment->author->name, 0, 1))); ?>

                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="bg-light rounded p-3">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <strong class="small"><?php echo e($comment->author->name); ?></strong>
                                    <small class="text-muted"><?php echo e($comment->created_at->diffForHumans()); ?></small>
                                </div>
                                <p class="mb-0 small"><?php echo e($comment->content); ?></p>
                            </div>
                            
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $comment->replies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $reply): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="d-flex gap-2 mt-2 ms-4">
                                <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center flex-shrink-0"
                                     style="width:32px;height:32px;font-size:0.75rem;">
                                    <?php echo e(strtoupper(substr($reply->author->name, 0, 1))); ?>

                                </div>
                                <div class="bg-light rounded p-2 flex-grow-1">
                                    <div class="d-flex justify-content-between mb-1">
                                        <strong class="small"><?php echo e($reply->author->name); ?></strong>
                                        <small class="text-muted"><?php echo e($reply->created_at->diffForHumans()); ?></small>
                                    </div>
                                    <p class="mb-0 small"><?php echo e($reply->content); ?></p>
                                </div>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="text-muted text-center py-3">No comments yet. Be the first!</p>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        
        <div class="col-lg-4">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($related->isNotEmpty()): ?>
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header fw-semibold"><i class="bi bi-journals me-2"></i>Related Posts</div>
                <div class="list-group list-group-flush">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $related; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e(route('blog.show', $r->slug)); ?>" class="list-group-item list-group-item-action">
                        <div class="fw-semibold small"><?php echo e($r->title); ?></div>
                        <small class="text-muted"><?php echo e($r->published_at->format('M j, Y')); ?></small>
                    </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <div class="card border-0 shadow-sm">
                <div class="card-header fw-semibold"><i class="bi bi-lightning me-2"></i>About the Author</div>
                <div class="card-body text-center">
                    <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mb-2"
                         style="width:60px;height:60px;font-size:1.25rem;">
                        <?php echo e(strtoupper(substr($post->author->name, 0, 1))); ?>

                    </div>
                    <h6 class="fw-bold mb-0"><?php echo e($post->author->name); ?></h6>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\dating\resources\views\blog\show.blade.php ENDPATH**/ ?>