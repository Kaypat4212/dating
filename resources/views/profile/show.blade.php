@extends('layouts.app')
@section('title', $isBlocked ? 'Profile Unavailable' : $profileUser->name)
@section('content')
@php
    $photosPrivate = $profileUser->profile?->private_photos && !$isMatched && auth()->id() !== $profileUser->id;
@endphp

{{-- ── Blocked screen ──────────────────────────────────────────────────────── --}}
@if($isBlocked)
<div class="container py-5" style="max-width:520px">
    <div class="card border-0 shadow-lg rounded-4 overflow-hidden text-center">

        {{-- Gradient banner --}}
        <div class="py-5 px-4" style="background:linear-gradient(135deg,#1a1a2e 0%,#16213e 60%,#0f3460 100%)">
            @if($iBlockedThem)
                <div class="mb-3" style="font-size:3.5rem">🚫</div>
                <h4 class="fw-bold text-white mb-2">You've blocked {{ $profileUser->name }}</h4>
                <p class="mb-0" style="color:rgba(255,255,255,.6);font-size:.95rem">
                    This person cannot see your profile, like you, or send you messages while they are blocked.
                </p>
            @else
                <div class="mb-3" style="font-size:3.5rem">🔒</div>
                <h4 class="fw-bold text-white mb-2">Profile not available</h4>
                <p class="mb-0" style="color:rgba(255,255,255,.6);font-size:.95rem">
                    This profile isn't available to you right now.
                </p>
            @endif
        </div>

        {{-- Body --}}
        <div class="card-body px-4 py-4">
            @if($iBlockedThem)
                <p class="text-muted mb-4" style="font-size:.9rem">
                    You blocked <strong>{{ $profileUser->name }}</strong>. You can unblock them at any time from your blocked
                    users list or by clicking below.
                </p>
                <form method="POST" action="{{ route('block.destroy', $profileUser->id) }}"
                      onsubmit="return confirm('Unblock {{ $profileUser->name }}?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-danger px-4 mb-3 w-100" style="border-radius:.75rem">
                        <i class="bi bi-slash-circle me-2"></i>Unblock {{ $profileUser->name }}
                    </button>
                </form>
                <a href="{{ route('account.blocked') }}" class="btn btn-outline-secondary w-100" style="border-radius:.75rem">
                    <i class="bi bi-list-ul me-2"></i>Manage blocked users
                </a>
            @else
                <p class="text-muted mb-4" style="font-size:.9rem">
                    This might be because the account has been deactivated, removed, or your access to it has been restricted.
                </p>
                <a href="{{ route('discover.index') }}" class="btn btn-primary px-4 w-100 mb-3" style="border-radius:.75rem">
                    <i class="bi bi-compass me-2"></i>Discover other profiles
                </a>
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary w-100" style="border-radius:.75rem">
                    <i class="bi bi-house me-2"></i>Go to dashboard
                </a>
            @endif
        </div>
    </div>
</div>
@else
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow">
                {{-- Cover / Photos --}}
                <div class="position-relative bg-light" style="height:320px;overflow:hidden">
                    @if($photos->isNotEmpty())
                    <div id="photoCarousel" class="carousel slide h-100" data-bs-ride="carousel">
                        <div class="carousel-inner h-100">
                            @foreach($photos as $i => $photo)
                            <div class="carousel-item h-100 {{ $i === 0 ? 'active' : '' }}">
                                <img src="{{ $photo->url }}" class="d-block w-100 h-100 object-fit-cover {{ $photosPrivate ? 'blur-premium' : '' }}" alt="{{ $profileUser->name }}">
                            </div>
                            @endforeach
                        </div>
                        @if($photos->count() > 1)
                        <button class="carousel-control-prev" data-bs-target="#photoCarousel" data-bs-slide="prev"><span class="carousel-control-prev-icon"></span></button>
                        <button class="carousel-control-next" data-bs-target="#photoCarousel" data-bs-slide="next"><span class="carousel-control-next-icon"></span></button>
                        @endif
                    </div>
                    @else
                    <div class="w-100 h-100 d-flex align-items-center justify-content-center"><i class="bi bi-person-circle display-1 text-muted"></i></div>
                    @endif
                    @if($photosPrivate)
                    <div class="position-absolute top-0 start-0 end-0 bottom-0 d-flex align-items-center justify-content-center flex-column gap-2"
                         style="background:rgba(0,0,0,.45);z-index:10">
                        <i class="bi bi-lock-fill text-white" style="font-size:2.5rem"></i>
                        <span class="text-white fw-semibold small text-center px-3">Photos are private — match to unlock</span>
                    </div>
                    @endif
                </div>

                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                        <div>
                            <h2 class="fw-bold mb-1">{{ $profileUser->name }}, {{ $profileUser->age }}
                                @if($profileUser->is_verified)
                                <span title="Verified profile" style="font-size:1.2rem;vertical-align:middle;color:#1d9bf0">&#9989;</span>
                                @endif
                            </h2>
                            <x-traveling-badge :user="$profileUser" />
                            @if($profile && $profile->city)
                            <p class="text-muted mb-1"><i class="bi bi-geo-alt me-1"></i>{{ $profile->city }}@if($profile->country), {{ $profile->country }}@endif</p>
                            @endif
                            @if($compatibility)
                            <span class="badge bg-primary fs-6"><i class="bi bi-magic me-1"></i>{{ $compatibility }}% Compatibility</span>
                            @endif
                        </div>
                        @if(auth()->id() !== $profileUser->id)
                        <div class="d-flex gap-2 flex-wrap">
                            @if($isMatched)
                                <a href="{{ route('conversations.show', $conversationId) }}" class="btn btn-success"><i class="bi bi-chat-heart me-2"></i>Message</a>
                            @elseif($hasLiked)
                                <form method="POST" action="{{ route('like.destroy', $profileUser->id) }}">@csrf @method('DELETE')<button class="btn btn-outline-secondary"><i class="bi bi-heart-fill text-danger me-2"></i>Liked</button></form>
                            @else
                                <form method="POST" action="{{ route('like.store', $profileUser->id) }}">@csrf<button class="btn btn-primary"><i class="bi bi-heart me-2"></i>Like</button></form>
                            @endif
                            {{-- Wave button --}}
                            <button class="btn btn-outline-warning wave-btn" data-user="{{ $profileUser->id }}" title="Send a wave">👋 Wave</button>
                            @if(!$isMatched)
                            {{-- Secret message button (only when not yet matched) --}}
                            <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#secretMsgModal" title="Send a secret message revealed only if you both match">
                                💌 Secret Message
                            </button>
                            @endif
                            {{-- Tip button (only show to other users) --}}
                            <button class="btn btn-outline-success tip-btn" data-user="{{ $profileUser->id }}" data-name="{{ $profileUser->name }}" title="Send a tip"><i class="bi bi-coin me-1"></i>Tip</button>
                            <button type="button" class="btn btn-outline-secondary report-btn"
                                data-user="{{ $profileUser->id }}"
                                data-name="{{ $profileUser->name }}"
                                title="Report {{ $profileUser->name }}"
                                aria-label="Report {{ $profileUser->name }}">
                                <i class="bi bi-flag"></i><span class="d-none d-sm-inline ms-1">Report</span>
                            </button>
                            <button type="button" class="btn btn-outline-danger block-btn"
                                data-user="{{ $profileUser->id }}"
                                data-name="{{ $profileUser->name }}"
                                title="Block {{ $profileUser->name }}"
                                aria-label="Block {{ $profileUser->name }}">
                                <i class="bi bi-slash-circle"></i><span class="d-none d-sm-inline ms-1">Block</span>
                            </button>
                        </div>
                        @endif
                    </div>

                    @if($profile && $profile->tagline)
                    <blockquote class="blockquote mt-3 mb-0 fs-5 fst-italic text-muted border-start border-primary border-3 ps-3">
                        "{{ $profile->tagline }}"
                    </blockquote>
                    @endif

                    @if($profile && $profile->about)
                    <div class="mt-4">
                        <h6 class="fw-bold">About {{ $profileUser->name }}</h6>
                        <p class="mb-0">{{ $profile->about }}</p>
                    </div>
                    @endif

                    {{-- Profile details --}}
                    @if($profile)
                    <div class="mt-4">
                        <h6 class="fw-bold mb-3">Profile Details</h6>
                        <div class="row g-2 text-center">
                            @if($profile->height_cm)<div class="col-4 col-md-3"><div class="card border-0 bg-light p-2 rounded-3"><div class="fw-semibold">{{ $profile->height_cm }} cm</div><div class="text-muted" style="font-size:.7rem">Height</div></div></div>@endif
                            @if($profile->body_type)<div class="col-4 col-md-3"><div class="card border-0 bg-light p-2 rounded-3"><div class="fw-semibold text-capitalize">{{ $profile->body_type }}</div><div class="text-muted" style="font-size:.7rem">Body Type</div></div></div>@endif
                            @if($profile->relationship_goal)<div class="col-4 col-md-3"><div class="card border-0 bg-light p-2 rounded-3"><div class="fw-semibold text-capitalize">{{ str_replace('_', ' ', $profile->relationship_goal) }}</div><div class="text-muted" style="font-size:.7rem">Looking for</div></div></div>@endif
                            @if($profile->wants_children)<div class="col-4 col-md-3"><div class="card border-0 bg-light p-2 rounded-3"><div class="fw-semibold">{{ ['yes'=>'Yes','no'=>'No','open'=>'Open to it','not_sure'=>'Not sure'][$profile->wants_children] ?? ucfirst(str_replace('_',' ',$profile->wants_children)) }}</div><div class="text-muted" style="font-size:.7rem">Wants kids</div></div></div>@endif
                            @if($profile->education_level)<div class="col-4 col-md-3"><div class="card border-0 bg-light p-2 rounded-3"><div class="fw-semibold text-capitalize">{{ $profile->education_level }}</div><div class="text-muted" style="font-size:.7rem">Education</div></div></div>@endif
                            @if($profile->smoking_habit)<div class="col-4 col-md-3"><div class="card border-0 bg-light p-2 rounded-3"><div class="fw-semibold text-capitalize">{{ $profile->smoking_habit }}</div><div class="text-muted" style="font-size:.7rem">Smoking</div></div></div>@endif
                            @if($profile->drinking_habit)<div class="col-4 col-md-3"><div class="card border-0 bg-light p-2 rounded-3"><div class="fw-semibold text-capitalize">{{ $profile->drinking_habit }}</div><div class="text-muted" style="font-size:.7rem">Drinking</div></div></div>@endif
                        </div>
                    </div>
                    @endif

                    {{-- Mood Status --}}
                    @if($profile && $profile->mood_status)
                    <div class="mb-3">
                        <span class="badge bg-warning-subtle text-warning-emphasis px-3 py-2 rounded-pill fs-6">
                            <i class="bi bi-emoji-smile me-1"></i>{{ $profile->mood_status }}
                        </span>
                    </div>
                    @endif

                    {{-- Availability Status --}}
                    @if($profile && $profile->availability_status)
                    @php
                        $availLabels = [
                            'free_tonight'  => ['🟢', 'Free tonight',     'success'],
                            'busy_this_week'=> ['🔴', 'Busy this week',   'danger'],
                            'looking_irl'   => ['📍', 'Looking to meet IRL','info'],
                            'open_to_chat'  => ['💬', 'Open to chat',     'primary'],
                            'offline'       => ['⚫', 'Offline',          'secondary'],
                        ];
                        [$avIcon, $avLabel, $avColor] = $availLabels[$profile->availability_status] ?? ['⚪', $profile->availability_status, 'secondary'];
                    @endphp
                    <div class="mb-3">
                        <span class="badge bg-{{ $avColor }}-subtle text-{{ $avColor }}-emphasis px-3 py-2 rounded-pill fs-6">
                            {{ $avIcon }} {{ $avLabel }}
                        </span>
                    </div>
                    @endif

                    {{-- Vibe Badge --}}
                    @if($profile && $profile->vibe_badge)
                    @php
                        $vibeLabels = [
                            'adventurer'       => ['🏕️', 'The Adventurer'],
                            'homebody'         => ['🛋️', 'The Homebody'],
                            'social_butterfly' => ['🦋', 'The Social Butterfly'],
                            'intellectual'     => ['🧠', 'The Intellectual'],
                            'romantic'         => ['🌹', 'The Romantic'],
                            'wild_card'        => ['🃏', 'The Wild Card'],
                        ];
                        [$vbIcon, $vbLabel] = $vibeLabels[$profile->vibe_badge] ?? ['✨', ucfirst($profile->vibe_badge)];
                    @endphp
                    <div class="mb-3">
                        <span class="badge px-3 py-2 rounded-pill fs-6"
                              style="background:linear-gradient(135deg,#f5f0ff,#ede9fe);color:#5b21b6;border:1px solid #c4b5fd"
                              title="Vibe type">
                            {{ $vbIcon }} {{ $vbLabel }}
                        </span>
                    </div>
                    @endif

                    {{-- Interests --}}
                    @if($profile && $profile->interests->isNotEmpty())
                    <div class="mt-4">
                        <h6 class="fw-bold mb-2">Interests</h6>
                        @foreach($profile->interests as $interest)
                        <span class="badge bg-primary-subtle text-primary-emphasis me-1 mb-1 px-3 py-2 rounded-pill" style="font-size:.8rem">{{ $interest->name }}</span>
                        @endforeach
                    </div>
                    @endif

                    {{-- Voice Prompts --}}
                    @if(!empty($voicePrompts) && $voicePrompts->isNotEmpty())
                    <div class="mt-4">
                        <h6 class="fw-bold mb-3"><i class="bi bi-mic-fill text-danger me-2"></i>Voice Prompts</h6>
                        <div class="d-flex flex-column gap-3">
                            @foreach($voicePrompts as $vp)
                            <div class="card border-0 bg-light rounded-3 p-3">
                                <p class="fw-semibold small mb-2 text-muted">"{{ $vp->question->prompt_text }}"</p>
                                <audio controls preload="none"
                                       src="{{ Storage::url($vp->audio_path) }}"
                                       class="w-100 voice-prompt-audio"
                                       style="height:40px;"
                       data-play-url="{{ route('extras.voice.play', $vp->id) }}"
                                ></audio>
                                @if($vp->duration_seconds)
                                <span class="text-muted" style="font-size:.7rem">{{ $vp->duration_seconds }}s &middot; {{ $vp->plays_count }} plays</span>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Pinned Badges --}}
                    @php $pinnedBadges = $profileUser->badges()->wherePivot('is_pinned', true)->get(); @endphp
                    @if($pinnedBadges->isNotEmpty())
                    <div class="mt-4">
                        <h6 class="fw-bold mb-3"><i class="bi bi-trophy-fill text-warning me-2"></i>Badges</h6>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($pinnedBadges as $badge)
                            <span class="badge rounded-pill d-flex align-items-center gap-1 px-3 py-2"
                                  style="background:linear-gradient(135deg,#fff9f0,#fde68a);color:#92400e;font-size:.8rem;border:1px solid #fde68a"
                                  title="{{ $badge->description }}">
                                {{ $badge->emoji }} {{ $badge->name }}
                            </span>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Icebreakers --}}
                    @if(!empty($icebreakerAnswers) && $icebreakerAnswers->isNotEmpty())
                    <div class="mt-4">
                        <h6 class="fw-bold mb-3"><i class="bi bi-snow2 text-primary me-2"></i>Icebreakers</h6>
                        <div class="row g-2">
                            @foreach($icebreakerAnswers as $ia)
                            @php
                                $q = $ia->question;
                                $isChoice = in_array($q->type, ['would_you_rather', 'this_or_that']);
                            @endphp
                            <div class="col-sm-6">
                                <div class="card border-0 bg-light rounded-3 p-3 h-100">
                                    <p class="small text-muted mb-2 fw-semibold" style="font-size:.8rem">{{ $q->question }}</p>
                                    @if($isChoice && $ia->choice)
                                        @php $chosen = $ia->choice === 'a' ? $q->option_a : $q->option_b; @endphp
                                        <span class="badge bg-primary rounded-pill px-3 py-2">{{ $chosen }}</span>
                                    @elseif($ia->answer)
                                        <p class="mb-0 fw-semibold" style="font-size:.9rem">{{ $ia->answer }}</p>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Tip Modal --}}
<div class="modal fade" id="tipModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-coin text-warning me-2"></i>Send a Tip</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="tipForm">
        @csrf
        <input type="hidden" id="tip-recipient-id" name="recipient_id">
        <div class="modal-body">
          <p class="text-muted small mb-3">Send credits to <strong id="tip-recipient-name"></strong> as a token of appreciation.</p>
          <div class="mb-3">
            <label class="form-label fw-semibold">Amount (credits)</label>
            <input type="number" class="form-control" id="tip-amount" name="amount" min="1" placeholder="e.g. 5" required>
            <div class="form-text">Your balance: <strong id="tip-my-balance">…</strong> credits</div>
          </div>
          <div class="mb-2">
            <label class="form-label fw-semibold">Message <span class="text-muted fw-normal">(optional)</span></label>
            <input type="text" class="form-control" id="tip-message" name="message" maxlength="255" placeholder="e.g. You're amazing!">
          </div>
          <div id="tip-alert" class="d-none"></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success" id="tip-submit-btn">
            <span id="tip-spinner" class="spinner-border spinner-border-sm d-none me-1"></span>
            <i class="bi bi-send me-1"></i>Send Tip
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
{{-- Block Confirmation Modal --}}
<div class="modal fade" id="blockConfirmModal" tabindex="-1" aria-hidden="true" aria-labelledby="blockConfirmLabel">
  <div class="modal-dialog modal-sm modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title text-danger" id="blockConfirmLabel"><i class="bi bi-slash-circle me-2"></i>Block User?</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cancel"></button>
      </div>
      <div class="modal-body">
        <p class="mb-1">Block <strong id="block-confirm-name"></strong>?</p>
        <p class="text-muted small mb-0">They won't be able to contact you or see your profile. You can unblock them later from your account settings.</p>
      </div>
      <div class="modal-footer border-0 pt-0">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger" id="block-confirm-btn">
          <span id="block-spinner" class="spinner-border spinner-border-sm d-none me-1" role="status" aria-hidden="true"></span>
          <i class="bi bi-slash-circle me-1"></i>Block
        </button>
      </div>
    </div>
  </div>
</div>

{{-- Report User Modal --}}
<div class="modal fade" id="reportModal" tabindex="-1" aria-hidden="true" aria-labelledby="reportModalLabel">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title text-warning" id="reportModalLabel"><i class="bi bi-flag me-2"></i>Report User</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cancel"></button>
      </div>
      <form method="POST" id="reportForm" action="{{ route('report.store', $profileUser->id) }}">
        @csrf
        <input type="hidden" name="reported_user_id" value="{{ $profileUser->id }}">
        <div class="modal-body">
          <p class="mb-3">Reporting <strong id="report-confirm-name"></strong>. Please select a reason:</p>
          <div class="mb-3">
            <select name="reason" id="report-reason" class="form-select" required>
              <option value="" disabled selected>-- Select a reason --</option>
              <option value="fake_profile">Fake or scam profile</option>
              <option value="inappropriate_photos">Inappropriate photos</option>
              <option value="harassment">Harassment or abuse</option>
              <option value="spam">Spam or bot</option>
              <option value="underage">Underage user</option>
              <option value="other">Other</option>
            </select>
          </div>
          <div class="mb-2">
            <textarea name="description" class="form-control" rows="3" maxlength="1000" placeholder="Additional details (optional)"></textarea>
          </div>
        </div>
        <div class="modal-footer border-0 pt-0">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-warning" id="report-submit-btn">
            <i class="bi bi-flag me-1"></i>Submit Report
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection

{{-- ── Secret Message Modal ─────────────────────────────────────────────── --}}
@if(auth()->id() !== $profileUser->id && !$isMatched)
<div class="modal fade" id="secretMsgModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title">💌 Secret Message</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p class="text-muted small">Write a message that will only be revealed to <strong>{{ $profileUser->name }}</strong> if you both match.</p>
        <textarea id="secretMsgBody" class="form-control" rows="4" maxlength="500"
                  placeholder="Your secret message…"></textarea>
        <div id="secretMsgFeedback" class="mt-2 small"></div>
      </div>
      <div class="modal-footer border-0 pt-0">
        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary btn-sm" id="secretMsgSend">Send Secret</button>
      </div>
    </div>
  </div>
</div>
@endif

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

// ── Voice Prompt play-count tracking ─────────────────────────────────────────
document.querySelectorAll('.voice-prompt-audio').forEach(function (audio) {
    var counted = false;
    audio.addEventListener('play', function () {
        if (counted) return;
        counted = true;
        var url  = audio.dataset.playUrl;
        var csrf = document.querySelector('meta[name="csrf-token"]');
        if (!url || !csrf) return;
        fetch(url, {
            method: 'GET',
            headers: { 'Accept': 'application/json' },
        }).catch(function () {});
    });
});

// ── Wave ──────────────────────────────────────────────────────────────────────
document.querySelectorAll('.wave-btn').forEach(btn => {
    btn.addEventListener('click', async () => {
        const userId = btn.dataset.user;
        const csrf   = document.querySelector('meta[name="csrf-token"]').content;
        btn.disabled = true;
        try {
            const res = await fetch('{{ route("wave.store", ":u") }}'.replace(':u', userId), {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/json' },
                body: JSON.stringify({ emoji: '👋' })
            });
            if (res.ok) {
                btn.textContent = '✅ Waved!';
                btn.classList.remove('btn-outline-warning');
                btn.classList.add('btn-success');
            } else {
                btn.disabled = false;
            }
        } catch {
            btn.disabled = false;
        }
    });
});

// ── Tip modal ─────────────────────────────────────────────────────────────────
let _tipModalInstance  = null;
let _tipCurrentBalance = 0;
let _tipBalanceLoaded  = false;
function getTipModal() {
    if (!_tipModalInstance) {
        _tipModalInstance = new bootstrap.Modal(document.getElementById('tipModal'));
    }
    return _tipModalInstance;
}

function tipAlert(type, html) {
    const el = document.getElementById('tip-alert');
    el.className = type ? 'alert alert-' + type : 'd-none';
    el.innerHTML = html || '';
}

document.querySelectorAll('.tip-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        // Reset form state
        document.getElementById('tipForm').reset();
        document.getElementById('tip-recipient-id').value = btn.dataset.user;
        document.getElementById('tip-recipient-name').textContent = btn.dataset.name;
        tipAlert(null, '');
        _tipBalanceLoaded = false;

        const submitBtn = document.getElementById('tip-submit-btn');
        const balEl     = document.getElementById('tip-my-balance');
        submitBtn.disabled = true;
        balEl.innerHTML    = '<span class="spinner-border spinner-border-sm" style="width:.9rem;height:.9rem"></span>';

        // Fetch current balance
        fetch('{{ route("wallet.balance") }}')
            .then(r => r.json())
            .then(d => {
                _tipCurrentBalance = parseInt(d.balance ?? 0, 10);
                _tipBalanceLoaded  = true;
                balEl.textContent  = _tipCurrentBalance;
                if (_tipCurrentBalance <= 0) {
                    tipAlert('warning',
                        '<i class="bi bi-exclamation-triangle me-1"></i>' +
                        'You have no credits. <a href="{{ route("wallet.index") }}" class="alert-link fw-semibold">Fund your wallet</a> first.');
                    submitBtn.disabled = true;
                } else {
                    submitBtn.disabled = false;
                }
            })
            .catch(() => {
                _tipBalanceLoaded  = true;
                _tipCurrentBalance = 0;
                balEl.textContent  = '?';
                tipAlert('warning', '<i class="bi bi-exclamation-triangle me-1"></i>Could not load balance. Try refreshing.');
            });

        getTipModal().show();
    });
});

// Live amount validation
document.getElementById('tip-amount')?.addEventListener('input', function () {
    if (!_tipBalanceLoaded) return;
    const amount    = parseInt(this.value, 10);
    const submitBtn = document.getElementById('tip-submit-btn');
    if (isNaN(amount) || amount < 1) {
        tipAlert(null, '');
        submitBtn.disabled = _tipCurrentBalance <= 0;
        return;
    }
    if (amount > _tipCurrentBalance) {
        tipAlert('warning',
            '<i class="bi bi-exclamation-triangle me-1"></i>' +
            'Amount (<strong>' + amount + '</strong>) exceeds your balance of <strong>' + _tipCurrentBalance + '</strong> credits.');
        submitBtn.disabled = true;
    } else {
        tipAlert(null, '');
        submitBtn.disabled = false;
    }
});

document.getElementById('tipForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const submitBtn = document.getElementById('tip-submit-btn');
    const spinner   = document.getElementById('tip-spinner');
    const amount    = parseInt(document.getElementById('tip-amount').value, 10);

    if (isNaN(amount) || amount < 1) {
        tipAlert('danger', '<i class="bi bi-exclamation-circle me-1"></i>Please enter a valid amount (minimum 1 credit).');
        return;
    }
    if (_tipBalanceLoaded && amount > _tipCurrentBalance) {
        tipAlert('danger',
            '<i class="bi bi-exclamation-circle me-1"></i>' +
            'Insufficient balance — you only have <strong>' + _tipCurrentBalance + '</strong> credits.');
        return;
    }

    submitBtn.disabled = true;
    spinner.classList.remove('d-none');
    tipAlert(null, '');

    const csrf = document.querySelector('meta[name="csrf-token"]').content;
    const body = new FormData(document.getElementById('tipForm'));
    try {
        const res  = await fetch('{{ route("tips.send") }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrf },
            body
        });
        let data = {};
        try { data = await res.json(); } catch (_) {}

        if (res.ok && data.success) {
            tipAlert('success', '<i class="bi bi-check-circle me-1"></i>Gift sent! 🎉');
            if (data.new_balance !== undefined) {
                _tipCurrentBalance = data.new_balance;
                document.getElementById('tip-my-balance').textContent = _tipCurrentBalance;
            }
            setTimeout(() => getTipModal().hide(), 1800);
        } else {
            let msg = data.error || data.message || 'Something went wrong.';
            if (res.status === 422 && data.errors) {
                msg = Object.values(data.errors).flat().join(' ');
            }
            if (res.status === 429) {
                msg = 'Too many requests — please wait a moment before trying again.';
            }
            tipAlert('danger', '<i class="bi bi-exclamation-circle me-1"></i>' + msg);
        }
    } catch {
        tipAlert('danger', '<i class="bi bi-wifi-off me-1"></i>Network error — please try again.');
    } finally {
        submitBtn.disabled = false;
        spinner.classList.add('d-none');
    }
});

// ── Block user ───────────────────────────────────────────────────────────────
function blockToast(msg, type) {
    const icons     = { success: 'bi-slash-circle-fill', danger: 'bi-exclamation-circle' };
    const container = document.getElementById('toastContainer');
    if (!container) return;
    const el = document.createElement('div');
    el.className = 'toast align-items-center text-bg-' + type + ' border-0';
    el.setAttribute('role', 'alert');
    el.setAttribute('aria-atomic', 'true');
    el.innerHTML =
        '<div class="d-flex"><div class="toast-body fw-semibold">' +
        '<i class="bi ' + (icons[type] || 'bi-info-circle') + ' me-2"></i>' + msg +
        '</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div>';
    container.appendChild(el);
    if (window.bootstrap?.Toast) { new bootstrap.Toast(el, { delay: 3500 }).show(); }
}

const blockConfirmModal = new bootstrap.Modal(document.getElementById('blockConfirmModal'));
let _blockTargetBtn = null;

document.querySelectorAll('.block-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        _blockTargetBtn = btn;
        document.getElementById('block-confirm-name').textContent = btn.dataset.name || 'this user';
        const confirmBtn = document.getElementById('block-confirm-btn');
        confirmBtn.disabled = false;
        document.getElementById('block-spinner').classList.add('d-none');
        blockConfirmModal.show();
    });
});

document.getElementById('block-confirm-btn').addEventListener('click', () => {
    if (!_blockTargetBtn) return;
    const csrf       = document.querySelector('meta[name="csrf-token"]').content;
    const confirmBtn = document.getElementById('block-confirm-btn');
    const spinner    = document.getElementById('block-spinner');
    const name       = document.getElementById('block-confirm-name').textContent;
    confirmBtn.disabled = true;
    spinner.classList.remove('d-none');

    fetch('{{ route("block.store", ":u") }}'.replace(':u', _blockTargetBtn.dataset.user), {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/json' },
    })
    .then(r => r.json())
    .then(d => {
        blockConfirmModal.hide();
        if (d.blocked) {
            blockToast(name + ' has been blocked.', 'success');
            setTimeout(() => { window.location.href = '{{ route("discover.index") }}'; }, 1800);
        } else {
            confirmBtn.disabled = false;
            spinner.classList.add('d-none');
            blockToast(d.error || 'Could not block user.', 'danger');
        }
    })
    .catch(() => {
        blockConfirmModal.hide();
        confirmBtn.disabled = false;
        spinner.classList.add('d-none');
        blockToast('Network error — please try again.', 'danger');
    });
});

// ── Report user ──────────────────────────────────────────────────────────────
const reportModal = new bootstrap.Modal(document.getElementById('reportModal'));

document.querySelectorAll('.report-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.getElementById('report-confirm-name').textContent = btn.dataset.name || 'this user';
        document.getElementById('report-reason').value = '';
        reportModal.show();
    });
});

}); // end DOMContentLoaded
</script>
@endpush

@push('scripts')
<script>
// ── Secret Message ─────────────────────────────────────────────────────────
(function () {
    const sendBtn = document.getElementById('secretMsgSend');
    if (!sendBtn) return;
    const csrf = document.querySelector('meta[name="csrf-token"]').content;
    sendBtn.addEventListener('click', async () => {
        const body = document.getElementById('secretMsgBody').value.trim();
        const fb   = document.getElementById('secretMsgFeedback');
        if (!body) { fb.innerHTML = '<span class="text-danger">Please write a message first.</span>'; return; }
        sendBtn.disabled = true;
        try {
            const res = await fetch('{{ route("secret-messages.store") }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/json' },
                body: JSON.stringify({ receiver_id: {{ $profileUser->id }}, body })
            });
            const data = await res.json();
            if (res.ok) {
                fb.innerHTML = '<span class="text-success">💌 Sent! It will appear in the chat only if you match.</span>';
                sendBtn.textContent = 'Sent!';
            } else {
                fb.innerHTML = `<span class="text-danger">${data.error ?? 'Failed to send.'}</span>`;
                sendBtn.disabled = false;
            }
        } catch (e) {
            fb.innerHTML = '<span class="text-danger">Network error.</span>';
            sendBtn.disabled = false;
        }
    });
})();
</script>
@endpush
