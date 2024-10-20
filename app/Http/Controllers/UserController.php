<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRegistrationRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
            ]);
        }

        if (
            !Auth::attempt(['email' => $request->email, 'password' => $request->password, 'status' => 1])
            && !Auth::attempt(['phone' => $request->email, 'password' => $request->password, 'status' => 1])
        ) {

            return response([
                'status' => false,
                'message' => "Email or password dose not match.",
            ]);
        } else {
            $token = $request->user()->createToken('admin-access-token', ['user'])->plainTextToken;
            return response()->json([
                'status' => true,
                'user' => new UserResource($request->user()),
                'token' => $token
            ]);
        }
    }
    public function profile(Request $request){
        return response()->json([
            'status' => true,
            'user' => new UserResource($request->user()),
            // 'token' => $token
        ]);
    }


    public function apiRegistration(UserRegistrationRequest $request){
        $validatedData = $request->validated();
        // dd($request->all());

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->password = Hash::make($request->password);
        $user->gender = $request->gender;
        $user->dob = $request->date_of_birth;

        if($user->save()){
            return response([
                'status' => true,
                'message' => 'Registration Success..',
            ]);
        }else{
            return response([
                'status' => false,
                'message' => 'Something went wrong..!',
            ]);
        }
    }
}
