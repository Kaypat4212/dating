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
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

class BlogPostResource extends Resource
{
    protected static ?string $model = BlogPost::class;

    public static function getNavigationIcon(): ?string  { return 'heroicon-o-newspaper'; }
    public static function getNavigationGroup(): ?string { return 'Community'; }
    public static function getNavigationLabel(): string  { return 'Blog Posts'; }
    public static function getNavigationSort(): ?int     { return 10; }

    public static function getNavigationBadge(): ?string
    {
        try {
            $count = static::getEloquentQuery()->where('status', 'draft')->count();
            return $count > 0 ? (string) $count : null;
        } catch (\Throwable) {
            return null;
        }
    }

    public static function getNavigationBadgeColor(): ?string { return 'warning'; }
    public static function getNavigationBadgeTooltip(): ?string { return 'Draft posts awaiting publish'; }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            // ── Main column (left, 2/3) ──────────────────────────────────
            Group::make([
                Section::make('Post Content')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Set $set, ?string $state) {
                                $set('slug', Str::slug($state ?? ''));
                            })
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->prefix('blog/')
                            ->helperText('Auto-generated from title. Edit to customise.')
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('excerpt')
                            ->rows(3)
                            ->maxLength(500)
                            ->helperText('Short summary shown on listing pages (max 500 chars).')
                            ->hintActions([
                                Forms\Components\Actions\Action::make('generateExcerpt')
                                    ->icon('heroicon-o-sparkles')
                                    ->label('Generate from content')
                                    ->tooltip('Auto-generate excerpt from blog content using AI')
                                    ->action(function (Forms\Set $set, Forms\Get $get) {
                                        $content = strip_tags($get('content') ?? '');
                                        $title = $get('title') ?? '';
                                        if (empty($content)) {
                                            \Filament\Notifications\Notification::make()
                                                ->title('No content to summarize')
                                                ->warning()
                                                ->send();
                                            return;
                                        }
                                        $excerpt = self::generateAiContent('excerpt', $title, $content);
                                        if ($excerpt) {
                                            $set('excerpt', $excerpt);
                                            \Filament\Notifications\Notification::make()
                                                ->title('Excerpt generated!')
                                                ->success()
                                                ->send();
                                        }
                                    }),
                            ])
                            ->columnSpanFull(),

                        Forms\Components\RichEditor::make('content')
                            ->required()
                            ->toolbarButtons([
                                'attachFiles',
                                'blockquote',
                                'bold',
                                'bulletList',
                                'codeBlock',
                                'h2',
                                'h3',
                                'italic',
                                'link',
                                'orderedList',
                                'redo',
                                'strike',
                                'underline',
                                'undo',
                            ])
                            ->hintActions([
                                Forms\Components\Actions\Action::make('generateContent')
                                    ->icon('heroicon-o-sparkles')
                                    ->label('Generate with AI')
                                    ->tooltip('Create blog post content using AI')
                                    ->modalHeading('Generate Blog Content')
                                    ->form([
                                        Forms\Components\Select::make('style')
                                            ->label('Writing style')
                                            ->options([
                                                'friendly' => 'Friendly & Conversational',
                                                'professional' => 'Professional',
                                                'romantic' => 'Romantic & Inspiring',
                                                'advice' => 'Advice & Tips',
                                                'story' => 'Personal Story',
                                            ])
                                            ->default('friendly')
                                            ->required(),
                                        Forms\Components\Textarea::make('topic')
                                            ->label('What is this blog post about?')
                                            ->placeholder('e.g. "5 tips for a successful first date"')
                                            ->required()
                                            ->rows(3),
                                    ])
                                    ->action(function (array $data, Forms\Set $set, Forms\Get $get) {
                                        $title = $get('title') ?? 'untitled post';
                                        $content = self::generateAiContent('blog', $title, $data['topic'] ?? '', $data['style'] ?? 'friendly');
                                        if ($content) {
                                            $set('content', $content);
                                            \Filament\Notifications\Notification::make()
                                                ->title('Blog content generated!')
                                                ->success()
                                                ->send();
                                        }
                                    }),
                                Forms\Components\Actions\Action::make('improveContent')
                                    ->icon('heroicon-o-arrow-path')
                                    ->label('Improve')
                                    ->tooltip('Enhance existing content with AI')
                                    ->action(function (Forms\Set $set, Forms\Get $get) {
                                        $existing = strip_tags($get('content') ?? '');
                                        if (empty($existing)) {
                                            \Filament\Notifications\Notification::make()
                                                ->title('No content to improve')
                                                ->warning()
                                                ->send();
                                            return;
                                        }
                                        $improved = self::generateAiContent('improve_blog', '', $existing);
                                        if ($improved) {
                                            $set('content', $improved);
                                            \Filament\Notifications\Notification::make()
                                                ->title('Content improved!')
                                                ->success()
                                                ->send();
                                        }
                                    }),
                            
])
                            ->columnSpanFull(),
                    ]),

                Section::make('Featured Image')
                    ->icon('heroicon-o-photo')
                    ->schema([
                        Forms\Components\FileUpload::make('featured_image')
                            ->image()
                            ->imageResizeMode('cover')
                            ->imageCropAspectRatio('16:9')
                            ->imageResizeTargetWidth('1200')
                            ->imageResizeTargetHeight('675')
                            ->disk('public')
                            ->directory('blog/images')
                            ->visibility('public')
                            ->maxSize(4096)
                            ->label(false)
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),

                Section::make('Tags')
                    ->icon('heroicon-o-tag')
                    ->schema([
                        Forms\Components\TagsInput::make('tags')
                            ->label(false)
                            ->placeholder('Add a tag and press Enter…')
                            ->suggestions([
                                'dating tips',
                                'relationships',
                                'love',
                                'communication',
                                'first date',
                                'long distance',
                                'marriage',
                                'breakup',
                                'self-love',
                            ])
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
            ])
            ->columnSpan(['lg' => 2]),

            // ── Sidebar column (right, 1/3) ──────────────────────────────
            Group::make([
                Section::make('Publish')
                    ->icon('heroicon-o-bolt')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'draft'     => 'Draft',
                                'published' => 'Published',
                                'archived'  => 'Archived',
                            ])
                            ->default('draft')
                            ->required()
                            ->live(),

                        Forms\Components\DateTimePicker::make('published_at')
                            ->label('Publish date')
                            ->helperText('Leave blank to publish immediately.')
                            ->visible(fn (Get $get) => $get('status') === 'published'),
                    ]),

                Section::make('Categorisation')
                    ->icon('heroicon-o-folder')
                    ->schema([
                        Forms\Components\Select::make('author_id')
                            ->relationship('author', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->label('Author'),

                        Forms\Components\Select::make('category_id')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')->required()->maxLength(80)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state ?? ''))),
                                Forms\Components\TextInput::make('slug')->required()->maxLength(80)->unique(),
                                Forms\Components\Textarea::make('description')->rows(2),
                                Forms\Components\TextInput::make('color')->default('#6c757d'),
                            ])
                            ->label('Category'),
                    ]),

                Section::make('Settings')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->schema([
                        Forms\Components\Toggle::make('is_featured')
                            ->label('Featured post')
                            ->helperText('Pinned to the top of the blog listing.'),

                        Forms\Components\Toggle::make('allow_comments')
                            ->label('Allow comments')
                            ->default(true),
                    ]),
            ])
            ->columnSpan(['lg' => 1]),
        ])
        ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\ImageColumn::make('featured_image')
                    ->disk('public')
                    ->label('')
                    ->width(60)
                    ->height(40)
                    ->defaultImageUrl(fn () => null)
                    ->extraImgAttributes(['class' => 'rounded object-cover']),

                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->limit(55)
                    ->description(fn (BlogPost $r) => $r->category?->name),

                Tables\Columns\TextColumn::make('author.name')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        'published' => 'success',
                        'archived'  => 'gray',
                        default     => 'warning',
                    }),

                Tables\Columns\TextColumn::make('views_count')
                    ->label('Views')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('comments_count')
                    ->label('Comments')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\IconColumn::make('is_featured')
                    ->boolean()
                    ->label('Featured')
                    ->toggleable(),

                Tables\Columns\IconColumn::make('allow_comments')
                    ->boolean()
                    ->label('Comments on')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('published_at')
                    ->dateTime('M j, Y')
                    ->sortable()
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('M j, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft'     => 'Draft',
                        'published' => 'Published',
                        'archived'  => 'Archived',
                    ]),

                Tables\Filters\SelectFilter::make('category')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('Featured'),

                Tables\Filters\TernaryFilter::make('allow_comments')
                    ->label('Comments enabled'),
            ])
            ->recordActions([
                Actions\EditAction::make(),
                Actions\Action::make('publish')
                    ->label('Publish')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (BlogPost $r) => $r->status !== 'published')
                    ->action(fn (BlogPost $r) => $r->update([
                        'status'       => 'published',
                        'published_at' => $r->published_at ?? now(),
                    ]))
                    ->requiresConfirmation(),
                Actions\Action::make('archive')
                    ->label('Archive')
                    ->icon('heroicon-o-archive-box')
                    ->color('gray')
                    ->visible(fn (BlogPost $r) => $r->status === 'published')
                    ->action(fn (BlogPost $r) => $r->update(['status' => 'archived']))
                    ->requiresConfirmation(),
                Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('bulk_publish')
                        ->label('Publish selected')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn ($records) => $records->each(fn (BlogPost $r) => $r->update([
                            'status'       => 'published',
                            'published_at' => $r->published_at ?? now(),
                        ])))
                        ->requiresConfirmation(),
                    BulkAction::make('bulk_draft')
                        ->label('Move to draft')
                        ->icon('heroicon-o-pencil')
                        ->color('warning')
                        ->action(fn ($records) => $records->each(fn (BlogPost $r) => $r->update(['status' => 'draft'])))
                        ->requiresConfirmation(),
                    BulkAction::make('bulk_archive')
                        ->label('Archive selected')
                        ->icon('heroicon-o-archive-box')
                        ->color('gray')
                        ->action(fn ($records) => $records->each(fn (BlogPost $r) => $r->update(['status' => 'archived'])))
                        ->requiresConfirmation(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    /**
     * Generate AI content for blog posts
     */
    protected static function generateAiContent(string $mode, string $title, string $context, string $style = 'friendly'): ?string
    {
        $apiKey = \App\Models\SiteSetting::get('ai_groq_api_key', '');
        
        if (empty($apiKey)) {
            \Filament\Notifications\Notification::make()
                ->title('AI not configured')
                ->body('Please configure Groq API key in Site Settings → AI Assistant')
                ->warning()
                ->send();
            return null;
        }

        $styleMap = [
            'friendly' => 'friendly, warm, and conversational',
            'professional' => 'professional and informative',
            'romantic' => 'romantic, inspiring, and heartfelt',
            'advice' => 'advisorial with practical tips',
            'story' => 'personal storytelling style',
        ];

        $prompts = [
            'excerpt' => "Write a compelling 2-sentence summary (max 150 chars) of this blog post titled '$title': $context",
            'blog' => "Write a complete, engaging blog post for a dating app blog. Title: '$title'. Topic: $context. Style: {$styleMap[$style]}. Include an introduction, 3-4 main points with examples, and a conclusion. Use HTML formatting (<h2>, <p>, <strong>, <ul>, <li>). Add emojis where appropriate. Make it 600-800 words.",
            'improve_blog' => "Improve this dating blog post content. Make it more engaging, add more detail, fix grammar, improve flow, and add emojis where appropriate. Keep HTML formatting: $context",
            'expand' => "Expand this blog content with more details, examples, and insights. Add 2-3 more paragraphs. Keep the same tone and HTML formatting: $context",
        ];

        try {
            $response = Http::timeout(30)
                ->withToken($apiKey)
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model' => \App\Models\SiteSetting::get('ai_groq_model', 'llama-3.1-8b-instant'),
                    'messages' => [
                        ['role' => 'system', 'content' => 'You are an expert dating and relationship writer who creates engaging, helpful blog content for a dating app audience.'],
                        ['role' => 'user', 'content' => $prompts[$mode] ?? $prompts['blog']],
                    ],
                    'max_tokens' => $mode === 'excerpt' ? 100 : 1500,
                    'temperature' => 0.7,
                ]);

            if (!$response->successful()) {
                throw new \Exception($response->body());
            }

            $content = data_get($response->json(), 'choices.0.message.content', '');
            
            // Clean up the response
            $content = trim($content, "\" \n\r\t");
            
            return $content ?: null;
            
        } catch (\Throwable $e) {
            \Filament\Notifications\Notification::make()
                ->title('AI generation failed')
                ->body($e->getMessage())
                ->danger()
                ->send();
            return null;
        }
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
