<?php

namespace App\Http\Controllers;

use App\Models\UserVerification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class VerificationController extends Controller
{
    /** Show the verification status / submission page. */
    public function show(Request $request): View
    {
        $user         = $request->user()->load('verification');
        $verification = $user->verification;

        return view('verification.show', compact('user', 'verification'));
    }

    /** Handle selfie + ID document upload. */
    public function store(Request $request)
    {
        $request->validate([
            'selfie'      => ['required', 'image', 'max:5120', 'mimes:jpg,jpeg,png,webp'],
            'id_document' => ['required', 'file',  'max:8192', 'mimes:jpg,jpeg,png,webp,pdf'],
        ]);

        $user         = $request->user();
        $verification = $user->verification;

        // Allow resubmission only if rejected or never submitted
        if ($verification && $verification->isPending()) {
            return back()->with('error', 'Your verification is already under review. Please wait for our team to process it.');
        }
        if ($verification && $verification->isApproved()) {
            return back()->with('error', 'Your profile is already verified.');
        }

        $selfiePath = $request->file('selfie')->store("verifications/{$user->id}", 'private');
        $idPath     = $request->file('id_document')->store("verifications/{$user->id}", 'private');

        // Delete old files if resubmitting after rejection
        if ($verification) {
            Storage::disk('private')->delete(array_filter([$verification->selfie_path, $verification->id_document_path]));
            $verification->update([
                'status'            => 'pending',
                'selfie_path'       => $selfiePath,
                'id_document_path'  => $idPath,
                'admin_notes'       => null,
                'reviewed_by'       => null,
                'reviewed_at'       => null,
            ]);
        } else {
            UserVerification::create([
                'user_id'          => $user->id,
                'status'           => 'pending',
                'selfie_path'      => $selfiePath,
                'id_document_path' => $idPath,
            ]);
        }

        return back()->with('success', '✅ Verification submitted! Our team will review it within 24–48 hours.');
    }
}
