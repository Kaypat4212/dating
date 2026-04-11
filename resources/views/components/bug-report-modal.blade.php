{{--
  Bug Report Modal Component
  Include once in layouts/app.blade.php.
  Triggered by any element with data-bs-target="#bugReportModal".
--}}
@auth
<div class="modal fade" id="bugReportModal" tabindex="-1" aria-labelledby="bugReportModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="max-width:520px">
    <div class="modal-content" style="border-radius:20px;overflow:hidden;border:none">
      <div class="modal-header border-0"
           style="background:linear-gradient(135deg,#ef4444 0%,#b91c1c 100%);color:#fff;padding:1.25rem 1.5rem .75rem">
        <div class="d-flex align-items-center gap-2">
          <span style="font-size:1.5rem">🐛</span>
          <div>
            <h5 class="modal-title fw-bold mb-0" id="bugReportModalLabel">Report a Bug</h5>
            <p class="mb-0" style="font-size:.75rem;opacity:.85">Help us fix issues faster</p>
          </div>
        </div>
        <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body p-4" style="background:var(--bs-body-bg)">
        <div id="bugReportSuccess" class="d-none text-center py-4">
          <div style="font-size:3rem">✅</div>
          <h6 class="fw-bold mt-3">Report Submitted!</h6>
          <p class="text-muted small mb-0">Thank you! We'll look into this and fix it as soon as possible.</p>
        </div>

        <form id="bugReportForm" novalidate>
          <div class="mb-3">
            <label class="form-label fw-semibold small">Category</label>
            <select name="category" id="brCategory" class="form-select form-select-sm" required>
              <option value="">Select a category…</option>
              @foreach(\App\Models\BugReport::CATEGORIES as $key => $label)
                <option value="{{ $key }}">{{ $label }}</option>
              @endforeach
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold small">Brief Summary</label>
            <input type="text" name="title" id="brTitle" class="form-control form-control-sm"
                   placeholder="e.g. 'Snap fails to send on mobile'" maxlength="200" required>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold small">Describe what happened</label>
            <textarea name="description" id="brDescription" class="form-control form-control-sm" rows="4"
                      placeholder="What were you doing? What did you expect to happen? What actually happened?"
                      maxlength="5000" required></textarea>
            <div class="d-flex justify-content-end mt-1">
              <small class="text-muted" id="brCharCount">0 / 5000</small>
            </div>
          </div>

          <div id="bugReportError" class="alert alert-danger alert-sm d-none p-2 small mb-3"></div>
        </form>
      </div>

      <div class="modal-footer border-0" style="background:var(--bs-body-bg);gap:.5rem">
        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill" data-bs-dismiss="modal">
          Cancel
        </button>
        <button type="button" class="btn btn-sm btn-danger rounded-pill px-4" id="bugReportSubmit">
          <i class="bi bi-send me-1"></i>Submit Report
        </button>
      </div>
    </div>
  </div>
</div>

<script>
// Bug report modal — script is inline (component renders after @stack('scripts'))
document.addEventListener('DOMContentLoaded', function () {
(function () {
    const form       = document.getElementById('bugReportForm');
    const submitBtn  = document.getElementById('bugReportSubmit');
    const successEl  = document.getElementById('bugReportSuccess');
    const errorEl    = document.getElementById('bugReportError');
    const charCount  = document.getElementById('brCharCount');
    const descEl     = document.getElementById('brDescription');
    if (!form) return;

    descEl?.addEventListener('input', () => {
        charCount.textContent = `${descEl.value.length} / 5000`;
    });

    submitBtn?.addEventListener('click', async () => {
        errorEl.classList.add('d-none');
        const category    = document.getElementById('brCategory')?.value?.trim();
        const title       = document.getElementById('brTitle')?.value?.trim();
        const description = descEl?.value?.trim();

        if (!category || !title || !description) {
            errorEl.textContent = 'Please fill in all fields.';
            errorEl.classList.remove('d-none');
            return;
        }

        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Sending…';

        try {
            const csrf = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
            const res  = await fetch('{{ route("bug-report.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
                body: JSON.stringify({
                    category,
                    title,
                    description,
                    page_url: window.location.href,
                }),
            });

            const data = await res.json();

            if (res.ok && data.success) {
                form.classList.add('d-none');
                successEl.classList.remove('d-none');
                submitBtn.classList.add('d-none');
                // Auto-close modal after 2.5s
                setTimeout(() => {
                    const modal = document.getElementById('bugReportModal');
                    window.bootstrap?.Modal?.getInstance(modal)?.hide();
                    // Reset for next use
                    setTimeout(() => {
                        form.reset();
                        form.classList.remove('d-none');
                        successEl.classList.add('d-none');
                        submitBtn.classList.remove('d-none');
                        charCount.textContent = '0 / 5000';
                    }, 500);
                }, 2500);
            } else {
                const msg = data.errors
                    ? Object.values(data.errors).flat().join(' ')
                    : (data.message ?? 'Failed to submit. Please try again.');
                errorEl.textContent = msg;
                errorEl.classList.remove('d-none');
            }
        } catch (e) {
            errorEl.textContent = 'Connection error. Please check your internet and try again.';
            errorEl.classList.remove('d-none');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="bi bi-send me-1"></i>Submit Report';
        }
    });
})();
});
</script>
@endauth
