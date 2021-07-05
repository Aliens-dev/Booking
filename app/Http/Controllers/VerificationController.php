<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class VerificationController extends ApiController
{
    public function verify(Request $request, $userId)
    {
        $user = User::find($userId);
        //dd(hash_equals(sha1($user->getEmailForVerification()), $request->hash));
        if (! hash_equals((string) $userId, (string) $user->getKey())) {
            return response()->json(['success' => false, 'message' => 'Failed to verify, please check you clicked the right link'], 403);
        }

        if (! hash_equals((string) $request->hash, sha1($user->getEmailForVerification()))) {
            return response()->json(['success' => false, 'message' => 'Failed to verify, please check you clicked the right link'], 403);
        }
        if($user->isEmailVerified()) {
            return response()->json(['success' => false, 'message' => 'email already verified'], 401);
        }
        $user->markEmailAsVerified();
        return response()->json(['success' => true, 'message' => 'email verified'], 200);

    }

    public function resend(Request $request)
    {

    }
}
