<?php

namespace App\Http\Controllers;

use App\Models\SubDistrict;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubDistrictController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }
    public function apiGetSubDistrict(Request $request)
    {
        $rules = [
            'district_id' => 'required|required',
        ];
        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails()) {
            return response([
                'status' => false,
                'message' => $validation->messages()->first(),
            ]);
        }

        $sub_districts = SubDistrict::where('district_id',$request->district_id)->get();
        return response([
            'sub_districts'=>$sub_districts
        ]);
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(SubDistrict $subDistrict)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SubDistrict $subDistrict)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SubDistrict $subDistrict)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SubDistrict $subDistrict)
    {
        //
    }
}
