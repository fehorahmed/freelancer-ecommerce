<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard', absolute: false) . '?verified=1');
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return redirect()->intended(route('dashboard', absolute: false) . '?verified=1');
    }


    public function verify(Request  $request)
    {
        $user = User::findOrFail($request->id);
        dd('sss');
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found.',
            ], 404);
        }
        dd('sss');
        if (!hash_equals((string) $request->id, (string) $user->getKey())) {
            return response()->json(['status' => false, 'message' => 'Invalid ID.'], 400);
        }

        if (!hash_equals((string) $request->hash, sha1($user->getEmailForVerification()))) {
            return response()->json(['status' => false, 'message' => 'Invalid hash.'], 400);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json(['status' => true, 'message' => 'Email already verified.']);
        }
        dd('sss');
        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return response()->json(['status' => true, 'message' => 'Email verified successfully.']);
    }
}
