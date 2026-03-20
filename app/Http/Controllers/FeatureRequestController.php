<?php

namespace App\Http\Controllers;

use App\Models\FeatureRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FeatureRequestController extends Controller
{
    public function create(): View
    {
        return view('pages.feature-request');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'type'  => 'required|in:feature,bug',
            'title' => 'required|string|max:200',
            'body'  => 'required|string|max:5000',
            'name'  => 'required|string|max:100',
            'email' => 'required|email|max:200',
        ]);

        $user = $request->user();

        FeatureRequest::create([
            'user_id' => $user?->id,
            'name'    => $user ? ($user->name ?? $user->username) : $data['name'],
            'email'   => $user ? $user->email : $data['email'],
            'type'    => $data['type'],
            'title'   => $data['title'],
            'body'    => $data['body'],
            'status'  => 'open',
        ]);

        return back()->with('success', 'Thank you! Your ' . ($data['type'] === 'bug' ? 'bug report' : 'feature request') . ' has been submitted. We\'ll review it and get back to you.');
    }
}
