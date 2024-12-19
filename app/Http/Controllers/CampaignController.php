<?php

namespace App\Http\Controllers;

use App\Http\Resources\CampainResource;
use App\Models\Campaign;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;

class CampaignController extends Controller
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
        $query = Campaign::query();
        if ($request->search) {
            $query->where('title', 'LIKE', "%{$request->search}%");
        }
        return CampainResource::collection($query->paginate($perPage));
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
            'title' => 'required|string|max:255|unique:campaigns,title',
            'banner' => 'required|image|max:1024',
            'description' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',

        ];

        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validation->errors()->first(),
                'errors' => $validation->errors()
            ], 422);
        }
        dd($request->all());
        $campain = new Campaign();
        $campain->title = $request->title;

        $url = Str::slug($request->name, '-');
        $finalURL = Str::replace('&', 'and', $url);

        $campain->url = $finalURL;
        $campain->description = $request->description;
        $campain->banner = $request->banner;
        $campain->start_date = $request->start_date;
        $campain->end_date = $request->end_date;
        $campain->start_time = $request->start_time;
        $campain->end_time = $request->end_time;

        if ($request->hasFile('banner')) {
            $file = $request->file('banner');

            $path = '\images\campain\banner';
            $dpath = '\images\campain\banner\mobile';

            Storage::disk('public')->delete($path . '\\' . $campain->banner);
            Storage::disk('public')->delete($dpath . '\\' . $campain->banner);

            $image_name = time() . rand(00, 99) . '.' . $file->getClientOriginalName();

            $resize_image = Image::make($file->getRealPath());
            $resize_image->resize(250, 250, function ($constraint) {
                $constraint->aspectRatio();
            });

            $path1 = Storage::disk('public')->put($path . '\\' . $image_name, File::get($file));
            $path2 = Storage::disk('public')->put($dpath . '\\' . $image_name, (string)$resize_image->encode());
            $campain->banner = $image_name;
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(Campaign $campaign)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Campaign $campaign)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Campaign $campaign)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Campaign $campaign)
    {
        //
    }
}
