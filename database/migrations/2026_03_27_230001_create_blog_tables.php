<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Blog Categories
        Schema::create('blog_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('icon')->nullable(); // emoji or icon class
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Blog Posts
        Schema::create('blog_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // Author
            $table->foreignId('category_id')->nullable()->constrained('blog_categories')->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->longText('content');
            $table->string('featured_image')->nullable();
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->integer('views_count')->default(0);
            $table->integer('comments_count')->default(0);
            $table->integer('likes_count')->default(0);
            $table->json('tags')->nullable(); // Array of tags
            $table->json('meta')->nullable(); // SEO metadata
            $table->boolean('featured')->default(false);
            $table->boolean('allow_comments')->default(true);
            $table->timestamps();
            
            $table->index(['status', 'published_at']);
            $table->index('featured');
        });

        // Blog Comments
        Schema::create('blog_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained('blog_posts')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('blog_comments')->cascadeOnDelete(); // For nested replies
            $table->text('content');
            $table->enum('status', ['pending', 'approved', 'spam', 'rejected'])->default('approved');
            $table->integer('likes_count')->default(0);
            $table->timestamps();
            
            $table->index(['post_id', 'status']);
        });

        // Blog Likes (for posts and comments)
        Schema::create('blog_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->morphs('likeable'); // blog_posts or blog_comments
            $table->timestamps();
            
            $table->unique(['user_id', 'likeable_id', 'likeable_type']);
        });

        // Blog Post Views (for analytics)
        Schema::create('blog_post_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained('blog_posts')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('ip_address', 45);
            $table->string('user_agent')->nullable();
            $table->timestamp('viewed_at');
            
            $table->index(['post_id', 'viewed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_post_views');
        Schema::dropIfExists('blog_likes');
        Schema::dropIfExists('blog_comments');
        Schema::dropIfExists('blog_posts');
        Schema::dropIfExists('blog_categories');
    }
};
