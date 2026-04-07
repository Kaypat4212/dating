<?php

namespace App\Http\Controllers;

use App\Models\SavedFilter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FilterPackController extends Controller
{
    /** List the authenticated user's saved filter packs (JSON). */
    public function index()
    {
        $packs = Auth::user()->savedFilters()->orderBy('is_default', 'desc')->orderBy('created_at')->get();
        return response()->json($packs);
    }

    /** Save current preference form values as a named pack (Premium only). */
    public function store(Request $request)
    {
        $user = Auth::user();

        if (! $user->is_premium) {
            return response()->json(['error' => 'Premium membership required.'], 403);
        }

        $validated = $request->validate([
            'name'                  => 'required|string|max:80',
            'criteria'              => 'required|array',
            'criteria.min_age'      => 'nullable|integer|min:18|max:99',
            'criteria.max_age'      => 'nullable|integer|min:18|max:99',
            'criteria.max_distance' => 'nullable|integer|min:1|max:500',
            'criteria.gender'       => 'nullable|string|max:20',
            'criteria.body_types'   => 'nullable|array',
            'criteria.online_only'  => 'nullable|boolean',
            'is_default'            => 'boolean',
        ]);

        // Enforce cap of 10 packs per user
        if ($user->savedFilters()->count() >= 10) {
            return response()->json(['error' => 'Maximum of 10 filter packs allowed.'], 422);
        }

        // Only one default allowed
        if (! empty($validated['is_default'])) {
            $user->savedFilters()->update(['is_default' => false]);
        }

        $pack = $user->savedFilters()->create([
            'name'       => $validated['name'],
            'criteria'   => $validated['criteria'],
            'is_default' => $validated['is_default'] ?? false,
        ]);

        return response()->json($pack, 201);
    }

    /** Set a pack as the default (replaces any existing default). */
    public function setDefault(SavedFilter $pack)
    {
        $this->authorise($pack);

        Auth::user()->savedFilters()->update(['is_default' => false]);
        $pack->update(['is_default' => true]);

        return response()->json(['success' => true]);
    }

    /** Delete a saved filter pack. */
    public function destroy(SavedFilter $pack)
    {
        $this->authorise($pack);
        $pack->delete();
        return response()->json(['success' => true]);
    }

    /** Throw 403 if the pack doesn't belong to the current user. */
    private function authorise(SavedFilter $pack): void
    {
        abort_if($pack->user_id !== Auth::id(), 403, 'Forbidden');
    }
}
