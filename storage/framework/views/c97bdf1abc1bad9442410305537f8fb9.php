
<?php $__env->startSection('title', 'Travel Buddy'); ?>
<?php $__env->startSection('content'); ?>
<div class="container py-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h2 class="fw-bold mb-0"><i class="bi bi-airplane me-2 text-primary"></i>Travel Buddy</h2>
            <p class="text-muted small mb-0">Find people exploring the same destinations</p>
        </div>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addPlanModal">
            <i class="bi bi-plus-lg me-1"></i>Add My Trip
        </button>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
    <div class="alert alert-success alert-dismissible fade show"><?php echo e(session('success')); ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($myPlans->isNotEmpty()): ?>
    <div class="mb-4">
        <h6 class="text-muted fw-semibold text-uppercase small mb-2">My Travel Plans</h6>
        <div class="row g-2">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $myPlans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm border-start border-primary border-3">
                    <div class="card-body py-2">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="fw-semibold"><?php echo e($plan->destination); ?></div>
                                <small class="text-muted"><?php echo e($plan->travel_from->format('M j')); ?> – <?php echo e($plan->travel_to->format('M j, Y')); ?></small>
                                <div><span class="badge bg-info text-dark"><?php echo e($plan->travel_type); ?></span></div>
                            </div>
                            <form action="<?php echo e(route('travel.destroy', $plan->id)); ?>" method="POST">
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Remove this plan?')"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($plans->isEmpty()): ?>
    <div class="text-center py-5 text-muted">
        <i class="bi bi-airplane fs-1"></i>
        <p class="mt-2">No travel plans yet. Add yours and find travel buddies!</p>
    </div>
    <?php else: ?>
    <h6 class="text-muted fw-semibold text-uppercase small mb-2">Community Travel Plans</h6>
    <div class="row g-3">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="col-sm-6 col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-start gap-3 mb-3">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($plan->user->primaryPhoto): ?>
                        <img src="<?php echo e($plan->user->primaryPhoto->url); ?>" class="rounded-circle object-fit-cover" width="44" height="44" alt="">
                        <?php else: ?>
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center flex-shrink-0"
                             style="width:44px;height:44px;"><?php echo e(strtoupper(substr($plan->user->name, 0, 1))); ?></div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <div>
                            <div class="fw-semibold"><?php echo e($plan->user->name); ?></div>
                            <small class="text-muted"><?php echo e($plan->user->profile?->country ?? 'Unknown'); ?></small>
                        </div>
                    </div>
                    <h6 class="fw-bold mb-1">
                        <i class="bi bi-geo-alt-fill text-danger me-1"></i><?php echo e($plan->destination); ?>

                    </h6>
                    <p class="text-muted small mb-2">
                        <i class="bi bi-calendar me-1"></i>
                        <?php echo e($plan->travel_from->format('M j')); ?> – <?php echo e($plan->travel_to->format('M j, Y')); ?>

                    </p>
                    <div class="mb-2">
                        <span class="badge bg-light text-dark border"><?php echo e($plan->travel_type); ?></span>
                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($plan->description): ?>
                    <p class="text-muted small"><?php echo e(Str::limit($plan->description, 100)); ?></p>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <form action="<?php echo e(route('travel.interest', $plan->id)); ?>" method="POST" class="mt-auto">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="btn btn-outline-primary btn-sm w-100">
                            <i class="bi bi-person-plus me-1"></i>Express Interest
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
    <div class="mt-4 d-flex justify-content-center"><?php echo e($plans->links()); ?></div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>


<div class="modal fade" id="addPlanModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-airplane me-2"></i>Add Travel Plan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?php echo e(route('travel.store')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Destination City *</label>
                            <input type="text" name="destination" class="form-control" required maxlength="150" placeholder="e.g. Paris">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Country *</label>
                            <input type="text" name="destination_country" class="form-control" required maxlength="100" placeholder="France">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">From Date *</label>
                            <input type="date" name="travel_from" class="form-control" required min="<?php echo e(date('Y-m-d')); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">To Date *</label>
                            <input type="date" name="travel_to" class="form-control" required min="<?php echo e(date('Y-m-d')); ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Travel Type</label>
                            <select name="travel_type" class="form-select">
                                <option value="solo">Solo traveler looking for company</option>
                                <option value="couple">Couple looking to meet other couples</option>
                                <option value="group">Group trip</option>
                                <option value="open">Open to anything</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea name="description" class="form-control" rows="3" maxlength="1000"
                                      placeholder="What are you planning to do there?"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Add Plan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\dating\resources\views\travel\index.blade.php ENDPATH**/ ?>