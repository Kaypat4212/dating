
<?php $__env->startSection('title', 'Icebreakers'); ?>
<?php $__env->startSection('content'); ?>
<div class="container py-4" style="max-width:900px;">
    <div class="mb-4">
        <h2 class="fw-bold mb-0"><i class="bi bi-snow2 text-primary me-2"></i>Icebreakers</h2>
        <p class="text-muted small">Answer fun questions that show on your profile and help start conversations.</p>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
    <div class="alert alert-success alert-dismissible fade show"><?php echo e(session('success')); ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php
        $types = [
            'would_you_rather' => ['label' => 'Would You Rather?', 'icon' => 'bi-shuffle', 'color' => 'text-primary'],
            'two_truths_lie'   => ['label' => 'Two Truths & a Lie', 'icon' => 'bi-shield-exclamation', 'color' => 'text-warning'],
            'this_or_that'     => ['label' => 'This or That', 'icon' => 'bi-toggles', 'color' => 'text-success'],
            'open_ended'       => ['label' => 'Open-Ended', 'icon' => 'bi-pencil', 'color' => 'text-info'],
        ];
    ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type => $meta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php $typeQuestions = $questions->where('type', $type); ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($typeQuestions->isNotEmpty()): ?>
    <div class="mb-4">
        <h6 class="fw-semibold text-uppercase small text-muted mb-3">
            <i class="bi <?php echo e($meta['icon']); ?> <?php echo e($meta['color']); ?> me-2"></i><?php echo e($meta['label']); ?>

        </h6>
        <div class="row g-3">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $typeQuestions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $q): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php $ans = $myAnswers[$q->id] ?? null; ?>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100 <?php echo e($ans ? 'border-start border-success border-3' : ''); ?>">
                    <div class="card-body">
                        <p class="fw-semibold mb-3"><?php echo e($q->question); ?></p>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array($type, ['would_you_rather', 'this_or_that']) && $q->option_a && $q->option_b): ?>
                        <form action="<?php echo e(route('icebreaker.answer')); ?>" method="POST" class="d-flex gap-2">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="question_id" value="<?php echo e($q->id); ?>">
                            <button type="submit" name="choice" value="a"
                                    class="btn btn-sm flex-grow-1 <?php echo e($ans?->choice === 'a' ? 'btn-primary' : 'btn-outline-primary'); ?>">
                                <?php echo e($q->option_a); ?>

                            </button>
                            <span class="text-muted align-self-center fw-bold">or</span>
                            <button type="submit" name="choice" value="b"
                                    class="btn btn-sm flex-grow-1 <?php echo e($ans?->choice === 'b' ? 'btn-danger' : 'btn-outline-danger'); ?>">
                                <?php echo e($q->option_b); ?>

                            </button>
                        </form>

                        <?php elseif($type === 'two_truths_lie'): ?>
                        <form action="<?php echo e(route('icebreaker.answer')); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="question_id" value="<?php echo e($q->id); ?>">
                            <textarea name="answer" class="form-control form-control-sm mb-2" rows="3"
                                      placeholder="Write your 2 truths and 1 lie (label which is the lie!)..." maxlength="500"><?php echo e($ans?->answer); ?></textarea>
                            <button type="submit" class="btn btn-sm btn-primary w-100">Save</button>
                        </form>

                        <?php else: ?>
                        <form action="<?php echo e(route('icebreaker.answer')); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="question_id" value="<?php echo e($q->id); ?>">
                            <textarea name="answer" class="form-control form-control-sm mb-2" rows="2"
                                      placeholder="Your answer..." maxlength="500"><?php echo e($ans?->answer); ?></textarea>
                            <button type="submit" class="btn btn-sm btn-primary w-100">Save</button>
                        </form>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($ans): ?>
                        <small class="text-success mt-2 d-block"><i class="bi bi-check-circle me-1"></i>Answered</small>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($questions->isEmpty()): ?>
    <div class="text-center py-5 text-muted">
        <i class="bi bi-snow2 fs-1"></i>
        <p class="mt-2">Icebreaker questions are being prepared. Check back soon!</p>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\dating\resources\views\icebreaker\index.blade.php ENDPATH**/ ?>