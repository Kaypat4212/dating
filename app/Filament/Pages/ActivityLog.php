<?php

namespace App\Filament\Pages;

use App\Models\UserActivityLog;
use App\Models\User;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\WithPagination;

class ActivityLog extends Page
{
    use WithPagination;

    protected string $view = 'filament.pages.activity-log';

    public static function getNavigationIcon(): ?string  { return 'heroicon-o-clipboard-document-list'; }
    public static function getNavigationGroup(): ?string { return 'Moderation'; }
    public static function getNavigationSort(): ?int     { return 10; }
    public static function getNavigationLabel(): string  { return 'Activity Log'; }
    public function getTitle(): string|Htmlable          { return 'Platform Activity Log'; }

    // Livewire reactive properties (filters)
    public string $search        = '';
    public string $filterAction  = '';
    public string $filterFlag    = '';
    public int    $perPage       = 25;

    protected $queryString = [
        'search'       => ['except' => ''],
        'filterAction' => ['except' => ''],
        'filterFlag'   => ['except' => ''],
    ];

    /** Paginated activity log entries. */
    public function getActivities(): LengthAwarePaginator
    {
        return UserActivityLog::with('user')
            ->when($this->search, function ($q) {
                $term = '%' . $this->search . '%';
                $q->whereHas('user', fn ($u) =>
                    $u->where('name', 'like', $term)
                      ->orWhere('email', 'like', $term)
                );
            })
            ->when($this->filterAction, fn ($q) => $q->where('action', $this->filterAction))
            ->when($this->filterFlag,   fn ($q) => $q->where('flag',   $this->filterFlag))
            ->orderByDesc('created_at')
            ->paginate($this->perPage);
    }

    /** Today's quick stats. */
    public function getStats(): array
    {
        $since = now()->startOfDay();

        return [
            'total_today'    => UserActivityLog::where('created_at', '>=', $since)->count(),
            'suspicious'     => UserActivityLog::where('flag', 'suspicious')
                                    ->where('created_at', '>=', $since)->count(),
            'logins_today'   => UserActivityLog::where('action', 'login')
                                    ->where('created_at', '>=', $since)->count(),
            'messages_today' => UserActivityLog::where('action', 'message_sent')
                                    ->where('created_at', '>=', $since)->count(),
            'reports_today'  => UserActivityLog::where('action', 'report_sent')
                                    ->where('created_at', '>=', $since)->count(),
            'flagged_users'  => User::where('is_suspicious', true)->count(),
        ];
    }

    /** Distinct action types for the filter dropdown. */
    public function getActionTypes(): array
    {
        return UserActivityLog::select('action')
            ->distinct()
            ->orderBy('action')
            ->pluck('action')
            ->toArray();
    }

    /** Reset pagination when filters change. */
    public function updatedSearch(): void       { $this->resetPage(); }
    public function updatedFilterAction(): void  { $this->resetPage(); }
    public function updatedFilterFlag(): void    { $this->resetPage(); }
    public function updatedPerPage(): void       { $this->resetPage(); }
}
