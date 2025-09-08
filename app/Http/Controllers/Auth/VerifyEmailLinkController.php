<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;

class VerifyEmailLinkController extends Controller
{
    public function __invoke(Request $request, $id, $hash)
    {
        // Ensure the signed URL is valid (not expired/tampered)
        if (! $request->hasValidSignature()) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Verification link expired or invalid. Please request a new one.']);
        }

        $user = User::findOrFail($id);

        // Ensure the hash matches the user's email
        if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Invalid verification link.']);
        }

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('login')->with('status', 'Email already verified. You can sign in.');
        }

        // Mark as verified and fire event
        $user->markEmailAsVerified();
        event(new Verified($user));

        // Do NOT auto-login; your policy is "verify before login"
        return redirect()->route('login')->with('status', 'Email verified! You can now sign in.');
    }
}
