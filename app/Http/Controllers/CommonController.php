<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommonController extends Controller
{
    public function profile()
    {

        return response()->json([
            'status' => true,
            'user' => new UserResource(auth()->user()),
        ]);
    }
    public function logout()
    {

        // Identify the authenticated guard
        if (Auth::guard('admin')->check()) {
            Auth::guard('admin')->logout();
        }

        if (Auth::guard('web')->check()) {
            Auth::guard('web')->logout();
        }

        // Invalidate token for API authentication (if using token-based guards)
        $user = request()->user(); // Fetch the authenticated user
        if ($user) {
            $user->currentAccessToken()->delete(); // Delete the token
        }

        return response()->json([
            'status' => true,
            'message' => 'Logout successful.',
        ]);
    }
}
