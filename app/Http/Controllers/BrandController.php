<?php

namespace App\Http\Controllers;

use App\Http\Resources\BrandResource;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;

class BrandController extends Controller
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
        $query = Brand::query();
        if($request->search){
            $query->where('name','LIKE',"%{$request->search}%");
        }
        return BrandResource::collection($query->paginate($perPage));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {



    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $rules = [
            'name'=>'required|string|max:255|unique:brands,name',
            'description'=>'nullable|string|max:255',
            'url'=>'nullable|string|max:255|unique:brands,url',
            'logo'=>'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:1024',
            'horizontal_banner'=>'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:1024',
            'vertical_banner'=>'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:1024',
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

        $data = new Brand();
        $data->name = $request->name;
        $data->description = $request->description;
        $data->url = $request->url;
        $data->status = $request->status;
        if ($request->hasFile('logo')) {
            $file = $request->file('logo');

            $path = '\images\brands\logo';
            $dpath = '\images\brands\logo\150';

            $image_name = time() . rand(00, 99) . '.' . $file->getClientOriginalName();

            $resize_image = Image::make($file->getRealPath());
            $resize_image->resize(150, 150, function ($constraint) {
                $constraint->aspectRatio();
            });

            $path1 = Storage::disk('public')->put($path . '\\' . $image_name, File::get($file));
            $path2 = Storage::disk('public')->put($dpath . '\\' . $image_name, (string)$resize_image->encode());
            $data->logo = $image_name;

        }

        if ($request->hasFile('horizontal_banner')) {
            $file = $request->file('horizontal_banner');

            $path = '\images\brands\banner';
            $dpath = '\images\brands\banner\mobile';

            $image_name = time() . rand(00, 99) . '.' . $file->getClientOriginalName();

            $resize_image = Image::make($file->getRealPath());
            $resize_image->resize(250, 250, function ($constraint) {
                $constraint->aspectRatio();
            });

            $path1 = Storage::disk('public')->put($path . '\\' . $image_name, File::get($file));
            $path2 = Storage::disk('public')->put($dpath . '\\' . $image_name, (string)$resize_image->encode());
            $data->horizontal_banner = $image_name;

        }

        if ($request->hasFile('vertical_banner')) {
            $file = $request->file('vertical_banner');

            $path = '\images\brands\banner';
            $dpath = '\images\brands\banner\mobile';

            $image_name = time() . rand(00, 99) . '.' . $file->getClientOriginalName();

            $resize_image = Image::make($file->getRealPath());
            $resize_image->resize(250, 250, function ($constraint) {
                $constraint->aspectRatio();
            });

            $path1 = Storage::disk('public')->put($path . '\\' . $image_name, File::get($file));
            $path2 = Storage::disk('public')->put($dpath . '\\' . $image_name, (string)$resize_image->encode());
            $data->vertical_banner = $image_name;

        }

        $data->created_by = auth()->id();

        if($data->save()){
            return response()->json([
                'status' => true,
                'message' => 'Brand created successfully.'
            ]);
        }else{
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong.'
            ]);
        }


    }

    /**
     * Display the specified resource.
     */
    public function show(Brand $brand)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit( $id)
    {
        $data = Brand::find($id);
        if($data){
            return response()->json([
                'status' => true,
                'data' => new BrandResource($data)
            ]);
        }else{
            return response()->json([
                'status' => false,
                'message' => 'Brand not found.'
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Brand $brand)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Brand $brand)
    {
        //
    }
}
