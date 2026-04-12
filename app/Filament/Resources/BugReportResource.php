<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BugReportResource\Pages;
use App\Models\BugReport;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Actions\DeleteBulkAction;
use Filament\Notifications\Notification;

class BugReportResource extends Resource
{
    protected static ?string $model = BugReport::class;

    public static function getNavigationIcon(): ?string  { return 'heroicon-o-bug-ant'; }
    public static function getNavigationGroup(): ?string { return 'System'; }
    public static function getNavigationLabel(): string  { return 'Bug Reports'; }
    public static function getNavigationSort(): ?int     { return 95; }

    public static function getNavigationBadge(): ?string
    {
        try {
            $count = static::getEloquentQuery()->where('status', 'open')->count();
            return $count > 0 ? (string) $count : null;
        } catch (\Throwable) {
            return null;
        }
    }

    public static function getNavigationBadgeColor(): ?string { return 'danger'; }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Report Details')
                ->icon('heroicon-o-document-text')
                ->schema([
                    Forms\Components\TextInput::make('title')
                        ->disabled()
                        ->columnSpanFull(),

                    Forms\Components\Textarea::make('description')
                        ->disabled()
                        ->rows(6)
                        ->columnSpanFull(),

                    Forms\Components\TextInput::make('category')
                        ->disabled(),

                    Forms\Components\TextInput::make('page_url')
                        ->label('Page URL')
                        ->disabled(),

                    Forms\Components\TextInput::make('browser')
                        ->disabled()
                        ->columnSpanFull(),
                ])->columns(2),

            Section::make('Admin Response')
                ->icon('heroicon-o-wrench-screwdriver')
                ->schema([
                    Forms\Components\Select::make('status')
                        ->options(BugReport::STATUSES)
                        ->required(),

                    Forms\Components\Textarea::make('admin_notes')
                        ->label('Admin Notes (internal)')
                        ->rows(4)
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('#')
                    ->sortable()
                    ->width(60),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'danger'  => 'open',
                        'warning' => 'in_progress',
                        'success' => 'resolved',
                        'gray'    => 'closed',
                    ])
                    ->sortable(),

                Tables\Columns\TextColumn::make('category')
                    ->formatStateUsing(fn($state) => BugReport::CATEGORIES[$state] ?? $state)
                    ->sortable(),

                Tables\Columns\TextColumn::make('title')
                    ->limit(60)
                    ->searchable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Reporter')
                    ->default('Anonymous')
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Reported')
                    ->since()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(BugReport::STATUSES),
                Tables\Filters\SelectFilter::make('category')
                    ->options(BugReport::CATEGORIES),
            ])
            ->actions([
                Tables\Actions\Action::make('markResolved')
                    ->label('Resolve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn(BugReport $r) => !$r->isResolved())
                    ->action(function (BugReport $record) {
                        $record->update(['status' => 'resolved', 'resolved_at' => now()]);
                        Notification::make()->title('Marked as resolved')->success()->send();
                    }),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
                Tables\Actions\BulkAction::make('markResolved')
                    ->label('Mark Resolved')
                    ->icon('heroicon-o-check-circle')
                    ->action(fn($records) => $records->each->update(['status' => 'resolved', 'resolved_at' => now()])),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBugReports::route('/'),
            'edit'  => Pages\EditBugReport::route('/{record}/edit'),
        ];
    }
}
