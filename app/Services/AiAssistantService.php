<?php

namespace App\Services;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\Http;

class AiAssistantService
{
    // ── Template fallbacks (used when no API key is configured) ──────────────

    private const REPLY_TEMPLATES = [
        "That's really interesting! Tell me more 😊",
        "Haha, I love that! What's the story behind it?",
        "I totally relate to that! How long have you been into it?",
        "You seem like such a fun person! What do you enjoy doing on weekends?",
        "That's awesome! I'd love to hear more about your experiences with that.",
    ];

    private const TOPIC_TEMPLATES = [
        "What's the most spontaneous thing you've ever done?",
        "If you could travel anywhere tomorrow, where would you go?",
        "What's your ideal weekend look like?",
        "What show have you been binging lately?",
        "What's something most people wouldn't guess about you?",
        "Do you have any hidden talents?",
        "What's the best meal you've ever had?",
    ];

    private const ICEBREAKER_TEMPLATES = [
        "Hey! Your profile really caught my eye — how's your day going? 😊",
        "I had to say hi — you seem like someone with great stories. What's the most interesting thing you've done recently?",
        "Your vibe is contagious! What are you passionate about these days?",
    ];

    private const BIO_TEMPLATES = [
        "Adventurer by day, Netflix addict by night 🌙 Looking for someone to share laughs, good food, and real conversations.",
        "I believe life's too short for boring coffee and bad vibes ☕ Passionate about travel, music, and finding the good in every day.",
        "Genuine, curious, and always up for new experiences. I love deep conversations, spontaneous adventures, and warm smiles 😊",
    ];

    private const USERNAME_TEMPLATES = [
        'sunrise_seeker', 'bold_and_kind', 'steady_soul', 'open_heart_x',
        'just_be_real', 'calm_vibes_99', 'curious_connect', 'warmth_finder',
        'brave_hello', 'true_connections',
    ];

    private const REPHRASE_TEMPLATES = [
        "I was just thinking about you — hope your day is going amazing! 😊",
        "Hey! I wanted to reach out and say I really enjoy our conversations 💬",
        "I had to say — talking with you is genuinely fun. How's everything going?",
    ];

    // ────────────────────────────────────────────────────────────────────────

    /**
     * Generate AI suggestions.
     *
     * @param  string  $type      reply | topics | icebreaker | bio
     * @param  array   $context   Arbitrary context data for the prompt
     * @return array              Up to 3 suggestion strings
     */
    public function suggest(string $type, array $context = []): array
    {
        if (!SiteSetting::get('ai_enabled', false)) {
            return $this->fallback($type);
        }

        $apiKey = SiteSetting::get('ai_groq_api_key');
        if (!$apiKey) {
            return $this->fallback($type);
        }

        $model  = SiteSetting::get('ai_groq_model', 'llama-3.1-8b-instant');
        $prompt = $this->buildPrompt($type, $context);

        try {
            $response = Http::timeout(15)
                ->withToken($apiKey)
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model'       => $model,
                    'temperature' => 0.8,
                    'max_tokens'  => 400,
                    'messages'    => [
                        ['role' => 'system', 'content' => $this->systemPrompt()],
                        ['role' => 'user',   'content' => $prompt],
                    ],
                ]);

            if ($response->failed()) {
                return $this->fallback($type);
            }

            $text = $response->json('choices.0.message.content', '');
            return $this->parseList($text) ?: $this->fallback($type);

        } catch (\Throwable) {
            return $this->fallback($type);
        }
    }

    // ── Prompt builders ──────────────────────────────────────────────────────

    private function systemPrompt(): string
    {
        return 'You are a friendly dating coach helping users craft warm, genuine, and engaging messages. '
             . 'Always respond with exactly 3 numbered suggestions (1. ... 2. ... 3. ...). '
             . 'Keep each suggestion under 100 words. Be natural, warm, and never creepy or pushy. '
             . 'Do not include any extra commentary — only the 3 numbered suggestions.';
    }

    private function buildPrompt(string $type, array $ctx): string
    {
        return match ($type) {
            'reply' => sprintf(
                "I'm chatting with %s on a dating app. Here are the last few messages:\n%s\n\n"
                . "Give me 3 genuine, friendly reply options I can send next.",
                $ctx['partner_name'] ?? 'my match',
                $ctx['last_messages'] ?? '(no messages yet)'
            ),

            'topics' => sprintf(
                "I'm talking with %s on a dating app. Their interests: %s. "
                . "Give me 3 interesting conversation topic ideas or questions I can ask them.",
                $ctx['partner_name'] ?? 'my match',
                $ctx['partner_interests'] ?? 'not specified'
            ),

            'icebreaker' => sprintf(
                "I want to send the first message to %s on a dating app. "
                . "Their bio says: \"%s\". Their interests: %s. "
                . "Give me 3 charming, original opening messages.",
                $ctx['partner_name'] ?? 'someone',
                $ctx['partner_bio'] ?? 'not shared',
                $ctx['partner_interests'] ?? 'not specified'
            ),

            'bio' => sprintf(
                "Help me write a dating profile bio. "
                . "My interests: %s. My age: %s. My occupation: %s. "
                . "Give me 3 short, authentic bio options (2–3 sentences each).",
                $ctx['interests'] ?? 'not specified',
                $ctx['age'] ?? 'not specified',
                $ctx['occupation'] ?? 'not specified'
            ),
            'username' => sprintf(
                "Suggest 5 creative, friendly, community-appropriate usernames for a dating app. "
                . "The person's name is \"%s\" and their interests include: %s. "
                . "Rules: lowercase letters, numbers, underscores only; 5\u201320 characters; no rude, sexual, or offensive words; nothing that implies hate or violence. "
                . "Return only the 5 usernames, one per line, no numbering, no explanation.",
                $ctx['name'] ?? 'user',
                $ctx['interests'] ?? 'not specified'
            ),
            'rephrase' => sprintf(
                "I'm chatting on a dating app. Rewrite this draft message to sound warmer, more engaging, "
                . "and natural — keep the same meaning but make it feel more genuine and inviting:\n\n\"%s\"\n\n"
                . "Return exactly 1 rewritten version. No explanation, no quotes, no numbering — just the rewritten message.",
                $ctx['draft'] ?? ''
            ),
            default => 'Give me 3 friendly conversation starters for a dating app.',
        };
    }

    // ── Parse "1. ... 2. ... 3. ..." from the model's reply ─────────────────

    private function parseList(string $text): array
    {
        $lines = preg_split('/\n+/', trim($text));
        $results = [];
        foreach ($lines as $line) {
            $cleaned = preg_replace('/^\d+[\.\)]\s*/', '', trim($line));
            if ($cleaned !== '') {
                $results[] = $cleaned;
            }
            if (count($results) >= 3) break;
        }
        return count($results) >= 1 ? $results : [];
    }

    // ── AI Chat (open-ended assistant conversation) ───────────────────────────

    /**
     * Respond to a free-form chat message with conversation history.
     *
     * @param  array   $history  [['role'=>'user'|'assistant', 'body'=>'…'], …]
     * @param  string  $message  The new user message
     * @return string            The assistant's reply
     */
    public function chat(array $history, string $message): string
    {
        if (!SiteSetting::get('ai_enabled', false)) {
            return $this->chatFallback();
        }

        $apiKey = SiteSetting::get('ai_groq_api_key');
        if (!$apiKey) {
            return $this->chatFallback();
        }

        $model    = SiteSetting::get('ai_groq_model', 'llama-3.1-8b-instant');
        $messages = [['role' => 'system', 'content' => $this->chatSystemPrompt()]];

        foreach ($history as $h) {
            $messages[] = ['role' => $h['role'], 'content' => $h['body']];
        }
        $messages[] = ['role' => 'user', 'content' => $message];

        try {
            $response = Http::timeout(20)
                ->withToken($apiKey)
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model'       => $model,
                    'temperature' => 0.7,
                    'max_tokens'  => 500,
                    'messages'    => $messages,
                ]);

            if ($response->failed()) {
                return $this->chatFallback();
            }

            return trim($response->json('choices.0.message.content', '')) ?: $this->chatFallback();

        } catch (\Throwable) {
            return $this->chatFallback();
        }
    }

    private function chatSystemPrompt(): string
    {
        $appName = config('app.name', 'this app');
        return "You are a friendly AI dating assistant built into {$appName}. "
             . 'You help users with dating advice, relationship tips, profile improvement, conversation starters, and general questions. '
             . 'Be warm, honest, practical, and encouraging. Keep responses concise — 2-3 short paragraphs at most. '
             . 'If asked who you are, say you are the app\'s built-in AI assistant. '
             . 'Only discuss topics related to dating, relationships, self-confidence, and personal connections.';
    }

    private function chatFallback(): string
    {
        $replies = [
            "Great question! My top tip: be genuine — authenticity always shines through on a dating profile. Show what makes you uniquely you!",
            "Dating can feel overwhelming, but remember: quality connections matter far more than quantity. Take your time and enjoy the journey.",
            "One of the best things you can do is keep your profile bio specific and personal. Instead of listing hobbies, share a little story or describe what lights you up.",
        ];
        return $replies[array_rand($replies)];
    }

    // ── Template fallbacks ───────────────────────────────────────────────────

    private function fallback(string $type): array
    {
        $pool = match ($type) {
            'reply'       => self::REPLY_TEMPLATES,
            'topics'      => self::TOPIC_TEMPLATES,
            'icebreaker'  => self::ICEBREAKER_TEMPLATES,
            'bio'         => self::BIO_TEMPLATES,
            'username'    => self::USERNAME_TEMPLATES,
            'rephrase'    => self::REPHRASE_TEMPLATES,
            default       => self::TOPIC_TEMPLATES,
        };

        shuffle($pool);
        return array_slice($pool, 0, 3);
    }
}
