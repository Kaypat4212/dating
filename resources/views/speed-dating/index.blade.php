@extends('layouts.app')
@section('title', 'Coffee Break Speed Dating')

@push('head')
<style>
.sd-card { max-width: 520px; margin: 0 auto; }
.sd-avatar { width: 56px; height: 56px; border-radius: 50%; object-fit: cover; }
.sd-avatar-ph { width: 56px; height: 56px; border-radius: 50%; background: linear-gradient(135deg,#c2185b22,#7b1fa222); display:flex;align-items:center;justify-content:center;font-weight:700;font-size:1.2rem; }
.sd-timer { font-size: 2rem; font-weight: 700; font-variant-numeric: tabular-nums; color: #c2185b; }
.sd-timer.urgent { color: #ef4444; animation: pulse 1s infinite; }
@keyframes pulse { 0%,100%{opacity:1} 50%{opacity:.6} }
.sd-msg-list { height: 300px; overflow-y: auto; display:flex;flex-direction:column;gap:.4rem;padding:.75rem; border:1px solid var(--bs-border-color);border-radius:12px;background:var(--bs-body-bg); }
.sd-bubble { max-width: 80%; padding: .45rem .8rem; border-radius: 16px; font-size: .92rem; word-break: break-word; }
.sd-bubble.me { align-self: flex-end; background: #c2185b; color: #fff; border-bottom-right-radius: 4px; }
.sd-bubble.them { align-self: flex-start; background: var(--bs-secondary-bg); border-bottom-left-radius: 4px; }
.sd-input-row { display:flex;gap:.5rem;margin-top:.5rem; }
.sd-input-row textarea { flex:1; resize:none; border-radius: 12px; }
.queue-pulse { animation: queuePulse 2s ease-in-out infinite; }
@keyframes queuePulse { 0%,100%{transform:scale(1)} 50%{transform:scale(1.04)} }
</style>
@endpush

@section('content')
<div class="container py-4">
    <div class="sd-card">
        <div class="d-flex align-items-center gap-3 mb-4">
            <span style="font-size:2rem">☕</span>
            <div>
                <h4 class="fw-bold mb-0">Coffee Break Speed Dating</h4>
                <p class="text-muted small mb-0">5-minute chats. Connect if the vibe is right.</p>
            </div>
        </div>

        {{-- ── Idle: not in queue ──────────────────────────────────────── --}}
        <div id="sdIdle" class="{{ $room || $inQueue ? 'd-none' : '' }} text-center py-5">
            <div style="font-size:4rem">☕</div>
            <h5 class="fw-bold mt-3">Ready for a 5-minute chat?</h5>
            <p class="text-muted mb-4">Join the queue and we'll pair you with someone nearby for a short coffee break chat. No pressure — just vibes.</p>
            <button id="sdJoinBtn" class="btn btn-primary btn-lg px-5 rounded-pill">
                <i class="bi bi-lightning me-2"></i>Join the Queue
            </button>
        </div>

        {{-- ── Waiting in queue ────────────────────────────────────────── --}}
        <div id="sdWaiting" class="{{ $inQueue && !$room ? '' : 'd-none' }} text-center py-5">
            <div class="queue-pulse" style="font-size:4rem">⏳</div>
            <h5 class="fw-bold mt-3">Finding your match…</h5>
            <p class="text-muted mb-3">We're pairing you with someone right now. Hold tight!</p>
            <div class="spinner-border text-primary mb-4" role="status"><span class="visually-hidden">Loading…</span></div>
            <br>
            <button id="sdLeaveBtn" class="btn btn-outline-secondary btn-sm rounded-pill">
                <i class="bi bi-x me-1"></i>Leave Queue
            </button>
        </div>

        {{-- ── Active room ─────────────────────────────────────────────── --}}
        <div id="sdActive" class="{{ $room ? '' : 'd-none' }}">
            {{-- Partner header --}}
            <div class="d-flex align-items-center gap-3 p-3 rounded-3 mb-3" style="background:var(--bs-secondary-bg)">
                <div id="sdPartnerAvatar"></div>
                <div class="flex-grow-1">
                    <div class="fw-semibold" id="sdPartnerName">…</div>
                    <div class="text-muted small">Coffee break partner</div>
                </div>
                <div>
                    <div class="text-muted small text-center mb-1">Ends in</div>
                    <div class="sd-timer text-center" id="sdTimer">5:00</div>
                </div>
            </div>

            {{-- Chat messages --}}
            <div class="sd-msg-list" id="sdMsgList"></div>

            {{-- Input --}}
            <div class="sd-input-row">
                <textarea id="sdInput" class="form-control" rows="2" placeholder="Say something…" maxlength="500"></textarea>
                <button id="sdSendBtn" class="btn btn-primary align-self-end">
                    <i class="bi bi-send-fill"></i>
                </button>
            </div>
        </div>

        {{-- ── Session ended, connect prompt ──────────────────────────── --}}
        <div id="sdEnded" class="d-none text-center py-5">
            <div style="font-size:4rem">⏰</div>
            <h5 class="fw-bold mt-3">Time's up!</h5>
            <p class="text-muted mb-4">Did you enjoy that chat? You can choose to connect and continue the conversation as a real match.</p>
            <div id="sdConnectWrap">
                <button id="sdConnectBtn" class="btn btn-success btn-lg px-5 rounded-pill me-2">
                    💞 Connect!
                </button>
                <button id="sdPassBtn" class="btn btn-outline-secondary btn-lg rounded-pill">
                    Pass
                </button>
            </div>
            <div id="sdWaitingConnect" class="d-none mt-3 text-muted small">
                <div class="spinner-border spinner-border-sm me-2"></div>Waiting for the other person…
            </div>
            <div id="sdMatchedMsg" class="d-none mt-3">
                <div class="alert alert-success">🎉 It's a match! <a id="sdConvLink" href="#" class="fw-bold">Open chat →</a></div>
            </div>
            <button id="sdNewRoundBtn" class="btn btn-outline-primary mt-3 d-none rounded-pill">
                <i class="bi bi-arrow-clockwise me-1"></i>Find Another Match
            </button>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const csrf = document.querySelector('meta[name="csrf-token"]').content;
    const base = '{{ rtrim(url("/speed-dating"), "/") }}';

    let state      = @json($room ? 'active' : ($inQueue ? 'waiting' : 'idle'));
    let roomId     = @json($room?->id);
    let lastMsgId  = 0;
    let timerSeconds = @json($room ? $room->secondsRemaining() : 300);
    let pollInterval, timerInterval, msgInterval;

    const $idle    = document.getElementById('sdIdle');
    const $waiting = document.getElementById('sdWaiting');
    const $active  = document.getElementById('sdActive');
    const $ended   = document.getElementById('sdEnded');
    const $timer   = document.getElementById('sdTimer');

    function showState(s) {
        [$idle, $waiting, $active, $ended].forEach(el => el?.classList.add('d-none'));
        ({ idle: $idle, waiting: $waiting, active: $active, ended: $ended })[s]?.classList.remove('d-none');
        state = s;
    }

    // ── Join queue ─────────────────────────────────────────────────────────
    document.getElementById('sdJoinBtn')?.addEventListener('click', async () => {
        await fetch(base + '/join', { method: 'POST', headers: { 'X-CSRF-TOKEN': csrf } });
        showState('waiting');
        startPoll();
    });

    // ── Leave queue ────────────────────────────────────────────────────────
    document.getElementById('sdLeaveBtn')?.addEventListener('click', async () => {
        await fetch(base + '/leave', { method: 'POST', headers: { 'X-CSRF-TOKEN': csrf } });
        stopPoll();
        showState('idle');
    });

    // ── Poll for status changes ────────────────────────────────────────────
    function startPoll() {
        if (pollInterval) return;
        pollInterval = setInterval(pollStatus, 3000);
    }

    function stopPoll() {
        clearInterval(pollInterval);
        pollInterval = null;
    }

    async function pollStatus() {
        try {
            const res  = await fetch(base + '/status');
            const data = await res.json();

            if (data.state === 'active' && state !== 'active') {
                roomId = data.room_id;
                timerSeconds = data.seconds_remaining;
                activateRoom(data);
                stopPoll();
            } else if (data.state === 'ended' && state === 'active') {
                endRoom();
            } else if (data.state === 'idle' && state === 'waiting') {
                showState('idle');
                stopPoll();
            }
        } catch(e) { /* network glitch */ }
    }

    // ── Activate room UI ───────────────────────────────────────────────────
    function activateRoom(data) {
        const other = data.other;
        const av = document.getElementById('sdPartnerAvatar');

        if (other.photo) {
            av.innerHTML = `<img src="${esc(other.photo)}" class="sd-avatar" alt="">`;
        } else {
            av.innerHTML = `<div class="sd-avatar-ph">${esc(other.name.charAt(0).toUpperCase())}</div>`;
        }
        document.getElementById('sdPartnerName').textContent = other.name;

        timerSeconds = data.seconds_remaining;
        startTimer();
        startMsgPoll();
        showState('active');
    }

    // Timer countdown
    function startTimer() {
        clearInterval(timerInterval);
        updateTimerDisplay();
        timerInterval = setInterval(() => {
            timerSeconds--;
            updateTimerDisplay();
            if (timerSeconds <= 0) {
                clearInterval(timerInterval);
                clearInterval(msgInterval);
                endRoom();
            }
        }, 1000);
    }

    function updateTimerDisplay() {
        const m = Math.floor(Math.max(0, timerSeconds) / 60);
        const s = Math.max(0, timerSeconds) % 60;
        $timer.textContent = m + ':' + String(s).padStart(2, '0');
        $timer.classList.toggle('urgent', timerSeconds <= 30);
    }

    // Message polling
    function startMsgPoll() {
        if (msgInterval) return;
        msgInterval = setInterval(fetchMessages, 2000);
        fetchMessages();
    }

    async function fetchMessages() {
        if (!roomId) return;
        try {
            const res  = await fetch(`${base}/${roomId}/messages?since=${lastMsgId}`);
            const data = await res.json();
            data.messages.forEach(m => {
                appendMsg(m.body, m.is_me, m.name, m.created_at);
                if (m.id > lastMsgId) lastMsgId = m.id;
            });
        } catch(e) {}
    }

    // ── Send message ───────────────────────────────────────────────────────
    async function sendMsg() {
        const input = document.getElementById('sdInput');
        const body  = input.value.trim();
        if (!body || !roomId) return;
        input.value = '';
        appendMsg(body, true, 'You', 'Now');
        try {
            const res = await fetch(`${base}/${roomId}/send`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/json' },
                body: JSON.stringify({ body })
            });
            const data = await res.json();
            if (data.id > lastMsgId) lastMsgId = data.id;
        } catch(e) {}
    }

    document.getElementById('sdSendBtn')?.addEventListener('click', sendMsg);
    document.getElementById('sdInput')?.addEventListener('keydown', e => {
        if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendMsg(); }
    });

    function appendMsg(body, isMe, name, time) {
        const list = document.getElementById('sdMsgList');
        const div  = document.createElement('div');
        div.className = `sd-bubble ${isMe ? 'me' : 'them'}`;
        div.innerHTML = `${esc(body)} <span style="font-size:.65rem;opacity:.65;margin-left:.4rem;">${esc(time)}</span>`;
        list.appendChild(div);
        list.scrollTop = list.scrollHeight;
    }

    // ── Session ended ──────────────────────────────────────────────────────
    function endRoom() {
        clearInterval(timerInterval);
        clearInterval(msgInterval);
        timerInterval = null;
        msgInterval = null;
        showState('ended');
    }

    // ── Connect / Pass ─────────────────────────────────────────────────────
    document.getElementById('sdConnectBtn')?.addEventListener('click', async () => {
        document.getElementById('sdConnectWrap').classList.add('d-none');
        document.getElementById('sdWaitingConnect').classList.remove('d-none');
        try {
            const res  = await fetch(`${base}/${roomId}/connect`, { method: 'POST', headers: { 'X-CSRF-TOKEN': csrf } });
            const data = await res.json();
            document.getElementById('sdWaitingConnect').classList.add('d-none');
            if (data.matched) {
                document.getElementById('sdMatchedMsg').classList.remove('d-none');
                document.getElementById('sdConvLink').href = data.conversation_url ?? '#';
            } else {
                // Not matched yet — show "waiting for them" message but keep polling
                document.getElementById('sdWaitingConnect').classList.remove('d-none');
                let waitPoll = setInterval(async () => {
                    const r2 = await fetch(base + '/status');
                    const d2 = await r2.json();
                    if (d2.matched_url) {
                        clearInterval(waitPoll);
                        document.getElementById('sdWaitingConnect').classList.add('d-none');
                        document.getElementById('sdMatchedMsg').classList.remove('d-none');
                        document.getElementById('sdConvLink').href = d2.matched_url;
                    }
                }, 3000);
            }
        } catch(e) {
            document.getElementById('sdConnectWrap').classList.remove('d-none');
            document.getElementById('sdWaitingConnect').classList.add('d-none');
        }
    });

    document.getElementById('sdPassBtn')?.addEventListener('click', () => {
        document.getElementById('sdConnectWrap').classList.add('d-none');
        document.getElementById('sdNewRoundBtn').classList.remove('d-none');
    });

    document.getElementById('sdNewRoundBtn')?.addEventListener('click', async () => {
        await fetch(base + '/join', { method: 'POST', headers: { 'X-CSRF-TOKEN': csrf } });
        roomId = null;
        lastMsgId = 0;
        document.getElementById('sdMsgList').innerHTML = '';
        showState('waiting');
        startPoll();
    });

    // ── If page loaded with an active room, start immediately ─────────────
    if (state === 'active' && roomId) {
        startTimer();
        startMsgPoll();
        // Fetch initial other-user info from a quick status call
        pollStatus();
    } else if (state === 'waiting') {
        startPoll();
    }

    function esc(s) {
        return String(s ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }
})();
</script>
@endpush
