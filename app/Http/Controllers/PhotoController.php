<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessProfilePhoto;
use App\Models\Photo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PhotoController extends Controller
{
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $request->validate([
            'photo'   => 'required|image|mimes:jpeg,png,jpg,webp|max:8192',
            'primary' => 'boolean',
        ]);

        $user = $request->user();

        if ($user->photos()->count() >= 6) {
            return $request->expectsJson()
                ? response()->json(['error' => 'Maximum 6 photos allowed.'], 422)
                : redirect()->route('profile.edit')->withErrors(['photo' => 'Maximum 6 photos allowed.']);
        }

        // Store original in a temp location
        $path = $request->file('photo')->store("photos/{$user->id}/originals", 'public');

        $photo = Photo::create([
            'user_id'    => $user->id,
            'path'       => $path,
            'is_primary' => $request->boolean('primary') || $user->photos()->count() === 0,
            'is_approved' => false,
            'sort_order'  => $user->photos()->count(),
        ]);

        // Queue background job for thumbnail generation
        ProcessProfilePhoto::dispatch($photo);

        return $request->expectsJson()
            ? response()->json(['photo' => $photo, 'message' => 'Photo uploaded! Pending approval.'])
            : redirect()->route('profile.edit')->with('success', 'Photo uploaded! It will appear after admin approval.');
    }

    public function setPrimary(Request $request, Photo $photo): JsonResponse|RedirectResponse
    {
        Gate::authorize('update', $photo);

        // Unset all primary flags for this user
        Photo::where('user_id', $request->user()->id)->update(['is_primary' => false]);
        $photo->update(['is_primary' => true]);

        return $request->expectsJson()
            ? response()->json(['success' => true])
            : redirect()->route('profile.edit')->with('success', 'Primary photo updated.');
    }

    public function destroy(Request $request, Photo $photo): JsonResponse|RedirectResponse
    {
        Gate::authorize('delete', $photo);
        $photo->delete();

        return $request->expectsJson()
            ? response()->json(['success' => true])
            : redirect()->route('profile.edit')->with('success', 'Photo deleted.');
    }
}
