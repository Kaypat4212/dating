<?php

namespace App\Http\Controllers;

use App\Models\ProfilePass;
use Illuminate\Http\Request;

class SecondChanceController extends Controller
{
    public function index(Request $request): \Illuminate\View\View
    {
        $user = $request->user();

        // Pull passes that are at least 30 days old and haven't been resurfaced yet
        $passes = ProfilePass::where('passer_id', $user->id)
            ->where('passed_at', '<=', now()->subDays(30))
            ->where('resurfaced', false)
            ->with(['passed.primaryPhoto', 'passed.profile'])
            ->orderByDesc('passed_at')
            ->paginate(12);

        // Mark them as resurfaced so they won't appear again
        $ids = $passes->pluck('id');
        if ($ids->isNotEmpty()) {
            ProfilePass::whereIn('id', $ids)->update(['resurfaced' => true]);
        }

        return view('second-chance.index', compact('passes'));
    }
}
