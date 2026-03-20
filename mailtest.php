<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

/** @var \Illuminate\Mail\Mailer $mailer */
$mailer = app('mailer');

$mailer->raw(
    'MailHog test from HeartsConnect — if you see this, mail is working! ✅',
    function (\Illuminate\Mail\Message $msg): void {
        $msg->to('test@example.com')->subject('MailHog Test ✅');
    }
);

echo 'Mail sent successfully! Check MailHog at http://localhost:8025' . PHP_EOL;
