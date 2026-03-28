@extends('layouts.app')
@section('title', 'Voice Prompts')

@push('styles')
<style>
.voice-card { transition: box-shadow .2s; }
.voice-card:hover { box-shadow: 0 .5rem 1.5rem rgba(0,0,0,.12) !important; }

@keyframes pulse-ring {
    0%   { transform: scale(.9); opacity:.8; }
    70%  { transform: scale(1.3); opacity:0; }
    100% { transform: scale(1.3); opacity:0; }
}
.recording-pulse {
    position: relative;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
.recording-pulse::before {
    content: '';
    position: absolute;
    width: 100%; height: 100%;
    border-radius: 50%;
    background: rgba(220,53,69,.4);
    animation: pulse-ring 1.2s ease-out infinite;
}

.waveform { display: flex; align-items: center; gap: 3px; height: 28px; }
.waveform span {
    display: block;
    width: 4px;
    background: #dc3545;
    border-radius: 2px;
    animation: wave 0.8s ease-in-out infinite;
    height: 10px;
}
.waveform span:nth-child(2)  { animation-delay: .12s; }
.waveform span:nth-child(3)  { animation-delay: .24s; }
.waveform span:nth-child(4)  { animation-delay: .36s; }
.waveform span:nth-child(5)  { animation-delay: .48s; }
@keyframes wave {
    0%, 100% { height: 8px; }
    50%       { height: 24px; }
}
</style>
@endpush

@section('content')
<div class="container py-4" style="max-width:920px;">

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ route('profile.edit') }}">Profile</a></li>
            <li class="breadcrumb-item active">Voice Prompts</li>
        </ol>
    </nav>

    {{-- Header --}}
    <div class="d-flex align-items-center gap-3 mb-4">
        <div class="bg-danger bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:56px;height:56px;">
            <i class="bi bi-mic-fill text-danger fs-4"></i>
        </div>
        <div>
            <h2 class="fw-bold mb-0">Voice Prompts</h2>
            <p class="text-muted small mb-0">Record up to 30 seconds answering a prompt — let people hear your personality.</p>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if($questions->isEmpty())
    <div class="text-center py-5 text-muted">
        <i class="bi bi-mic-mute fs-1 d-block mb-2 opacity-50"></i>
        No voice prompt questions are available yet. Check back soon!
    </div>
    @else

    {{-- Tip banner --}}
    <div class="alert alert-info d-flex gap-2 align-items-start py-2 mb-4 small">
        <i class="bi bi-info-circle-fill mt-1 flex-shrink-0"></i>
        <span><strong>How it works:</strong> Click <em>Record</em> on any question, speak naturally (max 30 s), then click <em>Stop</em>. Preview your audio, then hit <em>Save</em>.</span>
    </div>

    <div class="row g-3">
        @foreach($questions as $question)
        @php $existing = $myPrompts[$question->id] ?? null; @endphp
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100 voice-card">
                <div class="card-body d-flex flex-column">

                    {{-- Question --}}
                    <p class="fw-semibold mb-3 flex-grow-1">"{{ $question->prompt_text }}"</p>

                    {{-- Existing recording --}}
                    @if($existing)
                    <div class="mb-3 p-2 rounded bg-light border">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="badge bg-success-subtle text-success border border-success-subtle small">
                                <i class="bi bi-check-circle me-1"></i>Saved
                            </span>
                            @if($existing->duration_seconds)
                            <span class="text-muted small">{{ $existing->duration_seconds }}s</span>
                            @endif
                            <span class="ms-auto text-muted small"><i class="bi bi-headphones me-1"></i>{{ $existing->plays_count }} plays</span>
                        </div>
                        <audio controls preload="none"
                               src="{{ Storage::url($existing->audio_path) }}"
                               class="w-100" style="height:36px;"></audio>
                        <div class="text-end mt-1">
                            <form action="{{ route('extras.voice.destroy', $existing->id) }}" method="POST"
                                  style="display:inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2"
                                        onclick="return confirm('Delete this voice prompt?')">
                                    <i class="bi bi-trash me-1"></i>Delete
                                </button>
                            </form>
                        </div>
                    </div>
                    @endif

                    {{-- ── Recorder ── --}}
                    <div class="recorder" data-question="{{ $question->id }}">

                        {{-- Hidden upload form --}}
                        <form class="upload-form"
                              action="{{ route('extras.voice.store') }}" method="POST"
                              enctype="multipart/form-data" style="display:none">
                            @csrf
                            <input type="hidden" name="question_id" value="{{ $question->id }}">
                            <input type="hidden" name="duration" class="duration-input">
                            <input type="file"   name="audio"    class="audio-input" accept="audio/*">
                        </form>

                        {{-- IDLE --}}
                        <div class="state-idle">
                            <button type="button" class="btn btn-outline-danger btn-sm btn-record">
                                <i class="bi bi-mic me-1"></i>{{ $existing ? 'Re-record' : 'Start Recording' }}
                            </button>
                        </div>

                        {{-- RECORDING --}}
                        <div class="state-recording" style="display:none">
                            <div class="d-flex align-items-center gap-3 mb-2">
                                <span class="recording-pulse flex-shrink-0">
                                    <button type="button"
                                            class="btn btn-danger btn-sm btn-stop rounded-circle"
                                            style="width:38px;height:38px;position:relative;z-index:1;">
                                        <i class="bi bi-stop-fill"></i>
                                    </button>
                                </span>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between small mb-1">
                                        <span class="text-danger fw-semibold">
                                            <i class="bi bi-record-circle me-1"></i>Recording…
                                        </span>
                                        <span class="text-muted timer-display">0 / 30s</span>
                                    </div>
                                    <div class="progress" style="height:4px;">
                                        <div class="progress-bar bg-danger rec-progress" style="width:0%;transition:width .9s linear;"></div>
                                    </div>
                                </div>
                                <div class="waveform flex-shrink-0">
                                    <span></span><span></span><span></span><span></span><span></span>
                                </div>
                            </div>
                        </div>

                        {{-- PREVIEW --}}
                        <div class="state-preview" style="display:none">
                            <p class="small text-muted mb-1">
                                <i class="bi bi-play-circle me-1"></i>Preview — happy with it?
                            </p>
                            <audio controls class="w-100 preview-audio mb-2" style="height:36px;"></audio>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-success btn-sm btn-save flex-grow-1">
                                    <i class="bi bi-cloud-upload me-1"></i>Save Recording
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm btn-redo">
                                    <i class="bi bi-arrow-repeat me-1"></i>Redo
                                </button>
                            </div>
                        </div>

                        {{-- UPLOADING --}}
                        <div class="state-uploading" style="display:none">
                            <div class="d-flex align-items-center gap-2 text-muted small">
                                <div class="spinner-border spinner-border-sm text-danger"></div>
                                Saving your recording…
                            </div>
                        </div>

                    </div>{{-- /recorder --}}
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

</div>

@push('scripts')
<script>
(function () {
    'use strict';

    document.querySelectorAll('.recorder').forEach(function (rec) {
        var st = {
            mediaRecorder: null,
            chunks:  [],
            elapsed: 0,
            timer:   null,
            blob:    null,
        };

        var stateIdle     = rec.querySelector('.state-idle');
        var stateRec      = rec.querySelector('.state-recording');
        var statePreview  = rec.querySelector('.state-preview');
        var stateUploading= rec.querySelector('.state-uploading');
        var timerEl       = rec.querySelector('.timer-display');
        var progressEl    = rec.querySelector('.rec-progress');
        var previewAudio  = rec.querySelector('.preview-audio');
        var form          = rec.querySelector('.upload-form');
        var audioInput    = rec.querySelector('.audio-input');
        var durInput      = rec.querySelector('.duration-input');

        function showState(name) {
            [stateIdle, stateRec, statePreview, stateUploading].forEach(function(el) {
                el.style.display = 'none';
            });
            rec.querySelector('.state-' + name).style.display = 'block';
        }

        // ── Record ────────────────────────────────────────────────────────
        rec.querySelector('.btn-record').addEventListener('click', function () {
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                alert('Your browser does not support audio recording.\nPlease use Chrome, Edge, or Firefox.');
                return;
            }
            navigator.mediaDevices.getUserMedia({ audio: true }).then(function (stream) {
                st.chunks   = [];
                st.elapsed  = 0;
                if (previewAudio.src) { URL.revokeObjectURL(previewAudio.src); previewAudio.src = ''; }

                var candidates = [
                    'audio/webm;codecs=opus',
                    'audio/webm',
                    'audio/ogg;codecs=opus',
                    'audio/mp4'
                ];
                var mimeType = '';
                for (var i = 0; i < candidates.length; i++) {
                    if (typeof MediaRecorder.isTypeSupported === 'function' &&
                        MediaRecorder.isTypeSupported(candidates[i])) {
                        mimeType = candidates[i];
                        break;
                    }
                }

                st.mediaRecorder = new MediaRecorder(stream, mimeType ? { mimeType: mimeType } : {});
                st.mediaRecorder.ondataavailable = function (e) {
                    if (e.data && e.data.size > 0) st.chunks.push(e.data);
                };
                st.mediaRecorder.onstop = function () {
                    stream.getTracks().forEach(function (t) { t.stop(); });
                    st.blob = new Blob(st.chunks, { type: st.mediaRecorder.mimeType || 'audio/webm' });
                    previewAudio.src = URL.createObjectURL(st.blob);
                    showState('preview');
                };
                st.mediaRecorder.start(250);
                showState('recording');

                // Update timer / progress every second
                timerEl.textContent   = '0 / 30s';
                progressEl.style.width = '0%';
                st.timer = setInterval(function () {
                    st.elapsed++;
                    var pct = Math.min((st.elapsed / 30) * 100, 100);
                    timerEl.textContent    = st.elapsed + ' / 30s';
                    progressEl.style.width = pct + '%';
                    if (st.elapsed >= 30) stopRecording();
                }, 1000);

            }).catch(function (err) {
                console.error('Microphone error:', err);
                var msg = 'Microphone access was denied.';
                if (err.name === 'NotFoundError') msg = 'No microphone found on this device.';
                else if (err.name === 'NotAllowedError') msg = 'Microphone permission denied.\nClick the lock icon in your address bar to allow it.';
                alert(msg);
            });
        });

        // ── Stop ──────────────────────────────────────────────────────────
        function stopRecording() {
            clearInterval(st.timer);
            if (st.mediaRecorder && st.mediaRecorder.state !== 'inactive') {
                st.mediaRecorder.stop();
            }
        }
        rec.querySelector('.btn-stop').addEventListener('click', stopRecording);

        // ── Redo ──────────────────────────────────────────────────────────
        rec.querySelector('.btn-redo').addEventListener('click', function () {
            if (previewAudio.src) { URL.revokeObjectURL(previewAudio.src); previewAudio.src = ''; }
            showState('idle');
        });

        // ── Save ──────────────────────────────────────────────────────────
        rec.querySelector('.btn-save').addEventListener('click', function () {
            if (!st.blob) return;
            var mime = st.blob.type || 'audio/webm';
            var ext  = 'webm';
            if (mime.indexOf('ogg')  !== -1) ext = 'ogg';
            else if (mime.indexOf('mp4') !== -1) ext = 'mp4';

            var file = new File([st.blob], 'voice-prompt.' + ext, { type: mime });
            try {
                var dt = new DataTransfer();
                dt.items.add(file);
                audioInput.files = dt.files;
            } catch (e) {
                // DataTransfer not supported (rare) — skip; server will handle missing file gracefully
                console.warn('DataTransfer not supported:', e);
            }
            durInput.value = st.elapsed;
            showState('uploading');
            form.submit();
        });
    });
})();
</script>
@endpush
@endsection
