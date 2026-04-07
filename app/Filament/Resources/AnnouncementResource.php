<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AnnouncementResource\Pages;
use App\Models\Announcement;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Group;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\DeleteBulkAction;
use Filament\Notifications\Notification;

class AnnouncementResource extends Resource
{
    protected static ?string $model = Announcement::class;

    public static function getNavigationIcon(): ?string  { return 'heroicon-o-megaphone'; }
    public static function getNavigationGroup(): ?string { return 'Community'; }
    public static function getNavigationLabel(): string  { return 'Announcements'; }
    public static function getNavigationSort(): ?int     { return 2; }

    public static function getNavigationBadge(): ?string
    {
        try {
            $count = static::getEloquentQuery()->where('is_published', false)->count();
            return $count > 0 ? (string) $count : null;
        } catch (\Throwable) {
            return null;
        }
    }

    public static function getNavigationBadgeColor(): ?string   { return 'warning'; }
    public static function getNavigationBadgeTooltip(): ?string { return 'Unpublished drafts'; }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Group::make([
                Section::make('Announcement Content')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(140)
                            ->placeholder('e.g. "Speed Dating is here!"')
                            ->columnSpanFull(),

                        Forms\Components\RichEditor::make('body')
                            ->required()
                            ->toolbarButtons([
                                'bold', 'italic', 'underline', 'strike',
                                'bulletList', 'orderedList',
                                'h2', 'h3', 'blockquote',
                                'link', 'redo', 'undo',
                            ])
                            ->columnSpanFull()
                            ->helperText('Supports rich text formatting. Keep it concise for the best user experience.'),
                    ]),
            ])->columnSpan(2),

            Group::make([
                Section::make('Metadata')
                    ->icon('heroicon-o-tag')
                    ->schema([
                        Forms\Components\Select::make('type')
                            ->options([
                                'feature'     => '✨ New Feature',
                                'update'      => '🔄 Update',
                                'maintenance' => '🔧 Maintenance',
                                'message'     => '💬 Message',
                                'promo'       => '🎁 Promotion',
                            ])
                            ->required()
                            ->default('feature'),

                        Forms\Components\TextInput::make('version')
                            ->placeholder('e.g. v2.4')
                            ->maxLength(20)
                            ->helperText('Optional version tag shown on the card.'),

                        Forms\Components\TextInput::make('badge_label')
                            ->placeholder('e.g. NEW, HOT, FIXED')
                            ->maxLength(40)
                            ->helperText('Short badge text on the announcement card.'),

                        Forms\Components\Select::make('badge_color')
                            ->options([
                                'primary'   => 'Blue (Primary)',
                                'success'   => 'Green (Success)',
                                'warning'   => 'Yellow (Warning)',
                                'danger'    => 'Red (Danger)',
                                'info'      => 'Teal (Info)',
                                'secondary' => 'Grey (Secondary)',
                            ])
                            ->default('primary'),
                    ]),

                Section::make('Targeting & Visibility')
                    ->icon('heroicon-o-eye')
                    ->schema([
                        Forms\Components\Toggle::make('is_published')
                            ->label('Published')
                            ->helperText('Only published announcements are visible to users.')
                            ->default(false),

                        Forms\Components\Toggle::make('show_popup')
                            ->label('Auto-open modal')
                            ->helperText('Automatically opens the "What\'s New" popup on users\' next visit.')
                            ->default(true),

                        Forms\Components\DateTimePicker::make('published_at')
                            ->label('Schedule publish at')
                            ->helperText('Leave empty to publish immediately when toggled on.')
                            ->nullable(),

                        Forms\Components\Select::make('target_user_id')
                            ->label('Target user (optional)')
                            ->placeholder('All users')
                            ->searchable()
                            ->getSearchResultsUsing(fn (string $search) =>
                                \App\Models\User::where('name', 'like', "%{$search}%")
                                    ->orWhere('email', 'like', "%{$search}%")
                                    ->limit(20)
                                    ->pluck('name', 'id')
                                    ->toArray()
                            )
                            ->getOptionLabelUsing(fn ($value) =>
                                \App\Models\User::find($value)?->name ?? 'Unknown'
                            )
                            ->nullable()
                            ->helperText('Send to a specific user. Leave blank for everyone.'),
                    ]),

                Section::make('Reach')
                    ->icon('heroicon-o-chart-bar')
                    ->schema([
                        Forms\Components\Placeholder::make('reads_count')
                            ->label('Times read')
                            ->content(fn (?Announcement $record) => $record
                                ? $record->reads()->count() . ' users'
                                : '—'
                            ),
                    ])
                    ->hiddenOn('create'),
            ])->columnSpan(1),
        ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('#')
                    ->sortable()
                    ->width(50),

                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match($state) {
                        'feature'     => '✨ Feature',
                        'update'      => '🔄 Update',
                        'maintenance' => '🔧 Maintenance',
                        'message'     => '💬 Message',
                        'promo'       => '🎁 Promo',
                        default       => $state,
                    })
                    ->color(fn ($state) => match($state) {
                        'feature'     => 'success',
                        'update'      => 'primary',
                        'maintenance' => 'warning',
                        'message'     => 'info',
                        'promo'       => 'danger',
                        default       => 'secondary',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->limit(60)
                    ->weight('semibold'),

                Tables\Columns\TextColumn::make('version')
                    ->badge()
                    ->color('gray')
                    ->placeholder('—'),

                Tables\Columns\IconColumn::make('is_published')
                    ->label('Live')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('gray'),

                Tables\Columns\IconColumn::make('show_popup')
                    ->label('Popup')
                    ->boolean()
                    ->trueColor('primary')
                    ->falseColor('gray'),

                Tables\Columns\TextColumn::make('reads_count')
                    ->label('Reads')
                    ->getStateUsing(fn (Announcement $record) => $record->reads()->count())
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('published_at')
                    ->label('Scheduled')
                    ->dateTime('M j, Y g:i A')
                    ->placeholder('Immediate')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->since()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'feature'     => '✨ Feature',
                        'update'      => '🔄 Update',
                        'maintenance' => '🔧 Maintenance',
                        'message'     => '💬 Message',
                        'promo'       => '🎁 Promo',
                    ]),
                Tables\Filters\TernaryFilter::make('is_published')
                    ->label('Status')
                    ->placeholder('All')
                    ->trueLabel('Published')
                    ->falseLabel('Draft'),
            ])
            ->actions([
                Tables\Actions\Action::make('publish')
                    ->label('Publish')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Announcement $record) => ! $record->is_published)
                    ->action(function (Announcement $record) {
                        $record->update(['is_published' => true, 'published_at' => $record->published_at ?? now()]);
                        Notification::make()->title('Announcement published!')->success()->send();
                    }),

                Tables\Actions\Action::make('unpublish')
                    ->label('Unpublish')
                    ->icon('heroicon-o-x-circle')
                    ->color('warning')
                    ->visible(fn (Announcement $record) => $record->is_published)
                    ->action(function (Announcement $record) {
                        $record->update(['is_published' => false]);
                        Notification::make()->title('Announcement unpublished.')->warning()->send();
                    }),

                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('publishSelected')
                    ->label('Publish selected')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(fn ($records) => $records->each(fn ($r) => $r->update([
                        'is_published' => true,
                        'published_at' => $r->published_at ?? now(),
                    ])))
                    ->deselectRecordsAfterCompletion(),
                DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListAnnouncements::route('/'),
            'create' => Pages\CreateAnnouncement::route('/create'),
            'edit'   => Pages\EditAnnouncement::route('/{record}/edit'),
        ];
    }
}
