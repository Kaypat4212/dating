@extends('layouts.app')
@section('title', 'AI Dating Assistant')

@push('head')
<style>
main { padding-bottom: 0 !important; }

/* ── Chat page layout ─────────────────────────────────────────────────────── */
.ai-chat-page {
    display: flex;
    flex-direction: column;
    height: calc(100dvh - 56px);
    background: #f5f5f5;
}

/* ── Header ───────────────────────────────────────────────────────────────── */
.ai-chat-header {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    background: #fff;
    border-bottom: 1px solid #e8e8e8;
    flex-shrink: 0;
    position: sticky;
    top: 0;
    z-index: 10;
}
.ai-avatar {
    width: 44px; height: 44px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea, #764ba2);
    display: flex; align-items: center; justify-content: center;
    font-size: 1.4rem;
    flex-shrink: 0;
}
.ai-header-info .name { font-weight: 700; font-size: 1rem; line-height: 1.2; }
.ai-header-info .sub  { font-size: .75rem; color: #888; }
.ai-limit-badge {
    margin-left: auto;
    font-size: .7rem;
    padding: 3px 8px;
    border-radius: 20px;
}

/* ── Messages area ────────────────────────────────────────────────────────── */
.ai-messages {
    flex: 1;
    overflow-y: auto;
    padding: 16px;
    display: flex;
    flex-direction: column;
    gap: 10px;
    scroll-behavior: smooth;
}
.ai-bubble {
    max-width: 80%;
    padding: 10px 14px;
    border-radius: 18px;
    font-size: .92rem;
    line-height: 1.5;
    word-break: break-word;
    white-space: pre-wrap;
    animation: bubblePop .2s ease both;
}
@keyframes bubblePop { from { transform: scale(.9); opacity: 0; } to { transform: scale(1); opacity: 1; } }
.ai-bubble.user {
    align-self: flex-end;
    background: linear-gradient(135deg, #764ba2, #667eea);
    color: #fff;
    border-bottom-right-radius: 4px;
}
.ai-bubble.bot {
    align-self: flex-start;
    background: #fff;
    color: #1a1a2e;
    border-bottom-left-radius: 4px;
    box-shadow: 0 1px 3px rgba(0,0,0,.08);
}
.ai-bubble .meta {
    font-size: .68rem;
    opacity: .55;
    margin-top: 4px;
    text-align: right;
}
.ai-bubble.bot .meta { text-align: left; }

/* ── Empty state ──────────────────────────────────────────────────────────── */
.ai-empty {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    color: #888;
    padding: 32px;
}
.ai-suggestion-pills {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    justify-content: center;
    margin-top: 16px;
}
.ai-pill {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 20px;
    padding: 6px 14px;
    font-size: .82rem;
    cursor: pointer;
    transition: background .15s, border-color .15s;
}
.ai-pill:hover { background: #f3eeff; border-color: #764ba2; color: #764ba2; }

/* ── Input area ───────────────────────────────────────────────────────────── */
.ai-input-bar {
    display: flex;
    align-items: flex-end;
    gap: 8px;
    padding: 10px 16px;
    background: #fff;
    border-top: 1px solid #e8e8e8;
    flex-shrink: 0;
}
.ai-input-bar textarea {
    flex: 1;
    border: 1px solid #ddd;
    border-radius: 22px;
    padding: 9px 16px;
    resize: none;
    font-size: .92rem;
    line-height: 1.4;
    max-height: 130px;
    overflow-y: auto;
    outline: none;
    transition: border-color .2s;
}
.ai-input-bar textarea:focus { border-color: #764ba2; }
.ai-send-btn {
    width: 42px; height: 42px;
    border-radius: 50%;
    background: linear-gradient(135deg, #764ba2, #667eea);
    border: none;
    color: #fff;
    display: flex; align-items: center; justify-content: center;
    font-size: 1rem;
    flex-shrink: 0;
    transition: transform .1s, opacity .1s;
}
.ai-send-btn:disabled { opacity: .5; transform: scale(.9); }
.ai-send-btn:not(:disabled):hover { transform: scale(1.08); }

/* ── Typing indicator ─────────────────────────────────────────────────────── */
.ai-typing {
    align-self: flex-start;
    display: flex;
    gap: 4px;
    padding: 10px 14px;
    background: #fff;
    border-radius: 18px;
    border-bottom-left-radius: 4px;
    box-shadow: 0 1px 3px rgba(0,0,0,.08);
}
.ai-typing span {
    width: 7px; height: 7px;
    border-radius: 50%;
    background: #764ba2;
    animation: typing 1.2s infinite;
}
.ai-typing span:nth-child(2) { animation-delay: .2s; }
.ai-typing span:nth-child(3) { animation-delay: .4s; }
@keyframes typing { 0%,60%,100% { transform: translateY(0); } 30% { transform: translateY(-6px); } }
</style>
@endpush

@section('content')
@php
    $me   = auth()->user();
    $limitText = $isPremium
        ? '∞ Unlimited'
        : ($remaining > 0 ? "{$remaining} / {$limit} left this hour" : 'Limit reached');
    $limitClass = $isPremium
        ? 'text-bg-success'
        : ($remaining > 0 ? 'text-bg-secondary' : 'text-bg-danger');
@endphp

<div class="ai-chat-page">

    {{-- Header --}}
    <div class="ai-chat-header">
        <a href="{{ route('conversations.index') }}"
           class="btn btn-sm btn-light rounded-circle d-flex align-items-center justify-content-center"
           style="width:36px;height:36px;flex-shrink:0">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div class="ai-avatar">🤖</div>
        <div class="ai-header-info overflow-hidden">
            <div class="name">AI Dating Assistant</div>
            <div class="sub">Always here to help</div>
        </div>
        <span class="ai-limit-badge badge {{ $limitClass }}">{{ $limitText }}</span>
    </div>

    {{-- Messages --}}
    <div class="ai-messages" id="aiMessages">

        @if($history->isEmpty())
            <div class="ai-empty" id="aiEmpty">
                <div style="font-size:3rem" class="mb-3">🤖</div>
                <div class="fw-semibold mb-1" style="color:#444">Hey {{ $me->name }}! 👋</div>
                <div class="text-muted small mb-2">I'm your AI Dating Assistant. Ask me anything about dating, bios, conversation starters, or relationships!</div>
                <div class="ai-suggestion-pills" id="suggestionPills">
                    <button class="ai-pill" onclick="useSuggestion(this)">Write me a bio</button>
                    <button class="ai-pill" onclick="useSuggestion(this)">Best first message tips</button>
                    <button class="ai-pill" onclick="useSuggestion(this)">How to make my profile stand out</button>
                    <button class="ai-pill" onclick="useSuggestion(this)">Conversation topics for a first date</button>
                    <button class="ai-pill" onclick="useSuggestion(this)">Red flags to watch for</button>
                    <button class="ai-pill" onclick="useSuggestion(this)">Green flags to look for</button>
                </div>
            </div>
        @else
            @foreach($history as $msg)
            <div class="ai-bubble {{ $msg->role === 'user' ? 'user' : 'bot' }}">
                {{ $msg->body }}
                <div class="meta">{{ $msg->created_at->format('g:i A') }}</div>
            </div>
            @endforeach
        @endif

    </div>

    {{-- Input --}}
    <div class="ai-input-bar">
        @if($remaining <= 0 && !$isPremium)
            <div class="w-100 text-center py-2">
                <span class="text-danger small fw-semibold">
                    <i class="bi bi-clock me-1"></i>Hourly limit reached.
                </span>
                <a href="{{ route('premium.show') }}" class="btn btn-sm btn-warning ms-2 fw-semibold">
                    <i class="bi bi-star-fill me-1"></i>Go Premium for unlimited AI
                </a>
            </div>
        @else
            <textarea id="aiInput"
                      placeholder="Ask anything about dating…"
                      rows="1"
                      maxlength="600"
                      {{ ($remaining <= 0 && !$isPremium) ? 'disabled' : '' }}></textarea>
            <button id="aiSendBtn" class="ai-send-btn" disabled>
                <i class="bi bi-send-fill"></i>
            </button>
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script>
(function () {
    const input   = document.getElementById('aiInput');
    const sendBtn = document.getElementById('aiSendBtn');
    const msgs    = document.getElementById('aiMessages');
    const csrf    = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
    const replyUrl = '{{ route('ai.chat.reply') }}';

    if (!input || !sendBtn) return;

    // Auto-resize textarea
    input.addEventListener('input', () => {
        input.style.height = 'auto';
        input.style.height = Math.min(input.scrollHeight, 130) + 'px';
        sendBtn.disabled = !input.value.trim();
    });

    // Send on Enter (not Shift+Enter)
    input.addEventListener('keydown', e => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            if (!sendBtn.disabled) send();
        }
    });

    sendBtn.addEventListener('click', send);

    function useSuggestion(el) {
        input.value = el.textContent;
        input.dispatchEvent(new Event('input'));
        input.focus();
        const empty = document.getElementById('aiEmpty');
        if (empty) empty.remove();
    }
    window.useSuggestion = useSuggestion;

    function addBubble(role, text, time) {
        const div = document.createElement('div');
        div.className = `ai-bubble ${role === 'user' ? 'user' : 'bot'}`;
        div.innerHTML = `${escHtml(text)}<div class="meta">${time || ''}</div>`;
        msgs.appendChild(div);
        scrollBottom();
        return div;
    }

    function showTyping() {
        const t = document.createElement('div');
        t.className = 'ai-typing';
        t.id = 'aiTyping';
        t.innerHTML = '<span></span><span></span><span></span>';
        msgs.appendChild(t);
        scrollBottom();
    }

    function removeTyping() {
        document.getElementById('aiTyping')?.remove();
    }

    function scrollBottom() {
        msgs.scrollTop = msgs.scrollHeight;
    }

    function escHtml(s) {
        return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/\n/g,'<br>');
    }

    function formatTime(date) {
        return date.toLocaleTimeString([], { hour: 'numeric', minute: '2-digit' });
    }

    async function send() {
        const text = input.value.trim();
        if (!text) return;

        // Remove empty state
        document.getElementById('aiEmpty')?.remove();

        addBubble('user', text, formatTime(new Date()));
        input.value = '';
        input.style.height = 'auto';
        sendBtn.disabled = true;
        showTyping();

        try {
            const res = await fetch(replyUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ message: text }),
            });

            removeTyping();

            if (!res.ok) {
                const err = await res.json().catch(() => ({}));
                const errMsg = err.error ?? 'Something went wrong. Please try again.';
                addBubble('bot', errMsg, formatTime(new Date()));
                return;
            }

            const data = await res.json();
            addBubble('bot', data.reply ?? 'I\'m not sure about that one! Try rephrasing your question.', formatTime(new Date()));

            // Update limit badge if server returns remaining count
            if (typeof data.remaining !== 'undefined') {
                const badge = document.querySelector('.ai-limit-badge');
                if (badge) {
                    const r = data.remaining;
                    badge.textContent = r < 0 ? '∞ Unlimited' : `${r} / {{ $limit }} left this hour`;
                    badge.className = 'ai-limit-badge badge ' + (r < 0 ? 'text-bg-success' : (r > 0 ? 'text-bg-secondary' : 'text-bg-danger'));

                    if (r === 0) {
                        input.disabled = true;
                        sendBtn.disabled = true;
                    }
                }
            }
        } catch (e) {
            removeTyping();
            addBubble('bot', '⚠️ Network error. Please check your connection.', formatTime(new Date()));
        }
    }

    // Scroll to bottom on load
    scrollBottom();
})();
</script>
@endpush
