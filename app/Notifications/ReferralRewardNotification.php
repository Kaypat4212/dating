<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReferralRewardNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly string $referredName,
        private readonly int    $rewardDays,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $siteName = \App\Models\SiteSetting::get('site_name', config('app.name'));

        return (new MailMessage)
            ->subject("🎁 You earned {$this->rewardDays} days of free Premium!")
            ->greeting("Hi {$notifiable->name}!")
            ->line("Your friend **{$this->referredName}** just joined {$siteName} and verified their email.")
            ->line("As a thank-you for the referral, we've added **{$this->rewardDays} free Premium days** to your account!")
            ->action('View My Account', url('/account'))
            ->line('Keep sharing your invite link to earn more rewards!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'    => 'referral_reward',
            'message' => "🎁 You earned {$this->rewardDays} free Premium days because {$this->referredName} joined using your invite link!",
            'url'     => '/account',
        ];
    }
}
