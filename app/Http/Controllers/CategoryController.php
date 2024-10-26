<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;

class CategoryController extends Controller
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
        $query = Category::query();
        if ($request->search) {
            $query->where('name', 'LIKE', "%{$request->search}%");
        }
        return CategoryResource::collection($query->paginate($perPage));
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
            'root_id' => 'required|numeric',
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:1024',
            'horizontal_banner' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:1024',
            'vertical_banner' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:1024',
            'status' => 'required|boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:4000',
            'meta_keywords' => 'nullable|string|max:4000',
            'meta_og_description' => 'nullable|string|max:4000',
        ];
        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validation->errors()->first(),
                'errors' => $validation->errors()
            ], 422);
        }

        $url = Str::slug($request->name, '-');
        $finalURL = Str::replace('&', 'and', $url);

        $data = new Category();
        $data->root_id = $request->root_id;
        $data->url = $finalURL;
        $data->name = $request->name;
        $data->description = $request->description;
        $data->status = $request->status;
        $data->meta_title = $request->meta_title;
        $data->meta_description = $request->meta_description;
        $data->meta_keywords = $request->meta_keywords;
        $data->meta_og_description = $request->meta_og_description;
        $data->created_by = auth()->id();
        if ($request->hasFile('logo')) {
            $file = $request->file('logo');

            $path = '\images\categories\logo';
            $dpath = '\images\categories\logo\150';

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

            $path = '\images\categories\banner';
            $dpath = '\images\categories\banner\mobile';

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

            $path = '\images\categories\banner';
            $dpath = '\images\categories\banner\mobile';

            $image_name = time() . rand(00, 99) . '.' . $file->getClientOriginalName();

            $resize_image = Image::make($file->getRealPath());
            $resize_image->resize(250, 250, function ($constraint) {
                $constraint->aspectRatio();
            });

            $path1 = Storage::disk('public')->put($path . '\\' . $image_name, File::get($file));
            $path2 = Storage::disk('public')->put($dpath . '\\' . $image_name, (string)$resize_image->encode());
            $data->vertical_banner = $image_name;
        }


        if ($data->save()) {
            return response()->json([
                'status' => true,
                'message' => 'Category created successfully.'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong.'
            ], 404);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $data = Category::find($id);
        if ($data) {
            return response()->json([
                'status' => true,
                'data' => new CategoryResource($data)
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Category not found.'
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,  $id)
    {
        $rules = [
            'root_id' => 'required|numeric',
            'name' => 'required|string|max:255|unique:categories,name,' . $id,
            'description' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:1024',
            'horizontal_banner' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:1024',
            'vertical_banner' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:1024',
            'status' => 'required|boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:4000',
            'meta_keywords' => 'nullable|string|max:4000',
            'meta_og_description' => 'nullable|string|max:4000',
        ];
        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validation->errors()->first(),
                'errors' => $validation->errors()
            ], 422);
        }
        $url = Str::slug($request->name, '-');
        $finalURL = Str::replace('&', 'and', $url);

        $data =  Category::find($id);
        $data->root_id = $request->root_id;
        $data->url = $finalURL;
        $data->name = $request->name;
        $data->description = $request->description;
        $data->status = $request->status;
        $data->meta_title = $request->meta_title;
        $data->meta_description = $request->meta_description;
        $data->meta_keywords = $request->meta_keywords;
        $data->meta_og_description = $request->meta_og_description;
        $data->created_by = auth()->id();
        if ($request->hasFile('logo')) {
            $file = $request->file('logo');

            $path = '\images\categories\logo';
            $dpath = '\images\categories\logo\150';

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

        if ($request->hasFile('horizontal_banner')) {
            $file = $request->file('horizontal_banner');

            $path = '\images\categories\banner';
            $dpath = '\images\categories\banner\mobile';

            Storage::disk('public')->delete($path . '\\' . $data->horizontal_banner);
            Storage::disk('public')->delete($dpath . '\\' . $data->horizontal_banner);


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

            $path = '\images\categories\banner';
            $dpath = '\images\categories\banner\mobile';

            Storage::disk('public')->delete($path . '\\' . $data->vertical_banner);
            Storage::disk('public')->delete($dpath . '\\' . $data->vertical_banner);

            $image_name = time() . rand(00, 99) . '.' . $file->getClientOriginalName();

            $resize_image = Image::make($file->getRealPath());
            $resize_image->resize(250, 250, function ($constraint) {
                $constraint->aspectRatio();
            });

            $path1 = Storage::disk('public')->put($path . '\\' . $image_name, File::get($file));
            $path2 = Storage::disk('public')->put($dpath . '\\' . $image_name, (string)$resize_image->encode());
            $data->vertical_banner = $image_name;
        }

        if ($data->save()) {
            return response()->json([
                'status' => true,
                'message' => 'Category updated successfully.'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong.'
            ], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        //
    }
    public function getAllCategories()
    {


        $data = Category::getCategoryHierarchyAR();
        return response()->json(array('status' =>true, 'data' => $data));

    }
    public function getAllCategoryForAdmin()
    {
        $data = Category::getCategoryHierarchyForAdmin();
        return response()->json(array('status' =>true, 'data' => $data));

    }
}
