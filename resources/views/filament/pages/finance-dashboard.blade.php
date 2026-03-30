<x-filament-panels::page>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

@php $s = $this->getStats(); extract($s); @endphp

<style>
.fc-card {
    background: linear-gradient(145deg, rgba(15,10,30,0.97), rgba(25,15,50,0.95));
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 16px;
    padding: 1.4rem 1.6rem;
    transition: box-shadow 0.2s;
}
.fc-card:hover { box-shadow: 0 6px 24px rgba(0,0,0,0.35); }
.fc-card.danger  { border-color: rgba(239,68,68,0.35);  }
.fc-card.success { border-color: rgba(34,197,94,0.35);  }
.fc-card.info    { border-color: rgba(99,102,241,0.35); }
.fc-card.warning { border-color: rgba(234,179,8,0.35);  }
.stat-icon {
    width: 52px; height: 52px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.5rem;
}
.stat-value { font-size: 1.8rem; font-weight: 700; color: #f1f5f9; line-height: 1; }
.stat-label { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.06em; color: #94a3b8; }
.stat-sub   { font-size: 0.8rem; color: #64748b; margin-top: 0.25rem; }
.badge-pending { background: rgba(234,179,8,0.18); color: #fbbf24; border: 1px solid rgba(234,179,8,0.35); padding: .2rem .6rem; border-radius: 999px; font-size: .7rem; font-weight: 600; }
.section-title { font-size: .65rem; text-transform: uppercase; letter-spacing: .1em; color: #475569; margin-bottom: .75rem; font-weight: 700; }
.tx-row { border-bottom: 1px solid rgba(255,255,255,0.05); padding: .6rem 0; }
.tx-row:last-child { border-bottom: none; }
.tx-credit { color: #4ade80; }
.tx-debit  { color: #f87171; }
.btn-action {
    display: inline-flex; align-items: center; gap: .4rem;
    padding: .45rem 1rem; border-radius: 8px; font-size: .82rem; font-weight: 600;
    text-decoration: none; border: none; cursor: pointer; transition: opacity .2s;
}
.btn-action:hover { opacity: .85; }
.btn-approve { background: rgba(34,197,94,0.2); color: #4ade80; border: 1px solid rgba(34,197,94,0.4); }
.btn-withdraw { background: rgba(99,102,241,0.2); color: #a5b4fc; border: 1px solid rgba(99,102,241,0.4); }
.leader-row { display: flex; align-items: center; justify-content: space-between; padding: .5rem 0; border-bottom: 1px solid rgba(255,255,255,0.04); }
.leader-row:last-child { border-bottom: none; }
</style>

<div class="container-fluid px-1 py-2">

    {{-- Page Header --}}
    <div class="d-flex align-items-center gap-3 mb-4">
        <div style="width:48px;height:48px;border-radius:14px;background:linear-gradient(135deg,#6366f1,#a855f7);display:flex;align-items:center;justify-content:center;font-size:1.5rem;">💰</div>
        <div>
            <h2 class="text-white fw-bold mb-0" style="font-size:1.5rem;">Finance Dashboard</h2>
            <p class="mb-0" style="color:#64748b;font-size:.82rem;">All wallet activity at a glance</p>
        </div>
        <div class="ms-auto d-flex gap-2">
            <a href="{{ \Filament\Facades\Filament::getPanel()->route('resources.wallet-funding-requests.index') ?? url('/admin/wallet-funding-requests') }}" class="btn-action btn-approve">
                <i class="bi bi-arrow-down-circle"></i> Deposits
                @if($pendingDeposits > 0)
                    <span class="badge-pending">{{ $pendingDeposits }}</span>
                @endif
            </a>
            <a href="{{ url('/admin/wallet-withdrawal-requests') }}" class="btn-action btn-withdraw">
                <i class="bi bi-arrow-up-circle"></i> Withdrawals
                @if($pendingWithdrawals > 0)
                    <span class="badge-pending">{{ $pendingWithdrawals }}</span>
                @endif
            </a>
        </div>
    </div>

    {{-- Alert row --}}
    @if($pendingDeposits > 0 || $pendingWithdrawals > 0)
    <div class="mb-4 p-3 d-flex align-items-center gap-3" style="background:rgba(234,179,8,0.08);border:1px solid rgba(234,179,8,0.3);border-radius:12px;">
        <span style="font-size:1.4rem;">⚠️</span>
        <div>
            @if($pendingDeposits > 0)
                <span class="text-warning fw-semibold">{{ $pendingDeposits }} pending deposit{{ $pendingDeposits > 1 ? 's' : '' }}</span>
                ({{ number_format($pendingDepositAmt) }} credits)
                @if($pendingWithdrawals > 0) &nbsp;·&nbsp; @endif
            @endif
            @if($pendingWithdrawals > 0)
                <span class="text-warning fw-semibold">{{ $pendingWithdrawals }} pending withdrawal{{ $pendingWithdrawals > 1 ? 's' : '' }}</span>
                ({{ number_format($pendingWithdrawAmt) }} credits ≈ ${{ number_format($pendingWithdrawAmt / $creditsPerUsd, 2) }} USD)
            @endif
            <span style="color:#94a3b8;"> — action required</span>
        </div>
    </div>
    @endif

    {{-- Stats Row 1: Pending --}}
    <div class="row g-3 mb-3">
        <div class="col-6 col-md-3">
            <div class="fc-card warning">
                <div class="d-flex align-items-start gap-3">
                    <div class="stat-icon" style="background:rgba(234,179,8,0.15);">⏳</div>
                    <div>
                        <div class="stat-label">Pending Deposits</div>
                        <div class="stat-value text-warning">{{ $pendingDeposits }}</div>
                        <div class="stat-sub">{{ number_format($pendingDepositAmt) }} credits waiting</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="fc-card warning">
                <div class="d-flex align-items-start gap-3">
                    <div class="stat-icon" style="background:rgba(234,179,8,0.15);">🔄</div>
                    <div>
                        <div class="stat-label">Pending Withdrawals</div>
                        <div class="stat-value text-warning">{{ $pendingWithdrawals }}</div>
                        <div class="stat-sub">≈ ${{ number_format($pendingWithdrawAmt / $creditsPerUsd, 2) }} USD</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="fc-card success">
                <div class="d-flex align-items-start gap-3">
                    <div class="stat-icon" style="background:rgba(34,197,94,0.15);">✅</div>
                    <div>
                        <div class="stat-label">Deposits Approved Today</div>
                        <div class="stat-value text-success">{{ number_format($approvedDepositsToday) }}</div>
                        <div class="stat-sub">This month: {{ number_format($approvedDepositsMonth) }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="fc-card info">
                <div class="d-flex align-items-start gap-3">
                    <div class="stat-icon" style="background:rgba(99,102,241,0.15);">💸</div>
                    <div>
                        <div class="stat-label">Withdrawals Sent Today</div>
                        <div class="stat-value" style="color:#a5b4fc;">{{ number_format($approvedWithdrawToday) }}</div>
                        <div class="stat-sub">This month: {{ number_format($approvedWithdrawMonth) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Stats Row 2: Totals --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-4">
            <div class="fc-card success">
                <div class="stat-label">Total Credits Issued (All Time)</div>
                <div class="stat-value text-success mt-1">{{ number_format($totalCreditsIssued) }}</div>
                <div class="stat-sub">Deposits + admin credits</div>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="fc-card danger">
                <div class="stat-label">Total Credits Spent (All Time)</div>
                <div class="stat-value text-danger mt-1">{{ number_format($totalCreditsSpent) }}</div>
                <div class="stat-sub">Gifts + withdrawals + admin debit</div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="fc-card info">
                <div class="stat-label">Total Credits in Circulation</div>
                <div class="stat-value mt-1" style="color:#818cf8;">{{ number_format($totalInCirculation) }}</div>
                <div class="stat-sub">Sum of all user balances · ≈ ${{ number_format($totalInCirculation / $creditsPerUsd, 2) }} USD</div>
            </div>
        </div>
    </div>

    {{-- Bottom Row: Recent Transactions + Top Balances --}}
    <div class="row g-3">
        <div class="col-lg-8">
            <div class="fc-card" style="height:100%;">
                <div class="section-title"><i class="bi bi-clock-history me-1"></i> Recent Transactions</div>
                @forelse($recentTransactions as $tx)
                <div class="tx-row d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <span style="font-size:1.1rem;">{{ match($tx->type) {
                            'deposit'      => '💳',
                            'withdrawal'   => '🏧',
                            'tip_sent'     => '💸',
                            'tip_received' => '🎁',
                            'admin_credit' => '⬆️',
                            'admin_debit'  => '⬇️',
                            default        => '🔄',
                        } }}</span>
                        <div>
                            <div class="text-white" style="font-size:.85rem;font-weight:600;">{{ $tx->user?->name ?? 'Deleted' }}</div>
                            <div style="color:#64748b;font-size:.75rem;">{{ Str::limit($tx->description ?? $tx->type, 50) }}</div>
                        </div>
                    </div>
                    <div class="text-end">
                        <div class="fw-bold {{ $tx->isCredit() ? 'tx-credit' : 'tx-debit' }}" style="font-size:.9rem;">
                            {{ $tx->isCredit() ? '+' : '-' }}{{ number_format($tx->amount) }}
                        </div>
                        <div style="color:#475569;font-size:.7rem;">{{ $tx->created_at->diffForHumans() }}</div>
                    </div>
                </div>
                @empty
                <p style="color:#475569;" class="mb-0 text-center py-3">No transactions yet.</p>
                @endforelse
                <div class="mt-3">
                    <a href="{{ url('/admin/wallet-transactions') }}" style="color:#818cf8;font-size:.78rem;text-decoration:none;">
                        View all transactions &rarr;
                    </a>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="fc-card" style="height:100%;">
                <div class="section-title"><i class="bi bi-trophy me-1"></i> Top Credit Balances</div>
                @forelse($topBalances as $u)
                <div class="leader-row">
                    <div>
                        <div class="text-white" style="font-size:.85rem;font-weight:600;">{{ $u->name }}</div>
                        <div style="color:#475569;font-size:.73rem;">{{ $u->email }}</div>
                    </div>
                    <div class="text-success fw-bold" style="font-size:.9rem;">
                        {{ number_format($u->credit_balance) }}
                    </div>
                </div>
                @empty
                <p style="color:#475569;" class="mb-0 text-center py-3">No balances yet.</p>
                @endforelse
                <div class="mt-3">
                    <a href="{{ url('/admin/users?tableFilters[credit_balance][value]=1') }}" style="color:#818cf8;font-size:.78rem;text-decoration:none;">
                        View all users &rarr;
                    </a>
                </div>
            </div>
        </div>
    </div>

</div>
</x-filament-panels::page>
