
<div id="safety-banner" class="d-none"
     style="margin-left:calc(-50vw + 50%);margin-right:calc(-50vw + 50%);
            width:100vw;box-sizing:border-box;padding:.75rem 1rem 0;margin-bottom:1rem">
    <div style="
        background:linear-gradient(135deg,#fffbeb,#fef3c7);
        border-radius:1rem;
        box-shadow:0 2px 8px rgba(0,0,0,.08);
        padding:1rem 1.15rem;
        max-width:560px;
        margin:0 auto;
        box-sizing:border-box;
    ">
        <div style="display:flex;align-items:flex-start;gap:.75rem">
            <span style="font-size:1.4rem;line-height:1.2;flex-shrink:0">&#x26A0;&#xFE0F;</span>
            <div style="flex:1;min-width:0">
                <div style="font-weight:700;color:#92400e;margin-bottom:.35rem;font-size:.95rem">
                    Stay Safe &mdash; Real people, real precautions
                </div>
                <ul style="margin:.2rem 0 .65rem 0;padding-left:1.15rem;font-size:.83rem;color:#92400e;line-height:1.65">
                    <li>Look for the <strong>&#x2705; Verified badge</strong> &mdash; verified members have confirmed their identity.</li>
                    <li>Never send money, gift cards, or cryptocurrency to someone you haven&rsquo;t met in person.</li>
                    <li>Be cautious of profiles that quickly move the conversation off the app.</li>
                    <li>If something feels wrong, <strong>report the profile</strong> using the flag icon.</li>
                </ul>
                <div style="display:flex;flex-wrap:wrap;gap:.4rem">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (! (auth()->user()?->is_verified)): ?>
                    <a href="<?php echo e(route('verify.show')); ?>"
                       style="display:inline-flex;align-items:center;gap:.3rem;background:#f59e0b;color:#fff;
                              border:none;padding:.3rem .85rem;border-radius:.45rem;
                              font-size:.82rem;font-weight:600;text-decoration:none;white-space:nowrap">
                        <i class="bi bi-patch-check"></i> Get Verified
                    </a>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <button id="safety-dismiss"
                            style="background:transparent;color:#92400e;border:1.5px solid #f59e0b;
                                   padding:.3rem .85rem;border-radius:.45rem;
                                   font-size:.82rem;font-weight:600;cursor:pointer;white-space:nowrap">
                        Got it, dismiss
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    const STORAGE_KEY = 'hc_safety_dismissed';
    const banner      = document.getElementById('safety-banner');
    const dismissBtn  = document.getElementById('safety-dismiss');

    if (!localStorage.getItem(STORAGE_KEY)) {
        banner.classList.remove('d-none');
    }

    dismissBtn?.addEventListener('click', () => {
        banner.classList.add('d-none');
        localStorage.setItem(STORAGE_KEY, '1');
    });
})();
</script>
<?php /**PATH C:\xampp\htdocs\dating\resources\views\partials\safety-banner.blade.php ENDPATH**/ ?>