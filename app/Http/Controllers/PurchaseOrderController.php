<?php

namespace App\Http\Controllers;

use App\Http\Resources\PurchaseOrderResource;
use App\Models\ProductInventory;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PurchaseOrderController extends Controller
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
        $query = PurchaseOrder::query();
        if($request->search){
            $query->where('order_no','LIKE',"%{$request->search}%");
        }
        return PurchaseOrderResource::collection($query->paginate($perPage));
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
        $validator = Validator::make($request->all(), [
            'date' => "required|date",
            'supplier_id' => "required|numeric",
            'shipping_charge' => "required|numeric",
            'sub_total_amount' => "required|numeric",
            'grand_total_amount' => "required|numeric",
            'discount' => "nullable|numeric",
            'payment_type' => "required",
            'paid' =>"required|numeric",
            'due' => "required|numeric",
            'products' => 'required|array',
            'products.*.id' => 'required|numeric',
            'products.*.quantity' => 'required|numeric',
            'products.*.rate' => 'required|numeric',
            'products.*.total_amount' => 'required|numeric',
        ]);


        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }
        // dd($request->all());

        try {
            DB::beginTransaction();
            $maxIndex = PurchaseOrder::maxIn();
            $date = Carbon::now();
            $currenDate = $date->timestamp;
            $orderNo = 'PO-' . substr($currenDate, -4) . '' . Str::padLeft($maxIndex, 10, '0');
            $order = new PurchaseOrder();

            $order->date = $request->date;
            $order->order_no = $orderNo;
            $order->supplier_id = $request->supplier_id;
            $order->max_index = $maxIndex;
            $order->vat = 0;
            $order->paid = $request->paid;
            $order->due = $request->due;
            $order->shipping_charge = ceil($request->shipping_charge);
            $order->discount = $request->discount ? $request->discount : 0;
            $order->sub_total_amount = $request->sub_total_amount ;
            $order->grand_total_amount = (($request->sub_total_amount + $order->shipping_charge) - $order->discount);
            $order->created_by = Auth::id();

            if ($order->save()) {
                $isTransectionFail = false;
                $orderId = $order->id;
                $i = 0;

                // $data = DB::table('carts')->where('session_id', '=', $request->sessionId)->update(['is_ordered' => 1]);
                foreach ($request->products as $product) {
                    if ($product['id'] != 0) {
                        $orderDetails = new PurchaseOrderDetail();
                        $orderDetails->purchase_order_id = $orderId;
                        // $orderDetails->order_no = $orderNo;
                        $orderDetails->product_id = $product['id'];
                        // $orderDetails->attribute_id = $product['productAttrId'];
                        $orderDetails->rate = $product['rate'];
                        $orderDetails->quantity = $product['quantity'];
                        $orderDetails->discount_amount = 0;
                        $orderDetails->vat = 0;
                        $netPrice = ((($orderDetails->quantity * $orderDetails->rate) - $orderDetails->discount_amount) + $orderDetails->vat);
                        $orderDetails->total_amount = $netPrice;
                        //  $orderDetails->warranty_id = Product::getWarrantyId($product['Id']);

                        if (!$orderDetails->save()) {
                            $isTransectionFail = true;
                        }

                        $proInv = new ProductInventory();
                        $proInv->product_id = $product['id'];
                        // $proInv->product_attribute_id = $product['productAttrId'];
                        $proInv->stock_in = $product['quantity'];
                        $proInv->stock_out = 0;
                        $proInv->ref_type = ProductInventory::PURCHASE_ORDER;
                        $proInv->reference = 'Purchase Order';
                        $proInv->purchase_order_id = $orderId;
                        $proInv->date = date("Y-m-d");
                        $proInv->created_by = Auth::id();

                        if ($proInv->save()) {
                        } else {
                            $isTransectionFail = true;
                        }
                    }
                }

                if ($isTransectionFail) {
                    DB::rollBack();
                    return response()->json([
                        'status' => false,
                        'message' => 'Error occurred while creating Order!',
                    ],404);
                } else {
                    DB::commit();
                    return response()->json([
                        'status' => true,
                        'order_no' => $orderNo,
                        'message' => 'Order submitted successfully!',
                    ]);
                }
            } else {
                DB::rollBack();
                return response([
                    'status' => false,
                    'message' => 'Error occurred while creating Order!',
                ], 404);
            }

            DB::commit();
        } catch (\Throwable $exception) {

            DB::rollBack();
            return response([
                'status' => false,
                'message' => $exception->getMessage(),
            ], 404);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(PurchaseOrder $purchaseOrder)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PurchaseOrder $purchaseOrder)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PurchaseOrder $purchaseOrder)
    {
        //
    }
}
