<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use App\Models\User;
use App\Models\WalletFundingRequest;
use App\Models\WalletTransaction;
use App\Models\WalletWithdrawalRequest;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

class FinanceDashboard extends Page
{
    protected string $view = 'filament.pages.finance-dashboard';

    public static function getNavigationIcon(): ?string  { return 'heroicon-o-banknotes'; }
    public static function getNavigationGroup(): ?string { return 'Wallet'; }
    public static function getNavigationSort(): ?int     { return 1; }
    public static function getNavigationLabel(): string  { return 'Finance Overview'; }
    public function getTitle(): string|Htmlable          { return 'Finance Dashboard'; }

    public function getStats(): array
    {
        $creditsPerUsd = max(1, (float) SiteSetting::get('credits_per_usd', 10));

        $pendingDeposits    = WalletFundingRequest::where('status', 'pending')->count();
        $pendingDepositAmt  = WalletFundingRequest::where('status', 'pending')->sum('amount');

        $pendingWithdrawals    = WalletWithdrawalRequest::where('status', 'pending')->count();
        $pendingWithdrawAmt    = WalletWithdrawalRequest::where('status', 'pending')->sum('amount');

        $approvedDepositsToday = WalletFundingRequest::where('status', 'approved')
            ->whereDate('updated_at', today())->sum('amount');
        $approvedDepositsMonth = WalletFundingRequest::where('status', 'approved')
            ->whereMonth('updated_at', now()->month)->sum('amount');

        $approvedWithdrawToday = WalletWithdrawalRequest::where('status', 'approved')
            ->whereDate('updated_at', today())->sum('amount');
        $approvedWithdrawMonth = WalletWithdrawalRequest::where('status', 'approved')
            ->whereMonth('updated_at', now()->month)->sum('amount');

        $totalCreditsIssued = WalletTransaction::whereIn('type', ['deposit', 'admin_credit'])->sum('amount');
        $totalCreditsSpent  = WalletTransaction::whereIn('type', ['tip_sent', 'withdrawal', 'admin_debit'])->sum('amount');
        $totalInCirculation = User::sum('credit_balance');

        $recentTransactions = WalletTransaction::with('user')
            ->latest()
            ->limit(10)
            ->get();

        $topBalances = User::where('credit_balance', '>', 0)
            ->orderByDesc('credit_balance')
            ->limit(5)
            ->get(['id', 'name', 'email', 'credit_balance']);

        return compact(
            'creditsPerUsd',
            'pendingDeposits', 'pendingDepositAmt',
            'pendingWithdrawals', 'pendingWithdrawAmt',
            'approvedDepositsToday', 'approvedDepositsMonth',
            'approvedWithdrawToday', 'approvedWithdrawMonth',
            'totalCreditsIssued', 'totalCreditsSpent', 'totalInCirculation',
            'recentTransactions', 'topBalances'
        );
    }
}
