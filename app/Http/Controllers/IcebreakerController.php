<?php

namespace App\Http\Controllers;

use App\Models\IcebreakerAnswer;
use App\Models\IcebreakerQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class IcebreakerController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        $myAnswers = IcebreakerAnswer::where('user_id', $user->id)
            ->with('question')
            ->get()
            ->keyBy('question_id');

        $questions = IcebreakerQuestion::where('is_active', true)
            ->orderBy('order')
            ->get();

        return view('icebreaker.index', compact('questions', 'myAnswers'));
    }

    public function answer(Request $request): \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'question_id'    => ['required', 'exists:icebreaker_questions,id'],
            'answer'         => ['nullable', 'string', 'max:500'],
            'choice'         => ['nullable', 'in:a,b'],
            'show_on_profile'=> ['nullable', 'boolean'],
        ]);

        IcebreakerAnswer::updateOrCreate(
            ['user_id' => Auth::id(), 'question_id' => $request->question_id],
            [
                'answer'          => $request->answer,
                'choice'          => $request->choice,
                'show_on_profile' => $request->boolean('show_on_profile', true),
            ]
        );

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Answer saved!');
    }

    public function destroy(IcebreakerAnswer $icebreakerAnswer): \Illuminate\Http\RedirectResponse
    {
        abort_unless($icebreakerAnswer->user_id === Auth::id(), 403, 'You can only delete your own icebreaker answers.');
        $icebreakerAnswer->delete();
        return back()->with('success', 'Answer removed.');
    }

    public function questions(): \Illuminate\Http\JsonResponse
    {
        $questions = IcebreakerQuestion::where('is_active', true)
            ->inRandomOrder()
            ->limit(5)
            ->get(['id', 'question', 'type', 'option_a', 'option_b']);

        return response()->json($questions);
    }
}
