<div class="modal fade" id="fundWalletModal" tabindex="-1" aria-labelledby="fundWalletModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="fundWalletModalLabel"><i class="bi bi-plus-circle-fill text-primary me-2"></i>Fund Wallet</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="fundWalletForm" enctype="multipart/form-data">
        @csrf
        <div class="modal-body">

          {{-- Step 1: Choose amount --}}
          <div class="mb-4">
            <label for="fund-amount" class="form-label fw-semibold">Amount (credits)</label>
            <input type="number" class="form-control" id="fund-amount" name="amount" min="1" placeholder="e.g. 50" required>
            <div class="form-text" id="fund-usd-equiv" style="min-height:1.2em"></div>
          </div>

          {{-- Step 2: Wallet addresses + copy + QR --}}
          <div class="mb-4">
            <div class="fw-semibold mb-2">Send crypto to one of these addresses:</div>
            @forelse($wallets as $w)
              <div class="card mb-3 border">
                <div class="card-body py-3 px-3">
                  <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                    <div>
                      <span class="badge bg-primary me-1">{{ $w->currency }}</span>
                      <span class="text-muted small">{{ $w->network }}</span>
                      @if($w->label)
                        <span class="text-secondary small ms-1">— {{ $w->label }}</span>
                      @endif
                    </div>
                  </div>
                  <div class="d-flex align-items-center gap-2 mb-2">
                    <code class="flex-grow-1 bg-light rounded px-2 py-1 small" style="word-break:break-all">{{ $w->address }}</code>
                    <button type="button" class="btn btn-sm btn-outline-secondary copy-addr-btn flex-shrink-0" data-address="{{ $w->address }}" title="Copy address">
                      <i class="bi bi-copy"></i>
                    </button>
                  </div>
                  @if($w->qr_code_path)
                    <div class="text-center mt-2">
                      <img src="{{ asset('storage/'.$w->qr_code_path) }}" class="img-thumbnail" style="max-width:140px;max-height:140px" alt="QR Code for {{ $w->display_name }}">
                      <div class="text-muted small mt-1">Scan QR to send</div>
                    </div>
                  @endif
                </div>
              </div>
            @empty
              <div class="alert alert-warning">No wallet addresses configured yet. Please check back later.</div>
            @endforelse
          </div>

          {{-- Step 3: TXID + proof --}}
          <div class="mb-3">
            <label for="fund-txid" class="form-label fw-semibold">Transaction ID (TXID)</label>
            <input type="text" class="form-control" id="fund-txid" name="txid" placeholder="Paste your transaction hash here" required>
            <div class="form-text">After sending, paste the transaction hash/ID from your wallet app.</div>
          </div>
          <div class="mb-3">
            <label for="fund-proof" class="form-label fw-semibold">Proof of Payment (screenshot)</label>
            <input type="file" class="form-control" id="fund-proof" name="proof" accept="image/*" required>
            <div class="form-text">Upload a screenshot of your transaction confirmation.</div>
          </div>

          <div id="fund-alert" class="d-none"></div>
          <div class="alert alert-info small mb-0"><i class="bi bi-info-circle me-1"></i>Your request will be reviewed by an admin and credits added to your account once confirmed.</div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary" id="fund-submit-btn">
            <span id="fund-spinner" class="spinner-border spinner-border-sm d-none me-1"></span>
            <i class="bi bi-send me-1"></i>Submit Request
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="withdrawWalletModal" tabindex="-1" aria-labelledby="withdrawWalletModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="withdrawWalletModalLabel"><i class="bi bi-arrow-up-circle-fill text-danger me-2"></i>Withdraw Credits</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="withdrawWalletForm">
        @csrf
        <input type="hidden" id="withdraw-currency" name="currency">
        <input type="hidden" id="withdraw-network" name="network">
        <div class="modal-body">

          {{-- Balance display --}}
          <div class="alert alert-secondary py-2 px-3 mb-4 d-flex align-items-center justify-content-between">
            <span class="small text-muted">Available Balance</span>
            <span class="fw-bold fs-5" id="withdraw-balance-display">{{ auth()->user()->credit_balance }} <span class="text-muted small fw-normal">credits</span></span>
          </div>

          {{-- Step 1: Amount --}}
          <div class="mb-4">
            <label for="withdraw-amount" class="form-label fw-semibold">Amount to Withdraw (credits)</label>
            <input type="number" class="form-control" id="withdraw-amount" name="amount" min="10" placeholder="Minimum 10 credits" required>
            <div id="withdraw-usd-equiv" class="form-text"></div>
            <div class="form-text">Minimum withdrawal: 10 credits. Credits will be deducted immediately pending admin review.</div>
          </div>

          {{-- Step 2: Select crypto --}}
          @if($wallets->isNotEmpty())
          <div class="mb-4">
            <div class="fw-semibold mb-2">Select Payout Crypto <span class="text-danger">*</span></div>
            <div class="row g-2" id="crypto-selector">
              @foreach($wallets as $w)
              <div class="col-12 col-sm-6">
                <label class="d-block cursor-pointer">
                  <input type="radio" name="_crypto_pick" class="d-none crypto-radio"
                         data-currency="{{ $w->currency }}" data-network="{{ $w->network ?? '' }}" value="{{ $w->id }}">
                  <div class="card border-2 crypto-pick-card h-100" style="cursor:pointer;transition:border-color .15s,background .15s">
                    <div class="card-body py-2 px-3 d-flex align-items-center gap-3">
                      <div class="flex-shrink-0 d-flex align-items-center justify-content-center rounded-circle bg-primary bg-opacity-10" style="width:40px;height:40px">
                        <i class="bi bi-currency-bitcoin text-primary fs-5"></i>
                      </div>
                      <div>
                        <div class="fw-bold">{{ $w->currency }}</div>
                        @if($w->network)<div class="text-muted small">{{ $w->network }}</div>@endif
                        @if($w->label)<div class="text-secondary" style="font-size:.75rem">{{ $w->label }}</div>@endif
                      </div>
                      <div class="ms-auto crypto-check d-none"><i class="bi bi-check-circle-fill text-primary fs-5"></i></div>
                    </div>
                  </div>
                </label>
              </div>
              @endforeach
            </div>
            <div id="crypto-required-msg" class="text-danger small mt-1 d-none">Please select a crypto currency for payout.</div>
          </div>
          @endif

          {{-- Step 3: Destination address --}}
          <div class="mb-3">
            <label for="withdraw-destination" class="form-label fw-semibold">Your <span id="withdraw-crypto-label">Crypto</span> Wallet Address <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="withdraw-destination" name="destination" placeholder="Paste your receiving wallet address" required>
            <div class="form-text">Enter <strong>your own wallet address</strong> where you want to receive the payout.</div>
          </div>

          <div id="withdraw-alert" class="d-none"></div>
          <div class="alert alert-warning small mb-0"><i class="bi bi-exclamation-triangle me-1"></i>Withdrawals are processed manually by an admin within 24–48 hours. Credits are held until processed.</div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-danger" id="withdraw-submit-btn">
            <span id="withdraw-spinner" class="spinner-border spinner-border-sm d-none me-1"></span>
            <i class="bi bi-arrow-up-right me-1"></i>Submit Withdrawal
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

@push('scripts')
<script>
function showFundModal() {
  document.getElementById('fund-alert').className = 'd-none';
  document.getElementById('fund-alert').innerHTML = '';
  document.getElementById('fundWalletForm').reset();
  new bootstrap.Modal(document.getElementById('fundWalletModal')).show();
}
function showWithdrawModal() {
  document.getElementById('withdraw-alert').className = 'd-none';
  document.getElementById('withdraw-alert').innerHTML = '';
  document.getElementById('withdrawWalletForm').reset();
  document.getElementById('withdraw-currency').value = '';
  document.getElementById('withdraw-network').value = '';
  // Reset crypto card selection
  document.querySelectorAll('.crypto-pick-card').forEach(c => {
    c.classList.remove('border-primary', 'bg-primary', 'bg-opacity-10');
    c.classList.add('border');
    c.querySelector('.crypto-check')?.classList.add('d-none');
  });
  document.getElementById('withdraw-crypto-label').textContent = 'Crypto';
  const cReqMsg = document.getElementById('crypto-required-msg');
  if (cReqMsg) cReqMsg.classList.add('d-none');
  new bootstrap.Modal(document.getElementById('withdrawWalletModal')).show();
}

// Crypto card picker
document.addEventListener('click', function(e) {
  const card = e.target.closest('.crypto-pick-card');
  if (!card) return;
  const label = card.closest('label');
  if (!label) return;
  const radio = label.querySelector('.crypto-radio');
  if (!radio) return;
  radio.checked = true;
  // Update hidden fields
  document.getElementById('withdraw-currency').value = radio.dataset.currency;
  document.getElementById('withdraw-network').value  = radio.dataset.network;
  // Update destination label
  document.getElementById('withdraw-crypto-label').textContent = radio.dataset.currency;
  // Visual state — reset all
  document.querySelectorAll('.crypto-pick-card').forEach(c => {
    c.classList.remove('border-primary', 'bg-primary', 'bg-opacity-10');
    c.classList.add('border');
    c.querySelector('.crypto-check')?.classList.add('d-none');
  });
  // Highlight selected
  card.classList.remove('border');
  card.classList.add('border-primary', 'bg-primary', 'bg-opacity-10');
  card.querySelector('.crypto-check')?.classList.remove('d-none');
  const cReqMsg = document.getElementById('crypto-required-msg');
  if (cReqMsg) cReqMsg.classList.add('d-none');
});

// Copy address buttons
document.addEventListener('click', function(e) {
  const btn = e.target.closest('.copy-addr-btn');
  if (!btn) return;
  const addr = btn.dataset.address;
  navigator.clipboard.writeText(addr).then(() => {
    const orig = btn.innerHTML;
    btn.innerHTML = '<i class="bi bi-check-lg text-success"></i>';
    btn.disabled = true;
    setTimeout(() => { btn.innerHTML = orig; btn.disabled = false; }, 1500);
  });
});

const CSRF = () => document.querySelector('meta[name=csrf-token]').content;
const BASE = '{{ rtrim(config("app.url"), "/") }}{{ config("app.subdir", "") }}';
const CREDITS_PER_USD = {{ (float) ($creditsPerUsd ?? 10) }};

document.addEventListener('DOMContentLoaded', function() {
  // ── USD equivalent display ──
  const fundAmountInput = document.getElementById('fund-amount');
  const fundUsdEquiv    = document.getElementById('fund-usd-equiv');
  if (fundAmountInput && fundUsdEquiv) {
    fundAmountInput.addEventListener('input', function() {
      const credits = parseFloat(this.value);
      if (credits > 0 && CREDITS_PER_USD > 0) {
        const usd = (credits / CREDITS_PER_USD).toFixed(2);
        fundUsdEquiv.innerHTML = `<i class="bi bi-info-circle text-primary me-1"></i>≈ <strong>$${usd} USD</strong> at ${CREDITS_PER_USD} credits per $1`;
      } else {
        fundUsdEquiv.textContent = '';
      }
    });
  }

  const withdrawAmountInput = document.getElementById('withdraw-amount');
  const withdrawUsdEquiv    = document.getElementById('withdraw-usd-equiv');
  if (withdrawAmountInput && withdrawUsdEquiv) {
    withdrawAmountInput.addEventListener('input', function() {
      const credits = parseFloat(this.value);
      if (credits > 0 && CREDITS_PER_USD > 0) {
        const usd = (credits / CREDITS_PER_USD).toFixed(2);
        withdrawUsdEquiv.innerHTML = `<i class="bi bi-info-circle text-success me-1"></i>You will receive ≈ <strong>$${usd} USD</strong> (at ${CREDITS_PER_USD} credits per $1)`;
      } else {
        withdrawUsdEquiv.textContent = '';
      }
    });
  }

  // ── Shared JSON response helper (handles non-JSON server errors) ──────
  async function safeJson(r) {
    const text = await r.text();
    try { return JSON.parse(text); } catch { return null; }
  }

  // ── Fund form ──
  document.getElementById('fundWalletForm').onsubmit = async function(e) {
    e.preventDefault();
    const btn     = document.getElementById('fund-submit-btn');
    const spinner = document.getElementById('fund-spinner');
    const alertEl = document.getElementById('fund-alert');
    btn.disabled = true;
    spinner.classList.remove('d-none');
    alertEl.className = 'd-none';

    // Client-side file size guard (4 MB max)
    const proofFile = document.getElementById('fund-proof').files[0];
    if (proofFile && proofFile.size > 4 * 1024 * 1024) {
      alertEl.className = 'alert alert-danger';
      alertEl.innerHTML = '<i class="bi bi-exclamation-circle me-1"></i>Screenshot is too large (max 4 MB). Please use a smaller image.';
      btn.disabled = false;
      spinner.classList.add('d-none');
      return;
    }

    const fd = new FormData(this);
    let r;
    try {
      r = await fetch('{{ route("wallet.fund") }}', {
        method:  'POST',
        headers: { 'X-CSRF-TOKEN': CSRF() },
        body:    fd
      });
    } catch (networkErr) {
      // True network failure (offline, DNS, etc.)
      alertEl.className = 'alert alert-danger';
      alertEl.innerHTML = '<i class="bi bi-wifi-off me-1"></i>No internet connection. Please check your network and try again.';
      btn.disabled = false;
      spinner.classList.add('d-none');
      return;
    }

    // Handle specific HTTP statuses before trying to parse JSON
    if (r.status === 419) {
      alertEl.className = 'alert alert-warning';
      alertEl.innerHTML = '<i class="bi bi-arrow-clockwise me-1"></i>Your session expired. <a href="" onclick="location.reload();return false;" class="alert-link fw-semibold">Refresh the page</a> and try again.';
      btn.disabled = false;
      spinner.classList.add('d-none');
      return;
    }
    if (r.status === 413) {
      alertEl.className = 'alert alert-danger';
      alertEl.innerHTML = '<i class="bi bi-exclamation-circle me-1"></i>Screenshot file is too large for the server. Please compress or resize the image.';
      btn.disabled = false;
      spinner.classList.add('d-none');
      return;
    }
    if (r.status === 422) {
      const data = await safeJson(r);
      const errors = data?.errors ? Object.values(data.errors).flat().join('<br>') : (data?.message || 'Validation failed. Please check your inputs.');
      alertEl.className = 'alert alert-danger';
      alertEl.innerHTML = '<i class="bi bi-exclamation-circle me-1"></i>' + errors;
      btn.disabled = false;
      spinner.classList.add('d-none');
      return;
    }

    const data = await safeJson(r);
    if (!data) {
      alertEl.className = 'alert alert-danger';
      alertEl.innerHTML = '<i class="bi bi-exclamation-circle me-1"></i>Unexpected server response (HTTP ' + r.status + '). Please try again or contact support.';
    } else if (data.success) {
      alertEl.className = 'alert alert-success';
      alertEl.innerHTML = '<i class="bi bi-check-circle me-1"></i>' + (data.message || 'Funding request submitted! Awaiting admin review.');
      this.reset();
      if (typeof fetchWallet === 'function') fetchWallet();
    } else {
      alertEl.className = 'alert alert-danger';
      alertEl.innerHTML = '<i class="bi bi-exclamation-circle me-1"></i>' + (data.error || 'Failed to submit request.');
    }
    btn.disabled = false;
    spinner.classList.add('d-none');
  };

  // ── Withdraw form ──
  document.getElementById('withdrawWalletForm').onsubmit = async function(e) {
    e.preventDefault();
    const btn = document.getElementById('withdraw-submit-btn');
    const spinner = document.getElementById('withdraw-spinner');
    const alertEl = document.getElementById('withdraw-alert');

    // Validate crypto selection if wallets are available
    const currency = document.getElementById('withdraw-currency').value;
    const hasCryptoSelector = document.getElementById('crypto-selector');
    if (hasCryptoSelector && !currency) {
      const cReqMsg = document.getElementById('crypto-required-msg');
      if (cReqMsg) cReqMsg.classList.remove('d-none');
      return;
    }

    btn.disabled = true;
    spinner.classList.remove('d-none');
    alertEl.className = 'd-none';

    let r;
    try {
      r = await fetch('{{ route("wallet.withdraw") }}', {
        method:  'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF() },
        body: JSON.stringify({
          amount:      document.getElementById('withdraw-amount').value,
          destination: document.getElementById('withdraw-destination').value,
          currency:    document.getElementById('withdraw-currency').value || null,
          network:     document.getElementById('withdraw-network').value  || null,
        })
      });
    } catch (networkErr) {
      alertEl.className = 'alert alert-danger';
      alertEl.innerHTML = '<i class="bi bi-wifi-off me-1"></i>No internet connection. Please check your network and try again.';
      btn.disabled = false;
      spinner.classList.add('d-none');
      return;
    }
    if (r.status === 419) {
      alertEl.className = 'alert alert-warning';
      alertEl.innerHTML = '<i class="bi bi-arrow-clockwise me-1"></i>Your session expired. <a href="" onclick="location.reload();return false;" class="alert-link fw-semibold">Refresh the page</a> and try again.';
      btn.disabled = false;
      spinner.classList.add('d-none');
      return;
    }
    const data = await safeJson(r);
    if (!data) {
      alertEl.className = 'alert alert-danger';
      alertEl.innerHTML = '<i class="bi bi-exclamation-circle me-1"></i>Unexpected server response. Please try again.';
    } else if (data.success) {
        alertEl.className = 'alert alert-success';
        alertEl.innerHTML = '<i class="bi bi-check-circle me-1"></i>' + (data.message || 'Withdrawal request submitted! Awaiting admin review.');
        this.reset();
        document.getElementById('withdraw-currency').value = '';
        document.getElementById('withdraw-network').value = '';
        document.querySelectorAll('.crypto-pick-card').forEach(c => {
          c.classList.remove('border-primary', 'bg-primary', 'bg-opacity-10');
          c.classList.add('border');
          c.querySelector('.crypto-check')?.classList.add('d-none');
        });
      if (typeof fetchWallet === 'function') fetchWallet();
    } else {
      alertEl.className = 'alert alert-danger';
      alertEl.innerHTML = '<i class="bi bi-exclamation-circle me-1"></i>' + (data.error || data.message || 'Failed to submit withdrawal.');
    }
    btn.disabled = false;
    spinner.classList.add('d-none');
  };

});
</script>
@endpush
