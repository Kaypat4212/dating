<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FcmTokenController extends Controller
{
    /**
     * Save FCM device token for the authenticated user
     */
    public function store(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required|string'
        ]);

        $user = Auth::user();
        
        if ($user) {
            $user->fcm_token = $request->fcm_token;
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'FCM token saved successfully'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'User not authenticated'
        ], 401);
    }

    /**
     * Remove FCM token (for logout)
     */
    public function destroy(Request $request)
    {
        $user = Auth::user();
        
        if ($user) {
            $user->fcm_token = null;
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'FCM token removed successfully'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'User not authenticated'
        ], 401);
    }
}
