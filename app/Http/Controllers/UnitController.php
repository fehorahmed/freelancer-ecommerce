<?php

namespace App\Http\Controllers;

use App\Http\Resources\UnitResource;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;

class UnitController extends Controller
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
        $query = Unit::query();
        if($request->search){
            $query->where('name','LIKE',"%{$request->search}%");
        }

        return UnitResource::collection($query->paginate($perPage));
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
            'name'=>'required|string|max:255|unique:units,name',
            'description'=>'nullable|string|max:255',
            'logo'=>'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:1024',
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

        $data = new Unit();
        $data->name = $request->name;
        $data->description = $request->description;
        $data->status = $request->status;
        if ($request->hasFile('logo')) {
            $file = $request->file('logo');

            $path = '\images\units';
            $dpath = '\images\units\150';

            $image_name = time() . rand(00, 99) . '.' . $file->getClientOriginalName();

            $resize_image = Image::make($file->getRealPath());
            $resize_image->resize(150, 150, function ($constraint) {
                $constraint->aspectRatio();
            });

            $path1 = Storage::disk('public')->put($path . '\\' . $image_name, File::get($file));
            $path2 = Storage::disk('public')->put($dpath . '\\' . $image_name, (string)$resize_image->encode());
            $data->logo = $image_name;

        }
        $data->created_by = auth()->id();
        if($data->save()){
            return response()->json([
                'status' => true,
                'message' => 'Unit created successfully.'
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
    public function show(Unit $unit)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit( $id)
    {
        $data = Unit::find($id);
        if($data){
            return response()->json([
                'status' => true,
                'data' => new UnitResource($data)
            ]);
        }else{
            return response()->json([
                'status' => false,
                'message' => 'Unit not found.'
            ],404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $rules = [
            'name'=>'required|string|max:255|unique:units,name,'.$id,
            'description'=>'nullable|string|max:255',
            'logo'=>'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:1024',
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

        $data =  Unit::find($id);
        $data->name = $request->name;
        $data->description = $request->description;
        $data->status = $request->status;
        if ($request->hasFile('logo')) {
            $file = $request->file('logo');

            $path = '\images\units';
            $dpath = '\images\units\150';

            Storage::disk('public')->delete($path . '\\' . $data->logo);
            Storage::disk('public')->delete($dpath . '\\' . $data->logo);

            $image_name = time() . rand(00, 99) . '.' . $file->getClientOriginalName();

            $resize_image = Image::make($file->getRealPath());
            $resize_image->resize(150, 150, function ($constraint) {
                $constraint->aspectRatio();
            });

            $path1 = Storage::disk('public')->put($path . '\\' . $image_name, File::get($file));
            $path2 = Storage::disk('public')->put($dpath . '\\' . $image_name, (string)$resize_image->encode());
            $data->logo = $image_name;

        }


        $data->updated_by = auth()->id();

        if($data->save()){
            return response()->json([
                'status' => true,
                'message' => 'Unit updated successfully.'
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
    public function destroy(Unit $unit)
    {
        //
    }
    public function getActiveUnits()
    {
        return UnitResource::collection(Unit::where('status',1)->get());
    }
}
