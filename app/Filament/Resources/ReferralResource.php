<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReferralResource\Pages;
use App\Models\Referral;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class ReferralResource extends Resource
{
    protected static ?string $model = Referral::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-plus';
    protected static string|\UnitEnum|null $navigationGroup = 'Members';
    protected static ?string $label = 'Referrals';
    protected static ?int $navigationSort = 5;

    /** Read-only resource — no create/edit/delete from admin. */
    public static function canCreate(): bool { return false; }
    public static function canEdit($record): bool { return false; }
    public static function canDelete($record): bool { return false; }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('referrer.name')
                    ->label('Referrer')
                    ->searchable()
                    ->sortable()
                    ->url(fn ($record) => $record->referrer_id
                        ? url('/admin/users/' . $record->referrer_id . '/edit')
                        : null
                    ),

                TextColumn::make('referrer.referral_code')
                    ->label('Ref Code')
                    ->badge()
                    ->copyable()
                    ->fontFamily('mono'),

                TextColumn::make('referred.name')
                    ->label('New Member')
                    ->searchable()
                    ->sortable()
                    ->url(fn ($record) => $record->referred_id
                        ? url('/admin/users/' . $record->referred_id . '/edit')
                        : null
                    ),

                TextColumn::make('referred.email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('rewarded')
                    ->label('Rewarded')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('gray'),

                TextColumn::make('rewarded_at')
                    ->label('Rewarded At')
                    ->dateTime('M j, Y')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Referred On')
                    ->dateTime('M j, Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Filter::make('rewarded')
                    ->label('Rewarded only')
                    ->query(fn (Builder $q) => $q->where('rewarded', true)),

                Filter::make('not_rewarded')
                    ->label('Not yet rewarded')
                    ->query(fn (Builder $q) => $q->where('rewarded', false)),

                Filter::make('this_month')
                    ->label('This month')
                    ->query(fn (Builder $q) => $q->whereMonth('created_at', now()->month)
                                                  ->whereYear('created_at', now()->year)),
            ])
            ->recordActions([])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReferrals::route('/'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count() ?: null;
    }
}
