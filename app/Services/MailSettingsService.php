<?php

namespace App\Services;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\Config;

class MailSettingsService
{
    /**
     * Check if a specific email notification type is enabled by the admin.
     * Falls back to true (enabled) if the setting hasn't been saved yet.
     *
     * @param  string  $key  e.g. 'email_login_alert_enabled'
     */
    public static function emailEnabled(string $key): bool
    {
        $value = SiteSetting::get($key, true);

        // SiteSetting stores booleans as '1'/'0'/'' strings
        return filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? true;
    }

    /**
     * Read mail settings from SiteSetting and apply them to Laravel's runtime
     * mail config so that all outgoing mail uses the admin-configured transport.
     */
    public static function applyFromSettings(): void
    {
        $driver = SiteSetting::get('mail_driver') ?: config('mail.default', 'log');

        Config::set('mail.default', $driver);

        // From address / name
        if ($addr = SiteSetting::get('mail_from_address')) {
            Config::set('mail.from.address', $addr);
        }
        if ($name = SiteSetting::get('mail_from_name')) {
            Config::set('mail.from.name', $name);
        }

        match ($driver) {
            'smtp'     => static::applySmtp(),
            'mailhog'  => static::applyMailhog(),
            'sendmail' => static::applySendmail(),
            'mailgun'  => static::applyMailgun(),
            'ses'      => static::applySes(),
            'postmark' => static::applyPostmark(),
            'resend'   => static::applyResend(),
            default    => null,
        };
    }

    // ── SMTP ────────────────────────────────────────────────────────────────

    private static function applySmtp(): void
    {
        $encryption = SiteSetting::get('mail_smtp_encryption') ?: 'tls';

        // Symfony Mailer uses 'scheme', not 'encryption':
        //   tls  → 'smtp'  (STARTTLS, typically port 587)
        //   ssl  → 'smtps' (SSL/TLS,  typically port 465)
        //   ''   → 'smtp'  (plain,    typically port 25)
        $scheme = match ($encryption) {
            'ssl'   => 'smtps',
            default => 'smtp',
        };

        Config::set('mail.mailers.smtp', [
            'transport' => 'smtp',
            'scheme'    => $scheme,
            'host'      => SiteSetting::get('mail_smtp_host') ?: '127.0.0.1',
            'port'      => (int) (SiteSetting::get('mail_smtp_port') ?: 587),
            'username'  => SiteSetting::get('mail_smtp_username') ?: null,
            'password'  => SiteSetting::get('mail_smtp_password') ?: null,
            'timeout'   => null,
        ]);
    }

    // ── Mailhog (dev / local) ────────────────────────────────────────────────

    private static function applyMailhog(): void
    {
        $host = SiteSetting::get('mail_mailhog_host') ?: '127.0.0.1';
        $port = (int) (SiteSetting::get('mail_mailhog_port') ?: 1025);

        Config::set('mail.mailers.mailhog', [
            'transport' => 'smtp',
            'scheme'    => 'smtp',
            'host'      => $host,
            'port'      => $port,
            'username'  => null,
            'password'  => null,
            'timeout'   => 5,
        ]);
    }

    // ── Sendmail / cPanel PHP Mail ───────────────────────────────────────────

    private static function applySendmail(): void
    {
        $path = SiteSetting::get('mail_sendmail_path') ?: '/usr/sbin/sendmail -bs -i';
        Config::set('mail.mailers.sendmail.path', $path);
    }

    // ── Mailgun ──────────────────────────────────────────────────────────────

    private static function applyMailgun(): void
    {
        Config::set('services.mailgun.domain',   SiteSetting::get('mail_mailgun_domain'));
        Config::set('services.mailgun.secret',   SiteSetting::get('mail_mailgun_secret'));
        Config::set('services.mailgun.endpoint', SiteSetting::get('mail_mailgun_endpoint') ?: 'api.mailgun.net');
    }

    // ── Amazon SES ───────────────────────────────────────────────────────────

    private static function applySes(): void
    {
        Config::set('services.ses.key',    SiteSetting::get('mail_ses_key'));
        Config::set('services.ses.secret', SiteSetting::get('mail_ses_secret'));
        Config::set('services.ses.region', SiteSetting::get('mail_ses_region') ?: 'us-east-1');
    }

    // ── Postmark ─────────────────────────────────────────────────────────────

    private static function applyPostmark(): void
    {
        Config::set('services.postmark.token', SiteSetting::get('mail_postmark_token'));
    }

    // ── Resend ───────────────────────────────────────────────────────────────

    private static function applyResend(): void
    {
        Config::set('services.resend.key', SiteSetting::get('mail_resend_key'));
    }

    // ── Queue ─────────────────────────────────────────────────────────────────

    /**
     * Apply the admin-configured queue connection.
     * Defaults to 'sync' so emails fire immediately on shared/cPanel hosting
     * (no queue worker required). Change to 'database' only if you have a
     * persistent `php artisan queue:work` process running on the server.
     */
    public static function applyQueueSettings(): void
    {
        $connection = SiteSetting::get('queue_connection') ?: 'sync';
        Config::set('queue.default', $connection);
    }
}
