<?php

namespace App\Observers;

use App\Models\FlaggedKeyword;
use App\Models\KeywordFlag;
use App\Models\Message;
use App\Models\User;
use Filament\Notifications\Notification;

class MessageObserver
{
    public function created(Message $message): void
    {
        // Only scan text messages that have a body
        if ($message->type !== 'text' || blank($message->body)) {
            return;
        }

        $keywords = FlaggedKeyword::where('is_active', true)->get();
        if ($keywords->isEmpty()) {
            return;
        }

        $bodyLower = mb_strtolower($message->body);
        $matched   = [];

        foreach ($keywords as $keyword) {
            $word = mb_strtolower($keyword->word);
            // Use word-boundary-aware matching
            if (str_contains($bodyLower, $word)) {
                KeywordFlag::create([
                    'message_id'      => $message->id,
                    'keyword_id'      => $keyword->id,
                    'sender_id'       => $message->sender_id,
                    'conversation_id' => $message->conversation_id,
                    'matched_word'    => $keyword->word,
                    'is_reviewed'     => false,
                ]);
                $matched[] = $keyword->word;
            }
        }

        if (empty($matched)) {
            return;
        }

        // Silently notify all admin users via Filament's database notification bell
        $admins = User::role('admin')->get();
        $sender = $message->sender;

        $wordList  = implode(', ', array_unique($matched));
        $preview   = mb_substr($message->body, 0, 80) . (mb_strlen($message->body) > 80 ? '…' : '');

        foreach ($admins as $admin) {
            Notification::make()
                ->title('🚨 Flagged message detected')
                ->body("**{$sender?->name}** used: {$wordList}\n\"{$preview}\"")
                ->warning()
                ->sendToDatabase($admin);
        }
    }
}
