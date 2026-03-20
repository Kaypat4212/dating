@if ($status)
<div {{ $attributes->merge(['class' => '']) }}>
    <div class="d-flex align-items-center gap-2 p-3 rounded-3 mb-0" style="background:rgba(25,135,84,.18);border:1px solid rgba(25,135,84,.3);color:#d1fae5">
        <i class="bi bi-check-circle-fill" style="color:#4ade80;font-size:1.1rem;flex-shrink:0"></i>
        <span style="font-size:.875rem">{{ $status }}</span>
    </div>
</div>
@endif
