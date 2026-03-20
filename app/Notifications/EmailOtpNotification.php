<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmailOtpNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly string $otp) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $siteName = \App\Models\SiteSetting::get('site_name', config('app.name'));

        // Try DB template first
        $tpl = \App\Models\EmailTemplate::findByKey('email_otp');
        if ($tpl) {
            ['subject' => $subject, 'html' => $html] = $tpl->render([
                '{user_name}' => $notifiable->name,
                '{otp}'       => $this->otp,
                '{app_name}'  => $siteName,
                '{expires}'   => '15 minutes',
            ]);
            return (new MailMessage)
                ->subject($subject)
                ->view('emails.dynamic', compact('html', 'subject'));
        }

        return (new MailMessage)
            ->subject("Your {$siteName} verification code")
            ->greeting("Hello {$notifiable->name}!")
            ->line("Use the code below to verify your email address. It expires in **15 minutes**.")
            ->line('')
            ->line("## {$this->otp}")
            ->line('')
            ->line("If you didn't request this, you can safely ignore this email.")
            ->salutation("— The {$siteName} team");
    }
}
