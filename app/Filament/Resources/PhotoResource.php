<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PhotoResource\Pages;
use App\Models\Photo;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Illuminate\Support\HtmlString;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\DeleteBulkAction;
use Illuminate\Support\Facades\Storage;

class PhotoResource extends Resource
{
    protected static ?string $model = Photo::class;
    public static function getNavigationIcon(): ?string { return 'heroicon-o-photo'; }
    public static function getNavigationLabel(): string { return 'Photo Moderation'; }
    public static function getNavigationSort(): ?int { return 2; }

    public static function getNavigationGroup(): ?string { return 'Content'; }

    public static function getNavigationBadge(): ?string
    {
        $count = Photo::where('is_approved', false)->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Photo Details')
                ->description(fn (?Photo $record) => $record
                    ? new HtmlString('<img src="' . e($record->getUrlAttribute()) . '" style="max-height:200px;border-radius:10px;object-fit:cover;display:block;margin-bottom:.25rem">')
                    : null
                )
                ->schema([
                    Forms\Components\TextInput::make('user_id')->disabled()->label('User ID'),
                    Forms\Components\TextInput::make('path')->disabled()->label('File Path'),
                    Forms\Components\Toggle::make('is_primary')->label('Primary Photo'),
                    Forms\Components\Toggle::make('is_approved')->label('Approved'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('path')
                    ->label('Photo')
                    ->getStateUsing(fn (Photo $record): string => $record->getUrlAttribute())
                    ->imageSize(80)
                    ->square()
                    ->extraImgAttributes(['style' => 'object-fit:cover;border-radius:6px']),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('User Name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\IconColumn::make('is_primary')
                    ->label('Primary')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_approved')
                    ->label('Approved')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Uploaded')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_approved')
                    ->label('Approval Status')
                    ->placeholder('All photos')
                    ->trueLabel('Approved only')
                    ->falseLabel('Pending only'),

                Tables\Filters\TernaryFilter::make('is_primary')
                    ->label('Primary Photo Only'),
            ])
            ->recordActions([
                Actions\Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Photo $record) => ! $record->is_approved)
                    ->action(function (Photo $record) {
                        $record->update(['is_approved' => true]);
                        Notification::make()->title('Photo approved')->success()->send();
                    }),

                Actions\Action::make('reject')
                    ->label('Reject & Delete')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (Photo $record) {
                        if ($record->is_primary) {
                            $next = Photo::where('user_id', $record->user_id)
                                ->where('id', '!=', $record->id)
                                ->where('is_approved', true)
                                ->first();
                            if ($next) {
                                $next->update(['is_primary' => true]);
                            }
                        }
                        if ($record->path) Storage::disk('public')->delete($record->path);
                        if ($record->thumbnail_path) Storage::disk('public')->delete($record->thumbnail_path);
                        $record->delete();
                        Notification::make()->title('Photo rejected and removed')->danger()->send();
                    }),

                Actions\EditAction::make(),
            ])
            ->toolbarActions([
                Actions\BulkAction::make('bulk_approve')
                    ->label('Approve Selected')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(fn ($records) => $records->each->update(['is_approved' => true])),
                DeleteBulkAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPhotos::route('/'),
            'edit'  => Pages\EditPhoto::route('/{record}/edit'),
        ];
    }
}





