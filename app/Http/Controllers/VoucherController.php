<?php

namespace App\Http\Controllers;

use App\Http\Resources\VoucherResource;
use App\Http\Resources\VouvherResource;
use App\Models\Voucher;
use App\Models\VoucherProduct;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class VoucherController extends Controller
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
        $query = Voucher::query();
        if ($request->search) {
            $query->where('title', 'LIKE', "%{$request->search}%");
        }
        return VoucherResource::collection($query->paginate($perPage));
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
            'title' => "required",
            'coupon_code' => "required|unique:vouchers,coupon_code",
            'product_id.*' => "required|numeric",
            'no_of_usage' => "required|numeric|min:0",
            'discountby' => "required|string",
            'end_date' => 'required|date',
            'start_date' => 'required|date',
            // 'strt_time' => 'required',
            // 'end_time' => 'required',
        ];

        if ($request->discountby == "amount") {
            $rules['discount_amount'] = "required";
        }
        if ($request->discountby == "percentage") {
            $rules['discount_percentage'] = "required";
        }
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors()
            ],422);
        }


        try {
            DB::beginTransaction();

            $voucher = new Voucher();
            $voucher->title = $request->title;
            $voucher->coupon_code = $request->coupon_code;
            $voucher->no_of_usage = $request->no_of_usage;
            $voucher->discountby = $request->discountby;
            $voucher->discount_amount = $request->discount_amount;
            $voucher->discount_percentage = $request->discount_percentage;
            $voucher->start_date = $request->start_date;
            $voucher->start_time = $request->strt_time;
            $voucher->end_time = $request->end_time;
            $voucher->end_date = $request->end_date;
            $voucher->created_by = $userId = Auth::user()->id;
            if (!isset($request->apps_only)) {
                $voucher->is_apps_only = 0;
            } else {
                $voucher->is_apps_only = $request->apps_only;
            }
            if (isset($request->visibility)) {
                $voucher->visibility = 0;
            } else {
                $voucher->visibility = 1;
            }
            if ($voucher->save()) {
                $isTransectionFail = false;
                $i = 0;

                if (isset($request->product_id)) {
                    foreach ($request->product_id as $product) {
                        if ($product != 0) {
                            $voucherProducts = new VoucherProduct();
                            $voucherProducts->product_id = $product;
                            $voucherProducts->voucher_id = $voucher->id;
                            $voucherProducts->coupon_code = $request->coupon_code;
                            if (!$voucherProducts->save()) {
                                $isTransectionFail = true;
                            }
                        }
                        $i++;
                    }
                }
                if ($isTransectionFail) {
                    DB::rollBack();
                    return response()->json([
                        'status' => false,
                        'message' =>'Error occurred while creating Voucher.',
                    ],404);
                } else {
                    DB::commit();
                    return response()->json([
                        'status' => true,
                        'message' =>'Voucher Posted successfully',
                    ]);
                }
            } else {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => 'Error occurred while creating Product.',

                ],404);
            }

            DB::commit();
        } catch (\Throwable  $exception) {
            return response()->json([
                'status' => false,
                'message' => $exception->getMessage(),

            ],422);
            DB::rollBack();
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(Voucher $voucher)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit( $id)
    {
        $data = Voucher::find($id);
        if($data){
            return response()->json([
                'status' => true,
                'data' => new VoucherResource($data)
            ]);
        }else{
            return response()->json([
                'status' => false,
                'message' => 'Brand not found.'
            ],404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $rules = [
            'title' => "required",
            'coupon_code' => "required|unique:vouchers,coupon_code,".$id,
            'product_id.*' => "required|numeric",
            'no_of_usage' => "required|numeric|min:0",
            'discountby' => "required|string",
            'end_date' => 'required|date',
            'start_date' => 'required|date',
            // 'strt_time' => 'required',
            // 'end_time' => 'required',
        ];

        if ($request->discountby == "amount") {
            $rules['discount_amount'] = "required";
        }
        if ($request->discountby == "percentage") {
            $rules['discount_percentage'] = "required";
        }
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors()
            ],422);
        }

        try {
            DB::beginTransaction();

            $voucher = Voucher::find($id);
            $voucher->title = $request->title;
            $voucher->coupon_code = $request->coupon_code;
            $voucher->no_of_usage = $request->no_of_usage;
            $voucher->discountby = $request->discountby;
            $voucher->discount_amount = $request->discount_amount;
            $voucher->discount_percentage = $request->discount_percentage;
            $voucher->start_date = $request->start_date;
            $voucher->start_time = $request->strt_time;
            $voucher->end_time = $request->end_time;
            $voucher->end_date = $request->end_date;
            $voucher->created_by = $userId = Auth::user()->id;
            if (!isset($request->apps_only)) {
                $voucher->is_apps_only = 0;
            } else {
                $voucher->is_apps_only = $request->apps_only;
            }
            if (isset($request->visibility)) {
                $voucher->visibility = 0;
            } else {
                $voucher->visibility = 1;
            }
            if ($voucher->save()) {


                VoucherProduct::where('voucher_id',$voucher->id)->update(['status'=>0]);

                $isTransectionFail = false;
                $i = 0;

                if (isset($request->product_id)) {
                    foreach ($request->product_id as $product) {
                        if ($product != 0) {
                            $voucherProducts = new VoucherProduct();
                            $voucherProducts->product_id = $product;
                            $voucherProducts->voucher_id = $voucher->id;
                            $voucherProducts->coupon_code = $request->coupon_code;
                            if (!$voucherProducts->save()) {
                                $isTransectionFail = true;
                            }
                        }
                        $i++;
                    }
                }
                if ($isTransectionFail) {
                    DB::rollBack();
                    return response()->json([
                        'status' => false,
                        'message' =>'Error occurred while creating Voucher.',
                    ],404);
                } else {
                    DB::commit();
                    return response()->json([
                        'status' => true,
                        'message' =>'Voucher Updated successfully',
                    ]);
                }
            } else {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => 'Error occurred while creating Product.',

                ],404);
            }

            DB::commit();
        } catch (\Throwable  $exception) {
            return response()->json([
                'status' => false,
                'message' => $exception->getMessage(),

            ],422);
            DB::rollBack();
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Voucher $voucher)
    {
        //
    }
    public function getCouponDetails(Request $request)
    {
        $rules = [
            'coupon_code' => "required|string",
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors()
            ],422);
        }

        $voucher = Voucher::where(['coupon_code'=>$request->coupon_code,'status'=>1])
        ->whereDate('start_date', '<=', Carbon::now())
        ->whereDate('end_date', '>=', Carbon::now())
        ->first();
        if($voucher){

            if($voucher->discountby == 'amount'){
                $d_by = 'amount';
                $amount = $voucher->discount_amount;

            }
            if($voucher->discountby == 'percentage'){
                $d_by = 'percentage';
                $amount = $voucher->discount_percentage;
            }
            return response([
                'status'=>true,
                'discountby'=> $d_by,
                'amount'=> $amount,
            ]);


        }else{
            return response([
                'status'=>false,
                'message'=>'Coupon not found.'
            ]);
        }

    }

    public function getVoucherProducts($product_id, $coupon_code)
    {
        $vc = DB::select("SELECT vouchers.id,vouchers.start_date,vouchers.start_time,vouchers.end_date,vouchers.end_time,vouchers.title,voucher_products.coupon_code,voucher_products.product_id,voucher_products.status,vouchers.discount_amount,vouchers.discount_percentage,vouchers.no_of_usage FROM vouchers INNER JOIN voucher_products ON vouchers.id=voucher_products.voucher_id WHERE vouchers.status = 1 AND vouchers.is_apps_only = 0 AND voucher_products.product_id=$product_id AND voucher_products.coupon_code='$coupon_code';");
        $vc = Voucher::select([
            'vouchers.id',
            'vouchers.start_date',
            'vouchers.start_time',
            'vouchers.end_date',
            'vouchers.end_time',
            'vouchers.title',
            'voucher_products.coupon_code',
            'voucher_products.product_id',
            'voucher_products.status',
            'vouchers.discount_amount',
            'vouchers.discount_percentage',
            'vouchers.no_of_usage',
        ])
        ->join('voucher_products', 'vouchers.id', '=', 'voucher_products.voucher_id')
        ->where('vouchers.status', 1)
        ->where('voucher_products.product_id', $product_id)
        ->where('voucher_products.coupon_code', $coupon_code)
        ->get();
        // dd($vc);
        if($vc)
        {
            return VoucherResource::collection($vc);
        }
        else{
            return response()->json([
                'success' => false,
                'status_code' => 200,
                'message' => 'Product not Found.',
                'error' => 'Product not Found',
            ]);
        }
    }
}
