<?php

namespace App\Http\Controllers;

use App\Models\MatchQuestion;
use App\Models\MatchQuestionAnswer;
use App\Models\UserMatch;
use Illuminate\Http\Request;

class MatchQuestionController extends Controller
{
    public function index(Request $request): \Illuminate\View\View
    {
        $user  = $request->user();
        $today = now()->toDateString();

        // Pick today's question deterministically: hash(date) % total_active_questions
        $questions = MatchQuestion::where('is_active', true)->get();
        if ($questions->isEmpty()) {
            return view('match-question.index', [
                'question'  => null,
                'matches'   => collect(),
                'myAnswers' => collect(),
                'today'     => $today,
            ]);
        }
        $index    = abs(crc32($today)) % $questions->count();
        $question = $questions[$index];

        // Load user's active matches with eager load
        $matches = UserMatch::where(function ($q) use ($user) {
            $q->where('user1_id', $user->id)->orWhere('user2_id', $user->id);
        })->where('is_active', true)
          ->with(['user1.primaryPhoto', 'user2.primaryPhoto'])
          ->latest('matched_at')
          ->take(10)
          ->get();

        // Load answers this user already gave today
        $myAnswers = MatchQuestionAnswer::where('user_id', $user->id)
            ->where('question_id', $question->id)
            ->where('answered_date', $today)
            ->pluck('answer', 'match_id');

        // Load partner answers for each match
        $partnerAnswers = MatchQuestionAnswer::whereIn('match_id', $matches->pluck('id'))
            ->where('question_id', $question->id)
            ->where('answered_date', $today)
            ->where('user_id', '!=', $user->id)
            ->pluck('answer', 'match_id');

        return view('match-question.index', compact(
            'question', 'matches', 'myAnswers', 'partnerAnswers', 'today'
        ));
    }

    public function answer(Request $request): \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'match_id'    => 'required|integer',
            'question_id' => 'required|integer',
            'answer'      => 'required|string|max:500',
        ]);

        $user  = $request->user();
        $today = now()->toDateString();

        // Verify user is part of this match
        $match = UserMatch::where('id', $request->match_id)
            ->where(function ($q) use ($user) {
                $q->where('user1_id', $user->id)->orWhere('user2_id', $user->id);
            })->firstOrFail();

        MatchQuestionAnswer::updateOrCreate(
            [
                'match_id'       => $match->id,
                'user_id'        => $user->id,
                'question_id'    => $request->question_id,
                'answered_date'  => $today,
            ],
            ['answer' => mb_substr(strip_tags($request->input('answer')), 0, 500)]
        );

        if ($request->expectsJson()) {
            return response()->json(['saved' => true]);
        }
        return back()->with('success', 'Answer saved!');
    }
}
