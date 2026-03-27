
<?php $__env->startSection('title', 'Voice Prompts'); ?>
<?php $__env->startSection('content'); ?>
<div class="container py-4" style="max-width:900px;">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo e(route('profile.edit')); ?>">Profile</a></li>
            <li class="breadcrumb-item active">Voice Prompts</li>
        </ol>
    </nav>

    <div class="mb-4">
        <h2 class="fw-bold mb-0"><i class="bi bi-mic-fill text-danger me-2"></i>Voice Prompts</h2>
        <p class="text-muted small">Record 30-second answers to show your personality. Visitors can play them on your profile.</p>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
    <div class="alert alert-success alert-dismissible fade show"><?php echo e(session('success')); ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="row g-3">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $questions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $question): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php $existing = $myPrompts[$question->id] ?? null; ?>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <p class="fw-semibold mb-3"><?php echo e($question->prompt_text); ?></p>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($existing): ?>
                    <div class="d-flex align-items-center gap-2 mb-3 p-2 rounded bg-light">
                        <button type="button" class="btn btn-success btn-sm rounded-circle"
                                onclick="playVoicePrompt(<?php echo e($existing->id); ?>, this)"
                                style="width:36px;height:36px;">
                            <i class="bi bi-play-fill"></i>
                        </button>
                        <div class="flex-grow-1">
                            <div class="text-muted small">Recorded
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($existing->duration_seconds): ?> &bull; <?php echo e($existing->duration_seconds); ?>s <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                            <div class="text-muted small"><i class="bi bi-headphones me-1"></i><?php echo e($existing->plays_count); ?> plays</div>
                        </div>
                        <form action="<?php echo e(route('extras.voice.destroy', $existing->id)); ?>" method="POST">
                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                            <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this voice prompt?')">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    
                    <div x-data="voiceRecorder(<?php echo e($question->id); ?>)" x-init="init()">
                        <form action="<?php echo e(route('extras.voice.store')); ?>" method="POST" enctype="multipart/form-data"
                              x-ref="uploadForm">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="question_id" value="<?php echo e($question->id); ?>">
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
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
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
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\dating\resources\views\profile\extras\voice.blade.php ENDPATH**/ ?>