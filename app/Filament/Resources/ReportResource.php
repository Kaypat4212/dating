<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReportResource\Pages;
use App\Models\Report;
use Filament\Actions;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ReportResource extends Resource
{
    protected static ?string $model = Report::class;
    public static function getNavigationIcon(): ?string { return 'heroicon-o-flag'; }
    public static function getNavigationGroup(): ?string { return 'Moderation'; }
    public static function getNavigationSort(): ?int { return 1; }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getEloquentQuery()->where('status', 'pending')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\Select::make('reporter_id')->relationship('reporter', 'email')->searchable()->label('Reporter'),
            Forms\Components\Select::make('reported_user_id')->relationship('reportedUser', 'email')->searchable()->label('Reported User'),
            Forms\Components\Select::make('reason')->options([
                'spam' => 'Spam', 'harassment' => 'Harassment', 'fake_profile' => 'Fake Profile',
                'inappropriate_content' => 'Inappropriate Content', 'other' => 'Other',
            ]),
            Forms\Components\Textarea::make('description')->label('Description')->columnSpanFull(),
            Forms\Components\Select::make('status')->options(['pending' => 'Pending', 'reviewed' => 'Reviewed', 'action_taken' => 'Action Taken', 'dismissed' => 'Dismissed']),
            Forms\Components\Textarea::make('admin_note')->label('Admin Note')->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('reporter.email')->label('Reporter')->searchable(),
                Tables\Columns\TextColumn::make('reported.email')->label('Reported')->searchable(),
                Tables\Columns\TextColumn::make('reason')->formatStateUsing(fn ($s) => ucfirst(str_replace('_', ' ', $s))),
                Tables\Columns\TextColumn::make('description')->limit(40),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        'reviewed'     => 'info',
                        'action_taken' => 'success',
                        'dismissed'    => 'gray',
                        default        => 'warning',
                    }),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options(['pending' => 'Pending', 'reviewed' => 'Reviewed', 'action_taken' => 'Action Taken', 'dismissed' => 'Dismissed']),
            ])
            ->recordActions([
                Actions\Action::make('ban_user')
                    ->label('Ban Reported User')
                    ->icon('heroicon-o-no-symbol')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn (Report $record) => $record->reportedUser->update(['is_banned' => true])),
                Actions\EditAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReports::route('/'),
            'edit'  => Pages\EditReport::route('/{record}/edit'),
        ];
    }
}





