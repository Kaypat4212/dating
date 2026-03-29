<?php

namespace App\Http\Controllers;

use App\Models\Pet;
use App\Models\VoicePrompt;
use App\Models\VoicePromptQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProfileExtrasController extends Controller
{
    // ── Pets ────────────────────────────────────────────────────────────────

    public function petsIndex(): View
    {
        $user = Auth::user();
        $pets = Pet::where('user_id', $user->id)->orderBy('created_at')->get();
        return view('profile.extras.pets', compact('pets'));
    }

    public function storePet(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'name'        => ['required', 'string', 'max:80'],
            'type'        => ['required', 'in:dog,cat,bird,rabbit,fish,reptile,other'],
            'breed'       => ['nullable', 'string', 'max:100'],
            'age_years'   => ['nullable', 'integer', 'min:0', 'max:50'],
            'age_months'  => ['nullable', 'integer', 'min:0', 'max:11'],
            'size'        => ['nullable', 'in:tiny,small,medium,large,extra_large'],
            'about'       => ['nullable', 'string', 'max:500'],
            'photo'       => ['nullable', 'image', 'max:2048'],
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('pets', 'public');
        }

        Pet::create([
            'user_id'        => Auth::id(),
            'name'           => $request->name,
            'type'           => $request->type,
            'breed'          => $request->breed,
            'age_years'      => $request->age_years,
            'age_months'     => $request->age_months,
            'size'           => $request->size,
            'about'          => $request->about,
            'photo_path'     => $photoPath,
            'show_on_profile'=> true,
        ]);

        return back()->with('success', 'Pet added!');
    }

    public function destroyPet(Pet $pet): \Illuminate\Http\RedirectResponse
    {
        abort_unless($pet->user_id === Auth::id(), 403, 'You can only delete your own pets.');
        if ($pet->photo_path) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($pet->photo_path);
        }
        $pet->delete();
        return back()->with('success', 'Pet removed.');
    }

    // ── Voice Prompts ────────────────────────────────────────────────────────

    public function voiceIndex(): View
    {
        $user      = Auth::user();
        $questions = VoicePromptQuestion::where('is_active', true)->orderBy('order')->get();
        $myPrompts = VoicePrompt::where('user_id', $user->id)->with('question')->get()->keyBy('question_id');
        return view('profile.extras.voice', compact('questions', 'myPrompts'));
    }

    public function storeVoice(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'question_id' => ['required', 'exists:voice_prompt_questions,id'],
            'audio'       => ['required', 'file', 'mimetypes:audio/webm,audio/ogg,audio/mpeg,audio/mp4,audio/wav,video/webm', 'max:5120'],
        ]);

        $path = $request->file('audio')->store('voice-prompts', 'public');

        // Delete the old audio file if re-recording (avoids storage leak)
        $existing = VoicePrompt::where('user_id', Auth::id())
            ->where('question_id', $request->question_id)
            ->first();
        if ($existing && $existing->audio_path) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($existing->audio_path);
        }

        VoicePrompt::updateOrCreate(
            ['user_id' => Auth::id(), 'question_id' => $request->question_id],
            ['audio_path' => $path, 'duration_seconds' => $request->integer('duration', 0), 'show_on_profile' => true]
        );

        return back()->with('success', 'Voice prompt saved!');
    }

    public function destroyVoice(VoicePrompt $voicePrompt): \Illuminate\Http\RedirectResponse
    {
        abort_unless($voicePrompt->user_id === Auth::id(), 403, 'You can only delete your own voice prompts.');
        \Illuminate\Support\Facades\Storage::disk('public')->delete($voicePrompt->audio_path);
        $voicePrompt->delete();
        return back()->with('success', 'Voice prompt deleted.');
    }

    public function playVoice(VoicePrompt $voicePrompt): \Illuminate\Http\JsonResponse
    {
        $voicePrompt->increment('plays_count');
        return response()->json(['url' => $voicePrompt->audio_url]);
    }
}
