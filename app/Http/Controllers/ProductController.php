<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
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
        $query = Product::query();
        if ($request->search) {
            $query->where('name', 'LIKE', "%{$request->search}%");
        }
        return ProductResource::collection($query->paginate($perPage));
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
            'name' => 'required',
            'short_description' => 'required',
            'description' => 'required',
            // 'weight' => 'required|numeric',
            // 'length' => 'required|numeric',
            // 'width' => 'required|numeric',
            // 'height' => 'required|numeric',
            'sku' => 'required|unique:products',
            'brand' => 'required',
            'category' => 'required',
            'unit' => 'required',
            // 'product' => 'required',
            'image' => 'required|mimes:jpg,jpeg,png,webp,gif|max:2000',
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
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        //
    }
}
