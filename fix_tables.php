<?php
// One-time cleanup script - delete after use
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

$drops = ['icebreaker_sessions', 'icebreaker_answers', 'icebreaker_questions',
          'voice_prompts', 'voice_prompt_questions',
          'pets', 'travel_interests', 'travel_plans',
          'user_music_profiles', 'chat_rooms', 'chat_room_members',
          'chat_room_messages', 'chat_room_message_reactions', 'chat_room_invites',
          'forum_topic_likes', 'forum_reply_likes', 'forum_replies',
          'forum_topics', 'forum_subscriptions', 'forum_moderators', 'forum_categories',
          'forum_topic_followers',
          'blog_post_views', 'blog_post_likes', 'blog_comment_likes', 'blog_comments',
          'blog_posts', 'blog_categories'];

DB::statement('SET FOREIGN_KEY_CHECKS=0');
foreach ($drops as $table) {
    Schema::dropIfExists($table);
    echo "Dropped: $table\n";
}
DB::statement('SET FOREIGN_KEY_CHECKS=1');

// Remove migration records for these features so they can re-run
DB::table('migrations')
    ->where('migration', 'LIKE', '%blog%')
    ->orWhere('migration', 'LIKE', '%forum%')
    ->orWhere('migration', 'LIKE', '%chat_room%')
    ->orWhere('migration', 'LIKE', '%music_integration%')
    ->orWhere('migration', 'LIKE', '%travel_buddy%')
    ->orWhere('migration', 'LIKE', '%pet_profile%')
    ->orWhere('migration', 'LIKE', '%voice_prompt%')
    ->orWhere('migration', 'LIKE', '%icebreaker%')
    ->delete();
echo "Migration records cleaned.\nDone!\n";
