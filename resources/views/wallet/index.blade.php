
@extends('layouts.app')
@section('title', 'My Wallet')
@section('content')
<div class="container py-5">

    {{-- ── Balance Banner ─────────────────────────────────────────────────── --}}
    <div class="row justify-content-center mb-4">
        <div class="col-12 col-md-8 col-lg-6">
            <div class="card border-0 shadow-sm text-center" style="background:linear-gradient(135deg,#6a11cb 0%,#2575fc 100%);">
                <div class="card-body py-4">
                    <i class="bi bi-wallet2 fs-1 text-white opacity-75 mb-2 d-block"></i>
                    <div class="text-white text-uppercase small fw-semibold opacity-75 mb-1">Wallet Balance</div>
                    <div id="wallet-balance" class="display-5 fw-bold text-white">—</div>
                    <div class="text-white opacity-50 small mt-1">credits</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Fund & Withdraw (centre section) ───────────────────────────────── --}}
    <div class="row justify-content-center g-3 mb-5">
        <div class="col-12 col-sm-5 col-md-4 col-lg-3">
            <div class="card border-0 shadow-sm h-100 text-center">
                <div class="card-body py-4">
                    <div class="mb-3">
                        <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary bg-opacity-10" style="width:56px;height:56px">
                            <i class="bi bi-plus-circle-fill text-primary fs-3"></i>
                        </span>
                    </div>
                    <h6 class="fw-bold mb-1">Fund Wallet</h6>
                    <p class="text-muted small mb-3">Add credits via crypto payment. Submit your TXID and proof for admin review.</p>
                    <button class="btn btn-primary w-100" onclick="showFundModal()">
                        <i class="bi bi-plus-lg me-1"></i>Add Funds
                    </button>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-5 col-md-4 col-lg-3">
            <div class="card border-0 shadow-sm h-100 text-center">
                <div class="card-body py-4">
                    <div class="mb-3">
                        <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-danger bg-opacity-10" style="width:56px;height:56px">
                            <i class="bi bi-arrow-up-circle-fill text-danger fs-3"></i>
                        </span>
                    </div>
                    <h6 class="fw-bold mb-1">Withdraw</h6>
                    <p class="text-muted small mb-3">Request a payout to your crypto wallet. Reviewed and processed by admin.</p>
                    <button class="btn btn-outline-danger w-100" onclick="showWithdrawModal()">
                        <i class="bi bi-arrow-up-right me-1"></i>Withdraw
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Tip / Transaction History ────────────────────────────────────────── --}}
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="fw-bold mb-3"><i class="bi bi-clock-history me-2 text-secondary"></i>Transaction History</h5>
                    <ul class="nav nav-tabs mb-3" id="tipTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="received-tab" data-bs-toggle="tab" data-bs-target="#received" type="button" role="tab">
                                <i class="bi bi-arrow-down-circle me-1 text-success"></i>Received Tips
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="sent-tab" data-bs-toggle="tab" data-bs-target="#sent" type="button" role="tab">
                                <i class="bi bi-arrow-up-circle me-1 text-danger"></i>Sent Tips
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="deposits-tab" data-bs-toggle="tab" data-bs-target="#deposits" type="button" role="tab">
                                <i class="bi bi-plus-circle me-1 text-primary"></i>Deposits
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="withdrawals-tab" data-bs-toggle="tab" data-bs-target="#withdrawals" type="button" role="tab">
                                <i class="bi bi-cash-stack me-1 text-warning"></i>Withdrawals
                            </button>
                        </li>
                    </ul>
                    <div class="tab-content" id="tipTabsContent">
                        <div class="tab-pane fade show active" id="received" role="tabpanel">
                            <div id="received-tips">
                                <div class="text-center py-4 text-muted"><div class="spinner-border spinner-border-sm me-2"></div>Loading…</div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="sent" role="tabpanel">
                            <div id="sent-tips">
                                <div class="text-center py-4 text-muted"><div class="spinner-border spinner-border-sm me-2"></div>Loading…</div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="deposits" role="tabpanel">
                            <div id="deposit-history">
                                <div class="text-center py-4 text-muted"><div class="spinner-border spinner-border-sm me-2"></div>Loading…</div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="withdrawals" role="tabpanel">
                            <div id="withdrawal-history">
                                <div class="text-center py-4 text-muted"><div class="spinner-border spinner-border-sm me-2"></div>Loading…</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

@include('wallet._modals')
@endsection

@push('scripts')
<script>
function tipRow(label, name, amount, color, sign, date, message) {
    return `<div class="d-flex align-items-start justify-content-between py-2 border-bottom">
        <div>
            <div class="fw-semibold">${label}: <span class="text-dark">${name}</span></div>
            ${message ? `<div class="text-muted small">${message}</div>` : ''}
            <div class="text-muted" style="font-size:.78rem">${date}</div>
        </div>
        <div class="fw-bold ${color} ms-3 text-nowrap">${sign}${amount}</div>
    </div>`;
}
function statusBadge(status) {
    const map = { approved: 'success', rejected: 'danger', pending: 'warning' };
    return `<span class="badge bg-${map[status] ?? 'secondary'} text-capitalize">${status}</span>`;
}
function requestRow(icon, title, amount, sign, color, date, status, note) {
    const isRejected = status === 'rejected';
    const noteHtml   = note
        ? `<div class="small mt-1 ${isRejected ? 'text-danger' : 'text-muted'}"><i class="bi bi-info-circle me-1"></i>${note}</div>`
        : '';
    return `<div class="d-flex align-items-start justify-content-between py-2 border-bottom">
        <div class="flex-grow-1">
            <div class="fw-semibold"><i class="bi ${icon} me-1"></i>${title}</div>
            ${noteHtml}
            <div class="mt-1 d-flex align-items-center gap-2">
                ${statusBadge(status)}
                <span class="text-muted" style="font-size:.78rem">${date}</span>
            </div>
        </div>
        <div class="fw-bold ${color} ms-3 text-nowrap">${sign}${amount} <small class="fw-normal text-muted">cr</small></div>
    </div>`;
}
function emptyRow(msg) {
    return `<div class="text-center text-muted py-4"><i class="bi bi-inbox fs-3 d-block mb-2 opacity-50"></i>${msg}</div>`;
}
function fetchWallet() {
    fetch('{{ route("wallet.balance") }}').then(r=>r.json()).then(data => {
        document.getElementById('wallet-balance').textContent = data.balance ?? '0';
    }).catch(()=>{ document.getElementById('wallet-balance').textContent = '0'; });

    fetch('{{ route("wallet.received") }}').then(r=>r.json()).then(data => {
        const html = data.tips && data.tips.length
            ? data.tips.map(t => tipRow('From', t.sender?.name||'Unknown', t.amount, 'text-success', '+', t.created_at, t.message)).join('')
            : emptyRow('No tips received yet.');
        document.getElementById('received-tips').innerHTML = html;
    }).catch(()=>{ document.getElementById('received-tips').innerHTML = emptyRow('Could not load.'); });

    fetch('{{ route("wallet.sent") }}').then(r=>r.json()).then(data => {
        const html = data.tips && data.tips.length
            ? data.tips.map(t => tipRow('To', t.recipient?.name||'Unknown', t.amount, 'text-danger', '-', t.created_at, t.message)).join('')
            : emptyRow('No tips sent yet.');
        document.getElementById('sent-tips').innerHTML = html;
    }).catch(()=>{ document.getElementById('sent-tips').innerHTML = emptyRow('Could not load.'); });

    fetch('{{ route("wallet.funding-history") }}').then(r=>r.json()).then(data => {
        const html = data.requests && data.requests.length
            ? data.requests.map(r => requestRow('bi-plus-circle', 'Deposit Request', r.amount, '+', 'text-primary', r.created_at, r.status, r.admin_note || null)).join('')
            : emptyRow('No deposit requests yet.');
        document.getElementById('deposit-history').innerHTML = html;
    }).catch(()=>{ document.getElementById('deposit-history').innerHTML = emptyRow('Could not load.'); });

    fetch('{{ route("wallet.withdrawal-history") }}').then(r=>r.json()).then(data => {
        const html = data.requests && data.requests.length
            ? data.requests.map(r => requestRow('bi-cash-stack', 'Withdrawal Request', r.amount, '-', 'text-warning', r.created_at, r.status, r.admin_note || null)).join('')
            : emptyRow('No withdrawals yet.');
        document.getElementById('withdrawal-history').innerHTML = html;
    }).catch(()=>{ document.getElementById('withdrawal-history').innerHTML = emptyRow('Could not load.'); });
}
document.addEventListener('DOMContentLoaded', fetchWallet);
</script>
@endpush
