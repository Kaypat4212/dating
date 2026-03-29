<?php

namespace App\Notifications;

use App\Models\TravelInterest;
use App\Models\TravelPlan;
use App\Models\User;
use App\Services\MailSettingsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Sent to the travel plan OWNER when another user expresses interest in their plan.
 */
class TravelInterestReceivedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly TravelInterest $interest,
        public readonly User $interestedUser,
        public readonly TravelPlan $plan
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
        $reviewUrl   = route('travel.index');

        return (new MailMessage)
            ->subject("✈️ {$this->interestedUser->name} is interested in your trip to {$destination}!")
            ->greeting("Hi {$notifiable->name}!")
            ->line("**{$this->interestedUser->name}** has expressed interest in your travel plan to **{$destination}**.")
            ->line("Travel dates: {$this->plan->travel_from->format('M d, Y')} – {$this->plan->travel_to->format('M d, Y')}")
            ->action('Review & Respond', $reviewUrl)
            ->line('Head to your travel plans page to accept or decline their interest.')
            ->salutation('— The ' . config('app.name') . ' Team');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'               => 'travel_interest_received',
            'interest_id'        => $this->interest->id,
            'plan_id'            => $this->plan->id,
            'interested_user_id' => $this->interestedUser->id,
            'interested_user_name' => $this->interestedUser->name,
            'destination'        => $this->plan->destination . ', ' . $this->plan->destination_country,
            'message'            => "{$this->interestedUser->name} is interested in your trip to {$this->plan->destination}!",
            'url'                => route('travel.index'),
        ];
    }
}
