<?php

namespace Database\Seeders;

use App\Models\EmailTemplate;
use Illuminate\Database\Seeder;

class EmailTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [

            // ── Welcome ─────────────────────────────────────────────────────
            [
                'key'       => 'welcome',
                'name'      => 'Welcome Email',
                'subject'   => 'Welcome to {app_name}! 💕 Your account is ready',
                'variables' => ['{user_name}', '{app_name}', '{app_url}', '{setup_url}'],
                'body'      => <<<HTML
<h2>Welcome to {app_name}, {user_name}! 💕</h2>
<p>We're so excited to have you here. Your account is ready — your journey to finding meaningful connections starts right now.</p>

<div style="background:#fdf2f8;border-left:4px solid #e91e8c;padding:16px 20px;border-radius:6px;margin:20px 0">
  <strong>Here's how to get started:</strong>
  <ol style="margin:10px 0 0 0;padding-left:20px">
    <li>📸 <strong>Add your best photos</strong> — profiles with photos get 10× more matches</li>
    <li>✏️ <strong>Write your bio</strong> — show your personality and what makes you unique</li>
    <li>💛 <strong>Set your preferences</strong> — tell us who you're looking for</li>
    <li>🔍 <strong>Start swiping</strong> — your perfect match could be just a swipe away</li>
  </ol>
</div>

<p>The more complete your profile, the better your matches will be — so take a few minutes now to make a great impression!</p>

<p style="text-align:center;margin:30px 0">
  <a href="{setup_url}" style="background:#e91e8c;color:#fff;padding:12px 28px;border-radius:6px;text-decoration:none;font-weight:600">Complete My Profile →</a>
</p>

<p>We can't wait to see you find your person. 💕</p>
<p>Warmly,<br><strong>The {app_name} Team</strong></p>
<hr style="border:none;border-top:1px solid #eee;margin:24px 0">
<p style="font-size:12px;color:#999">You created this account using this email address. Visit your <a href="{app_url}/account/settings">account settings</a> to manage email preferences.</p>
HTML,
            ],

            // ── New Match ────────────────────────────────────────────────────
            [
                'key'       => 'new_match',
                'name'      => 'New Match',
                'subject'   => "It's a Match! 💕 You and {match_name} liked each other",
                'variables' => ['{user_name}', '{match_name}', '{conversation_url}', '{app_name}', '{app_url}'],
                'body'      => <<<HTML
<h2>It's a Match! 💕</h2>
<p>Hi <strong>{user_name}</strong>, you and <strong>{match_name}</strong> liked each other — it's a match!</p>

<div style="background:#fdf2f8;border-left:4px solid #e91e8c;padding:16px 20px;border-radius:6px;margin:20px 0">
  This is your moment. The first message sets the tone — be genuine, be curious, be yourself. 😊
</div>

<p style="text-align:center;margin:30px 0">
  <a href="{conversation_url}" style="background:#e91e8c;color:#fff;padding:12px 28px;border-radius:6px;text-decoration:none;font-weight:600">Send a Message 💬</a>
</p>

<p>Don't keep them waiting — your perfect match is ready to chat!</p>
<p>With love,<br><strong>The {app_name} Team</strong></p>
<hr style="border:none;border-top:1px solid #eee;margin:24px 0">
<p style="font-size:12px;color:#999">You matched via {app_name}. Manage your notification preferences in <a href="{app_url}/account/settings">account settings</a>.</p>
HTML,
            ],

            // ── New Message ──────────────────────────────────────────────────
            [
                'key'       => 'new_message',
                'name'      => 'New Message',
                'subject'   => '{sender_name} sent you a message 💬',
                'variables' => ['{user_name}', '{sender_name}', '{message_preview}', '{conversation_url}', '{app_name}', '{app_url}'],
                'body'      => <<<HTML
<h2>New Message from {sender_name} 💬</h2>
<p>Hi <strong>{user_name}</strong>, you have a new message waiting!</p>

<div style="background:#fdf2f8;border-left:4px solid #e91e8c;padding:16px 20px;border-radius:6px;margin:20px 0">
  <strong>{sender_name} says:</strong><br>
  <em>"{message_preview}"</em>
</div>

<p>Don't leave them waiting — log in and reply now!</p>

<p style="text-align:center;margin:30px 0">
  <a href="{conversation_url}" style="background:#e91e8c;color:#fff;padding:12px 28px;border-radius:6px;text-decoration:none;font-weight:600">Read &amp; Reply</a>
</p>

<p>Happy chatting,<br><strong>The {app_name} Team</strong></p>
<hr style="border:none;border-top:1px solid #eee;margin:24px 0">
<p style="font-size:12px;color:#999">You're receiving this because you have email notifications enabled. Manage your preferences in <a href="{app_url}/account/settings">account settings</a>.</p>
HTML,
            ],

            // ── Profile Liked ────────────────────────────────────────────────
            [
                'key'       => 'profile_liked',
                'name'      => 'Profile Liked',
                'subject'   => 'Someone liked your profile on {app_name}! 😍',
                'variables' => ['{user_name}', '{app_name}', '{app_url}', '{premium_url}'],
                'body'      => <<<HTML
<h2>Someone Liked Your Profile! 😍</h2>
<p>Hi <strong>{user_name}</strong>, great news — someone just liked your profile on {app_name}!</p>

<div style="background:#fdf2f8;border-left:4px solid #e91e8c;padding:16px 20px;border-radius:6px;margin:20px 0">
  <strong>Want to know who it is?</strong><br><br>
  Upgrade to Premium to see exactly who liked you, send unlimited messages, and boost your profile to the top. 🚀
</div>

<p>You could be one step away from your perfect match!</p>

<p style="text-align:center;margin:30px 0">
  <a href="{premium_url}" style="background:#e91e8c;color:#fff;padding:12px 28px;border-radius:6px;text-decoration:none;font-weight:600">See Who Liked You →</a>
</p>

<p>Don't miss your chance,<br><strong>The {app_name} Team</strong></p>
<hr style="border:none;border-top:1px solid #eee;margin:24px 0">
<p style="font-size:12px;color:#999">Manage your notification preferences in <a href="{app_url}/account/settings">account settings</a>.</p>
HTML,
            ],

            // ── Premium Purchased ────────────────────────────────────────────
            [
                'key'       => 'premium_purchased',
                'name'      => 'Premium Purchased',
                'subject'   => '🌟 Welcome to {app_name} Premium!',
                'variables' => ['{user_name}', '{plan}', '{expires_at}', '{app_name}', '{app_url}', '{discover_url}'],
                'body'      => <<<HTML
<h2>You're Now a Premium Member! 🌟</h2>
<p>Hi <strong>{user_name}</strong>, welcome to {app_name} Premium! Your <strong>{plan}</strong> plan is now active.</p>

<div style="background:#fdf2f8;border-left:4px solid #e91e8c;padding:16px 20px;border-radius:6px;margin:20px 0">
  <strong>Your Premium benefits are unlocked:</strong>
  <ul style="margin:10px 0 0;padding-left:20px">
    <li>👁️ See who liked your profile — no more guessing</li>
    <li>💬 Unlimited messages with all your matches</li>
    <li>🚀 Weekly profile boost — appear at the top of the deck</li>
    <li>🌍 Browse profiles from anywhere in the world</li>
    <li>🔒 Advanced privacy controls</li>
  </ul>
  <p style="margin:12px 0 0"><strong>Active until:</strong> {expires_at}</p>
</div>

<p>Make the most of it — try boosting your profile today and watch your matches soar!</p>

<p style="text-align:center;margin:30px 0">
  <a href="{discover_url}" style="background:#e91e8c;color:#fff;padding:12px 28px;border-radius:6px;text-decoration:none;font-weight:600">Start Exploring →</a>
</p>

<p>Enjoy every perk,<br><strong>The {app_name} Team</strong></p>
<hr style="border:none;border-top:1px solid #eee;margin:24px 0">
<p style="font-size:12px;color:#999">Questions about your subscription? Visit your <a href="{app_url}/account/settings">account settings</a>.</p>
HTML,
            ],

            // ── Premium Expired ──────────────────────────────────────────────
            [
                'key'       => 'premium_expired',
                'name'      => 'Premium Expired',
                'subject'   => 'Your {app_name} Premium has expired',
                'variables' => ['{user_name}', '{app_name}', '{app_url}', '{premium_url}'],
                'body'      => <<<HTML
<h2>Your Premium Membership Has Expired 😢</h2>
<p>Hi <strong>{user_name}</strong>, your {app_name} Premium membership has expired.</p>

<div style="background:#fff8f0;border-left:4px solid #f59e0b;padding:16px 20px;border-radius:6px;margin:20px 0">
  <strong>You've lost access to:</strong>
  <ul style="margin:10px 0 0;padding-left:20px">
    <li>👁️ Seeing who liked your profile</li>
    <li>💬 Unlimited messaging</li>
    <li>🚀 Profile boosts</li>
  </ul>
</div>

<p>Renew now to get back all your premium perks and keep connecting!</p>

<p style="text-align:center;margin:30px 0">
  <a href="{premium_url}" style="background:#e91e8c;color:#fff;padding:12px 28px;border-radius:6px;text-decoration:none;font-weight:600">Renew Premium →</a>
</p>

<p>We hope to have you back soon,<br><strong>The {app_name} Team</strong></p>
<hr style="border:none;border-top:1px solid #eee;margin:24px 0">
<p style="font-size:12px;color:#999">Visit your <a href="{app_url}/account/settings">account settings</a> to manage your subscription.</p>
HTML,
            ],

            // ── Login Alert ──────────────────────────────────────────────────
            [
                'key'       => 'login_alert',
                'name'      => 'Login Alert',
                'subject'   => 'New Login to Your {app_name} Account',
                'variables' => ['{user_name}', '{ip}', '{device}', '{login_time}', '{app_name}', '{app_url}', '{settings_url}'],
                'body'      => <<<HTML
<h2>New Login Detected</h2>
<p>Hi <strong>{user_name}</strong>, we noticed a new sign-in to your {app_name} account.</p>

<div style="background:#f8fafc;border:1px solid #e2e8f0;padding:16px 20px;border-radius:6px;margin:20px 0">
  <table style="width:100%;border-collapse:collapse">
    <tr><td style="padding:4px 0;color:#64748b;width:120px">Time:</td><td><strong>{login_time}</strong></td></tr>
    <tr><td style="padding:4px 0;color:#64748b">IP Address:</td><td><strong>{ip}</strong></td></tr>
    <tr><td style="padding:4px 0;color:#64748b">Device:</td><td><strong>{device}</strong></td></tr>
  </table>
</div>

<p>If this was you, no action is needed — enjoy the app! 💕</p>
<p>If you don't recognise this login, please <strong>secure your account immediately</strong> by changing your password.</p>

<p style="text-align:center;margin:30px 0">
  <a href="{settings_url}" style="background:#dc2626;color:#fff;padding:12px 28px;border-radius:6px;text-decoration:none;font-weight:600">Secure My Account</a>
</p>

<p>Stay safe,<br><strong>The {app_name} Team</strong></p>
<hr style="border:none;border-top:1px solid #eee;margin:24px 0">
<p style="font-size:12px;color:#999">You're receiving this email because login alerts are enabled for your account. You can turn them off from your <a href="{app_url}/account/settings">notification settings</a>.</p>
HTML,
            ],

            // ── Like Reset ───────────────────────────────────────────────────
            [
                'key'       => 'like_reset',
                'name'      => 'Daily Likes Reset',
                'subject'   => '💖 Your daily likes have reset — go find your match!',
                'variables' => ['{user_name}', '{app_name}', '{swipe_url}', '{premium_url}'],
                'body'      => <<<HTML
<h2>Your Likes Have Reset! 💖</h2>
<p>Hey <strong>{user_name}</strong>! Your 15 free daily likes have just reset.</p>

<div style="background:#fdf2f8;border-left:4px solid #e91e8c;padding:16px 20px;border-radius:6px;margin:20px 0">
  Head back and discover new profiles waiting for you. Your next match could be just a swipe away!
</div>

<p style="text-align:center;margin:30px 0">
  <a href="{swipe_url}" style="background:#e91e8c;color:#fff;padding:12px 28px;border-radius:6px;text-decoration:none;font-weight:600">Start Swiping Now →</a>
</p>

<p>Upgrade to Premium anytime for <strong>unlimited likes</strong> and more!</p>
<p style="text-align:center;margin:20px 0">
  <a href="{premium_url}" style="color:#e91e8c;text-decoration:none;font-weight:600">✨ Get Premium →</a>
</p>

<p>With love,<br><strong>The {app_name} Team</strong></p>
HTML,
            ],

            // ── Email OTP Verification ───────────────────────────────────────
            [
                'key'       => 'email_otp',
                'name'      => 'Email OTP Verification Code',
                'subject'   => 'Your {app_name} verification code: {otp}',
                'variables' => ['{user_name}', '{otp}', '{app_name}', '{expires}'],
                'body'      => <<<HTML
<h2>Your Verification Code 🔐</h2>
<p>Hi <strong>{user_name}</strong>, here is your one-time verification code:</p>

<div style="text-align:center;margin:32px 0">
  <div style="display:inline-block;background:#f3f4f6;border:2px dashed #e91e8c;border-radius:16px;padding:20px 40px">
    <span style="font-size:2.5rem;font-weight:700;letter-spacing:.35em;color:#1a1a2e;font-family:monospace">{otp}</span>
  </div>
</div>

<p style="text-align:center;color:#64748b;font-size:.9rem">This code expires in <strong>{expires}</strong>. Do not share it with anyone.</p>

<p>If you didn't request this code, you can safely ignore this email — your account is still secure.</p>

<p>Best,<br><strong>The {app_name} Team</strong></p>
HTML,
            ],

            // ── Feature Request Reply ────────────────────────────────────────
            [
                'key'       => 'feature_request_reply',
                'name'      => 'Feature Request Reply',
                'subject'   => 'Re: {request_type} — {request_title}',
                'variables' => ['{user_name}', '{request_type}', '{request_title}', '{request_body}', '{admin_response}', '{app_name}', '{submit_url}'],
                'body'      => <<<HTML
<h2>Re: {request_type} — {request_title}</h2>
<p>Hi {user_name},</p>
<p>Thank you for taking the time to submit your {request_type}. Here is our response:</p>

<div style="background:#fdf2f8;border-left:4px solid #e91e8c;padding:16px 20px;border-radius:6px;margin:20px 0">
  {admin_response}
</div>

<p><strong>Your original submission:</strong></p>
<blockquote style="border-left:3px solid #cbd5e1;margin:0;padding:12px 16px;color:#64748b">
  <strong>{request_title}</strong><br>{request_body}
</blockquote>

<p style="text-align:center;margin:30px 0">
  <a href="{submit_url}" style="background:#e91e8c;color:#fff;padding:12px 28px;border-radius:6px;text-decoration:none;font-weight:600">Submit Another Request</a>
</p>

<p>Thanks again for helping us improve {app_name}!</p>
<p>Warm regards,<br><strong>The {app_name} Team</strong></p>
<hr style="border:none;border-top:1px solid #eee;margin:24px 0">
<p style="font-size:12px;color:#999">This is a reply to a {request_type} you submitted at {app_name}. If you did not submit this, please ignore this email.</p>
HTML,
            ],

        ];

        foreach ($templates as $tpl) {
            EmailTemplate::updateOrCreate(
                ['key' => $tpl['key']],
                $tpl
            );
        }
    }
}
