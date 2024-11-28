<?php

namespace App\Http\Controllers;

use App\Http\Resources\WishListResource;
use App\Models\WishList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class WishListController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
            'product_id'           => 'required|numeric',
        ];
        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validation->errors()->first(),
                'errors' => $validation->errors()
            ], 422);
        }
        $data_ck = WishList::where(['product_id' => $request->product_id, 'user_id' => auth()->id()])->first();
        if ($data_ck) {
            return response()->json([
                'status' => false,
                'message' => 'Product already in wishlist.',
            ], 404);
        }
        $data = new WishList();
        $data->product_id = $request->product_id;
        $data->user_id = auth()->id();
        if ($data->save()) {
            return response()->json([
                'status' => true,
                'message' => 'Product added on wishlist.',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong.',
            ], 404);
        }
    }

    public function deleteByProductId($product_id)
    {
        $user_id = Auth::user()->id;
        $wishList = WishList::where('product_id', '=', $product_id)->where('user_id', '=', $user_id)->first();

        if (!$wishList) {
            return response()->json([
                'status' => false,
                'message' => 'Record not found!',
            ]);
        }

        if ($wishList->delete()) {
            return response()->json([
                'status' => true,
                'message' => 'Record has been deleted successfully!',
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Please try again!',
            ], 404);
        }
    }

    public function viewWishList()
    {
        $userId = Auth::user()->id;
        $data = WishList::where('user_id', '=', $userId)->get();
        return WishListResource::collection($data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, WishList $wishList)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(WishList $wishList)
    {
        //
    }
}
