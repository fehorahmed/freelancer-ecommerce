<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserAddressResource;
use App\Models\SubDistrict;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserAddressController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => "required",
            'phone' => "required|string|max:255",
            'alternative_phone' => "nullable|string|max:255",
            'address' => "required|string|max:255",
            'sub_district_id' => "required|numeric",
            'title' => "nullable|string|max:255",
        ]);
        if ($validator->fails()) {
            return response([
                'status' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),

            ], 422);
        }

        $res = new UserAddress();
        $res->user_id = Auth::user()->id;
        $res->name = $request->name;
        $res->phone = $request->phone;
        $res->alt_phone = $request->alternative_phone;
        $res->address = $request->address;
        $res->sub_district_id = $request->sub_district_id;
        $subD = SubDistrict::find($request->sub_district_id);
        // if ($subD) {
        //     $res->district_id = $subD->district->id;
        //     $res->division_id = $subD->district->division->id;
        // }
        $res->title = $request->title;
        if ($res->save()) {
            return response([
                'status' => true,
                'message' => 'Address successfully added.',
                'res' => $res
            ], 200);
        }
    }


    public function getAllAddress()
    {
        $user = Auth::user()->id;
        return UserAddressResource::collection(UserAddress::where('user_id', '=', $user)->get());
    }
    public function editAddress($id)
    {
        $user = Auth::user()->id;
        return new UserAddressResource(UserAddress::find($id));
    }
    public function updateAddress(Request $request, $id)
    {

        $validator = Validator::make($request->all(), [
            'name' => "required",
            'phone' => "required|string|max:255",
            'alternative_phone' => "nullable|string|max:255",
            'address' => "required|string|max:255",
            'sub_district_id' => "required|numeric",
            'title' => "nullable|string|max:255",
        ]);
        if ($validator->fails()) {
            return response([
                'status' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),

            ], 422);
        }

        $res = UserAddress::find($id);
        $res->user_id = Auth::user()->id;
        $res->name = $request->name;
        $res->phone = $request->phone;
        $res->alt_phone = $request->alternative_phone;
        $res->address = $request->address;
        $res->sub_district_id = $request->sub_district_id;
        $subD = SubDistrict::find($request->sub_district_id);
        // if ($subD) {
        //     $res->district_id = $subD->district->id;
        //     $res->division_id = $subD->district->division->id;
        // }
        $res->title = $request->title;
        if ($res->save()) {
            return response([
                'status' => true,
                'message' => 'Address successfully updated.',
                'res' => $res
            ], 200);
        }
    }

    public function deleteAddress(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'address_id' => "required|numeric",
        ]);
        if ($validator->fails()) {
            return response([
                'status' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }
        $data = UserAddress::find($request->address_id);
        if (!$data) {
            return response([
                'status' => false,
                'message' => 'Address not found.',
            ], 422);
        }
        if ($data->delete()) {
            return response([
                'status' => true,
                'message' => 'Delete Success.',
            ]);
        } else {
            return response([
                'status' => false,
                'message' => 'Something went wrong.',
            ], 422);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(UserAddress $userAddress)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, UserAddress $userAddress)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UserAddress $userAddress)
    {
        //
    }
}
