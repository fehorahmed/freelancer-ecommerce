<?php

namespace App\Http\Controllers;

use App\Http\Resources\WarrantyResource;
use App\Models\Warranty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WarrantyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->per_page) {
            $perPage = $request->per_page;
        } else {
            $perPage = 10;
        }
        $query = Warranty::query();
        if ($request->search) {
            $query->where('title', 'LIKE', "%{$request->search}%");
        }
        return WarrantyResource::collection($query->paginate($perPage));
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
        $rules = [
            'title'=>'required|string|max:255|unique:units,name',
            'description'=>'nullable|string|max:255',
            'status'=>'required|boolean',
        ];

        $validation = Validator::make($request->all(),$rules);
        if($validation->fails()){
            return response()->json([
                'status' => false,
                'message' => $validation->errors()->first(),
                'errors' => $validation->errors()
            ],422);
        }
        $data = new Warranty();
        $data->title = $request->title;
        $data->description = $request->description;
        $data->status = $request->status;
        $data->created_by = auth()->id();
        if($data->save()){
            return response()->json([
                'status' => true,
                'message' => 'Warranty created successfully.'
            ]);
        }else{
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong.'
            ],404);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Warranty $warranty)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit( $id)
    {
        $data = Warranty::find($id);
        if($data){
            return response()->json([
                'status' => true,
                'data' => new WarrantyResource($data)
            ]);
        }else{
            return response()->json([
                'status' => false,
                'message' => 'Warranty not found.'
            ],404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $rules = [
            'title'=>'required|string|max:255|unique:units,name,'.$id,
            'description'=>'nullable|string|max:255',
            'status'=>'required|boolean',
        ];

        $validation = Validator::make($request->all(),$rules);
        if($validation->fails()){
            return response()->json([
                'status' => false,
                'message' => $validation->errors()->first(),
                'errors' => $validation->errors()
            ],422);
        }
        $data =  Warranty::find($id);
        $data->title = $request->title;
        $data->description = $request->description;
        $data->status = $request->status;
        $data->updated_by = auth()->id();
        if($data->save()){
            return response()->json([
                'status' => true,
                'message' => 'Warranty created successfully.'
            ]);
        }else{
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong.'
            ],404);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Warranty $warranty)
    {
        //
    }
    public function getActiveWarranty()
    {
        return WarrantyResource::collection(Warranty::where('status',1)->get());
    }
}
