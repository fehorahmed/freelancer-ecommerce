<?php

namespace App\Http\Controllers;

use App\Http\Resources\PageResource;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;

class PageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return PageResource::collection(Page::where('status', 1)->get());
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
            'title'           => 'required|string|max:255|unique:pages,title',
            'url'           => [
                'required',
                'string',
                'max:255',
                'unique:pages,url',
                'regex:/^[^\s]+$/'
            ],
            'text'           => 'nullable|stringmax:20000',
            'image'           => 'nullable|image',
            'status'           => 'required|boolean',
        ];
        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validation->errors()->first(),
                'errors' => $validation->errors()
            ], 422);
        }

        $data = new Page();
        $data->title = $request->title;
        $data->url = $request->url;
        $data->text = $request->text;
        $data->status = $request->status;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $path = '\images\pages';
            $dpath = '\images\pages\150';
            $image_name = time() . rand(00, 99) . '.' . $file->getClientOriginalName();
            $resize_image = Image::make($file->getRealPath());
            $resize_image->resize(500, 500, function ($constraint) {
                $constraint->aspectRatio();
            });

            $path1 = Storage::disk('public')->put($path . '\\' . $image_name, File::get($file));
            $path2 = Storage::disk('public')->put($dpath . '\\' . $image_name, (string)$resize_image->encode());
            $data->image = $image_name;
        }


        $data->created_by = auth()->id();
        if ($data->save()) {
            return response()->json([
                'status' => true,
                'message' => 'Page create successfully.',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong.',
            ], 404);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Page $page)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $data = Page::find($id);
        if($data){
            return response()->json([
                'status' => true,
                'data' => new PageResource($data)
            ]);
        }else{
            return response()->json([
                'status' => false,
                'message' => 'Page not found.'
            ],404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,  $id)
    {
        $rules = [
            'title'           => 'required|string|max:255|unique:pages,title,'.$id,
            // 'url'           => [
            //     'required',
            //     'string',
            //     'max:255',
            //     'unique:pages,url,'.$id,
            //     'regex:/^[^\s]+$/'
            // ],
            'text'           => 'nullable|stringmax:20000',
            'image'           => 'nullable|image',
            'status'           => 'required|boolean',
        ];
        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validation->errors()->first(),
                'errors' => $validation->errors()
            ], 422);
        }

        $data =  Page::find($id);
        $data->title = $request->title;
        // $data->url = $request->url;
        $data->text = $request->text;
        $data->status = $request->status;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $path = '\images\pages';
            $dpath = '\images\pages\150';

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


        $data->created_by = auth()->id();
        if ($data->save()) {
            return response()->json([
                'status' => true,
                'message' => 'Page updated successfully.',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong.',
            ], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Page $page)
    {
        //
    }
    public function apiGetPage($url)
    {
        $page = Page::where('url',$url)->first();
        if($page){

            return new PageResource($page);

        }else{
            return response()->json([
                'status' => false,
                'message' => 'Page not found.',
            ], 404);
        }
    }
}
