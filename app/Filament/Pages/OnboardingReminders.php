<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use App\Models\User;
use App\Notifications\ProfileReminderNotification;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class OnboardingReminders extends Page
{
    protected string $view = 'filament.pages.onboarding-reminders';

    public static function getNavigationIcon(): ?string  { return 'heroicon-o-user-plus'; }
    public static function getNavigationLabel(): string  { return 'Onboarding Reminders'; }
    public static function getNavigationGroup(): ?string { return 'Users'; }
    public static function getNavigationSort(): ?int     { return 10; }

    public function getTitle(): string | Htmlable { return 'Onboarding Reminders'; }

    public static function canAccess(): bool
    {
        return Auth::check() && (Auth::id() === 1 || Auth::user()->hasRole('admin'));
    }

    // ── Settings form state ────────────────────────────────────────────────

    public array $data = [];

    // ── Table state ────────────────────────────────────────────────────────

    public string  $search    = '';
    public string  $filterBy  = 'all';   // all | no_photo | no_bio | no_location | no_interests
    public string  $sortField = 'created_at';
    public string  $sortDir   = 'desc';
    public int     $perPage   = 20;

    // ── Lifecycle ──────────────────────────────────────────────────────────

    public function mount(): void
    {
        $this->form->fill([
            'onboarding_reminder_enabled'        => filter_var(SiteSetting::get('onboarding_reminder_enabled', '1'),       FILTER_VALIDATE_BOOLEAN),
            'onboarding_reminder_min_hours'      => (int) SiteSetting::get('onboarding_reminder_min_hours',  24),
            'onboarding_reminder_interval_hours' => (int) SiteSetting::get('onboarding_reminder_interval_hours', 48),
            'onboarding_reminder_max_count'      => (int) SiteSetting::get('onboarding_reminder_max_count',  3),
            'onboarding_reminder_message'        => SiteSetting::get('onboarding_reminder_message', ''),
        ]);
    }

    // ── Filament form schema ───────────────────────────────────────────────

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('Reminder Settings')
                    ->description('Control when and how reminders are sent to users who have not completed their profile.')
                    ->schema([
                        Toggle::make('onboarding_reminder_enabled')
                            ->label('Enable automatic reminders')
                            ->helperText('When enabled, users who haven\'t completed their profile receive reminder emails.')
                            ->columnSpanFull(),

                        TextInput::make('onboarding_reminder_min_hours')
                            ->label('Hours after registration before first reminder')
                            ->numeric()->minValue(1)->maxValue(720)
                            ->suffix('hours')
                            ->helperText('Wait this long after sign-up before sending the first reminder.'),

                        TextInput::make('onboarding_reminder_interval_hours')
                            ->label('Hours between subsequent reminders')
                            ->numeric()->minValue(1)->maxValue(720)
                            ->suffix('hours'),

                        TextInput::make('onboarding_reminder_max_count')
                            ->label('Maximum reminders per user')
                            ->numeric()->minValue(1)->maxValue(10)
                            ->suffix('emails')
                            ->helperText('Stop reminding a user after this many emails.'),

                        Textarea::make('onboarding_reminder_message')
                            ->label('Custom message (optional)')
                            ->rows(3)
                            ->helperText('Appended to every reminder email. Leave blank for default message.')
                            ->placeholder('e.g. If you need any help, reply to this email and we\'ll assist you personally.')
                            ->columnSpanFull(),
                    ])
                    ->columns(3),
            ]);
    }

    // ── Actions ───────────────────────────────────────────────────────────

    protected function getHeaderActions(): array
    {
        return [
            Action::make('saveSettings')
                ->label('Save Settings')
                ->icon('heroicon-o-check')
                ->color('primary')
                ->action('save'),

            Action::make('sendAllEligible')
                ->label('Send All Eligible Reminders')
                ->icon('heroicon-o-paper-airplane')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Send Reminders to All Eligible Users')
                ->modalDescription('This will send a reminder email to every incomplete user who hasn\'t exceeded the limit and has waited long enough. Continue?')
                ->action('sendBulkReminders'),
        ];
    }

    // ── Save settings ──────────────────────────────────────────────────────

    public function save(): void
    {
        $state = $this->form->getState();

        SiteSetting::set('onboarding_reminder_enabled',        $state['onboarding_reminder_enabled'] ? '1' : '0');
        SiteSetting::set('onboarding_reminder_min_hours',      (string) $state['onboarding_reminder_min_hours']);
        SiteSetting::set('onboarding_reminder_interval_hours', (string) $state['onboarding_reminder_interval_hours']);
        SiteSetting::set('onboarding_reminder_max_count',      (string) $state['onboarding_reminder_max_count']);
        SiteSetting::set('onboarding_reminder_message',        $state['onboarding_reminder_message'] ?? '');

        Notification::make()
            ->title('Settings saved')
            ->success()
            ->send();
    }

    // ── Send individual reminder ───────────────────────────────────────────

    public function sendReminder(int $userId): void
    {
        $user = User::with(['profile', 'photos'])->find($userId);

        if (!$user || $user->profile_complete) {
            Notification::make()->title('User not found or already complete')->warning()->send();
            return;
        }

        $missing = $this->getMissingItems($user);
        if (empty($missing)) {
            Notification::make()->title('Nothing missing for this user')->warning()->send();
            return;
        }

        $adminMessage = SiteSetting::get('onboarding_reminder_message', '');

        try {
            $user->notify(new ProfileReminderNotification(
                missingItems:   $missing,
                onboardingStep: (int) $user->onboarding_step,
                adminMessage:   $adminMessage,
            ));

            $user->increment('reminder_count');
            $user->update(['last_reminder_at' => now()]);

            Notification::make()
                ->title("Reminder sent to {$user->name}")
                ->success()
                ->send();
        } catch (\Throwable $e) {
            Notification::make()
                ->title('Failed to send reminder')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    // ── Bulk send ──────────────────────────────────────────────────────────

    public function sendBulkReminders(): void
    {
        $eligible = $this->getEligibleUsers();
        $count    = 0;
        $adminMsg = SiteSetting::get('onboarding_reminder_message', '');

        foreach ($eligible as $user) {
            $missing = $this->getMissingItems($user);
            if (empty($missing)) continue;

            try {
                $user->notify(new ProfileReminderNotification(
                    missingItems:   $missing,
                    onboardingStep: (int) $user->onboarding_step,
                    adminMessage:   $adminMsg,
                ));
                $user->increment('reminder_count');
                $user->update(['last_reminder_at' => now()]);
                $count++;
            } catch (\Throwable) {
                // skip failures silently — keep sending to others
            }
        }

        Notification::make()
            ->title("Sent {$count} reminder" . ($count !== 1 ? 's' : ''))
            ->success()
            ->send();
    }

    // ── Stats (used in blade) ──────────────────────────────────────────────

    public function getStats(): array
    {
        try {
            $incomplete = User::where('profile_complete', false)
                ->where('is_banned', false)
                ->whereNot('id', 1)
                ->get();

            $total        = $incomplete->count();
            $eligible     = $this->getEligibleUsers()->count();
            $remindedToday = User::where('profile_complete', false)
                ->whereDate('last_reminder_at', today())
                ->count();
            $neverStarted = $incomplete->filter(fn($u) => (int)$u->onboarding_step === 0)->count();

            return [
                'total'          => $total,
                'eligible'       => $eligible,
                'reminded_today' => $remindedToday,
                'never_started'  => $neverStarted,
            ];
        } catch (\Throwable) {
            return ['total' => 0, 'eligible' => 0, 'reminded_today' => 0, 'never_started' => 0];
        }
    }

    // ── Paginated table data (used in blade) ──────────────────────────────

    public function getIncompleteUsers(): LengthAwarePaginator
    {
        $query = User::with(['profile', 'photos'])
            ->where('profile_complete', false)
            ->where('is_banned', false)
            ->whereNot('id', 1);

        if ($this->search) {
            $search = '%' . $this->search . '%';
            $query->where(fn($q) => $q->where('name', 'like', $search)->orWhere('email', 'like', $search));
        }

        switch ($this->filterBy) {
            case 'no_photo':
                $query->whereDoesntHave('photos', fn($q) => $q->where('is_approved', true));
                break;
            case 'no_bio':
                $query->where(fn($q) => $q
                    ->doesntHave('profile')
                    ->orWhereHas('profile', fn($p) => $p->whereNull('bio')->orWhere('bio', ''))
                );
                break;
            case 'no_location':
                $query->where(fn($q) => $q
                    ->doesntHave('profile')
                    ->orWhereHas('profile', fn($p) => $p->whereNull('city')->whereNull('latitude'))
                );
                break;
            case 'eligible':
                try {
                    $minH  = (int) SiteSetting::get('onboarding_reminder_min_hours', 24);
                    $maxC  = (int) SiteSetting::get('onboarding_reminder_max_count', 3);
                    $intH  = (int) SiteSetting::get('onboarding_reminder_interval_hours', 48);
                    $query->where('created_at', '<=', now()->subHours($minH))
                          ->where('reminder_count', '<', $maxC)
                          ->where(fn($q) => $q
                              ->whereNull('last_reminder_at')
                              ->orWhere('last_reminder_at', '<=', now()->subHours($intH))
                          );
                } catch (\Throwable) {
                    // reminder columns don't exist yet — skip filter
                }
                break;
        }

        $allowed = ['name', 'email', 'created_at', 'last_active_at', 'onboarding_step'];
        $sortField = in_array($this->sortField, $allowed) ? $this->sortField : 'created_at';
        $query->orderBy($sortField, $this->sortDir);

        try {
            return $query->paginate($this->perPage);
        } catch (\Throwable) {
            return new \Illuminate\Pagination\LengthAwarePaginator([], 0, $this->perPage);
        }
    }

    // ── Eligible users query (for bulk send) ──────────────────────────────

    protected function getEligibleUsers()
    {
        try {
            $minH = (int) SiteSetting::get('onboarding_reminder_min_hours', 24);
            $maxC = (int) SiteSetting::get('onboarding_reminder_max_count', 3);
            $intH = (int) SiteSetting::get('onboarding_reminder_interval_hours', 48);

            return User::with(['profile', 'photos'])
                ->where('profile_complete', false)
                ->where('is_banned', false)
                ->whereNot('id', 1)
                ->where('created_at', '<=', now()->subHours($minH))
                ->where('reminder_count', '<', $maxC)
                ->where(fn($q) => $q
                    ->whereNull('last_reminder_at')
                    ->orWhere('last_reminder_at', '<=', now()->subHours($intH))
                )
                ->get();
        } catch (\Throwable) {
            return collect();
        }
    }

    // ── Sort toggle (called from blade) ───────────────────────────────────

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDir   = 'desc';
        }
    }

    // ── Profile completeness helper ───────────────────────────────────────

    public static function getMissingItems(User $user): array
    {
        $missing = [];
        $profile = $user->relationLoaded('profile') ? $user->profile : $user->load('profile')->profile;

        // Step 1: Basic identity
        if (!$user->gender)         $missing[] = 'Gender';
        if (!$user->seeking)        $missing[] = 'Looking for';
        if (!$user->date_of_birth)  $missing[] = 'Date of birth';

        // Step 2: About me
        if (!$profile || (empty($profile->bio) && empty($profile->headline))) {
            $missing[] = 'Bio / Headline';
        }

        // Step 3: Photos
        $hasPhoto = $user->relationLoaded('photos')
            ? $user->photos->where('is_approved', true)->isNotEmpty()
            : $user->photos()->where('is_approved', true)->exists();

        if (!$hasPhoto) $missing[] = 'Profile photo';

        // Step 4: Location
        if (!$profile?->city && !$profile?->latitude) {
            $missing[] = 'Location';
        }

        // Step 5: Interests
        try {
            if (!$profile || $profile->interests()->doesntExist()) {
                $missing[] = 'Interests';
            }
        } catch (\Throwable) {
            // pivot table may not exist yet
        }

        return $missing;
    }
}
