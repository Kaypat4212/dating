{{--
    Online Status Dot

    Usage:
        <x-online-status-dot :user="$user" />
        <x-online-status-dot :user="$user" size="10" />
        <x-online-status-dot :user="$user" :label="true" />

    Props:
        $user   — App\Models\User instance
        $size   — dot size in px (default 10)
        $label  — whether to show the text label (default false)
--}}
@props(['user', 'size' => 10, 'label' => false])

@php
    $status = $user->onlineStatus();
    $color  = $user->onlineStatusColor();
    $text   = $user->onlineStatusLabel();
@endphp

<span class="d-inline-flex align-items-center gap-1" title="{{ $text }}">
    <span style="display:inline-block;width:{{ $size }}px;height:{{ $size }}px;border-radius:50%;
                 background:{{ $color }};flex-shrink:0;
                 {{ $status === 'online' ? 'box-shadow:0 0 0 2px rgba(34,197,94,.35)' : '' }}">
    </span>
    @if($label)
    <span class="small" style="color:{{ $color }};white-space:nowrap">{{ $text }}</span>
    @endif
</span>
