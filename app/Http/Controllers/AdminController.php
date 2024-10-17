<?php

namespace App\Http\Controllers;

use App\Http\Resources\AdminResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    public function apiLogin(Request $request)
    {

        // $credentials = $request->only('email', 'password');

        $rules = [
            'email' => 'required',
            'password' => 'required'
        ];

        $validation = Validator::make($request->all(), $rules);

        if ($validation->fails()) {
            return response([
                'status' => false,
                'message' => $validation->messages()->first(),
            ]);
        }
        // if (Auth::attempt($credentials)) {
        //     $token = $request->user()->createToken('user-access-token')->plainTextToken;
        //     return response()->json(['token' => $token]);
        // } else {
        //     return response()->json(['message' => 'Unauthorized'], 401);
        // }

        if (
            !Auth::attempt(['email' => $request->email, 'password' => $request->password, 'role' => 3])
            && !Auth::attempt(['phone' => $request->email, 'password' => $request->password, 'role' => 3])
        ) {

            return response([
                'status' => false,
                'message' => "Email or password dose not match.",
            ]);
        } else {
            $token = $request->user()->createToken('admin-access-token', ['admin'])->plainTextToken;
            return response()->json([
                'status' => true,
                'user' => new AdminResource($request->user()),
                'token' => $token
            ]);
        }
    }
    public function profile(Request $request){
        return response()->json([
            'status' => true,
            'user' => new AdminResource($request->user()),
            // 'token' => $token
        ]);
    }
}
