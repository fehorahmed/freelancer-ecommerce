<?php

namespace App\Http\Controllers;

use App\Http\Resources\GlobalConfigResource;
use App\Models\GlobalConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\File;

class GlobalConfigController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $datas = GlobalConfig::all();
        return GlobalConfigResource::collection($datas);
    }
    public function getGlobalConfigForWeb()
    {
        $datas = GlobalConfig::all();
        return GlobalConfigResource::collection($datas);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        // $rules = [
        //     'tittle'           => 'required|string',
        //     'top_logo'           => 'nullable|image',
        // ];
        // $validation = Validator::make($request->all(), $rules);
        // if ($validation->fails()) {
        //     return response()->json([
        //         'status' => false,
        //         'message' => $validation->errors()->first(),
        //         'errors' => $validation->errors()
        //     ], 422);
        // }

        $request->request->remove('_token');
        foreach ($request->all() as $key => $value) {

            // if ($key == 'top_logo') {


            // }
            if ($request->hasFile($key) && $request->file($key)->isValid()) {
                $rules = [
                    $key           => 'image|max:2048',
                ];
                $validation = Validator::make($request->all(), $rules);
                if ($validation->fails()) {
                    return response()->json([
                        'status' => false,
                        'message' => $validation->errors()->first(),
                        'errors' => $validation->errors()
                    ], 422);
                }

                $file = $value;

                $path = '\images\logo';
                $dpath = '\images\logo\150';
                $image_ck = GlobalConfig::where('key', $key)->first();
                if ($image_ck) {
                    Storage::disk('public')->delete($path . '\\' . $image_ck->value);
                    Storage::disk('public')->delete($dpath . '\\' . $image_ck->value);
                }

                $image_name = time() . rand(00, 99) . '.' . $file->getClientOriginalName();
                $resize_image = Image::make($file->getRealPath());
                $resize_image->resize(100, 100, function ($constraint) {
                    $constraint->aspectRatio();
                });

                $path1 = Storage::disk('public')->put($path . '\\' . $image_name, File::get($file));
                $path2 = Storage::disk('public')->put($dpath . '\\' . $image_name, (string)$resize_image->encode());
                $path = $image_name;

                $this->GlobalConfigUpdate($key, $path);
            } else {
                $this->GlobalConfigUpdate($key, $value);
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Global Config updated successfully.'
        ]);
    }

    private function GlobalConfigUpdate($key, $value)
    {
        $config = GlobalConfig::where('key', $key)->first();

        if ($config != NULL) {
            $config->value = is_array($value) ? implode(',', $value) : $value;
            return $config->save();
        } else {
            $config = new GlobalConfig();
            $config->key   = $key;
            $config->value = is_array($value) ? implode(',', $value) : $value;
            return $config->save();
        }
    }
}
