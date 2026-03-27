<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ChatRoomResource\Pages\ListChatRooms;
use App\Filament\Resources\ChatRoomResource\Pages\EditChatRoom;
use App\Models\ChatRoom;
use Filament\Actions;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ChatRoomResource extends Resource
{
    protected static ?string $model = ChatRoom::class;

    public static function getNavigationIcon(): ?string { return 'heroicon-o-chat-bubble-oval-left-ellipsis'; }
    public static function getNavigationGroup(): ?string { return 'Community'; }
    public static function getNavigationSort(): ?int { return 12; }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\TextInput::make('name')->required()->maxLength(80),
            Forms\Components\Textarea::make('description')->rows(2)->columnSpanFull(),
            Forms\Components\Select::make('type')
                ->options(['public' => 'Public', 'private' => 'Private', 'interest' => 'Interest-based', 'location' => 'Location-based'])
                ->required(),
            Forms\Components\TextInput::make('max_members')->numeric()->default(100),
            Forms\Components\Toggle::make('is_active')->default(true),
            Forms\Components\Toggle::make('requires_approval'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('messages_count', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('creator.name')->searchable(),
                Tables\Columns\TextColumn::make('type')->badge(),
                Tables\Columns\TextColumn::make('members_count')->label('Members')->sortable(),
                Tables\Columns\TextColumn::make('messages_count')->label('Messages')->sortable(),
                Tables\Columns\IconColumn::make('is_active')->boolean()->label('Active'),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->recordActions([
                Actions\Action::make('toggle_active')
                    ->label('Activate/Deactivate')
                    ->icon('heroicon-o-power')
                    ->action(fn (ChatRoom $record) => $record->update(['is_active' => !$record->is_active])),
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListChatRooms::route('/'),
            'edit'  => EditChatRoom::route('/{record}/edit'),
        ];
    }
}
