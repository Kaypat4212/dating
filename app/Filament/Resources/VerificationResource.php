<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VerificationResource\Pages;
use App\Models\UserVerification;
use App\Notifications\VerificationApprovedNotification;
use App\Notifications\VerificationRejectedNotification;
use Filament\Actions;
use Filament\Forms;
use Filament\Schemas\Components\Html;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;

class VerificationResource extends Resource
{
    protected static ?string $model = UserVerification::class;

    public static function getNavigationIcon(): ?string  { return 'heroicon-o-shield-check'; }
    public static function getNavigationGroup(): ?string { return 'Members'; }
    public static function getNavigationSort(): ?int     { return 3; }
    public static function getNavigationLabel(): string  { return 'Verifications'; }

    public static function getNavigationBadge(): ?string
    {
        return (string) UserVerification::where('status', 'pending')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): string
    {
        return 'warning';
    }

    /** Used by the list table row's "View" action modal. */
    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Submission')->schema([
                Forms\Components\TextInput::make('user.name')->label('User')->disabled(),
                Forms\Components\TextInput::make('status')->disabled(),
                Forms\Components\DateTimePicker::make('created_at')->label('Submitted')->disabled(),
            ])->columns(3),

            Section::make('Review')->schema([
                Forms\Components\Select::make('status')
                    ->options(['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected'])
                    ->required(),
                Forms\Components\Textarea::make('admin_notes')
                    ->label('Notes / Rejection Reason')
                    ->rows(3)
                    ->columnSpanFull(),
            ])->columns(2),
        ]);
    }

    /** Full view page â€” shows the actual uploaded documents. */
    public static function infolist(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Member Details')->schema([
                Html::make(function ($record) {
                    $u = $record->user;
                    $statusColors = ['pending' => '#f59e0b', 'approved' => '#10b981', 'rejected' => '#ef4444'];
                    $color = $statusColors[$record->status] ?? '#6b7280';
                    $photo = $u->primaryPhoto
                        ? '<img src="'.asset('storage/photos/'.($u->primaryPhoto->filename ?? $u->primaryPhoto->path)).'" style="width:56px;height:56px;border-radius:50%;object-fit:cover;border:2px solid #e5e7eb">'
                        : '<div style="width:56px;height:56px;border-radius:50%;background:#e5e7eb;display:flex;align-items:center;justify-content:center;font-size:1.5rem">&#x1F464;</div>';

                    return new HtmlString('
                        <div style="display:flex;align-items:center;gap:1rem;padding:.5rem 0">
                            '.$photo.'
                            <div>
                                <div style="font-size:1.1rem;font-weight:700">'.$u->name.'</div>
                                <div style="color:#6b7280;font-size:.875rem">'.$u->email.'</div>
                                <div style="margin-top:.25rem">
                                    <span style="background:'.$color.';color:#fff;padding:.15rem .7rem;border-radius:9999px;font-size:.75rem;font-weight:600;text-transform:uppercase">
                                        '.$record->status.'
                                    </span>
                                    <span style="color:#9ca3af;font-size:.8rem;margin-left:.5rem">Submitted '.$record->created_at->format('M j, Y g:ia').'</span>
                                </div>
                            </div>
                        </div>
                    ');
                }),
            ])->columnSpanFull(),

            Section::make('Submitted Documents')->schema([
                Html::make(function ($record) {
                    $selfieUrl = $record->selfie_path
                        ? route('admin.verify.doc', [$record->id, 'selfie'])
                        : null;
                    $idUrl = $record->id_document_path
                        ? route('admin.verify.doc', [$record->id, 'id'])
                        : null;
                    $isPdf = $record->id_document_path && str_ends_with(strtolower($record->id_document_path), '.pdf');

                    $selfieHtml = $selfieUrl
                        ? '<img src="'.$selfieUrl.'" style="width:100%;max-height:340px;object-fit:contain;border-radius:.5rem;border:1px solid #e5e7eb;background:#f9fafb" loading="lazy">'
                        : '<div style="height:200px;display:flex;align-items:center;justify-content:center;background:#f3f4f6;border-radius:.5rem;color:#9ca3af">No selfie uploaded</div>';

                    $idHtml = $idUrl
                        ? ($isPdf
                            ? '<a href="'.$idUrl.'" target="_blank" style="display:flex;align-items:center;gap:.5rem;padding:1rem;background:#f3f4f6;border-radius:.5rem;color:#2563eb;text-decoration:none;font-weight:600"><span style="font-size:1.5rem">&#x1F4C4;</span> Open PDF Document</a>'
                            : '<img src="'.$idUrl.'" style="width:100%;max-height:340px;object-fit:contain;border-radius:.5rem;border:1px solid #e5e7eb;background:#f9fafb" loading="lazy">')
                        : '<div style="height:200px;display:flex;align-items:center;justify-content:center;background:#f3f4f6;border-radius:.5rem;color:#9ca3af">No ID document uploaded</div>';

                    return new HtmlString('
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem">
                            <div>
                                <div style="font-weight:600;margin-bottom:.5rem;color:#374151">&#x1F4F8; Selfie</div>
                                '.$selfieHtml.'
                                '.($selfieUrl ? '<a href="'.$selfieUrl.'" target="_blank" style="display:inline-block;margin-top:.4rem;font-size:.8rem;color:#6b7280">Open in new tab &#x2197;</a>' : '').'
                            </div>
                            <div>
                                <div style="font-weight:600;margin-bottom:.5rem;color:#374151">&#x1FAA9; Government ID</div>
                                '.$idHtml.'
                                '.($idUrl && !$isPdf ? '<a href="'.$idUrl.'" target="_blank" style="display:inline-block;margin-top:.4rem;font-size:.8rem;color:#6b7280">Open in new tab &#x2197;</a>' : '').'
                            </div>
                        </div>
                    ');
                }),
            ])->columnSpanFull(),

            Section::make('Admin Notes')->schema([
                Html::make(function ($record) {
                    $notes = $record->admin_notes ? e($record->admin_notes) : '<em style="color:#9ca3af">None</em>';
                    $reviewer = $record->reviewer ? e($record->reviewer->name) : '&mdash;';
                    $reviewedAt = $record->reviewed_at ? $record->reviewed_at->format('M j, Y g:ia') : '&mdash;';

                    return new HtmlString('
                        <div style="font-size:.875rem;color:#374151">
                            <div><strong>Notes:</strong> '.$notes.'</div>
                            <div style="margin-top:.4rem;color:#6b7280">
                                Reviewed by <strong>'.$reviewer.'</strong> on '.$reviewedAt.'
                            </div>
                        </div>
                    ');
                }),
            ])->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'asc')
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default    => 'warning',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Submitted')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('reviewed_at')
                    ->label('Reviewed')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected']),
            ])
            ->recordActions([
                // Approve action
                Actions\Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (UserVerification $record) => $record->status !== 'approved')
                    ->action(function (UserVerification $record) {
                        $record->update([
                            'status'      => 'approved',
                            'reviewed_by' => Auth::id(),
                            'reviewed_at' => now(),
                        ]);
                        $record->user->update(['is_verified' => true]);
                        $record->user->notify(new VerificationApprovedNotification());
                    }),

                // Reject action â€” pop-up asks for a reason
                Actions\Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->schema([
                        Forms\Components\Textarea::make('reason')
                            ->label('Reason for rejection (shown to user)')
                            ->rows(3),
                    ])
                    ->visible(fn (UserVerification $record) => $record->status !== 'rejected')
                    ->action(function (UserVerification $record, array $data) {
                        $record->update([
                            'status'      => 'rejected',
                            'admin_notes' => $data['reason'] ?? null,
                            'reviewed_by' => Auth::id(),
                            'reviewed_at' => now(),
                        ]);
                        $record->user->update(['is_verified' => false]);
                        $record->user->notify(new VerificationRejectedNotification($data['reason'] ?? null));
                    }),

                Actions\ViewAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVerifications::route('/'),
            'view'  => Pages\ViewVerification::route('/{record}'),
        ];
    }
}