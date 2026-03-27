

<?php $__env->startSection('title', 'My Wallet'); ?>
<?php $__env->startSection('content'); ?>
<div class="container py-4" style="max-width:860px">

    
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm text-center h-100" style="background:linear-gradient(135deg,#6a11cb 0%,#2575fc 100%);">
                <div class="card-body py-4">
                    <i class="bi bi-wallet2 fs-2 text-white opacity-75 mb-2 d-block"></i>
                    <div class="text-white text-uppercase" style="font-size:.7rem;font-weight:700;opacity:.75;letter-spacing:.08em">Available Balance</div>
                    <div id="wallet-balance" class="fw-bold text-white mt-1" style="font-size:2.4rem;line-height:1.1">—</div>
                    <div class="text-white opacity-50 small mt-1">credits</div>
                    <div class="mt-3 d-flex gap-2 justify-content-center">
                        <button class="btn btn-light btn-sm fw-semibold px-3" onclick="showFundModal()">
                            <i class="bi bi-plus-lg me-1"></i>Add
                        </button>
                        <button class="btn btn-outline-light btn-sm fw-semibold px-3" onclick="showWithdrawModal()">
                            <i class="bi bi-arrow-up-right me-1"></i>Withdraw
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center py-4">
                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width:48px;height:48px;background:rgba(34,197,94,.12)">
                        <i class="bi bi-arrow-down-circle-fill text-success fs-4"></i>
                    </div>
                    <div class="text-muted small">Tips Received</div>
                    <div id="stat-received" class="fw-bold fs-4 text-success">—</div>
                    <div class="text-muted" style="font-size:.7rem">credits total</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center py-4">
                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width:48px;height:48px;background:rgba(239,68,68,.1)">
                        <i class="bi bi-arrow-up-circle-fill text-danger fs-4"></i>
                    </div>
                    <div class="text-muted small">Tips Sent</div>
                    <div id="stat-sent" class="fw-bold fs-4 text-danger">—</div>
                    <div class="text-muted" style="font-size:.7rem">credits total</div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="fw-bold mb-3"><i class="bi bi-clock-history me-2 text-secondary"></i>Transaction History</h5>
                    <ul class="nav nav-tabs mb-3" id="tipTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all-activity" type="button" role="tab">
                                <i class="bi bi-list-ul me-1 text-secondary"></i>All Activity
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="received-tab" data-bs-toggle="tab" data-bs-target="#received" type="button" role="tab">
                                <i class="bi bi-arrow-down-circle me-1 text-success"></i>Received
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="sent-tab" data-bs-toggle="tab" data-bs-target="#sent" type="button" role="tab">
                                <i class="bi bi-arrow-up-circle me-1 text-danger"></i>Sent
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
                        <div class="tab-pane fade show active" id="all-activity" role="tabpanel">
                            <div id="all-activity-list">
                                <div class="text-center py-4 text-muted"><div class="spinner-border spinner-border-sm me-2"></div>Loading…</div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="received" role="tabpanel">
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

<?php echo $__env->make('wallet._modals', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function fmtDate(iso) {
    if (!iso) return '—';
    const d = new Date(iso);
    return d.toLocaleDateString(undefined, { month:'short', day:'numeric', year:'numeric' })
        + ' · ' + d.toLocaleTimeString(undefined, { hour:'2-digit', minute:'2-digit' });
}
function tipRow(label, name, amount, color, sign, iso, message) {
    const isCredit = sign === '+';
    const borderColor = isCredit ? '#22c55e' : '#ef4444';
    const bgColor     = isCredit ? 'rgba(34,197,94,.04)' : 'rgba(239,68,68,.04)';
    const badgeClass  = isCredit ? 'success' : 'danger';
    const icon        = isCredit ? 'bi-arrow-down-circle-fill' : 'bi-arrow-up-circle-fill';
    return `<div class="d-flex align-items-center gap-3 py-2 px-3 mb-2 rounded-3"
                 style="border-left:3px solid ${borderColor};background:${bgColor};">
        <i class="bi ${icon} ${color} fs-5 flex-shrink-0"></i>
        <div class="flex-grow-1 min-w-0">
            <div class="fw-semibold text-truncate">${label}: <span>${name}</span></div>
            ${message ? `<div class="text-muted small fst-italic text-truncate">"${message}"</div>` : ''}
            <div class="text-muted" style="font-size:.75rem">${fmtDate(iso)}</div>
        </div>
        <div class="fw-bold ${color} ms-2 text-nowrap fs-6">${sign}${amount} <small class="fw-normal text-muted">cr</small></div>
    </div>`;
}
function statusBadge(status) {
    const map = { approved: 'success', rejected: 'danger', pending: 'warning' };
    return `<span class="badge bg-${map[status] ?? 'secondary'} text-capitalize">${status}</span>`;
}
function requestRow(iconClass, title, amount, sign, color, iso, status, note) {
    const isCredit    = sign === '+';
    const borderColor = isCredit ? '#3b82f6' : '#f59e0b';
    const bgColor     = isCredit ? 'rgba(59,130,246,.04)' : 'rgba(245,158,11,.04)';
    const isRejected  = status === 'rejected';
    const noteHtml    = note
        ? `<div class="small mt-1 ${isRejected ? 'text-danger' : 'text-muted'}"><i class="bi bi-info-circle me-1"></i>${note}</div>`
        : '';
    return `<div class="d-flex align-items-center gap-3 py-2 px-3 mb-2 rounded-3"
                 style="border-left:3px solid ${borderColor};background:${bgColor};">
        <i class="bi ${iconClass} ${color} fs-5 flex-shrink-0"></i>
        <div class="flex-grow-1 min-w-0">
            <div class="fw-semibold">${title}</div>
            ${noteHtml}
            <div class="mt-1 d-flex align-items-center gap-2 flex-wrap">
                ${statusBadge(status)}
                <span class="text-muted" style="font-size:.75rem">${fmtDate(iso)}</span>
            </div>
        </div>
        <div class="fw-bold ${color} ms-2 text-nowrap fs-6">${sign}${amount} <small class="fw-normal text-muted">cr</small></div>
    </div>`;
}
function emptyRow(msg) {
    return `<div class="text-center text-muted py-5"><i class="bi bi-inbox fs-2 d-block mb-2 opacity-40"></i><span class="small">${msg}</span></div>`;
}

async function fetchWallet() {
    // Balance + stats
    fetch('<?php echo e(route("wallet.balance")); ?>').then(r=>r.json()).then(data => {
        document.getElementById('wallet-balance').textContent   = data.balance        ?? '0';
        document.getElementById('stat-received').textContent    = data.received_total ?? '0';
        document.getElementById('stat-sent').textContent        = data.sent_total     ?? '0';
    }).catch(()=>{ document.getElementById('wallet-balance').textContent = '0'; });

    // Collect all data for the unified "All Activity" tab
    let allRows = [];

    // Received tips
    const rcvResp = await fetch('<?php echo e(route("wallet.received")); ?>').then(r=>r.json()).catch(()=>({tips:[]}));
    const receivedRows = (rcvResp.tips || []).map(t =>
        tipRow('Gift from', t.sender?.name||'Unknown', t.amount, 'text-success', '+', t.created_at, t.message)
    );
    document.getElementById('received-tips').innerHTML = receivedRows.length
        ? receivedRows.join('') : emptyRow('No gifts received yet.');
    allRows.push(...(rcvResp.tips || []).map(t => ({ html: tipRow('Gift from', t.sender?.name||'Unknown', t.amount, 'text-success', '+', t.created_at, t.message), ts: new Date(t.created_at) })));

    // Sent tips
    const sentResp = await fetch('<?php echo e(route("wallet.sent")); ?>').then(r=>r.json()).catch(()=>({tips:[]}));
    const sentRows = (sentResp.tips || []).map(t =>
        tipRow('Gift to', t.recipient?.name||'Unknown', t.amount, 'text-danger', '-', t.created_at, t.message)
    );
    document.getElementById('sent-tips').innerHTML = sentRows.length
        ? sentRows.join('') : emptyRow('No gifts sent yet.');
    allRows.push(...(sentResp.tips || []).map(t => ({ html: tipRow('Gift to', t.recipient?.name||'Unknown', t.amount, 'text-danger', '-', t.created_at, t.message), ts: new Date(t.created_at) })));

    // Deposits
    const depResp = await fetch('<?php echo e(route("wallet.funding-history")); ?>').then(r=>r.json()).catch(()=>({requests:[]}));
    const depRows = (depResp.requests || []).map(r =>
        requestRow('bi-plus-circle', 'Deposit Request', r.amount, '+', 'text-primary', r.created_at, r.status, r.admin_note || null)
    );
    document.getElementById('deposit-history').innerHTML = depRows.length
        ? depRows.join('') : emptyRow('No deposit requests yet.');
    allRows.push(...(depResp.requests || []).map(r => ({ html: requestRow('bi-plus-circle', 'Deposit Request', r.amount, '+', 'text-primary', r.created_at, r.status, r.admin_note || null), ts: new Date(r.created_at) })));

    // Withdrawals
    const wdrResp = await fetch('<?php echo e(route("wallet.withdrawal-history")); ?>').then(r=>r.json()).catch(()=>({requests:[]}));
    const wdrRows = (wdrResp.requests || []).map(r =>
        requestRow('bi-cash-stack', 'Withdrawal Request', r.amount, '-', 'text-warning', r.created_at, r.status, r.admin_note || null)
    );
    document.getElementById('withdrawal-history').innerHTML = wdrRows.length
        ? wdrRows.join('') : emptyRow('No withdrawals yet.');
    allRows.push(...(wdrResp.requests || []).map(r => ({ html: requestRow('bi-cash-stack', 'Withdrawal Request', r.amount, '-', 'text-warning', r.created_at, r.status, r.admin_note || null), ts: new Date(r.created_at) })));

    // Render unified activity feed (newest first)
    allRows.sort((a, b) => b.ts - a.ts);
    document.getElementById('all-activity-list').innerHTML = allRows.length
        ? allRows.map(r => r.html).join('')
        : emptyRow('No transactions yet. Fund your wallet to get started!');
}
document.addEventListener('DOMContentLoaded', fetchWallet);
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\dating\resources\views\wallet\index.blade.php ENDPATH**/ ?>