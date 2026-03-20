@extends('layouts.app')
@section('title', 'Setup — Step 3 of 5')
@section('content')
<div class="container py-5" style="max-width:640px">

    {{-- Progress --}}
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-1">
            <span class="small fw-semibold text-muted">Step {{ $step }} of {{ $total }}</span>
            <span class="small text-muted">{{ round(($step/$total)*100) }}% complete</span>
        </div>
        <div class="progress" style="height:6px;border-radius:10px">
            <div class="progress-bar bg-primary" style="width:{{ round(($step/$total)*100) }}%"></div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5">
        <div class="text-center mb-4">
            <div class="display-5 mb-2">📸</div>
            <h3 class="fw-bold">Add Your Photos</h3>
            <p class="text-muted">Profiles with photos get <strong>8× more matches</strong>. Add at least 1 photo.</p>
        </div>

        {{-- Uploaded photos grid --}}
        <div id="photo-grid" class="row g-2 mb-4">
            @foreach($photos as $photo)
            <div class="col-4" id="photo-item-{{ $photo->id }}">
                <div class="position-relative">
                    <img src="{{ Storage::url($photo->path) }}"
                         class="img-fluid rounded-3 w-100 object-fit-cover"
                         style="height:120px;object-fit:cover"
                         alt="Photo">
                    @if($photo->is_primary)
                    <span class="position-absolute top-0 start-0 badge bg-primary rounded-pill m-1" style="font-size:.65rem">Main</span>
                    @endif
                    <div class="position-absolute bottom-0 end-0 d-flex gap-1 m-1">
                        @unless($photo->is_primary)
                        <button type="button"
                            class="btn btn-sm btn-light btn-set-primary py-0 px-1"
                            data-id="{{ $photo->id }}"
                            title="Set as main photo"
                            style="font-size:.7rem">★</button>
                        @endunless
                        <button type="button"
                            class="btn btn-sm btn-danger btn-delete-photo py-0 px-1"
                            data-id="{{ $photo->id }}"
                            title="Delete"
                            style="font-size:.7rem">✕</button>
                    </div>
                </div>
            </div>
            @endforeach

            {{-- Upload slot --}}
            <div class="col-4" id="upload-slot">
                <label for="photo-input"
                    class="d-flex flex-column align-items-center justify-content-center bg-light rounded-3 border border-dashed text-muted"
                    style="height:120px;cursor:pointer;border-style:dashed!important">
                    <i class="bi bi-plus-lg fs-4"></i>
                    <small>Upload</small>
                </label>
                <input type="file" id="photo-input" accept="image/*" class="d-none" multiple>
            </div>
        </div>

        <div id="upload-progress" class="d-none mb-3">
            <div class="progress" style="height:6px">
                <div id="upload-bar" class="progress-bar progress-bar-striped progress-bar-animated bg-success" style="width:0%"></div>
            </div>
            <small class="text-muted" id="upload-status"></small>
        </div>

        {{-- Advance to step 4 --}}
        <form method="POST" action="{{ route('setup.store', ['step' => 3]) }}" id="advance-form">
            @csrf
            <div class="d-flex justify-content-between mt-2">
                <a href="{{ route('setup.step', 2) }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Back
                </a>
                <button type="submit" class="btn btn-primary px-4 fw-bold" id="btn-continue">
                    Continue <i class="bi bi-arrow-right ms-1"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
const photoInput = document.getElementById('photo-input');
const uploadSlot  = document.getElementById('upload-slot');
const progressWrap = document.getElementById('upload-progress');
const progressBar  = document.getElementById('upload-bar');
const uploadStatus = document.getElementById('upload-status');
const photoGrid    = document.getElementById('photo-grid');
const csrf         = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
const routePrimary = '{{ url("photos") }}';   // base: /dating/public/photos
const routeDelete  = '{{ url("photos") }}';

photoInput.addEventListener('change', async () => {
    const files = Array.from(photoInput.files);
    if (!files.length) return;

    progressWrap.classList.remove('d-none');

    for (let i = 0; i < files.length; i++) {
        uploadStatus.textContent = `Uploading ${i + 1} of ${files.length}…`;
        progressBar.style.width = `${Math.round(((i) / files.length) * 100)}%`;

        const fd = new FormData();
        fd.append('photo', files[i]);

        try {
            const res = await fetch('{{ route("photos.store") }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                body: fd,
            });
            const data = await res.json();

            if (res.ok && data.photo) {
                appendPhotoCard(data.photo);
            } else {
                alert(data.message || 'Upload failed.');
            }
        } catch (e) {
            alert('Network error during upload.');
        }
    }

    progressBar.style.width = '100%';
    uploadStatus.textContent = 'Done!';
    setTimeout(() => progressWrap.classList.add('d-none'), 1000);
    photoInput.value = '';
});

function appendPhotoCard(photo) {
    const col = document.createElement('div');
    col.className = 'col-4';
    col.id = `photo-item-${photo.id}`;
    col.innerHTML = `
        <div class="position-relative">
            <img src="${photo.url}" class="img-fluid rounded-3 w-100" style="height:120px;object-fit:cover" alt="Photo">
            <div class="position-absolute bottom-0 end-0 d-flex gap-1 m-1">
                <button type="button" class="btn btn-sm btn-light btn-set-primary py-0 px-1" data-id="${photo.id}" title="Set as main" style="font-size:.7rem">★</button>
                <button type="button" class="btn btn-sm btn-danger btn-delete-photo py-0 px-1" data-id="${photo.id}" title="Delete" style="font-size:.7rem">✕</button>
            </div>
        </div>`;
    photoGrid.insertBefore(col, document.getElementById('upload-slot'));
    col.querySelector('.btn-set-primary').addEventListener('click', setPrimary);
    col.querySelector('.btn-delete-photo').addEventListener('click', deletePhoto);
}

// Delegate existing buttons
photoGrid.addEventListener('click', async e => {
    const setPrim = e.target.closest('.btn-set-primary');
    const del     = e.target.closest('.btn-delete-photo');
    if (setPrim) await setPrimary.call(setPrim, e);
    if (del)     await deletePhoto.call(del, e);
});

async function setPrimary(e) {
    const id = this.dataset?.id || e.currentTarget?.dataset?.id;
    if (!id) return;
    const res = await fetch(`${routePrimary}/${id}/primary`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
    });
    if (res.ok) location.reload();
}

async function deletePhoto(e) {
    const id = this.dataset?.id || e.currentTarget?.dataset?.id;
    if (!id || !confirm('Delete this photo?')) return;
    const res = await fetch(`${routeDelete}/${id}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
    });
    if (res.ok) document.getElementById(`photo-item-${id}`)?.remove();
}
</script>
@endsection
