<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'reported_user_id' => 'required|exists:users,id',
            'reason'           => 'required|in:fake_profile,inappropriate_photos,harassment,spam,underage,other',
            'description'      => 'nullable|max:1000',
        ]);

        if ((int) $data['reported_user_id'] === $request->user()->id) {
            return back()->withErrors(['reported_user_id' => 'Cannot report yourself.']);
        }

        Report::firstOrCreate(
            ['reporter_id' => $request->user()->id, 'reported_user_id' => $data['reported_user_id']],
            ['reason' => $data['reason'], 'description' => $data['description'] ?? null]
        );

        // Log report actions (both sides) for spam scoring
        ActivityLogger::log($request->user(), 'report_sent', [
            'target_user_id' => $data['reported_user_id'],
            'reason'         => $data['reason'],
        ], $request);
        ActivityLogger::log((int) $data['reported_user_id'], 'report_received', [
            'by_user_id' => $request->user()->id,
            'reason'     => $data['reason'],
        ]);

        return back()->with('success', 'Report submitted. Our team will review it shortly.');
    }
}
