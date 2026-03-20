@extends('layouts.app')
@section('title', 'Notifications')
@section('content')
<div class="container py-4" style="max-width:700px">

    {{-- Premium unlock banner --}}
    @if($isPremium)
    <div class="alert alert-warning d-flex align-items-center gap-2 mb-4 border-0 shadow-sm" style="background:linear-gradient(135deg,#ffd700,#ffb300);color:#5a3e00">
        <i class="bi bi-star-fill fs-5 flex-shrink-0"></i>
        <div>
            <strong>Premium Active</strong> — All your notifications are fully unlocked.
            See exactly who liked and viewed your profile below.
        </div>
    </div>
    @endif

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0"><i class="bi bi-bell me-2 text-primary"></i>Notifications</h4>
        @if($notifications->isNotEmpty())
        <form method="POST" action="{{ route('notifications.read-all') }}">
            @csrf
            <button class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-check2-all me-1"></i>Mark all read
            </button>
        </form>
        @endif
    </div>

    @if($notifications->isEmpty())
    <div class="text-center py-5">
        <div class="display-1 mb-3">🔔</div>
        <h5>No notifications yet</h5>
        <p class="text-muted">When you get matches, messages, or likes you will see them here.</p>
    </div>
    @else

    <div class="d-flex flex-column gap-2">
        @foreach($notifications as $n)
        @php
            $data    = $n->data;
            $type    = $data['type'] ?? 'generic';
            $isRead  = (bool) $n->read_at;
            $isPremiumType = in_array($type, ['premium_expired', 'premium_purchased', 'profile_liked', 'profile_viewed']);

            // Resolve href — strip stored origin so it works on any host
            $href = null;
            if (isset($data['url'])) {
                $p    = parse_url($data['url'], PHP_URL_PATH) ?? '/';
                $q    = parse_url($data['url'], PHP_URL_QUERY);
                $href = $p . ($q ? '?' . $q : '');
            }

            // Icon + colour per type
            $iconMap = [
                'new_match'              => ['bi-hearts',               'text-danger'],
                'new_message'            => ['bi-chat-heart-fill',      'text-primary'],
                'profile_liked'          => ['bi-heart-fill',           'text-danger'],
                'profile_viewed'         => ['bi-eye-fill',             'text-info'],
                'wave_received'          => ['bi-hand-index-thumb-fill','text-warning'],
                'verification_approved'  => ['bi-patch-check-fill',     'text-success'],
                'verification_rejected'  => ['bi-x-circle-fill',        'text-danger'],
                'daily_summary'          => ['bi-bar-chart-fill',       'text-primary'],
                'feature_usage'          => ['bi-lightning-fill',       'text-warning'],
                'premium_purchased'      => ['bi-star-fill',            'text-warning'],
                'premium_expired'        => ['bi-star-half',            'text-secondary'],
                'welcome'                => ['bi-balloon-heart-fill',   'text-pink'],
            ];
            [$icon, $iconClass] = $iconMap[$type] ?? ['bi-bell-fill', 'text-secondary'];

            // Label badge per type
            $labelMap = [
                'new_match'             => ['Match',        'success'],
                'new_message'           => ['Message',      'primary'],
                'profile_liked'         => ['Like',         'danger'],
                'profile_viewed'        => ['Profile View', 'info'],
                'wave_received'         => ['Wave',         'warning'],
                'verification_approved' => ['Verified',     'success'],
                'verification_rejected' => ['Verification', 'danger'],
                'daily_summary'         => ['Summary',      'secondary'],
                'feature_usage'         => ['Activity',     'secondary'],
                'premium_purchased'     => ['Premium',      'warning'],
                'premium_expired'       => ['Premium',      'secondary'],
                'welcome'               => ['Welcome',      'primary'],
            ];
            [$label, $labelColor] = $labelMap[$type] ?? ['Notification', 'secondary'];

            // Is this a "premium teaser" notification shown to non-premium users?
            $isPremiumTeaser = in_array($type, ['profile_liked', 'profile_viewed']) && ! $isPremium;

            // For premium users: resolve the actor (liker / viewer) from batch-loaded collection
            // Fall back to name stored in data, then to a DB-resolved actor
            $actor = null;
            if ($isPremium) {
                $actorId = $data['liker_id'] ?? $data['viewer_id'] ?? null;
                if ($actorId) {
                    $actor = $actors->get($actorId);
                }
            }

            // Build premium-revealed href and message
            if ($isPremium && $actor && $type === 'profile_liked') {
                $href    = '/' . ltrim(parse_url(route('profile.show', $actor->username ?? $actor->id), PHP_URL_PATH), '/');
                $revealedMsg = '<strong>' . e($actor->name) . '</strong> liked your profile! ❤️';
            } elseif ($isPremium && $actor && $type === 'profile_viewed') {
                $href    = '/' . ltrim(parse_url(route('profile.show', $actor->username ?? $actor->id), PHP_URL_PATH), '/');
                $revealedMsg = '<strong>' . e($actor->name) . '</strong> viewed your profile 👁️';
            } elseif ($isPremium && $type === 'profile_liked' && isset($data['liker_name'])) {
                // Old notification: name in data but no loaded actor
                $likerUsername = $data['liker_username'] ?? null;
                $href = $likerUsername ? '/' . ltrim(parse_url(route('profile.show', $likerUsername), PHP_URL_PATH), '/') : $href;
                $revealedMsg = '<strong>' . e($data['liker_name']) . '</strong> liked your profile! ❤️';
            } elseif ($isPremium && $type === 'profile_viewed' && isset($data['viewer_name'])) {
                $viewerUsername = $data['viewer_username'] ?? null;
                $href = $viewerUsername ? '/' . ltrim(parse_url(route('profile.show', $viewerUsername), PHP_URL_PATH), '/') : $href;
                $revealedMsg = '<strong>' . e($data['viewer_name']) . '</strong> viewed your profile 👁️';
            } else {
                $revealedMsg = null;
            }
        @endphp

        <div class="card border-0 shadow-sm position-relative {{ $isRead ? '' : 'border-start border-4 border-primary' }}"
             style="{{ $isRead ? '' : 'border-left-color:var(--bs-primary)!important' }}">
            <div class="card-body py-3 px-3 d-flex align-items-start gap-3">

                {{-- Icon bubble --}}
                <div class="flex-shrink-0 rounded-circle d-flex align-items-center justify-content-center"
                     style="width:44px;height:44px;background:rgba(0,0,0,.05)">
                    <i class="bi {{ $icon }} {{ $iconClass }} fs-5"></i>
                </div>

                {{-- Body --}}
                <div class="flex-grow-1 min-w-0">
                    <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                        <span class="badge bg-{{ $labelColor }} bg-opacity-75 small">{{ $label }}</span>
                        @if(in_array($type, ['premium_purchased', 'premium_expired', 'profile_liked', 'profile_viewed']))
                        <span class="badge bg-warning text-dark small"><i class="bi bi-star-fill me-1"></i>Premium</span>
                        @endif
                        @if(! $isRead)
                        <span class="badge bg-primary bg-opacity-25 text-primary small">New</span>
                        @endif
                    </div>

                    @if($isPremiumTeaser)
                        {{-- Locked teaser for profile_liked / profile_viewed --}}
                        <div class="d-flex align-items-center gap-2">
                            <span class="text-muted small" style="filter:blur(3.5px);user-select:none" aria-hidden="true">
                                @if($type === 'profile_liked') Someone liked your profile ❤️
                                @else Someone viewed your profile 👁️
                                @endif
                            </span>
                            <i class="bi bi-lock-fill text-warning"></i>
                        </div>
                        <div class="mt-2">
                            <a href="{{ parse_url(route('premium.show'), PHP_URL_PATH) }}"
                               class="btn btn-sm btn-warning fw-semibold stretched-link">
                                <i class="bi bi-star-fill me-1"></i>Upgrade to see who
                            </a>
                        </div>
                    @elseif($revealedMsg)
                        {{-- Premium user: show real actor name --}}
                        <p class="mb-1 small text-body">{!! $revealedMsg !!}</p>
                        <span class="text-muted" style="font-size:.7rem">
                            <i class="bi bi-clock me-1"></i>{{ $n->created_at->diffForHumans() }}
                        </span>
                    @else
                        <p class="mb-1 small text-body">{{ $data['message'] ?? '' }}</p>
                        <span class="text-muted" style="font-size:.7rem">
                            <i class="bi bi-clock me-1"></i>{{ $n->created_at->diffForHumans() }}
                        </span>
                    @endif
                </div>

                {{-- Mark-read button --}}
                @if(! $isRead)
                <form method="POST" action="{{ route('notifications.read', $n->id) }}" class="flex-shrink-0 ms-1">
                    @csrf
                    <button type="submit" class="btn btn-link p-0 text-muted" title="Mark as read"
                            style="font-size:.8rem;line-height:1;position:relative;z-index:2">
                        <i class="bi bi-check-circle fs-5"></i>
                    </button>
                </form>
                @endif
            </div>

            {{-- Clickable overlay (only if not a premium teaser — teaser has its own stretched-link) --}}
            @if($href && ! $isPremiumTeaser)
            <a href="{{ $href }}" class="stretched-link" aria-label="Open notification"></a>
            @endif
        </div>
        @endforeach
    </div>

    <div class="mt-4 d-flex justify-content-center">
        {{ $notifications->links('pagination::bootstrap-5') }}
    </div>

    @endif
</div>
@endsection

