@props([
    'name' => null,
    'showText' => true,
    'textClass' => '',
    'badgeClass' => '',
    'size' => 'md',
])

@php
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
@endphp

<span {{ $attributes->class(['site-logo d-inline-flex align-items-center gap-2']) }}>
    <span
        class="site-logo-badge {{ $badgeClass }}"
        style="width:{{ $selectedSize['badge'] }};height:{{ $selectedSize['badge'] }};background:{{ $gradient }};font-size:{{ $selectedSize['font'] }};"
        aria-hidden="true"
    >{{ $initials }}</span>

    @if($showText)
        <span class="site-logo-name {{ $textClass }}" style="font-size:{{ $selectedSize['name'] }};">{{ $brandName }}</span>
    @endif
</span>

@once
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
@endonce