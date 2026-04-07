{{--
  What's New Modal Component
  Include once in layouts/app.blade.php after the navbar.
  Auto-fetches unread announcements via JS and opens the modal if any exist.
--}}
@auth
<div class="modal fade" id="whatsNewModal" tabindex="-1" aria-labelledby="whatsNewModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable" style="max-width:560px">
    <div class="modal-content" style="border-radius:20px;overflow:hidden;border:none">

      {{-- Header --}}
      <div class="modal-header border-0 pb-0"
           style="background:linear-gradient(135deg,#7c3aed 0%,#c2185b 60%,#f97316 100%);color:#fff;padding:1.5rem 1.5rem 1rem">
        <div class="d-flex align-items-center gap-3 w-100">
          <span style="font-size:2rem">🎉</span>
          <div class="flex-grow-1">
            <h5 class="modal-title fw-bold mb-0" id="whatsNewModalLabel" style="font-size:1.25rem">
              What's New
            </h5>
            <p class="mb-0 mt-1" style="font-size:.78rem;opacity:.85">
              Fresh updates from the team
            </p>
          </div>
          <div class="d-flex align-items-center gap-2">
            <span id="wnUnreadBadge" class="badge rounded-pill"
                  style="background:rgba(255,255,255,.25);font-size:.7rem;display:none"></span>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
        </div>
      </div>

      {{-- Body --}}
      <div class="modal-body p-0" style="background:var(--bs-body-bg)">
        {{-- Loading skeleton --}}
        <div id="wnLoading" class="p-4 text-center">
          <div class="spinner-border text-primary" style="width:1.5rem;height:1.5rem" role="status">
            <span class="visually-hidden">Loading…</span>
          </div>
        </div>

        {{-- Items list --}}
        <div id="wnList" class="d-none"></div>

        {{-- Empty state --}}
        <div id="wnEmpty" class="d-none text-center py-5 px-4">
          <div style="font-size:3rem">✅</div>
          <h6 class="fw-bold mt-3">You're all caught up!</h6>
          <p class="text-muted small mb-0">No new announcements right now.</p>
        </div>
      </div>

      {{-- Footer --}}
      <div class="modal-footer border-0" style="background:var(--bs-body-bg);gap:.5rem">
        <a href="{{ route('announcements.index') }}" class="btn btn-sm btn-outline-primary rounded-pill me-auto">
          <i class="bi bi-megaphone me-1"></i>View All
        </a>
        <button type="button" id="wnMarkAllBtn" class="btn btn-sm btn-outline-secondary rounded-pill d-none">
          <i class="bi bi-check2-all me-1"></i>Mark all read
        </button>
        <button type="button" class="btn btn-sm btn-primary rounded-pill" data-bs-dismiss="modal">
          Close
        </button>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
(function () {
    const csrf     = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
    const modal    = document.getElementById('whatsNewModal');
    if (!modal) return;

    const bsModal  = new bootstrap.Modal(modal, { backdrop: true, keyboard: true });
    const $loading = document.getElementById('wnLoading');
    const $list    = document.getElementById('wnList');
    const $empty   = document.getElementById('wnEmpty');
    const $badge   = document.getElementById('wnUnreadBadge');
    const $markAll = document.getElementById('wnMarkAllBtn');

    const TYPE_COLORS = {
        feature:     'success',
        update:      'primary',
        maintenance: 'warning',
        message:     'info',
        promo:       'danger',
    };

    // ── Fetch & render unread announcements ─────────────────────────────────
    async function load() {
        try {
            const res  = await fetch('{{ route("announcements.unread") }}', { headers: { 'Accept': 'application/json' } });
            const data = await res.json();

            $loading.classList.add('d-none');

            if (!data.items || data.items.length === 0) {
                $empty.classList.remove('d-none');
                return;
            }

            $badge.textContent = data.unread_count + ' new';
            $badge.style.display = '';
            $markAll.classList.remove('d-none');

            data.items.forEach(item => {
                $list.appendChild(renderItem(item));
            });

            $list.classList.remove('d-none');
        } catch (e) {
            $loading.innerHTML = '<p class="text-muted small p-3 mb-0">Could not load announcements.</p>';
        }
    }

    function renderItem(item) {
        const typeColor = TYPE_COLORS[item.type] ?? 'secondary';
        const div = document.createElement('div');
        div.className = 'wn-modal-item';
        div.dataset.id = item.id;
        div.style.cssText = 'border-bottom:1px solid var(--bs-border-color);padding:1rem 1.25rem;transition:background .15s;' + (item.is_read ? '' : 'border-left:3px solid #7c3aed;');

        const badge  = item.badge_label
            ? `<span class="badge bg-${esc(item.badge_color)} rounded-pill" style="font-size:.6rem">${esc(item.badge_label)}</span>`
            : '';
        const version = item.version
            ? `<span style="font-size:.6rem;font-weight:700;padding:2px 7px;border-radius:20px;background:var(--bs-secondary-bg);color:var(--bs-secondary-color);text-transform:uppercase;letter-spacing:.04em">${esc(item.version)}</span>`
            : '';
        const dot = !item.is_read
            ? `<span style="display:inline-block;width:7px;height:7px;border-radius:50%;background:#7c3aed;flex-shrink:0" title="Unread"></span>`
            : '';

        div.innerHTML = `
            <div class="d-flex align-items-start gap-3">
                <span style="font-size:1.4rem;line-height:1;flex-shrink:0;margin-top:2px">${esc(item.type_icon)}</span>
                <div class="flex-grow-1 overflow-hidden">
                    <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                        <span class="fw-semibold" style="font-size:.92rem">${esc(item.title)}</span>
                        ${dot}${badge}${version}
                    </div>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-${typeColor}-subtle text-${typeColor}" style="font-size:.6rem">${esc(item.type)}</span>
                        ${item.published_at ? `<span class="text-muted" style="font-size:.68rem">${esc(item.published_at)}</span>` : ''}
                    </div>
                    <div class="wn-body-html text-body" style="font-size:.85rem;line-height:1.55">${item.body}</div>
                </div>
            </div>`;

        return div;
    }

    // ── Mark all read ────────────────────────────────────────────────────────
    $markAll.addEventListener('click', async () => {
        try {
            await fetch('{{ route("announcements.read-all") }}', {
                method: 'POST', headers: { 'X-CSRF-TOKEN': csrf }
            });
            // Remove unread indicators
            document.querySelectorAll('.wn-modal-item').forEach(el => {
                el.style.borderLeft = '';
                el.querySelector('[title="Unread"]')?.remove();
            });
            $badge.style.display = 'none';
            $markAll.classList.add('d-none');
            // Update nav badge
            const navBadge = document.getElementById('wnNavBadge');
            if (navBadge) navBadge.style.display = 'none';
        } catch (e) {}
    });

    // ── On modal open, load if list is empty ─────────────────────────────────
    modal.addEventListener('show.bs.modal', () => {
        if ($list.childElementCount === 0 && $empty.classList.contains('d-none')) {
            $loading.classList.remove('d-none');
            load();
        }
    });

    // ── Auto-open if there are unread announcements ──────────────────────────
    // Check nav badge count set server-side
    const navBadge = document.getElementById('wnNavBadge');
    const wnCount  = parseInt(navBadge?.dataset.count ?? '0', 10);
    if (wnCount > 0) {
        // Delay slightly so page feels settled
        setTimeout(() => {
            bsModal.show();
            load();
        }, 1800);
    }

    function esc(s) {
        return String(s ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }
})();
</script>
@endpush

<style>
.wn-body-html h2 { font-size:.95rem;font-weight:700;margin-top:.8rem; }
.wn-body-html h3 { font-size:.88rem;font-weight:700;margin-top:.6rem; }
.wn-body-html ul, .wn-body-html ol { padding-left:1.2rem;margin-bottom:.4rem; }
.wn-body-html p { margin-bottom:.4rem; }
.wn-body-html a { color:#7c3aed; }
.wn-modal-item:last-child { border-bottom: none !important; }
</style>
@endauth
