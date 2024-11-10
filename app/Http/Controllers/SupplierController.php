<?php

namespace App\Http\Controllers;

use App\Http\Resources\SupplierResource;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SupplierController extends Controller
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
        $query = Supplier::query();
        if($request->search){
            $query->where('name','LIKE',"%{$request->search}%");
        }
        return SupplierResource::collection($query->paginate($perPage));
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
            'name'=>'required|string|max:255|unique:suppliers,name',
            'description'=>'nullable|string|max:255',
            'status'=>'required|boolean',
            'phone'=>'nullable|string|max:255',
        ];

        $validation = Validator::make($request->all(),$rules);
        if($validation->fails()){
            return response()->json([
                'status' => false,
                'message' => $validation->errors()->first(),
                'errors' => $validation->errors()
            ],422);
        }

        $data = new Supplier();
        $data->name = $request->name;
        $data->description = $request->description;
        $data->phone = $request->phone;
        $data->status = $request->status;
        $data->created_by = auth()->id();

        if($data->save()){
            return response()->json([
                'status' => true,
                'message' => 'Supplier created successfully.'
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
    public function show(Supplier $supplier)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit( $id)
    {
        $data = Supplier::find($id);
        if($data){
            return response()->json([
                'status' => true,
                'data' => new SupplierResource($data)
            ]);
        }else{
            return response()->json([
                'status' => false,
                'message' => 'Supplier not found.'
            ],404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $rules = [
            'name'=>'required|string|max:255|unique:brands,name,'.$id,
            'description'=>'nullable|string|max:255',
            'phone'=>'nullable|string|max:255',

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

        $data =  Supplier::find($id);
        $data->name = $request->name;
        $data->description = $request->description;
        $data->status = $request->status;
        $data->phone = $request->phone;

        $data->updated_by = auth()->id();

        if($data->save()){
            return response()->json([
                'status' => true,
                'message' => 'Supplier updated successfully.'
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
    public function destroy(Supplier $supplier)
    {
        //
    }
}
