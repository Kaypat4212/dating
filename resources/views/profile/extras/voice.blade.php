@extends('layouts.app')
@section('title', 'Voice Prompts')
@section('content')
<div class="container py-4" style="max-width:900px;">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('profile.edit') }}">Profile</a></li>
            <li class="breadcrumb-item active">Voice Prompts</li>
        </ol>
    </nav>

    <div class="mb-4">
        <h2 class="fw-bold mb-0"><i class="bi bi-mic-fill text-danger me-2"></i>Voice Prompts</h2>
        <p class="text-muted small">Record 30-second answers to show your personality. Visitors can play them on your profile.</p>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    <div class="row g-3">
        @foreach($questions as $question)
        @php $existing = $myPrompts[$question->id] ?? null; @endphp
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <p class="fw-semibold mb-3">{{ $question->prompt_text }}</p>

                    @if($existing)
                    <div class="d-flex align-items-center gap-2 mb-3 p-2 rounded bg-light">
                        <button type="button" class="btn btn-success btn-sm rounded-circle"
                                onclick="playVoicePrompt({{ $existing->id }}, this)"
                                style="width:36px;height:36px;">
                            <i class="bi bi-play-fill"></i>
                        </button>
                        <div class="flex-grow-1">
                            <div class="text-muted small">Recorded
                                @if($existing->duration_seconds) &bull; {{ $existing->duration_seconds }}s @endif
                            </div>
                            <div class="text-muted small"><i class="bi bi-headphones me-1"></i>{{ $existing->plays_count }} plays</div>
                        </div>
                        <form action="{{ route('extras.voice.destroy', $existing->id) }}" method="POST">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this voice prompt?')">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                    @endif

                    {{-- Record button --}}
                    <div x-data="voiceRecorder({{ $question->id }})" x-init="init()">
                        <form action="{{ route('extras.voice.store') }}" method="POST" enctype="multipart/form-data"
                              x-ref="uploadForm">
                            @csrf
                            <input type="hidden" name="question_id" value="{{ $question->id }}">
                            <input type="hidden" name="duration" x-model="duration">
                            <input type="file" name="audio" x-ref="audioInput" class="d-none" accept="audio/*">
                        </form>

                        <div class="d-flex gap-2 align-items-center">
                            <button type="button" class="btn btn-outline-danger btn-sm"
                                    x-show="!recording && !recorded"
                                    @click="startRecording()">
                                <i class="bi bi-mic me-1"></i>Record
                            </button>
                            <button type="button" class="btn btn-danger btn-sm"
                                    x-show="recording"
                                    @click="stopRecording()">
                                <i class="bi bi-stop-circle me-1"></i>Stop (<span x-text="elapsed"></span>s)
                            </button>
                            <template x-if="recorded">
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-success btn-sm" @click="submitRecording()">
                                        <i class="bi bi-upload me-1"></i>Save
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" @click="reset()">
                                        <i class="bi bi-arrow-repeat me-1"></i>Redo
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

@push('scripts')
<script>
function voiceRecorder(questionId) {
    return {
        recording: false,
        recorded: false,
        mediaRecorder: null,
        chunks: [],
        elapsed: 0,
        duration: 0,
        timer: null,

        init() {},

        async startRecording() {
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                this.mediaRecorder = new MediaRecorder(stream);
                this.chunks = [];
                this.elapsed = 0;
                this.mediaRecorder.ondataavailable = e => this.chunks.push(e.data);
                this.mediaRecorder.onstop = () => this.onStop(stream);
                this.mediaRecorder.start();
                this.recording = true;
                this.timer = setInterval(() => {
                    this.elapsed++;
                    if (this.elapsed >= 30) this.stopRecording();
                }, 1000);
            } catch(err) {
                alert('Microphone access denied. Please allow microphone access.');
            }
        },

        stopRecording() {
            clearInterval(this.timer);
            if (this.mediaRecorder) this.mediaRecorder.stop();
            this.recording = false;
        },

        onStop(stream) {
            stream.getTracks().forEach(t => t.stop());
            this.duration = this.elapsed;
            this.recorded = true;
        },

        submitRecording() {
            const blob = new Blob(this.chunks, { type: 'audio/webm' });
            const file = new File([blob], 'voice-prompt.webm', { type: 'audio/webm' });
            const dt = new DataTransfer();
            dt.items.add(file);
            this.$refs.audioInput.files = dt.files;
            this.$refs.uploadForm.submit();
        },

        reset() {
            this.recorded = false;
            this.chunks = [];
            this.elapsed = 0;
        }
    };
}

async function playVoicePrompt(id, btn) {
    const resp = await fetch(`/profile/extras/voice/${id}/play`);
    const data = await resp.json();
    const audio = new Audio(data.url);
    btn.innerHTML = '<i class="bi bi-pause-fill"></i>';
    audio.play();
    audio.onended = () => { btn.innerHTML = '<i class="bi bi-play-fill"></i>'; };
}
</script>
@endpush
@endsection
