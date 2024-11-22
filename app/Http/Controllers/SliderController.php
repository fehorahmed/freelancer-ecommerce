<?php

namespace App\Http\Controllers;

use App\Http\Resources\SliderResource;
use App\Models\Slider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class SliderController extends Controller
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
        $query = Slider::query();

        return SliderResource::collection($query->paginate($perPage));
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
            'serial'=>'required|numeric',
            'image'=>'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];

        $validation = Validator::make($request->all(),$rules);
        if($validation->fails()){
            return response()->json([
                'status' => false,
                'message' => $validation->errors()->first(),
                'errors' => $validation->errors()
            ],422);
        }


        $slider = new Slider();
        $slider->serial = $request->serial;

        if ($request->hasFile('image')) {
            $file = $request->file('image');

            $path = '\images\sliders';
            $dpath = '\images\sliders\1600';
            $image_name = time() . rand(00, 99) . '.' . $file->getClientOriginalName();
            $resize_image = Image::make($file->getRealPath());
            $resize_image->resize(1600, 600, function ($constraint) {
                $constraint->aspectRatio();
            });
            $path1 = Storage::disk('public')->put($path . '\\' . $image_name, File::get($file));
            $path2 = Storage::disk('public')->put($dpath . '\\' . $image_name, (string)$resize_image->encode());
            $slider->image = $image_name;

        }
        if($slider->save()){
            return response()->json([
                'status' => true,
                'message' => 'Slider upload successfully.'
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
    public function show(Slider $slider)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit( $id)
    {
        $data = Slider::find($id);
        if($data){
            return response()->json([
                'status' => true,
                'data' => new SliderResource($data)
            ]);
        }else{
            return response()->json([
                'status' => false,
                'message' => 'Slider not found.'
            ],404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $rules = [
            'serial'=>'required|numeric',
            'image'=>'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];

        $validation = Validator::make($request->all(),$rules);
        if($validation->fails()){
            return response()->json([
                'status' => false,
                'message' => $validation->errors()->first(),
                'errors' => $validation->errors()
            ],422);
        }


        $slider =  Slider::find($id);
        $slider->serial = $request->serial;

        if ($request->hasFile('image')) {
            $file = $request->file('image');

            $path = '\images\sliders';
            $dpath = '\images\sliders\1600';

            Storage::disk('public')->delete($path . '\\' . $slider->image);
            Storage::disk('public')->delete($dpath . '\\' . $slider->image);



            $image_name = time() . rand(00, 99) . '.' . $file->getClientOriginalName();
            $resize_image = Image::make($file->getRealPath());
            $resize_image->resize(1600, 600, function ($constraint) {
                $constraint->aspectRatio();
            });
            $path1 = Storage::disk('public')->put($path . '\\' . $image_name, File::get($file));
            $path2 = Storage::disk('public')->put($dpath . '\\' . $image_name, (string)$resize_image->encode());
            $slider->image = $image_name;

        }
        if($slider->save()){
            return response()->json([
                'status' => true,
                'message' => 'Slider update successfully.'
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
    public function destroy( $id)
    {
        $slider =  Slider::find($id);

        if($slider){

            if($slider->delete()){
                return response()->json([
                    'status' => true,
                    'data' => 'Deleted successfully.'
                ]);
            }else{
                return response()->json([
                    'status' => false,
                    'message' => 'Something went wrong.'
                ],422);
            }

        }else{
            return response()->json([
                'status' => false,
                'message' => 'Slider not found.'
            ],404);
        }
    }
    public function getAllSlider()
    {
        return SliderResource::collection(Slider::orderBy('serial','ASC')->get());
    }
}
