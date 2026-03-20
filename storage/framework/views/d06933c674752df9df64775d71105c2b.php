<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'name' => null,
    'showText' => true,
    'textClass' => '',
    'badgeClass' => '',
    'size' => 'md',
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'name' => null,
    'showText' => true,
    'textClass' => '',
    'badgeClass' => '',
    'size' => 'md',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    use App\Models\SiteSetting as SS;

    $brandName = trim((string) ($name ?: SS::get('site_name', config('app.name', 'HeartsConnect'))));
    $brandName = $brandName !== '' ? $brandName : 'HeartsConnect';
    $parts = preg_split('/\s+/u', $brandName, -1, PREG_SPLIT_NO_EMPTY) ?: [];
    $initials = collect($parts)
        ->take(2)
        ->map(fn (string $part) => mb_strtoupper(mb_substr($part, 0, 1)))
        ->implode('');
    if ($initials === '') {
        $initials = mb_strtoupper(mb_substr($brandName, 0, 1));
    }

    $gradients = [
        'linear-gradient(135deg,#f48fb1 0%,#ce93d8 60%,#ffd54f 100%)',
        'linear-gradient(135deg,#ff8a80 0%,#e57373 55%,#ba68c8 100%)',
        'linear-gradient(135deg,#ec407a 0%,#ab47bc 50%,#7e57c2 100%)',
        'linear-gradient(135deg,#42a5f5 0%,#7e57c2 55%,#ec407a 100%)',
        'linear-gradient(135deg,#26c6da 0%,#5c6bc0 55%,#ab47bc 100%)',
    ];
    $gradient = $gradients[abs(crc32($brandName)) % count($gradients)];

    $sizeMap = [
        'sm' => ['badge' => '1.85rem', 'font' => '.72rem', 'name' => '1rem'],
        'md' => ['badge' => '2.1rem', 'font' => '.78rem', 'name' => '1.15rem'],
        'lg' => ['badge' => '2.5rem', 'font' => '.9rem', 'name' => '1.35rem'],
    ];
    $selectedSize = $sizeMap[$size] ?? $sizeMap['md'];
?>

<span <?php echo e($attributes->class(['site-logo d-inline-flex align-items-center gap-2'])); ?>>
    <span
        class="site-logo-badge <?php echo e($badgeClass); ?>"
        style="width:<?php echo e($selectedSize['badge']); ?>;height:<?php echo e($selectedSize['badge']); ?>;background:<?php echo e($gradient); ?>;font-size:<?php echo e($selectedSize['font']); ?>;"
        aria-hidden="true"
    ><?php echo e($initials); ?></span>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showText): ?>
        <span class="site-logo-name <?php echo e($textClass); ?>" style="font-size:<?php echo e($selectedSize['name']); ?>;"><?php echo e($brandName); ?></span>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</span>

<?php if (! $__env->hasRenderedOnce('1e45943b-6448-4040-87e8-2b036563f55e')): $__env->markAsRenderedOnce('1e45943b-6448-4040-87e8-2b036563f55e'); ?>
<style>
.site-logo-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 9999px;
    color: #fff;
    font-weight: 800;
    letter-spacing: .03em;
    box-shadow: 0 8px 20px rgba(0,0,0,.25);
    border: 1px solid rgba(255,255,255,.2);
    flex: 0 0 auto;
}

.site-logo-name {
    font-family: 'Playfair Display', serif;
    font-weight: 900;
    line-height: 1;
    letter-spacing: -.02em;
    background: linear-gradient(135deg,#f48fb1 0%,#ce93d8 55%,#ffd54f 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
</style>
<?php endif; ?><?php /**PATH C:\xampp\htdocs\dating\resources\views\components\site-logo.blade.php ENDPATH**/ ?>