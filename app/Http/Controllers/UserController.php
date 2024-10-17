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
            !Auth::attempt(['email' => $request->email, 'password' => $request->password, 'role' => 1])
            && !Auth::attempt(['phone' => $request->email, 'password' => $request->password, 'role' => 1])
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
        $user->father_name = $request->father_name;
        $user->nid = $request->nid;
        $user->job_type = $request->job_type;
        $user->present_division_id = $request->present_division_id;
        $user->present_district_id = $request->present_district_id;
        $user->present_sub_district_id = $request->present_sub_district_id;
        $user->present_address = $request->present_address;
        $user->permanent_division_id = $request->permanent_division_id;
        $user->permanent_district_id = $request->permanent_district_id;
        $user->permanent_sub_district_id = $request->permanent_sub_district_id;
        $user->permanent_address = $request->permanent_address;
        if($user->job_type == 'GOVT'){
            $user->office_division_id = $request->office_division_id;
            $user->office_district_id = $request->office_district_id;
            $user->office_sub_district_id = $request->office_sub_district_id;
            $user->office_address = $request->office_address;
        }

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
