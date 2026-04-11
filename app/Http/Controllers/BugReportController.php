<?php

namespace App\Http\Controllers;

use App\Models\BugReport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BugReportController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:200',
            'description' => 'required|string|max:5000',
            'category'    => 'required|string|in:' . implode(',', array_keys(BugReport::CATEGORIES)),
            'page_url'    => 'nullable|string|max:500',
        ]);

        BugReport::create([
            'user_id'     => $request->user()?->id,
            'title'       => $validated['title'],
            'description' => $validated['description'],
            'category'    => $validated['category'],
            'page_url'    => $validated['page_url'] ?? $request->header('Referer'),
            'browser'     => substr($request->userAgent() ?? '', 0, 200),
            'status'      => 'open',
        ]);

        return response()->json(['success' => true, 'message' => 'Bug report submitted. Thank you!']);
    }
}
