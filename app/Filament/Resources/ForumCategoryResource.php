<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ForumCategoryResource\Pages;
use App\Models\ForumCategory;
use Filament\Actions;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Illuminate\Support\Str;

class ForumCategoryResource extends Resource
{
    protected static ?string $model = ForumCategory::class;

    public static function getNavigationIcon(): ?string  { return 'heroicon-o-chat-bubble-oval-left-ellipsis'; }
    public static function getNavigationGroup(): ?string { return 'Community'; }
    public static function getNavigationLabel(): string  { return 'Forum Categories'; }
    public static function getNavigationSort(): ?int     { return 13; }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make()->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(80)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (Set $set, Get $get, ?string $state, ?string $operation) {
                        if ($operation === 'create') {
                            $set('slug', Str::slug($state ?? ''));
                        }
                    }),

                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->maxLength(80)
                    ->unique(ignoreRecord: true),

                Forms\Components\Textarea::make('description')
                    ->rows(3)
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('icon')
                    ->placeholder('emoji or icon class, e.g. 💬')
                    ->maxLength(40),

                Forms\Components\ColorPicker::make('color')
                    ->default('#6366f1'),

                Forms\Components\TextInput::make('order')
                    ->numeric()
                    ->default(0)
                    ->label('Sort order'),

                Forms\Components\Toggle::make('is_active')
                    ->default(true)
                    ->label('Active'),

                Forms\Components\Toggle::make('requires_verified')
                    ->label('Verified users only')
                    ->helperText('Only email-verified users can post in this category.'),

                Forms\Components\TextInput::make('country_code')
                    ->label('Country code (optional)')
                    ->placeholder('e.g. NG, US')
                    ->maxLength(5)
                    ->helperText('Restrict this category to a specific country.'),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('order')
            ->reorderable('order')
            ->columns([
                Tables\Columns\TextColumn::make('order')
                    ->label('#')
                    ->sortable()
                    ->width(40),

                Tables\Columns\TextColumn::make('icon')
                    ->label('')
                    ->width(30),

                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->description(fn (ForumCategory $r) => $r->slug),

                Tables\Columns\ColorColumn::make('color'),

                Tables\Columns\TextColumn::make('topics_count')
                    ->label('Topics')
                    ->counts('topics')
                    ->sortable(),

                Tables\Columns\IconColumn::make('requires_verified')
                    ->boolean()
                    ->label('Verified only'),

                Tables\Columns\TextColumn::make('country_code')
                    ->label('Country')
                    ->placeholder('All')
                    ->toggleable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active'),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')->label('Active'),
                Tables\Filters\TernaryFilter::make('requires_verified')->label('Verified only'),
            ])
            ->recordActions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListForumCategories::route('/'),
            'create' => Pages\CreateForumCategory::route('/create'),
            'edit'   => Pages\EditForumCategory::route('/{record}/edit'),
        ];
    }
}
