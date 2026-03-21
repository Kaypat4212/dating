@extends('layouts.app')
@section('title', 'Chat with ' . $other->name)

@push('head')
<style>
main { padding-bottom: 0 !important; }
/* ── Gift bubble ────────────────────────────────────────── */
.gift-bubble {
    background: linear-gradient(135deg,#fff0f6,#ffe4ef);
    border-radius: 16px;
    border: 1px solid rgba(194,24,91,.2);
    min-width: 120px;
    animation: giftPop .35s cubic-bezier(.34,1.56,.64,1) both;
}
@keyframes giftPop { from { transform:scale(.6); opacity:0; } to { transform:scale(1); opacity:1; } }
/* ── Gift popover ───────────────────────────────────────── */
.chat-gift-popover {
    position: absolute;
    bottom: calc(100% + 8px);
    left: 50%;
    transform: translateX(-50%);
    background: #fff;
    border: 1px solid #e0e0e0;
    border-radius: 16px;
    padding: 12px;
    width: 220px;
    box-shadow: 0 4px 20px rgba(0,0,0,.15);
    z-index: 200;
}
.gift-popover-title {
    font-size: .75rem;
    font-weight: 600;
    color: #888;
    text-transform: uppercase;
    letter-spacing: .05em;
    margin-bottom: 10px;
    text-align: center;
}
.gift-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 8px;
}
.gift-choice {
    display: flex;
    flex-direction: column;
    align-items: center;
    background: #f8f8f8;
    border: none;
    border-radius: 12px;
    padding: 8px 4px;
    cursor: pointer;
    transition: background .15s;
    line-height: 1;
}
.gift-choice:hover { background: #fce4ec; }
.gift-emoji { font-size: 1.4rem; }
.gift-label { font-size: .6rem; color: #888; margin-top: 3px; }
/* ── Redesigned 2-row chat footer ──────────────────────────── */
.chat-footer {
    flex-direction: column !important;
    align-items: stretch !important;
    gap: 0 !important;
    padding: 0 !important;
}
/* Row 1 – action icon strip */
.chat-actions-bar {
    display: flex;
    align-items: center;
    gap: .2rem;
    padding: .38rem .8rem;
    border-bottom: 1px solid var(--bs-border-color);
}
.chat-act-btn {
    width: 36px; height: 36px; border-radius: 50%; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
    padding: 0; border: none;
    background: transparent; color: var(--bs-secondary-color);
    font-size: 1rem; cursor: pointer;
    transition: background .15s, color .15s;
}
.chat-act-btn:hover,
.chat-act-btn:focus { background: var(--bs-secondary-bg); color: var(--bs-body-color); outline: none; }
.chat-act-tip { color: #f59e0b !important; }
.chat-act-tip:hover { background: rgba(245,158,11,.12) !important; color: #d97706 !important; }
/* Row 2 – textarea + send */
.chat-input-row {
    display: flex;
    align-items: flex-end;
    gap: .45rem;
    padding: .5rem .75rem .6rem;
}
.chat-textarea {
    min-height: 52px !important;
    max-height: 160px !important;
    font-size: .95rem !important;
    line-height: 1.5 !important;
}
.chat-send-btn {
    width: 44px !important; height: 44px !important;
    border-radius: 50% !important; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center; padding: 0 !important;
}
.chat-ai-popover {
    position: absolute;
    bottom: calc(100% + 8px);
    left: 50%;
    transform: translateX(-50%);
    background: #fff;
    border: 1px solid #f3c0d5;
    border-radius: 16px;
    padding: 10px;
    width: 185px;
    box-shadow: 0 4px 20px rgba(0,0,0,.15);
    z-index: 200;
}
.ai-option-btn {
    display: block;
    width: 100%;
    background: none;
    border: none;
    text-align: left;
    padding: 8px 10px;
    border-radius: 10px;
    font-size: .85rem;
    cursor: pointer;
    transition: background .15s;
}
.ai-option-btn:hover { background: #fce4ec; }
.ai-suggestion-card {
    background: #fff9fb;
    border: 1px solid #f3c0d5;
    border-radius: 12px;
    padding: 10px 14px;
    margin-bottom: 8px;
    font-size: .9rem;
    cursor: pointer;
    transition: background .15s;
    position: relative;
}
.ai-suggestion-card:hover { background: #fce4ec; }
.ai-suggestion-card .ai-use-btn {
    display: block;
    margin-top: 6px;
    font-size: .75rem;
    color: #c2185b;
    font-weight: 600;
}
</style>
@endpush

@section('content')
@php
    /** @var \App\Models\User $me */
    $me           = auth()->user();
    /** @var \App\Models\User $other */
    $visibleLastSeen = $other->visibleLastSeenTo($me);
    $isOnline        = $visibleLastSeen && $visibleLastSeen->gt(now()->subMinutes(10));
@endphp

<div class="chat-page">

    {{-- -- Header ------------------------------------------------------------ --}}
    <div class="chat-header">
        <a href="{{ route('conversations.index') }}"
           class="chat-footer-btn flex-shrink-0"
           style="text-decoration:none;display:flex;align-items:center;justify-content:center">
            <i class="bi bi-arrow-left"></i>
        </a>

        <a href="{{ $other->username ? route('profile.show', $other->username) : '#' }}"
           class="d-flex align-items-center gap-2 text-decoration-none text-reset flex-grow-1 overflow-hidden">
            @if($other->primaryPhoto)
                <img src="{{ $other->primaryPhoto->thumbnail_url }}"
                     class="chat-header-avatar" alt="{{ $other->name }}">
            @else
                <div class="chat-header-avatar-ph">{{ strtoupper(mb_substr($other->name, 0, 1)) }}</div>
            @endif
            <div class="overflow-hidden">
                <div class="fw-semibold lh-1 mb-1 text-truncate">
                    {{ $other->name }}
                    @if($other->is_verified ?? false)
                        <i class="bi bi-patch-check-fill text-info ms-1" style="font-size:.8rem" title="Verified"></i>
                    @endif
                </div>
                <div id="chatStatusLine" class="chat-status-line {{ $isOnline ? 'chat-status-online' : '' }}">
                    @if($isOnline)
                        <span class="d-inline-block rounded-circle bg-success me-1"
                              style="width:7px;height:7px;vertical-align:middle"></span>Online now
                    @elseif($visibleLastSeen)
                        Active {{ $visibleLastSeen->diffForHumans() }}
                    @else
                        {{-- last seen hidden or never set --}}
                        Tap to view profile
                    @endif
                </div>
                <div id="typingStatusLine" class="chat-status-line d-none" style="color:#c2185b">
                    <i class="bi bi-three-dots"></i> typing�
                </div>
            </div>
        </a>

        <a href="{{ $other->username ? route('profile.show', $other->username) : '#' }}"
           class="chat-footer-btn flex-shrink-0"
           style="text-decoration:none;display:flex;align-items:center;justify-content:center"
           title="View Profile">
            <i class="bi bi-person"></i>
        </a>
    </div>

    {{-- -- Messages ---------------------------------------------------------- --}}
    <div class="chat-body-scroll" id="chatBody">
        @php
            $prevDate = null;
            $msgs     = $messages->values();
            $total    = count($msgs);
        @endphp

        @foreach($msgs as $i => $msg)
        @php
            $isMe    = $msg->sender_id === $me->id;
            $date    = $msg->created_at->toDateString();
            $isFirst = ($i === 0 || $msgs[$i-1]->sender_id !== $msg->sender_id);
            $isLast  = ($i === $total-1 || $msgs[$i+1]->sender_id !== $msg->sender_id);
            $reactions = $msg->reactions ?? collect();
        @endphp

        {{-- Date separator --}}
        @if($date !== $prevDate)
            @php $prevDate = $date; @endphp
            <div class="date-sep">
                {{ $msg->created_at->isToday() ? 'Today' : ($msg->created_at->isYesterday() ? 'Yesterday' : $msg->created_at->format('M j, Y')) }}
            </div>
        @endif

        <div class="msg-row {{ $isMe ? 'me' : 'them' }} {{ $isFirst ? 'group-start' : '' }}">

            @if(!$isMe)
            <div class="msg-avatar-cell {{ $isLast ? '' : 'hidden' }}">
                @if($msg->sender?->primaryPhoto)
                    <img src="{{ $msg->sender->primaryPhoto->thumbnail_url }}" alt="{{ $msg->sender->name ?? '' }}">
                @else
                    <div class="av-ph">{{ strtoupper(mb_substr($msg->sender->name ?? '?', 0, 1)) }}</div>
                @endif
            </div>
            @endif

            <div class="message-bubble {{ $isMe ? 'me' : 'them' }} {{ !$isFirst ? 'not-first' : '' }} {{ !$isLast ? 'not-last' : '' }} position-relative {{ $msg->isImage() || $msg->isAudio() ? 'media-bubble' : '' }}"
                 data-msg-id="{{ $msg->id }}">

                @if($msg->isImage())
                    {{-- Image attachment --}}
                    <a href="{{ $msg->attachment_url }}" target="_blank" class="msg-img-link">
                        <img src="{{ $msg->attachment_url }}"
                             alt="{{ $msg->attachment_name ?? 'Image' }}"
                             class="msg-image"
                             loading="lazy">
                    </a>
                @elseif($msg->isAudio())
                    {{-- Audio attachment --}}
                    <div class="msg-audio">
                        <i class="bi bi-music-note-beamed me-2 text-primary"></i>
                        <audio controls preload="none" class="msg-audio-player">
                            <source src="{{ $msg->attachment_url }}" type="{{ $msg->attachment_mime ?? 'audio/mpeg' }}">
                        </audio>
                        <div class="msg-audio-name text-truncate small mt-1 text-muted">{{ $msg->attachment_name }}</div>
                    </div>
                @elseif($msg->type === 'gift')
                    {{-- Virtual gift --}}
                    <div class="gift-bubble text-center px-3 py-2">
                        <div style="font-size:2.5rem;line-height:1">{{ explode(' ', $msg->body)[0] }}</div>
                        <div class="small fw-semibold mt-1" style="color:#c2185b">{{ implode(' ', array_slice(explode(' ', $msg->body), 1)) }}</div>
                        <div class="tiny text-muted" style="font-size:.65rem">Virtual gift 🎁</div>
                    </div>
                @else
                    {{-- Text --}}
                    {{ $msg->body }}
                @endif

                <div class="message-meta d-flex align-items-center gap-1">
                    {{ $msg->created_at->format('g:i A') }}
                    @if($isMe)
                        @if($msg->read_at)
                            <i class="bi bi-check2-all text-primary" title="Seen {{ $msg->read_at->format('g:i A') }}"></i>
                        @else
                            <i class="bi bi-check2 text-muted" title="Delivered"></i>
                        @endif
                    @endif
                </div>

                @if($reactions->isNotEmpty())
                <div class="msg-reactions">
                    @foreach($reactions->groupBy('emoji') as $emoji => $group)
                        <span class="reaction-badge">{{ $emoji }}{{ $group->count() > 1 ? ' '.$group->count() : '' }}</span>
                    @endforeach
                </div>
                @endif

                @if($msg->type === 'text')
                <div class="reaction-picker-trigger" title="React">+</div>
                <div class="reaction-picker d-none" data-msg="{{ $msg->id }}"></div>
                @endif
            </div>

            @if($isMe)
            <div class="msg-avatar-cell" style="visibility:hidden"></div>
            @endif

        </div>
        @endforeach

        {{-- Typing indicator --}}
        <div id="typingRow" class="msg-row them group-start d-none">
            <div class="msg-avatar-cell">
                @if($other->primaryPhoto)
                    <img src="{{ $other->primaryPhoto->thumbnail_url }}" alt="{{ $other->name }}">
                @else
                    <div class="av-ph">{{ strtoupper(mb_substr($other->name, 0, 1)) }}</div>
                @endif
            </div>
            <div class="typing-bubble">
                <div class="typing-dot"></div>
                <div class="typing-dot"></div>
                <div class="typing-dot"></div>
            </div>
        </div>
    </div>

    {{-- -- Attachment preview bar -------------------------------------------- --}}
    <div id="attachPreviewBar" class="chat-attach-preview d-none">
        <div id="attachPreviewInner"></div>
        <button type="button" id="attachCancel" class="btn btn-sm btn-outline-secondary ms-2">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>

    {{-- -- Footer / Input --------------------------------------------------- --}}
    <div class="chat-footer">
        {{-- Hidden file input --}}
        <input type="file" id="attachInput"
               accept="image/jpeg,image/png,image/gif,image/webp,audio/mpeg,audio/ogg,audio/wav,audio/mp4,audio/aac,audio/x-m4a,audio/webm"
               class="d-none">

        {{-- ── Row 1: action icon strip ─────────────────────────────── --}}
        <div class="chat-actions-bar">

            {{-- Attachment --}}
            <button type="button" class="chat-act-btn" id="attachBtn" title="Send image or audio">
                <i class="bi bi-paperclip"></i>
            </button>

            @if(auth()->user()->isPremiumActive())
            {{-- Virtual gift (Premium) --}}
            <div class="position-relative">
                <button type="button" class="chat-act-btn" id="giftBtn" title="Send a virtual gift">
                    <i class="bi bi-gift"></i>
                </button>
                <div id="giftPopover" class="chat-gift-popover d-none">
                    <div class="gift-popover-title">Send a gift</div>
                    <div class="gift-grid" id="giftGrid"></div>
                </div>
            </div>
            @else
            {{-- Placeholder so layout is consistent for non-premium --}}
            <div id="giftPopover" class="d-none"><div id="giftGrid"></div></div>
            @endif

            {{-- Tip credits --}}
            <button type="button" class="chat-act-btn chat-act-tip" id="chatTipBtn" title="Send a credit tip">
                <i class="bi bi-coin"></i>
            </button>

            {{-- Emoji --}}
            <div class="position-relative">
                <button type="button" class="chat-act-btn" id="emojiBtn" title="Emoji">
                    <i class="bi bi-emoji-smile"></i>
                </button>
                <div id="emojiPopover" class="chat-emoji-popover d-none"></div>
            </div>

            {{-- AI Assistant ✨ --}}
            <div class="position-relative">
                <button type="button" class="chat-act-btn" id="aiBtn" title="AI Writing Helper">✨</button>
                <div id="aiPopover" class="chat-ai-popover d-none">
                    <div class="gift-popover-title">AI Writing Helper</div>
                    <button type="button" class="ai-option-btn" data-ai-type="reply">💬 Suggest a reply</button>
                    <button type="button" class="ai-option-btn" data-ai-type="topics">🎯 Topic ideas</button>
                    <button type="button" class="ai-option-btn" data-ai-type="icebreaker">👋 Icebreaker</button>
                </div>
            </div>

            {{-- Rephrase with AI --}}
            <div class="ms-auto">
                <button id="aiRephraseBtn" class="chat-act-btn" type="button" title="Rewrite with AI">
                    <span class="d-none spinner-border spinner-border-sm text-danger" id="aiRephraseSpinner" style="width:.8rem;height:.8rem"></span>
                    <i class="bi bi-pencil-square"></i>
                </button>
            </div>
        </div>

        {{-- ── Row 2: textarea + send ──────────────────────────────────── --}}
        <div class="chat-input-row">
            <textarea id="msgInput"
                      class="form-control chat-textarea"
                      placeholder="Type a message…"
                      rows="2"
                      autocomplete="off"
                      style="resize:none;"></textarea>
            <button id="btnSend" class="btn btn-primary chat-send-btn" title="Send (Enter)">
                <i class="bi bi-send-fill"></i>
            </button>
        </div>
    </div>

    {{-- ── Tip Modal ─────────────────────────────────────────────────────── --}}
    <div class="modal fade" id="chatTipModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-sm">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title"><i class="bi bi-coin text-warning me-2"></i>Send a Tip to {{ $other->name }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <form id="chatTipForm">
            @csrf
            <div class="modal-body">
              <div class="d-flex align-items-center gap-2 mb-3 p-2 rounded-3" style="background:var(--bs-secondary-bg)">
                @if($other->primaryPhoto)
                  <img src="{{ $other->primaryPhoto->thumbnail_url }}" class="rounded-circle" style="width:38px;height:38px;object-fit:cover" alt="">
                @else
                  <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width:38px;height:38px;background:linear-gradient(135deg,#7c3aed,#ec4899);color:#fff">{{ strtoupper(mb_substr($other->name,0,1)) }}</div>
                @endif
                <div>
                  <div class="fw-semibold lh-1">{{ $other->name }}</div>
                  <div class="small text-muted">Your balance: <strong id="chatTipBalance">…</strong> credits</div>
                </div>
              </div>
              <div class="mb-3">
                <label class="form-label fw-semibold">Amount <span class="text-muted fw-normal">(credits)</span></label>
                <input type="number" class="form-control" id="chatTipAmount" name="amount" min="1" placeholder="e.g. 5" required>
              </div>
              <div class="mb-2">
                <label class="form-label fw-semibold">Note <span class="text-muted fw-normal">(optional)</span></label>
                <input type="text" class="form-control" id="chatTipMessage" maxlength="255" placeholder="e.g. You're amazing!">
              </div>
              <div id="chatTipAlert" class="d-none"></div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-warning fw-semibold" id="chatTipSubmitBtn" disabled>
                <span id="chatTipSpinner" class="spinner-border spinner-border-sm d-none me-1"></span>
                <i class="bi bi-send me-1"></i>Send Tip
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    {{-- AI Suggestions Tray --}}
    <div id="aiTray" class="d-none ai-tray-enhanced" style="
        position:fixed;bottom:0;left:0;right:0;
        background:#fff;border-top:1.5px solid #f0d0e0;
        padding:16px 10px 24px;z-index:300;
        box-shadow:0 -6px 28px rgba(244,63,94,.12);
        max-height:60vh;overflow-y:auto;">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <span class="fw-semibold small" style="color:#c2185b;font-size:.98rem">✨ AI Suggestions</span>
            <button type="button" id="aiTrayClose" class="btn-close btn-close-sm"></button>
        </div>
        <div id="aiSuggestionsWrapper">
            <div id="aiLoadingSpinner" class="text-center py-3 d-none">
                <div class="spinner-border spinner-border-sm text-danger" role="status"></div>
                <span class="ms-2 small text-muted">✨ Generating suggestions…</span>
            </div>
            <div id="aiSuggestionsList" class="ai-suggestions-list-enhanced"></div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
(function () {
    const convId     = {{ $conversation->id }};
    const myId       = {{ auth()->id() }};
    const csrf       = document.querySelector('meta[name="csrf-token"]').content;
    const chatBody   = document.getElementById('chatBody');
    const msgInput   = document.getElementById('msgInput');
    const btnSend    = document.getElementById('btnSend');
    const typingRow  = document.getElementById('typingRow');
    const typingLine = document.getElementById('typingStatusLine');
    const statusLine = document.getElementById('chatStatusLine');
    const attachBtn  = document.getElementById('attachBtn');
    const attachInput= document.getElementById('attachInput');
    const previewBar = document.getElementById('attachPreviewBar');
    const previewInner = document.getElementById('attachPreviewInner');
    const attachCancel = document.getElementById('attachCancel');

    const base = window.location.pathname.replace(/\/messages.*$/, '');

    function scrollBottom(smooth) {
        chatBody.scrollTo({ top: chatBody.scrollHeight, behavior: smooth ? 'smooth' : 'instant' });
    }
    scrollBottom(false);

    // -- Auto-grow textarea --------------------------------------------------
    msgInput.addEventListener('input', () => {
        msgInput.style.height = 'auto';
        msgInput.style.height = Math.min(msgInput.scrollHeight, 160) + 'px';
    });

    // -- Emoji / Reaction definitions (Unicode escapes avoid file‑encoding issues) --
    const REACTIONS = ['\u2764\uFE0F','\uD83D\uDE02','\uD83D\uDC4D','\uD83D\uDE2E','\uD83D\uDE22','\uD83D\uDE21'];
    const EMOJIS    = ['\uD83D\uDE0A','\uD83D\uDE02','\u2764\uFE0F','\uD83D\uDE0D','\uD83D\uDC4D','\uD83D\uDE4F','\uD83D\uDE18','\uD83E\uDD7A','\uD83D\uDE2D','\uD83D\uDD25','\uD83D\uDE0E','\uD83E\uDD23','\uD83D\uDE01','\uD83D\uDCAF','\u2728','\uD83C\uDF89','\uD83D\uDE0B','\uD83E\uDD29','\uD83D\uDE07','\uD83D\uDE04'];

    // -- Emoji popover -------------------------------------------------------
    const emojiBtn     = document.getElementById('emojiBtn');
    const emojiPopover = document.getElementById('emojiPopover');

    // Populate emoji popover from JS (avoids file encoding issues)
    EMOJIS.forEach(e => {
        const s = document.createElement('span');
        s.dataset.emoji = e;
        s.textContent = e;
        emojiPopover.appendChild(s);
    });

    // Populate reaction pickers from PHP-rendered messages
    document.querySelectorAll('.reaction-picker[data-msg]').forEach(picker => {
        const msgId = picker.dataset.msg;
        REACTIONS.forEach(e => {
            const s = document.createElement('span');
            s.className = 'reaction-choice';
            s.dataset.emoji = e;
            s.dataset.msg = msgId;
            s.textContent = e;
            picker.appendChild(s);
        });
    });

    emojiBtn.addEventListener('click', e => { e.stopPropagation(); emojiPopover.classList.toggle('d-none'); });
    emojiPopover.querySelectorAll('span').forEach(s => {
        s.addEventListener('click', e => {
            e.stopPropagation();
            const pos = msgInput.selectionStart ?? msgInput.value.length;
            const v   = msgInput.value;
            msgInput.value = v.slice(0, pos) + s.dataset.emoji + v.slice(pos);
            msgInput.focus();
            msgInput.dispatchEvent(new Event('input'));
            emojiPopover.classList.add('d-none');
        });
    });
    document.addEventListener('click', () => emojiPopover.classList.add('d-none'));

    // -- Gift picker ---------------------------------------------------------
    // Gifts defined with Unicode escapes to avoid file-encoding corruption
    const GIFTS = [
        { emoji: '\uD83C\uDF39', label: 'Rose',      price: {{ (int) ($giftPrices['gift_price_rose']      ?? 10) }} },
        { emoji: '\uD83D\uDC96', label: 'Heart',     price: {{ (int) ($giftPrices['gift_price_heart']     ?? 10) }} },
        { emoji: '\uD83C\uDF81', label: 'Gift Box',  price: {{ (int) ($giftPrices['gift_price_gift_box']  ?? 10) }} },
        { emoji: '\uD83C\uDF6B', label: 'Chocolate', price: {{ (int) ($giftPrices['gift_price_chocolate'] ?? 10) }} },
        { emoji: '\u2B50',       label: 'Star',      price: {{ (int) ($giftPrices['gift_price_star']      ?? 10) }} },
        { emoji: '\uD83D\uDC8E', label: 'Diamond',   price: {{ (int) ($giftPrices['gift_price_diamond']   ?? 10) }} },
        { emoji: '\uD83C\uDF38', label: 'Flower',    price: {{ (int) ($giftPrices['gift_price_flower']    ?? 10) }} },
        { emoji: '\uD83D\uDC8C', label: 'Love',      price: {{ (int) ($giftPrices['gift_price_love']      ?? 10) }} },
    ];
    const giftGrid = document.getElementById('giftGrid');
    if (giftGrid) {
        GIFTS.forEach(g => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'gift-choice';
            btn.dataset.gift = g.emoji + ' ' + g.label;
            btn.dataset.price = g.price;
            btn.title = g.label;
            btn.innerHTML = `<span class="gift-emoji">${g.emoji}</span><span class="gift-label">${g.label}</span><span class="gift-label" style="color:#c2185b;font-size:.58rem">${g.price} cr</span>`;
            giftGrid.appendChild(btn);
        });
    }
    const giftBtn     = document.getElementById('giftBtn');
    const giftPopover = document.getElementById('giftPopover');
    if (giftBtn && giftPopover) {
        giftBtn.addEventListener('click', e => { e.stopPropagation(); giftPopover.classList.toggle('d-none'); });
        giftPopover.addEventListener('click', async e => {
            const btn = e.target.closest('.gift-choice');
            if (!btn) return;
            e.stopPropagation();
            const gift  = btn.dataset.gift;
            const price = parseInt(btn.dataset.price, 10) || 0;
            giftPopover.classList.add('d-none');
            appendBubble(gift, true, new Date().toISOString(), 'gift');
            try {
                const body = { body: gift, type: 'gift' };
                if (price > 0) body.gift_price = price;
                await fetch(`${base}/messages/${convId}`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/json' },
                    body: JSON.stringify(body)
                });
            } catch (err) { console.error(err); }
        });
        document.addEventListener('click', () => giftPopover.classList.add('d-none'));
    }

    // -- Attachment picker ---------------------------------------------------
    let pendingFile = null;
    attachBtn.addEventListener('click', () => attachInput.click());
    attachInput.addEventListener('change', () => {
        const file = attachInput.files[0];
        if (!file) return;
        pendingFile = file;
        const isImg = file.type.startsWith('image/');
        previewInner.innerHTML = isImg
            ? `<img src="${URL.createObjectURL(file)}" class="chat-preview-img" alt="preview"> <span class="small text-muted ms-2">${esc(file.name)}</span>`
            : `<i class="bi bi-music-note-beamed fs-4 text-primary me-2"></i><span class="small text-muted">${esc(file.name)} (${(file.size/1024/1024).toFixed(1)} MB)</span>`;
        previewBar.classList.remove('d-none');
        msgInput.placeholder = isImg ? 'Add a caption (optional)�' : 'Add a message (optional)�';
    });
    attachCancel.addEventListener('click', () => {
        pendingFile = null;
        attachInput.value = '';
        previewBar.classList.add('d-none');
        previewInner.innerHTML = '';
        msgInput.placeholder = 'Type a message�';
    });

    // -- Send (text OR attachment) -------------------------------------------
    async function send() {
        const body = msgInput.value.trim();

        if (pendingFile) {
            const fd = new FormData();
            fd.append('attachment', pendingFile);
            if (body) fd.append('body', body);
            fd.append('_token', csrf);

            const isImg = pendingFile.type.startsWith('image/');
            appendBubble(null, true, new Date().toISOString(), isImg ? 'image' : 'audio',
                         isImg ? URL.createObjectURL(pendingFile) : null, pendingFile.name);

            // Reset attachment state
            pendingFile = null;
            attachInput.value = '';
            previewBar.classList.add('d-none');
            previewInner.innerHTML = '';
            msgInput.value = '';
            msgInput.style.height = 'auto';
            msgInput.placeholder = 'Type a message�';

            try {
                await fetch(`${base}/messages/${convId}`, { method: 'POST', headers: {'X-CSRF-TOKEN': csrf}, body: fd });
            } catch (err) { console.error(err); }
            return;
        }

        if (!body) return;
        msgInput.value = '';
        msgInput.style.height = 'auto';
        appendBubble(body, true, new Date().toISOString(), 'text');
        try {
            await fetch(`${base}/messages/${convId}`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/json' },
                body: JSON.stringify({ body })
            });
        } catch (err) { console.error(err); }
    }

    btnSend.addEventListener('click', send);
    msgInput.addEventListener('keydown', e => {
        if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); send(); }
    });

    // -- Build bubble HTML ---------------------------------------------------
    function appendBubble(body, isMe, createdAt, type = 'text', attachUrl = null, attachName = null, id = null) {
        typingRow.classList.add('d-none');
        const t   = new Date(createdAt).toLocaleTimeString([], { hour: 'numeric', minute: '2-digit' });
        const chk = isMe ? '<i class="bi bi-check2 text-muted"></i>' : '';

        const row = document.createElement('div');
        row.className = `msg-row ${isMe ? 'me' : 'them'} group-start`;

        if (!isMe) {
            const av = document.createElement('div');
            av.className = 'msg-avatar-cell';
            row.appendChild(av);
        }

        const bubble = document.createElement('div');
        bubble.className = `message-bubble ${isMe ? 'me' : 'them'} position-relative${type !== 'text' ? ' media-bubble' : ''}`;
        if (id) bubble.dataset.msgId = id;

        let content = '';
        if (type === 'image' && attachUrl) {
            content = `<a href="${attachUrl}" target="_blank" class="msg-img-link"><img src="${attachUrl}" alt="${esc(attachName ?? 'Image')}" class="msg-image" loading="lazy"></a>`;
        } else if (type === 'audio' && attachUrl) {
            content = `<div class="msg-audio"><i class="bi bi-music-note-beamed me-2 text-primary"></i><audio controls preload="none" class="msg-audio-player"><source src="${attachUrl}"></audio><div class="msg-audio-name text-truncate small mt-1 text-muted">${esc(attachName ?? '')}</div></div>`;
        } else if (type === 'gift') {
            const parts = (body ?? '').split(' ');
            const emoji = parts[0] ?? '';
            const label = parts.slice(1).join(' ');
            content = `<div class="gift-bubble text-center px-3 py-2"><div style="font-size:2.5rem;line-height:1">${esc(emoji)}</div><div class="small fw-semibold mt-1" style="color:#c2185b">${esc(label)}</div><div class="tiny text-muted" style="font-size:.65rem">Virtual gift \uD83C\uDF81</div></div>`;
        } else {
            content = esc(body ?? '');
        }

        bubble.innerHTML = content
            + `<div class="message-meta d-flex align-items-center gap-1">${t} ${chk}</div>`
            + (type === 'text'
                ? `<div class="reaction-picker-trigger" title="React">+</div><div class="reaction-picker d-none">${REACTIONS.map(e=>`<span class="reaction-choice" data-emoji="${e}">${e}</span>`).join('')}</div>`
                : '');

        row.appendChild(bubble);

        if (isMe) {
            const sp = document.createElement('div');
            sp.className = 'msg-avatar-cell';
            sp.style.visibility = 'hidden';
            row.appendChild(sp);
        }

        chatBody.appendChild(row);
        scrollBottom(true);
        if (type === 'text') attachReaction(bubble);
    }

    function esc(s) {
        if (!s) return '';
        return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    // -- Typing broadcast ----------------------------------------------------
    let typingDebounce;
    msgInput.addEventListener('input', () => {
        clearTimeout(typingDebounce);
        typingDebounce = setTimeout(() => {
            fetch(`${base}/messages/${convId}/typing`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/json' }
            }).catch(() => {});
        }, 300);
    });

    // -- WebSockets ----------------------------------------------------------
    let clearTypingTimer;
    if (window.Echo) {
        window.Echo.private(`conversation.${convId}`)
            .listen('.message.sent', e => {
                if (e.sender_id !== myId) {
                    typingRow.classList.add('d-none');
                    appendBubble(e.body, false, e.created_at, e.type ?? 'text',
                                 e.attachment_url ?? null, e.attachment_name ?? null, e.id ?? null);
                }
            })
            .listen('.user.typing', e => {
                if (e.user_id !== myId) {
                    typingLine.classList.remove('d-none');
                    statusLine.classList.add('d-none');
                    typingRow.classList.remove('d-none');
                    scrollBottom(true);
                    clearTimeout(clearTypingTimer);
                    clearTypingTimer = setTimeout(() => {
                        typingLine.classList.add('d-none');
                        statusLine.classList.remove('d-none');
                        typingRow.classList.add('d-none');
                    }, 2500);
                }
            });
    }

    // -- Reactions -----------------------------------------------------------
    function attachReaction(bubble) {
        const trigger = bubble.querySelector('.reaction-picker-trigger');
        const picker  = bubble.querySelector('.reaction-picker');
        if (!trigger || !picker) return;
        trigger.addEventListener('click', e => {
            e.stopPropagation();
            document.querySelectorAll('.reaction-picker').forEach(p => { if (p !== picker) p.classList.add('d-none'); });
            picker.classList.toggle('d-none');
        });
        picker.querySelectorAll('.reaction-choice').forEach(choice => {
            choice.addEventListener('click', async e => {
                e.stopPropagation();
                const msgId = bubble.dataset.msgId;
                if (!msgId) return;
                picker.classList.add('d-none');
                await fetch(`${base}/messages/react/${msgId}`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/json' },
                    body: JSON.stringify({ emoji: choice.dataset.emoji })
                }).catch(() => {});
            });
        });
    }

    document.querySelectorAll('.message-bubble').forEach(attachReaction);
    document.addEventListener('click', () =>
        document.querySelectorAll('.reaction-picker:not(.d-none)').forEach(p => p.classList.add('d-none'))
    );

    // ── AI Writing Assistant ─────────────────────────────────────────────────
    const aiBtn       = document.getElementById('aiBtn');
    const aiPopover   = document.getElementById('aiPopover');
    const aiTray      = document.getElementById('aiTray');
    const aiTrayClose = document.getElementById('aiTrayClose');
    const aiList      = document.getElementById('aiSuggestionsList');
    const aiSpinner   = document.getElementById('aiLoadingSpinner');
    const aiRephraseBtn = document.getElementById('aiRephraseBtn');
    const aiRephraseSpinner = document.getElementById('aiRephraseSpinner');
    const partnerId   = {{ $other->id }};
    const aiRoute     = '{{ route('ai.suggest') }}';

    aiBtn.addEventListener('click', e => { e.stopPropagation(); aiPopover.classList.toggle('d-none'); });
    document.addEventListener('click', () => aiPopover.classList.add('d-none'));
    aiTrayClose.addEventListener('click', () => aiTray.classList.add('d-none'));

    document.querySelectorAll('.ai-option-btn').forEach(btn => {
        btn.addEventListener('click', async e => {
            e.stopPropagation();
            aiPopover.classList.add('d-none');
            aiList.innerHTML = '';
            aiSpinner.classList.remove('d-none');
            aiTray.classList.remove('d-none');

            try {
                const res = await fetch(aiRoute, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        type:            btn.dataset.aiType,
                        conversation_id: convId,
                        partner_id:      partnerId,
                    }),
                });
                const data = await res.json();
                aiSpinner.classList.add('d-none');

                (data.suggestions ?? []).forEach(s => {
                    const card = document.createElement('div');
                    card.className = 'ai-suggestion-card ai-suggestion-card-enhanced';
                    card.innerHTML = `<div class="ai-suggestion-main">${esc(s)}</div><div class="ai-card-footer"><button class="ai-use-btn btn btn-link p-0">Use ↑</button><button class="ai-copy-btn btn btn-link p-0 text-muted ms-2" title="Copy"><i class="bi bi-clipboard"></i></button></div>`;
                    card.querySelector('.ai-use-btn').addEventListener('click', (ev) => {
                        ev.stopPropagation();
                        msgInput.value = s;
                        msgInput.dispatchEvent(new Event('input'));
                        msgInput.focus();
                        aiTray.classList.add('d-none');
                    });
                    card.querySelector('.ai-copy-btn').addEventListener('click', (ev) => {
                        ev.stopPropagation();
                        navigator.clipboard.writeText(s);
                        card.querySelector('.ai-copy-btn').innerHTML = '<i class="bi bi-clipboard-check"></i>';
                        setTimeout(()=>{card.querySelector('.ai-copy-btn').innerHTML = '<i class="bi bi-clipboard"></i>';}, 1200);
                    });
                    aiList.appendChild(card);
                });

                if (!data.suggestions?.length) {
                    aiList.innerHTML = '<p class="text-muted small text-center mt-2">No suggestions available.</p>';
                }
            } catch {
                aiSpinner.classList.add('d-none');
                aiList.innerHTML = '<p class="text-danger small text-center mt-2">Failed to load suggestions.</p>';
            }
        });
    });

    // Rephrase button logic
    aiRephraseBtn.addEventListener('click', async () => {
        const text = msgInput.value.trim();
        if (!text) return;
        aiRephraseBtn.disabled = true;
        aiRephraseSpinner.classList.remove('d-none');
        msgInput.disabled = true;
        // Show typing animation in textarea
        let typingInterval;
        function showTypingAnim() {
            let dots = 0;
            typingInterval = setInterval(() => {
                msgInput.value = 'Rewriting'+'.'.repeat(dots%4);
                dots++;
            }, 350);
        }
        showTypingAnim();
        try {
            const res = await fetch(aiRoute, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    type: 'rephrase',
                    conversation_id: convId,
                    partner_id: partnerId,
                    draft: text
                }),
            });
            const data = await res.json();
            clearInterval(typingInterval);
            if (data.suggestions && data.suggestions[0]) {
                // Animate typing out the rewritten text
                let rewritten = data.suggestions[0];
                let i = 0;
                msgInput.value = '';
                function typeChar() {
                    if (i <= rewritten.length) {
                        msgInput.value = rewritten.slice(0, i);
                        i++;
                        setTimeout(typeChar, 18 + Math.random()*40);
                    } else {
                        msgInput.dispatchEvent(new Event('input'));
                        msgInput.disabled = false;
                    }
                }
                typeChar();
            } else {
                msgInput.value = text;
                msgInput.disabled = false;
            }
        } catch {
            clearInterval(typingInterval);
            msgInput.value = text;
            msgInput.disabled = false;
        }
        aiRephraseBtn.disabled = false;
        aiRephraseSpinner.classList.add('d-none');
    });
    // ── Chat Tip ─────────────────────────────────────────────────────────────
    (function () {
        const chatTipBtn    = document.getElementById('chatTipBtn');
        const chatTipModal  = new bootstrap.Modal(document.getElementById('chatTipModal'));
        const chatTipForm   = document.getElementById('chatTipForm');
        const chatTipAmtEl  = document.getElementById('chatTipAmount');
        const chatTipMsgEl  = document.getElementById('chatTipMessage');
        const chatTipBalEl  = document.getElementById('chatTipBalance');
        const chatTipAlert  = document.getElementById('chatTipAlert');
        const chatTipSubmit = document.getElementById('chatTipSubmitBtn');
        const chatTipSpinner= document.getElementById('chatTipSpinner');
        let _tipBal = 0, _tipBalLoaded = false;

        function tipAlert(type, html) {
            if (!type) { chatTipAlert.className = 'd-none'; chatTipAlert.innerHTML = ''; return; }
            chatTipAlert.className = 'alert alert-' + type + ' py-2 small mt-2';
            chatTipAlert.innerHTML = html;
        }

        chatTipBtn?.addEventListener('click', () => {
            chatTipForm.reset();
            chatTipAmtEl.value = '';
            chatTipMsgEl.value = '';
            tipAlert(null);
            chatTipSubmit.disabled = true;
            _tipBalLoaded = false;
            chatTipBalEl.innerHTML = '<span class="spinner-border spinner-border-sm" style="width:.8rem;height:.8rem"></span>';

            fetch('{{ route("wallet.balance") }}')
                .then(r => r.json())
                .then(d => {
                    _tipBal = parseInt(d.balance ?? 0, 10);
                    _tipBalLoaded = true;
                    chatTipBalEl.textContent = _tipBal;
                    if (_tipBal <= 0) {
                        tipAlert('warning', '<i class="bi bi-exclamation-triangle me-1"></i>No credits. <a href="{{ route("wallet.index") }}" class="alert-link fw-semibold">Fund your wallet</a>.');
                        chatTipSubmit.disabled = true;
                    }
                })
                .catch(() => { _tipBalLoaded = true; chatTipBalEl.textContent = '?'; });

            chatTipModal.show();
        });

        chatTipAmtEl?.addEventListener('input', function () {
            if (!_tipBalLoaded) return;
            const amt = parseInt(this.value, 10);
            if (isNaN(amt) || amt < 1) { tipAlert(null); chatTipSubmit.disabled = _tipBal <= 0; return; }
            if (amt > _tipBal) {
                tipAlert('warning', `<i class="bi bi-exclamation-triangle me-1"></i>Amount (${amt}) exceeds your balance of <strong>${_tipBal}</strong> credits.`);
                chatTipSubmit.disabled = true;
            } else {
                tipAlert(null);
                chatTipSubmit.disabled = false;
            }
        });

        chatTipForm?.addEventListener('submit', async (e) => {
            e.preventDefault();
            const amt = parseInt(chatTipAmtEl.value, 10);
            if (isNaN(amt) || amt < 1) { tipAlert('danger', 'Enter a valid amount (min 1 credit).'); return; }
            if (_tipBalLoaded && amt > _tipBal) { tipAlert('danger', 'Insufficient balance.'); return; }

            chatTipSubmit.disabled = true;
            chatTipSpinner.classList.remove('d-none');
            tipAlert(null);

            try {
                const res = await fetch('{{ route("tips.send") }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({ recipient_id: {{ $other->id }}, amount: amt, message: chatTipMsgEl.value.trim() || null }),
                });
                const data = await res.json();
                if (!res.ok) {
                    tipAlert('danger', '<i class="bi bi-x-circle me-1"></i>' + (data.error ?? 'Could not send tip.'));
                    chatTipSubmit.disabled = false;
                } else {
                    tipAlert('success', `<i class="bi bi-check-circle me-1"></i>Tip of <strong>${amt}</strong> credits sent to <strong>{{ $other->name }}</strong>!`);
                    _tipBal -= amt;
                    chatTipBalEl.textContent = _tipBal;
                    chatTipAmtEl.value = '';
                    chatTipMsgEl.value = '';
                    setTimeout(() => chatTipModal.hide(), 1800);
                }
            } catch {
                tipAlert('danger', 'Network error. Please try again.');
                chatTipSubmit.disabled = false;
            }
            chatTipSpinner.classList.add('d-none');
        });
    })();
    // ────────────────────────────────────────────────────────────────────────
})();
</script>
@endpush
