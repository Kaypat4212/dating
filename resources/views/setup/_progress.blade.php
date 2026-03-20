<div class="d-flex justify-content-between mb-4">
@for($i = 1; $i <= 5; $i++)
<div class="flex-fill mx-1">
    <div class="rounded-pill" style="height:6px;background:{{ $i <= $current ? 'var(--bs-primary)' : '#dee2e6' }}"></div>
    <div class="text-center mt-1" style="font-size:.65rem;color:{{ $i <= $current ? 'var(--bs-primary)' : '#999' }}">{{ $i }}</div>
</div>
@endfor
</div>
