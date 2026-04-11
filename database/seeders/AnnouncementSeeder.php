<?php

namespace Database\Seeders;

use App\Models\Announcement;
use Illuminate\Database\Seeder;

class AnnouncementSeeder extends Seeder
{
    public function run(): void
    {
        $announcements = [
            [
                'title'        => 'Welcome to HeartsConnect! 💕',
                'body'         => '<p>We\'re thrilled to have you here! HeartsConnect is your new favorite place to meet amazing people. <strong>Start swiping and making connections today!</strong></p>',
                'type'         => 'message',
                'version'      => null,
                'badge_label'  => 'WELCOME',
                'badge_color'  => 'primary',
                'is_published' => true,
                'show_popup'   => true,
                'published_at' => now(),
            ],
            [
                'title'        => '📸 Snapchat-Style Snaps Are Here!',
                'body'         => '<p>Send <strong>disappearing photos and videos</strong> to your matches! Snaps automatically delete after viewing.</p><ul><li>📸 Tap the camera icon in any conversation</li><li>🔥 Build your streak by staying in touch daily</li><li>⏱️ Content vanishes in 10 seconds after viewing</li></ul>',
                'type'         => 'feature',
                'version'      => 'v2.5',
                'badge_label'  => 'NEW',
                'badge_color'  => 'success',
                'is_published' => true,
                'show_popup'   => true,
                'published_at' => now()->subDays(2),
            ],
            [
                'title'        => '🎥 Voice & Video Calls Now Available',
                'body'         => '<p>Connect with your matches face-to-face! <strong>Voice and video calls</strong> are now enabled for all matched users.</p><p>Click the phone or video icon in any conversation to start a call. No external apps needed!</p>',
                'type'         => 'feature',
                'version'      => 'v2.4',
                'badge_label'  => 'HOT',
                'badge_color'  => 'danger',
                'is_published' => true,
                'show_popup'   => true,
                'published_at' => now()->subDays(5),
            ],
            [
                'title'        => '🚀 Real-Time Messaging is Live',
                'body'         => '<p>Messages now appear <strong>instantly</strong> without refreshing! Your conversations are always up-to-date thanks to our new WebSocket integration.</p>',
                'type'         => 'update',
                'version'      => 'v2.3',
                'badge_label'  => null,
                'badge_color'  => 'primary',
                'is_published' => true,
                'show_popup'   => false,
                'published_at' => now()->subDays(10),
            ],
            [
                'title'        => '🎯 Improved Matching Algorithm',
                'body'         => '<p>We\'ve fine-tuned our matching system to show you <strong>better, more relevant matches</strong> based on your preferences and activity.</p>',
                'type'         => 'update',
                'version'      => 'v2.2',
                'badge_label'  => null,
                'badge_color'  => 'info',
                'is_published' => true,
                'show_popup'   => false,
                'published_at' => now()->subDays(15),
            ],
            [
                'title'        => '🔒 Enhanced Privacy & Security',
                'body'         => '<p>Your safety is our priority. We\'ve added:</p><ul><li>End-to-end encryption for messages</li><li>Report & block features</li><li>Photo verification badges</li><li>Safe Date mode with location sharing</li></ul>',
                'type'         => 'feature',
                'version'      => 'v2.1',
                'badge_label'  => 'TRUSTED',
                'badge_color'  => 'success',
                'is_published' => true,
                'show_popup'   => false,
                'published_at' => now()->subDays(20),
            ],
        ];

        foreach ($announcements as $announcement) {
            Announcement::updateOrCreate(
                ['title' => $announcement['title']],
                $announcement
            );
        }
    }
}
