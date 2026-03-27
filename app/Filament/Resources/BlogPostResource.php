<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BlogPostResource\Pages\CreateBlogPost;
use App\Filament\Resources\BlogPostResource\Pages\EditBlogPost;
use App\Filament\Resources\BlogPostResource\Pages\ListBlogPosts;
use App\Models\BlogPost;
use Filament\Actions;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BlogPostResource extends Resource
{
    protected static ?string $model = BlogPost::class;

    public static function getNavigationIcon(): ?string { return 'heroicon-o-newspaper'; }
    public static function getNavigationGroup(): ?string { return 'Community'; }
    public static function getNavigationSort(): ?int { return 10; }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\Select::make('author_id')
                ->relationship('author', 'name')
                ->searchable()
                ->required(),
            Forms\Components\Select::make('category_id')
                ->relationship('category', 'name')
                ->searchable()
                ->required(),
            Forms\Components\TextInput::make('title')->required()->maxLength(255)->columnSpanFull(),
            Forms\Components\TextInput::make('slug')->required()->maxLength(255),
            Forms\Components\Select::make('status')
                ->options(['draft' => 'Draft', 'published' => 'Published', 'archived' => 'Archived'])
                ->required(),
            Forms\Components\DateTimePicker::make('published_at'),
            Forms\Components\Textarea::make('excerpt')->rows(2)->columnSpanFull(),
            Forms\Components\Textarea::make('content')->rows(10)->columnSpanFull(),
            Forms\Components\Toggle::make('is_featured'),
            Forms\Components\Toggle::make('allow_comments')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('title')->searchable()->limit(50),
                Tables\Columns\TextColumn::make('author.name')->searchable(),
                Tables\Columns\TextColumn::make('category.name'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        'published' => 'success',
                        'archived'  => 'gray',
                        default     => 'warning',
                    }),
                Tables\Columns\TextColumn::make('views_count')->label('Views')->sortable(),
                Tables\Columns\IconColumn::make('is_featured')->boolean()->label('Featured'),
                Tables\Columns\TextColumn::make('published_at')->dateTime()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(['draft' => 'Draft', 'published' => 'Published', 'archived' => 'Archived']),
            ])
            ->recordActions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListBlogPosts::route('/'),
            'create' => CreateBlogPost::route('/create'),
            'edit'   => EditBlogPost::route('/{record}/edit'),
        ];
    }
}
