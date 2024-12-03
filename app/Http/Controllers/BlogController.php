<?php

namespace App\Http\Controllers;

use App\Http\Resources\BlogResource;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;

class BlogController extends Controller
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
        $query = Blog::query();
        if ($request->search) {
            $query->where('title', 'LIKE', "%{$request->search}%");
        }
        return BlogResource::collection($query->paginate($perPage));
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
            'title' => 'required|string|max:255|unique:blogs,title',
            'short_description' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:1024',
            'status' => 'required|boolean',
        ];

        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validation->errors()->first(),
                'errors' => $validation->errors()
            ], 422);
        }

        $data = new Blog();
        $data->title = $request->title;
        $data->short_description = $request->short_description;
        $data->description = $request->description;
        $data->status = $request->status;
        $data->created_by = auth()->id();
        if ($request->hasFile('image')) {
            $file = $request->file('image');

            $path = '\images\blog';
            $dpath = '\images\blog\150';

            // Storage::disk('public')->delete($path . '\\' . $data->image);
            // Storage::disk('public')->delete($dpath . '\\' . $data->image);

            $image_name = time() . rand(00, 99) . '.' . $file->getClientOriginalName();

            $resize_image = Image::make($file->getRealPath());
            $resize_image->resize(500, 500, function ($constraint) {
                $constraint->aspectRatio();
            });

            $path1 = Storage::disk('public')->put($path . '\\' . $image_name, File::get($file));
            $path2 = Storage::disk('public')->put($dpath . '\\' . $image_name, (string)$resize_image->encode());
            $data->image = $image_name;
        }
        if ($data->save()) {
            return response()->json([
                'status' => true,
                'message' => 'Blog create successfully.'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong.'
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Blog $blog)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $data = Blog::find($id);
        if($data){
            return response()->json([
                'status' => true,
                'data' => new BlogResource($data)
            ]);
        }else{
            return response()->json([
                'status' => false,
                'message' => 'Blog not found.'
            ],404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $rules = [
            'title' => 'required|string|max:255|unique:blogs,title,' . $id,
            'short_description' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:1024',
            'status' => 'required|boolean',
        ];

        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validation->errors()->first(),
                'errors' => $validation->errors()
            ], 422);
        }

        $data = Blog::find($id);
        $data->title = $request->title;
        $data->short_description = $request->short_description;
        $data->description = $request->description;
        $data->status = $request->status;
        $data->created_by = auth()->id();
        if ($request->hasFile('image')) {
            $file = $request->file('image');

            $path = '\images\blog';
            $dpath = '\images\blog\150';

            Storage::disk('public')->delete($path . '\\' . $data->image);
            Storage::disk('public')->delete($dpath . '\\' . $data->image);

            $image_name = time() . rand(00, 99) . '.' . $file->getClientOriginalName();

            $resize_image = Image::make($file->getRealPath());
            $resize_image->resize(500, 500, function ($constraint) {
                $constraint->aspectRatio();
            });

            $path1 = Storage::disk('public')->put($path . '\\' . $image_name, File::get($file));
            $path2 = Storage::disk('public')->put($dpath . '\\' . $image_name, (string)$resize_image->encode());
            $data->image = $image_name;
        }
        if ($data->save()) {
            return response()->json([
                'status' => true,
                'message' => 'Blog create successfully.'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong.'
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Blog $blog)
    {
        //
    }
}
