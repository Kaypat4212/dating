<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FeatureRequestResource\Pages;
use App\Models\FeatureRequest;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\DeleteBulkAction;
use Illuminate\Support\Facades\Mail;

class FeatureRequestResource extends Resource
{
    protected static ?string $model = FeatureRequest::class;

    public static function getNavigationIcon(): ?string  { return 'heroicon-o-light-bulb'; }
    public static function getNavigationGroup(): ?string { return 'Moderation'; }
    public static function getNavigationLabel(): string  { return 'Feature Requests'; }
    public static function getNavigationSort(): ?int     { return 3; }

    public static function getNavigationBadge(): ?string
    {
        $count = static::getEloquentQuery()->where('status', 'open')->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'primary';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Submission')->schema([
                Forms\Components\TextInput::make('name')->disabled()->label('Name'),
                Forms\Components\TextInput::make('email')->disabled()->label('Email'),
                Forms\Components\Select::make('type')
                    ->options(['feature' => '💡 Feature Request', 'bug' => '🐛 Bug Report'])
                    ->disabled(),
                Forms\Components\TextInput::make('title')->disabled()->label('Title')->columnSpanFull(),
                Forms\Components\Textarea::make('body')->disabled()->label('Message')->rows(6)->columnSpanFull(),
            ])->columns(2),

            Section::make('Admin Response')->schema([
                Forms\Components\Select::make('status')
                    ->options([
                        'open'        => 'Open',
                        'in_progress' => 'In Progress',
                        'resolved'    => 'Resolved',
                        'declined'    => 'Declined',
                    ])
                    ->required(),
                Forms\Components\DateTimePicker::make('responded_at')->label('Responded At')->disabled(),
                Forms\Components\Textarea::make('admin_response')
                    ->label('Your Reply (sent to user by email when you save)')
                    ->rows(5)
                    ->columnSpanFull(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn (string $s) => $s === 'bug' ? '🐛 Bug' : '💡 Feature')
                    ->color(fn (string $s) => $s === 'bug' ? 'danger' : 'info'),
                Tables\Columns\TextColumn::make('title')->searchable()->limit(50),
                Tables\Columns\TextColumn::make('name')->searchable()->label('Submitted by'),
                Tables\Columns\TextColumn::make('email')->searchable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $s) => match ($s) {
                        'open'        => 'primary',
                        'in_progress' => 'warning',
                        'resolved'    => 'success',
                        'declined'    => 'gray',
                        default       => 'secondary',
                    }),
                Tables\Columns\TextColumn::make('created_at')->since()->sortable()->label('Received'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options([
                    'open'        => 'Open',
                    'in_progress' => 'In Progress',
                    'resolved'    => 'Resolved',
                    'declined'    => 'Declined',
                ]),
                Tables\Filters\SelectFilter::make('type')->options([
                    'feature' => 'Feature Request',
                    'bug'     => 'Bug Report',
                ]),
            ])
            ->recordActions([
                Actions\Action::make('reply')
                    ->label('Respond')
                    ->icon('heroicon-o-envelope')
                    ->color('primary')
                    ->modalHeading(fn (FeatureRequest $record) => 'Respond to: ' . $record->title)
                    ->modalWidth('lg')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'open'        => 'Open',
                                'in_progress' => 'In Progress',
                                'resolved'    => 'Resolved',
                                'declined'    => 'Declined',
                            ])
                            ->default(fn (FeatureRequest $record) => $record->status)
                            ->required(),
                        Forms\Components\Textarea::make('admin_response')
                            ->label('Reply (emailed to user)')
                            ->rows(5)
                            ->required()
                            ->default(fn (FeatureRequest $record) => $record->admin_response),
                        Forms\Components\Checkbox::make('send_email')
                            ->label('Send reply email to user')
                            ->default(true),
                    ])
                    ->action(function (FeatureRequest $record, array $data): void {
                        $record->update([
                            'status'         => $data['status'],
                            'admin_response' => $data['admin_response'],
                            'responded_at'   => now(),
                        ]);

                        if ($data['send_email'] ?? true) {
                            self::sendReplyEmail($record, $data['admin_response']);
                        }

                        Notification::make()
                            ->title('Response saved' . (($data['send_email'] ?? true) ? ' and email sent' : ''))
                            ->success()
                            ->send();
                    }),
                Actions\EditAction::make()->label('Edit'),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function afterSave(FeatureRequest $record, array $data): void
    {
        // Send email if admin_response changed
        if (!empty($data['admin_response']) && $data['admin_response'] !== $record->getOriginal('admin_response')) {
            $record->update(['responded_at' => now()]);
            self::sendReplyEmail($record, $data['admin_response']);
        }
    }

    public static function sendReplyEmail(FeatureRequest $record, string $response): void
    {
        $siteName = \App\Models\SiteSetting::get('site_name', config('app.name'));
        $type     = $record->type === 'bug' ? 'Bug Report' : 'Feature Request';

        try {
            Mail::html(
                view('emails.feature-request-reply', compact('record', 'response', 'siteName', 'type'))->render(),
                function ($message) use ($record, $siteName, $type) {
                    $message->to($record->email, $record->name)
                            ->subject("[{$siteName}] Re: {$type} — {$record->title}");
                }
            );
        } catch (\Exception $e) {
            // Log silently — don't crash the admin save
            logger()->error('Feature request reply email failed: ' . $e->getMessage());
        }
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFeatureRequests::route('/'),
            'edit'  => Pages\EditFeatureRequest::route('/{record}/edit'),
        ];
    }
}
