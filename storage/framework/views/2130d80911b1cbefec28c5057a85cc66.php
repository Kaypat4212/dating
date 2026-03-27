
<?php $__env->startSection('title', 'My Pets'); ?>
<?php $__env->startSection('content'); ?>
<div class="container py-4" style="max-width:900px;">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo e(route('profile.edit')); ?>">Profile</a></li>
            <li class="breadcrumb-item active">My Pets</li>
        </ol>
    </nav>

    <div class="d-flex align-items-center justify-content-between mb-4">
        <h2 class="fw-bold mb-0"><i class="bi bi-heart-fill text-danger me-2"></i>My Pets</h2>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addPetModal">
            <i class="bi bi-plus-lg me-1"></i>Add Pet
        </button>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
    <div class="alert alert-success alert-dismissible fade show"><?php echo e(session('success')); ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pets->isEmpty()): ?>
    <div class="text-center py-5 text-muted">
        <i class="bi bi-emoji-smile fs-1"></i>
        <p class="mt-2">No pets added yet. Add your furry (or scaly!) friends to your profile.</p>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPetModal">
            <i class="bi bi-plus-lg me-1"></i>Add My First Pet
        </button>
    </div>
    <?php else: ?>
    <div class="row g-3">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $pets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="col-sm-6 col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pet->photo_path): ?>
                <img src="<?php echo e(Storage::url($pet->photo_path)); ?>" class="card-img-top" style="height:180px;object-fit:cover;" alt="<?php echo e($pet->name); ?>">
                <?php else: ?>
                <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height:120px;">
                    <i class="bi bi-emoji-smile text-muted" style="font-size:3rem;"></i>
                </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <div class="card-body">
                    <h5 class="fw-bold mb-1"><?php echo e($pet->name); ?></h5>
                    <p class="text-muted small mb-2">
                        <?php echo e(ucfirst($pet->type)); ?>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pet->breed): ?> – <?php echo e($pet->breed); ?><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pet->age_years || $pet->age_months): ?>
                            (<?php echo e($pet->age_years ? $pet->age_years . 'y' : ''); ?><?php echo e($pet->age_months ? ' ' . $pet->age_months . 'm' : ''); ?>)
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </p>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pet->about): ?><p class="small text-muted"><?php echo e($pet->about); ?></p><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <div class="card-footer bg-transparent">
                    <form action="<?php echo e(route('extras.pets.destroy', $pet->id)); ?>" method="POST">
                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                        <button type="submit" class="btn btn-sm btn-outline-danger w-100" onclick="return confirm('Remove <?php echo e($pet->name); ?>?')">
                            <i class="bi bi-trash me-1"></i>Remove
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>


<div class="modal fade" id="addPetModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Add Pet</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?php echo e(route('extras.pets.store')); ?>" method="POST" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Pet's Name *</label>
                            <input type="text" name="name" class="form-control" required maxlength="80" placeholder="e.g. Buddy">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Type *</label>
                            <select name="type" class="form-select" required>
                                <option value="dog">Dog</option>
                                <option value="cat">Cat</option>
                                <option value="bird">Bird</option>
                                <option value="rabbit">Rabbit</option>
                                <option value="fish">Fish</option>
                                <option value="reptile">Reptile</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Breed</label>
                            <input type="text" name="breed" class="form-control" maxlength="100" placeholder="Golden Retriever">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Age (years)</label>
                            <input type="number" name="age_years" class="form-control" min="0" max="50" placeholder="3">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Months</label>
                            <input type="number" name="age_months" class="form-control" min="0" max="11" placeholder="6">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Size</label>
                            <select name="size" class="form-select">
                                <option value="">Select size</option>
                                <option value="tiny">Tiny</option>
                                <option value="small">Small</option>
                                <option value="medium">Medium</option>
                                <option value="large">Large</option>
                                <option value="extra_large">Extra Large</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">About</label>
                            <textarea name="about" class="form-control" rows="2" maxlength="500" placeholder="Tell us about your pet..."></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Photo</label>
                            <input type="file" name="photo" class="form-control" accept="image/*">
                            <small class="text-muted">Max 2MB</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Add Pet</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\dating\resources\views\profile\extras\pets.blade.php ENDPATH**/ ?>