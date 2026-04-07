<?php

namespace App\Notifications;

use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProfileReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly array $missingItems,
        public readonly int   $onboardingStep,
        public readonly string $adminMessage = '',
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $appName   = config('app.name', 'HeartsConnect');
        $setupUrl  = config('app.url') . '/setup/step/' . max(1, $this->onboardingStep + 1);
        $missing   = $this->missingItems;

        // Try dynamic email template first
        $tpl = EmailTemplate::findByKey('profile_reminder');
        if ($tpl) {
            $missingHtml = implode('', array_map(fn($item) => "<li>{$item}</li>", $missing));
            ['subject' => $subject, 'html' => $html] = $tpl->render([
                '{user_name}'    => $notifiable->name,
                '{app_name}'     => $appName,
                '{app_url}'      => config('app.url'),
                '{setup_url}'    => $setupUrl,
                '{missing_list}' => $missingHtml,
                '{admin_message}'=> $this->adminMessage,
            ]);
            return (new MailMessage)
                ->subject($subject)
                ->view('emails.dynamic', ['html' => $html, 'subject' => $subject]);
        }

        // Fallback: inline markdown
        $mail = (new MailMessage)
            ->subject("Complete your {$appName} profile 💕")
            ->greeting("Hey {$notifiable->name}!")
            ->line("You're so close to connecting with great people. Your profile is missing a few things:");

        foreach ($missing as $item) {
            $mail->line("→ **{$item}**");
        }

        if ($this->adminMessage) {
            $mail->line('---')
                 ->line($this->adminMessage);
        }

        return $mail
            ->action('Complete My Profile →', $setupUrl)
            ->line("It only takes a minute — and once done, you'll start getting matches right away!")
            ->salutation("With ❤️, The {$appName} Team");
    }
}
