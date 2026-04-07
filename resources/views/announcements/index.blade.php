@extends('layouts.app')
@section('title', "What's New")

@push('head')
<style>
.wn-hero {
    background: linear-gradient(135deg, #7c3aed 0%, #c2185b 60%, #f97316 100%);
    border-radius: 20px;
    color: #fff;
    padding: 2.5rem 2rem;
    margin-bottom: 2rem;
    position: relative;
    overflow: hidden;
}
.wn-hero::before {
    content: '';
    position: absolute;
    inset: 0;
    background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.04'%3E%3Ccircle cx='30' cy='30' r='20'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    pointer-events: none;
}
.wn-card {
    border: 1px solid var(--bs-border-color);
    border-radius: 16px;
    transition: box-shadow .2s, transform .15s;
    background: var(--bs-body-bg);
    overflow: hidden;
}
.wn-card:hover {
    box-shadow: 0 6px 24px rgba(0,0,0,.08);
    transform: translateY(-2px);
}
.wn-card.unread {
    border-left: 4px solid #7c3aed;
}
.wn-card-header {
    display: flex;
    align-items: center;
    gap: .75rem;
    padding: 1.1rem 1.25rem .6rem;
    border-bottom: 1px solid var(--bs-border-color);
}
.wn-type-icon {
    font-size: 1.5rem;
    line-height: 1;
    flex-shrink: 0;
}
.wn-card-body {
    padding: 1rem 1.25rem 1.25rem;
}
.wn-card-body p:last-child { margin-bottom: 0; }
.wn-version-tag {
    font-size: .65rem;
    font-weight: 700;
    padding: 2px 8px;
    border-radius: 20px;
    background: var(--bs-secondary-bg);
    color: var(--bs-secondary-color);
    letter-spacing: .04em;
    text-transform: uppercase;
}
.wn-dot {
    display: inline-block;
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #7c3aed;
    flex-shrink: 0;
}
.wn-body-html h2 { font-size: 1.05rem; font-weight: 700; margin-top: 1rem; }
.wn-body-html h3 { font-size: .95rem; font-weight: 700; margin-top: .8rem; }
.wn-body-html ul, .wn-body-html ol { padding-left: 1.4rem; }
.wn-body-html a { color: #7c3aed; }
</style>
@endpush

@section('content')
<div class="container py-4" style="max-width:720px">

    {{-- Hero --}}
    <div class="wn-hero">
        <div class="d-flex align-items-center gap-3 mb-2">
            <span style="font-size:2.5rem">🎉</span>
            <div>
                <h2 class="fw-bold mb-1" style="font-size:1.8rem">What's New</h2>
                <p class="mb-0 opacity-75">All the latest updates, features and messages from the team.</p>
            </div>
        </div>
        <div class="mt-3">
            <span class="badge rounded-pill" style="background:rgba(255,255,255,.2);font-size:.78rem">
                {{ $announcements->count() }} announcement{{ $announcements->count() !== 1 ? 's' : '' }}
            </span>
        </div>
    </div>

    @forelse($announcements as $item)
    <div class="wn-card mb-3 {{ $item['is_read'] ? '' : 'unread' }}">
        <div class="wn-card-header">
            <span class="wn-type-icon">{{ $item['type_icon'] }}</span>
            <div class="flex-grow-1 overflow-hidden">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="fw-bold text-truncate">{{ $item['title'] }}</span>
                    @if(!$item['is_read'])
                        <span class="wn-dot" title="Unread"></span>
                    @endif
                    @if($item['badge_label'])
                        <span class="badge bg-{{ $item['badge_color'] }} rounded-pill" style="font-size:.65rem">{{ $item['badge_label'] }}</span>
                    @endif
                    @if($item['version'])
                        <span class="wn-version-tag">{{ $item['version'] }}</span>
                    @endif
                </div>
                <div class="text-muted mt-1" style="font-size:.73rem">
                    <span class="badge bg-{{ $item['type_color'] }}-subtle text-{{ $item['type_color'] }}" style="font-size:.65rem">
                        {{ ucfirst($item['type']) }}
                    </span>
                    @if($item['published_at'])
                        <span class="ms-2">{{ $item['published_at'] }}</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="wn-card-body">
            <div class="wn-body-html">{!! $item['body'] !!}</div>
        </div>
    </div>
    @empty
    <div class="text-center py-5">
        <div style="font-size:3.5rem">📭</div>
        <h5 class="fw-bold mt-3">Nothing new yet</h5>
        <p class="text-muted">Check back soon for updates and announcements.</p>
    </div>
    @endforelse

</div>
@endsection
