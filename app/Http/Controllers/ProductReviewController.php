<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductReviewResource;
use App\Models\ProductReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductReviewController extends Controller
{

    public function apiProductReview(Request $request)
    {
        $rules = [
            'product_id' => 'required|numeric',
            'review' => 'nullable|string|max:1000',
            'star' => 'nullable|numeric',

        ];

        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validation->errors()->first(),
                'errors' => $validation->errors()
            ], 422);
        }

        $review = new ProductReview();
        $review->product_id = $request->product_id;
        $review->user_id = auth()->id();
        $review->review = $request->review;
        $review->star = $request->star ?? 0;
        if ($review->save()) {
            return response()->json([
                'status' => true,
                'message' => 'Your review successfully submitted.',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong.',
            ]);
        }
    }
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
        $query = ProductReview::orderBy('status', 'asc');
        // if($request->search){
        //     $query->where('name','LIKE',"%{$request->search}%");
        // }
        return ProductReviewResource::collection($query->paginate($perPage));
    }


    public function changeStatus(Request $request, $id)
    {
        $rules = [
            'status' => 'required|string|in:CONFIRM,DELETE',
        ];

        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validation->errors()->first(),
                'errors' => $validation->errors()
            ], 422);
        }

        $data = ProductReview::find($id);
        if(!$data){
            return response()->json([
                'status' => false,
                'message' => 'Review not found.',
            ], 404);
        }
        if ($request->status == 'CONFIRM') {
            $data->status = 1;
            $data->save();
            return response()->json([
                'status' => true,
                'message' => 'Status change successful.',
            ]);
        }
        if ($request->status == 'DELETE') {
            $data->delete();
            return response()->json([
                'status' => true,
                'message' => 'Status change successful.',
            ]);
        }


    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(ProductReview $productReview)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductReview $productReview)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProductReview $productReview)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductReview $productReview)
    {
        //
    }
}
