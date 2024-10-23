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
            ],403);
        }
        if (
            !Auth::guard('admin')->attempt(['email' => $request->email, 'password' => $request->password,'status'=>1])
            && !Auth::guard('admin')->attempt(['phone' => $request->email, 'password' => $request->password,'status'=>1])
        ) {

            return response([
                'status' => false,
                'message' => "Email or password dose not match.",
            ],404);
        } else {
            // $token = $request->user()->createToken('admin-access-token', ['admin'])->plainTextToken;
            $token = Auth::guard('admin')->user()->createToken('admin-access-token', ['admin'])->plainTextToken;

            return response()->json([
                'status' => true,
                'user' => new AdminResource(Auth::guard('admin')->user()),
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
