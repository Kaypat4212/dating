
<?php $__env->startSection('title', 'Invoice ' . $payment->invoice_number); ?>
<?php $__env->startSection('content'); ?>

<style>
@media print {
    nav, header, footer, .no-print { display: none !important; }
    .container { max-width: 100% !important; }
    .invoice-card { box-shadow: none !important; border: 1px solid #ddd !important; }
}
</style>

<div class="container py-4" style="max-width:760px">

    
    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <a href="<?php echo e(route('premium.show')); ?>" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to Premium
        </a>
        <button onclick="window.print()" class="btn btn-sm btn-outline-primary">
            <i class="bi bi-printer me-1"></i> Print / Save PDF
        </button>
    </div>

    <div class="card border-0 shadow-sm invoice-card p-4 p-md-5">

        
        <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-3">
            <div>
                <h4 class="fw-bold mb-0 text-primary">HeartsConnect</h4>
                <div class="text-muted small mt-1">Premium Membership</div>
            </div>
            <div class="text-end">
                <div class="fw-bold fs-5">#<?php echo e($payment->invoice_number); ?></div>
                <div class="text-muted small">Issued: <?php echo e($payment->created_at->format('F j, Y')); ?></div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($payment->approved_at): ?>
                <div class="text-muted small">Approved: <?php echo e($payment->approved_at->format('F j, Y')); ?></div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>

        <hr>

        
        <div class="row mb-4">
            <div class="col-sm-6 mb-3 mb-sm-0">
                <div class="text-muted small fw-semibold text-uppercase mb-1">Bill To</div>
                <div class="fw-semibold"><?php echo e($payment->user->name); ?></div>
                <div class="text-muted small"><?php echo e($payment->user->email); ?></div>
            </div>
            <div class="col-sm-6 text-sm-end">
                <div class="text-muted small fw-semibold text-uppercase mb-1">Status</div>
                <?php
                    $statusClass = match($payment->status) {
                        'approved' => 'bg-success',
                        'rejected' => 'bg-danger',
                        default    => 'bg-warning text-dark',
                    };
                ?>
                <span class="badge <?php echo e($statusClass); ?> px-3 py-2 fs-6">
                    <?php echo e(ucfirst($payment->status)); ?>

                </span>
            </div>
        </div>

        
        <div class="table-responsive mb-2">
            <table class="table table-borderless mb-0">
                <thead style="background:#f8f9fa">
                    <tr>
                        <th class="ps-3">Description</th>
                        <th class="text-end pe-3">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($payment->is_upgrade && $payment->upgrade_from_plan): ?>
                    
                    <tr>
                        <td class="ps-3">
                            <div class="fw-semibold">Premium — <?php echo e($payment->plan_label); ?> Plan (Upgrade)</div>
                            <div class="text-muted small">
                                Upgrading from: <?php echo e($payment->upgrade_plan_label); ?> plan
                            </div>
                        </td>
                        <td class="text-end pe-3 fw-semibold">$<?php echo e(number_format($planFull['price'], 2)); ?></td>
                    </tr>
                    <tr class="text-success">
                        <td class="ps-3">
                            <div class="fw-semibold">Prorated Credit Applied</div>
                            <div class="text-muted small">Remaining value from current <?php echo e($payment->upgrade_plan_label); ?> plan</div>
                        </td>
                        <td class="text-end pe-3 fw-semibold">−$<?php echo e(number_format($payment->upgrade_credit, 2)); ?></td>
                    </tr>
                    <?php else: ?>
                    
                    <tr>
                        <td class="ps-3">
                            <div class="fw-semibold">Premium Membership — <?php echo e($payment->plan_label); ?></div>
                            <div class="text-muted small">New subscription</div>
                        </td>
                        <td class="text-end pe-3 fw-semibold">$<?php echo e(number_format($payment->amount, 2)); ?></td>
                    </tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
                <tfoot>
                    <tr style="border-top:2px solid #dee2e6">
                        <td class="ps-3 fw-bold fs-5">Total Due</td>
                        <td class="text-end pe-3 fw-bold fs-5 text-primary">$<?php echo e(number_format($payment->amount, 2)); ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <hr>

        
        <div class="row mb-2">
            <div class="col-sm-6">
                <div class="text-muted small fw-semibold text-uppercase mb-2">Payment Details</div>
                <table class="table table-sm table-borderless mb-0" style="font-size:.9rem">
                    <tr>
                        <td class="text-muted pe-3">Method</td>
                        <td class="fw-semibold">Cryptocurrency (<?php echo e($payment->crypto_currency); ?>)</td>
                    </tr>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($payment->wallet_address): ?>
                    <tr>
                        <td class="text-muted pe-3">Wallet</td>
                        <td class="font-monospace" style="font-size:.78rem;word-break:break-all"><?php echo e($payment->wallet_address); ?></td>
                    </tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($payment->tx_hash): ?>
                    <tr>
                        <td class="text-muted pe-3">TX Hash</td>
                        <td class="font-monospace" style="font-size:.78rem;word-break:break-all"><?php echo e($payment->tx_hash); ?></td>
                    </tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </table>
            </div>
            <div class="col-sm-6 text-sm-end mt-3 mt-sm-0">
                <div class="text-muted small fw-semibold text-uppercase mb-2">Plan Details</div>
                <div class="fw-semibold"><?php echo e($payment->plan_label); ?></div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($payment->approved_at): ?>
                    <?php
                        $days = match($payment->plan) { '30day' => 30, '90day' => 90, '365day' => 365, default => 30 };
                        $expiresAt = $payment->approved_at->copy()->addDays($days);
                    ?>
                    <div class="text-muted small">Valid until: <?php echo e($expiresAt->format('F j, Y')); ?></div>
                <?php else: ?>
                    <div class="text-muted small">Awaiting approval</div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>

        
        <div class="mt-4 pt-3 border-top text-muted small text-center">
            Thank you for your support! ❤️ — This invoice is automatically generated.
            For questions, contact <strong>support@heartsconnect.com</strong>.
        </div>

    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\dating\resources\views\premium\invoice.blade.php ENDPATH**/ ?>