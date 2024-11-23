<?php

namespace App\Http\Controllers;

use App\Http\Resources\GlobalConfigResource;
use App\Models\GlobalConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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

        $rules = [
            'tittle'           => 'required|string',
            'top_logo'           => 'nullable|image',
        ];
        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validation->errors()->first(),
                'errors' => $validation->errors()
            ], 422);
        }

        $request->request->remove('_token');
        foreach ($request->all() as $key => $value) {
            $this->GlobalConfigUpdate($key, $value);
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

            if ($key == 'top_logo') {
            } else {
                $config->value = is_array($value) ? implode(',', $value) : $value;
            }
            return $config->save();
        } else {
            $config = new GlobalConfig();

            $config->key   = $key;
            if ($key == 'top_logo') {
            } else {
                $config->value = is_array($value) ? implode(',', $value) : $value;
            }
            return $config->save();
        }
    }
}
