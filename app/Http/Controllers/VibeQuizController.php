<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VibeQuizController extends Controller
{
    /** The 5 vibe quiz questions with answer-to-vibe mappings. */
    private const QUESTIONS = [
        [
            'text'    => "It's Friday night — what's your ideal plan?",
            'answers' => [
                'a' => ['label' => '🏕️ Something outdoors/adventurous',   'vibe' => 'adventurer'],
                'b' => ['label' => '🛋️ Netflix and takeout at home',        'vibe' => 'homebody'],
                'c' => ['label' => '🎉 Out with a big group of friends',    'vibe' => 'social_butterfly'],
                'd' => ['label' => '📚 A quiet evening reading/learning',   'vibe' => 'intellectual'],
            ],
        ],
        [
            'text'    => 'What kind of first date sounds most appealing?',
            'answers' => [
                'a' => ['label' => '🧗 Rock climbing or hiking',  'vibe' => 'adventurer'],
                'b' => ['label' => '☕ Coffee and deep conversation', 'vibe' => 'intellectual'],
                'c' => ['label' => '🎊 Festival or concert',       'vibe' => 'social_butterfly'],
                'd' => ['label' => '🍷 Romantic dinner for two',   'vibe' => 'romantic'],
            ],
        ],
        [
            'text'    => 'Which best describes your energy level?',
            'answers' => [
                'a' => ['label' => '⚡ Always on the go',         'vibe' => 'adventurer'],
                'b' => ['label' => '🌿 Calm and grounded',        'vibe' => 'homebody'],
                'c' => ['label' => '🌪️ High energy in bursts',    'vibe' => 'wild_card'],
                'd' => ['label' => '🌸 Warm and steady',          'vibe' => 'romantic'],
            ],
        ],
        [
            'text'    => 'How do you prefer to spend your weekends?',
            'answers' => [
                'a' => ['label' => '🗺️ Exploring new places',     'vibe' => 'adventurer'],
                'b' => ['label' => '🏠 Home with close friends',  'vibe' => 'homebody'],
                'c' => ['label' => '🎭 Events and social scenes', 'vibe' => 'social_butterfly'],
                'd' => ['label' => '💡 DIY projects or creative pursuits', 'vibe' => 'intellectual'],
            ],
        ],
        [
            'text'    => 'Pick your life motto:',
            'answers' => [
                'a' => ['label' => '"Life is an adventure, not a destination." ', 'vibe' => 'adventurer'],
                'b' => ['label' => '"The best things in life are simple."',       'vibe' => 'homebody'],
                'c' => ['label' => '"The more, the merrier!"',                    'vibe' => 'social_butterfly'],
                'd' => ['label' => '"Experience love deeply."',                   'vibe' => 'romantic'],
            ],
        ],
    ];

    private const VIBE_META = [
        'adventurer'       => ['emoji' => '🏕️', 'label' => 'The Adventurer',       'desc' => 'You live for experiences and love exploring the unknown.'],
        'homebody'         => ['emoji' => '🛋️', 'label' => 'The Homebody',         'desc' => "Comfort and coziness are your love language."],
        'social_butterfly' => ['emoji' => '🦋', 'label' => 'The Social Butterfly', 'desc' => 'You light up any room and thrive in community.'],
        'intellectual'     => ['emoji' => '🧠', 'label' => 'The Intellectual',     'desc' => 'Deep conversations and growth are what you seek.'],
        'romantic'         => ['emoji' => '🌹', 'label' => 'The Romantic',         'desc' => 'Love, warmth and deep connection fuel your world.'],
        'wild_card'        => ['emoji' => '🃏', 'label' => 'The Wild Card',        'desc' => 'Unpredictable, spontaneous, and full of surprises.'],
    ];

    public function show(): \Illuminate\View\View
    {
        $questions = self::QUESTIONS;
        $existing  = auth()->user()->profile?->vibe_badge;
        $vibeMeta  = self::VIBE_META;
        return view('vibe.quiz', compact('questions', 'existing', 'vibeMeta'));
    }

    public function submit(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'answers'   => 'required|array|size:5',
            'answers.*' => 'required|in:a,b,c,d',
        ]);

        /** @var array<string,int> Tally vibe scores */
        $scores = [];
        foreach ($request->input('answers') as $qIndex => $answer) {
            $q     = self::QUESTIONS[$qIndex] ?? null;
            if (!$q) continue;
            $vibe  = $q['answers'][$answer]['vibe'] ?? null;
            if ($vibe) {
                $scores[$vibe] = ($scores[$vibe] ?? 0) + 1;
            }
        }

        arsort($scores);
        $winner = array_key_first($scores) ?? 'wild_card';

        $user = $request->user();
        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            ['vibe_badge' => $winner]
        );

        return redirect()->route('vibe.quiz')
            ->with('vibe_result', $winner);
    }
}
