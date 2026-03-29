<?php

namespace App\Notifications;

use App\Models\Conversation;
use App\Models\TravelInterest;
use App\Models\TravelPlan;
use App\Models\User;
use App\Services\MailSettingsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Sent to the user who expressed interest, after the plan owner responds.
 * Works for both 'accepted' and 'declined' responses.
 */
class TravelInterestRespondedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly TravelInterest $interest,
        public readonly User $planOwner,
        public readonly TravelPlan $plan,
        public readonly string $status, // 'accepted' | 'declined'
        public readonly ?Conversation $conversation = null
    ) {}

    public function via(object $notifiable): array
    {
        $channels = ['database'];

        if (MailSettingsService::emailEnabled('email_feature_usage_enabled')) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        $destination = $this->plan->destination . ', ' . $this->plan->destination_country;

        if ($this->status === 'accepted') {
            $actionUrl = $this->conversation
                ? route('conversations.show', $this->conversation)
                : route('travel.index');

            return (new MailMessage)
                ->subject("🎉 {$this->planOwner->name} accepted your interest — {$destination}!")
                ->greeting("Great news, {$notifiable->name}!")
                ->line("**{$this->planOwner->name}** has accepted your interest in their trip to **{$destination}**.")
                ->line("Travel dates: {$this->plan->travel_from->format('M d, Y')} – {$this->plan->travel_to->format('M d, Y')}")
                ->action('Start Chatting', $actionUrl)
                ->line("A conversation has been opened for you — say hello and start planning!")
                ->salutation('— The ' . config('app.name') . ' Team');
        }

        // Declined
        return (new MailMessage)
            ->subject("Travel interest update — {$destination}")
            ->greeting("Hi {$notifiable->name},")
            ->line("Unfortunately **{$this->planOwner->name}** has declined your interest in their trip to **{$destination}**.")
            ->line("Don't worry — there are plenty of other travel buddies looking for companions.")
            ->action('Browse Travel Plans', route('travel.index'))
            ->salutation('— The ' . config('app.name') . ' Team');
    }

    public function toArray(object $notifiable): array
    {
        $isAccepted = $this->status === 'accepted';

        return [
            'type'          => 'travel_interest_responded',
            'interest_id'   => $this->interest->id,
            'plan_id'       => $this->plan->id,
            'plan_owner_id' => $this->planOwner->id,
            'plan_owner_name' => $this->planOwner->name,
            'destination'   => $this->plan->destination . ', ' . $this->plan->destination_country,
            'status'        => $this->status,
            'message'       => $isAccepted
                ? "{$this->planOwner->name} accepted your travel interest! Start chatting."
                : "{$this->planOwner->name} has declined your travel interest.",
            'url'           => $isAccepted && $this->conversation
                ? route('conversations.show', $this->conversation)
                : route('travel.index'),
        ];
    }
}
