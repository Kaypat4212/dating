<?php

namespace App\Filament\Widgets;

use App\Models\SiteSetting;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Http;

class AdminAiAssistantWidget extends Widget
{
    protected string $view = 'filament.widgets.admin-ai-assistant';

    protected static ?int $sort = 10;

    protected int | string | array $columnSpan = 'full';

    // ── Livewire state ───────────────────────────────────────────────────────

    public string $prompt = '';

    public string $response = '';

    public bool $loading = false;

    public string $error = '';

    /** Quick-prompt suggestions shown as chips */
    public array $suggestions = [
        'Write a welcome email for new members',
        'Suggest 5 dating profile bio tips',
        'Draft a push notification for a new feature',
        'Write FAQ answer: "Is my data private?"',
        'Create a 7-day onboarding email sequence outline',
        'Suggest improvements for user retention',
    ];

    // ── Actions ──────────────────────────────────────────────────────────────

    public function useSuggestion(string $text): void
    {
        $this->prompt = $text;
        $this->ask();
    }

    public function ask(): void
    {
        $this->error    = '';
        $this->response = '';

        $prompt = trim($this->prompt);
        if ($prompt === '') {
            $this->error = 'Please enter a prompt first.';
            return;
        }

        $apiKey = SiteSetting::get('ai_groq_api_key', '');
        $model  = SiteSetting::get('ai_groq_model', 'llama-3.1-8b-instant');

        if (empty($apiKey)) {
            $this->error = 'No Groq API key configured. Go to Site Settings → AI Assistant to add one.';
            return;
        }

        $this->loading = true;

        try {
            $res = Http::withToken($apiKey)
                ->timeout(30)
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model'       => $model,
                    'max_tokens'  => 1024,
                    'temperature' => 0.7,
                    'messages'    => [
                        [
                            'role'    => 'system',
                            'content' => 'You are a helpful admin assistant for HeartsConnect, a modern dating platform. '
                                       . 'Help with content writing, marketing copy, user communication, moderation decisions, '
                                       . 'and platform management. Keep responses concise and actionable.',
                        ],
                        [
                            'role'    => 'user',
                            'content' => $prompt,
                        ],
                    ],
                ]);

            if ($res->successful()) {
                $this->response = $res->json('choices.0.message.content', '');
            } else {
                $this->error = 'API error: ' . ($res->json('error.message') ?? $res->status());
            }
        } catch (\Exception $e) {
            $this->error = 'Request failed: ' . $e->getMessage();
        } finally {
            $this->loading = false;
        }
    }

    public function clearAll(): void
    {
        $this->prompt   = '';
        $this->response = '';
        $this->error    = '';
    }
}
