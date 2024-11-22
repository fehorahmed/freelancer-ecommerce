<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRegistrationRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
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
            ], 403);
        }

        if (
            !Auth::attempt(['email' => $request->email, 'password' => $request->password, 'status' => 1])
            && !Auth::attempt(['phone' => $request->email, 'password' => $request->password, 'status' => 1])
        ) {

            return response([
                'status' => false,
                'message' => "Email or password dose not match.",
            ], 404);
        } else {
            $token = $request->user()->createToken('admin-access-token', ['user'])->plainTextToken;
            return response()->json([
                'status' => true,
                'user' => new UserResource($request->user()),
                'token' => $token
            ]);
        }
    }
    public function profile(Request $request)
    {
        return response()->json([
            'status' => true,
            'user' => new UserResource($request->user()),
            // 'token' => $token
        ]);
    }


    public function apiRegistration(UserRegistrationRequest $request)
    {
        $validatedData = $request->validated();
        // dd($request->all());

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->password = Hash::make($request->password);
        $user->gender = $request->gender;
        $user->dob = $request->date_of_birth;

        if ($user->save()) {
            return response([
                'status' => true,
                'message' => 'Registration Success..',
            ]);
        } else {
            return response([
                'status' => false,
                'message' => 'Something went wrong..!',
            ], 500);
        }
    }
    public function apiChangePassword(Request $request)
    {
        $rules = [
            'old_password' => 'required|string|min:6|max:255',
            'password' => 'required|confirmed|string|min:6|max:255',

        ];


        if(!Hash::check($request->old_password,auth()->user()->password)){
            return response()->json([
                'status' => false,
                'message' => 'Old password is not correct.',
            ], 404);
        }

        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validation->errors()->first(),
                'errors' => $validation->errors()
            ], 422);
        }
        $user = User::find(auth()->id());
        $user->password = Hash::make($request->password);
        if ($user->save()) {
            return response([
                'status' => true,
                'message' => 'Password update success..',
            ]);
        } else {
            return response([
                'status' => false,
                'message' => 'Something went wrong..!',
            ], 500);
        }
    }

    public function apiForgetPassword(Request $request){
        $rules = [
            'email' => 'required|email|string|max:255',
        ];

        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validation->errors()->first(),
                'errors' => $validation->errors()
            ], 422);
        }

        $user = User::where(['email'=>$request->email,'status'=>1])->first();
        if(!$user){
            return response()->json([
                'status' => false,
                'message' => 'User not found on this email.',
            ], 404);
        }



        $status = Password::sendResetLink(
            $request->only('email')
        );

        // Return appropriate response
        if ($status === Password::RESET_LINK_SENT) {
            return response()->json(['message' => __($status)]);
        }

        return response()->json(['error' => __($status)], 400);

    }

    public function apiResetPassword(Request $request){
        $rules = [
            'email' => 'required|email',
            'token' => 'required',
            'password' => 'required|min:8|confirmed',
        ];

        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validation->errors()->first(),
                'errors' => $validation->errors()
            ], 422);
        }
          // Reset the password
          $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                ])->save();
            }
        );

        // Return appropriate response
        if ($status === Password::PASSWORD_RESET) {
            return response()->json(['message' => __($status)]);
        }

        return response()->json(['error' => __($status)], 400);
    }
}
