<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderHistoryResource;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderStatus;
use App\Models\PaymentDetail;
use App\Models\Product;
use App\Models\ProductInventory;
use App\Models\UserAddress;
use App\Models\Voucher;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class OrderController extends Controller
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
        $query = Order::query();
        if ($request->search) {
            $query->where('order_no', 'LIKE', "%{$request->search}%");
        }
        return OrderResource::collection($query->paginate($perPage));
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
    public function customerOrderStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_name' => "required|string|max:255",
            'customer_number' => "required|numeric",
            'shipping_charge' => "required|numeric",
            'total_amount' => "required",
            'discount' => "nullable|numeric",
            'coupon_code' => "nullable|string|max:255",
            'payment_type' => "required",
            'products' => 'required|array',
            'products.*.id' => 'required|numeric',
            'products.*.quantity' => 'required|numeric',
            'products.*.sale_price' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }


        foreach ($request->products as $product) {
            if ($product['id'] > 0) {
                $pid = $product['id'];
                // $paid = $product['productAttrId'];
                $stock = ProductInventory::getStock($pid, "");
                $pquantity = $product['quantity'];
                $pName = Product::getProductName($pid);
                if ($pquantity > $stock) {

                    if ($stock > 0) {
                        return response([
                            'status' => false,
                            'message' => $pName . ' Quantity is grater then Stock! Please decrease quantity.',
                        ], 404);
                    } else {
                        return response([
                            'status' => false,
                            'message' => $pName . ' is not available in Stock! Please remove product.',
                        ], 404);
                    }
                }
            }
        }

        $sale_price = 0;
        foreach ($request->products as $product) {
            $sale_price  +=  $product['sale_price'] * $product['quantity'];
        }
        // if ((float)$sale_price !=  (float)$request->total_amount) {
        //     return response([
        //         'status' => false,
        //         'message' => 'Price is not match!',
        //     ], 404);
        // }

        try {
            DB::beginTransaction();
            $maxIndex = Order::maxIn();
            $date = Carbon::now();
            $currenDate = $date->timestamp;
            $orderNo = 'AC-' . substr($currenDate, -4) . '' . Str::padLeft($maxIndex, 10, '0');
            $order = new Order();
            $order->user_id = null;
            $order->customer_name = $request->customer_name;
            $order->customer_number = $request->customer_number;
            $order->order_no = $orderNo;
            $order->max_index = $maxIndex;
            $order->vat = 0;
            $order->shipping_charge = ceil($request->shipping_charge);
            $order->discount = $request->discount ? $request->discount : 0;
            $order->coupon_code = $request->coupon_code ?? null;
            $order->total_amount = (float)$total = (($request->total_amount + $order->shipping_charge) - $order->discount);
            $order->created_by = Auth::id();
            // $order->user_address_id = $request->address_id;

            if ($order->save()) {
                $isTransectionFail = false;
                $orderId = $order->id;
                $i = 0;

                // $data = DB::table('carts')->where('session_id', '=', $request->sessionId)->update(['is_ordered' => 1]);
                foreach ($request->products as $product) {
                    if ($product['id'] != 0) {
                        $orderDetails = new OrderDetail();
                        $orderDetails->order_id = $orderId;
                        // $orderDetails->order_no = $orderNo;
                        $orderDetails->product_id = $product['id'];
                        // $orderDetails->attribute_id = $product['productAttrId'];
                        $orderDetails->discount_type = '';
                        $orderDetails->quantity = $product['quantity'];
                        $orderDetails->discount_amount = 0;
                        $orderDetails->vat = 0;
                        $orderDetails->product_price = $product['sale_price'];
                        $netPrice = ((($orderDetails->quantity * $orderDetails->product_price) - $orderDetails->discount_amount) + $orderDetails->vat);
                        $orderDetails->net_price = $netPrice;
                        //  $orderDetails->warranty_id = Product::getWarrantyId($product['Id']);

                        if (!$orderDetails->save()) {
                            $isTransectionFail = true;
                        }

                        $proInv = new ProductInventory();
                        $proInv->product_id = $product['id'];
                        // $proInv->product_attribute_id = $product['productAttrId'];
                        $proInv->stock_in = 0;
                        $proInv->stock_out = $product['quantity'];
                        $proInv->ref_type = ProductInventory::ORDER;
                        $proInv->reference = 'Place Order by Customer';
                        $proInv->date = date("Y-m-d");
                        $proInv->created_by = null;

                        if ($proInv->save()) {
                        } else {
                            $isTransectionFail = true;
                        }
                    }
                }

                $orderStatus = new OrderStatus();
                $orderStatus->order_id = $orderId;
                $orderStatus->status = OrderStatus::PENDING;
                $orderStatus->remarks = "Order Submitted by Admin";
                $orderStatus->date = $date = date("Y-m-d H:i:s");
                $orderStatus->updated_by = Auth::id();
                if (!$orderStatus->save()) {
                    $isTransectionFail = true;
                }

                $paymentDetails = new PaymentDetail();
                $paymentDetails->order_id = $orderId;
                // $paymentDetails->order_no = $orderNo;
                // $paymentDetails->user_id = $userId;
                if ($request->payment_type == 'COD') {
                    $paymentDetails->payment_type = PaymentDetail::CASH_ON_DELIVERY;
                    $paymentDetails->transaction_no = "";
                }
                if ($request->payment_type == 'BKASH') {
                    $paymentDetails->payment_type = PaymentDetail::BKASH;
                    $paymentDetails->transaction_no = "";
                }
                if ($request->payment_type == 'NAGAD') {
                    $paymentDetails->payment_type = PaymentDetail::NAGAD;
                    $paymentDetails->transaction_no = "";
                }
                if ($request->payment_type == 'SSL') {
                    $paymentDetails->payment_type = PaymentDetail::SSL_COMMERZ;
                    $paymentDetails->transaction_no = "";
                }

                if (!$paymentDetails->save()) {
                    $isTransectionFail = true;
                }

                if ($isTransectionFail) {
                    DB::rollBack();
                    return response()->json([
                        'status' => false,
                        'message' => 'Error occurred while creating Order!',
                    ], 404);
                } else {
                    DB::commit();
                    return response()->json([
                        'status' => true,
                        'order_no' => $orderNo,
                        'amount' => $total,
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
    public function show($id)
    {
        return new OrderResource(Order::find($id));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        //
    }

    public function statusChange(Request $request, $id)
    {

        $validator = Validator::make($request->all(), [
            'status' => "required|numeric",
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $order = Order::find($id);
        if (!$order) {
            return response()->json([
                'status' => false,
                'message' => 'Order not found.',
            ], 404);
        }

        try {
            $data =  new OrderStatus();
            $data->order_id = $order->id;
            $data->status = $request->status;
            $data->remarks = 'Change by Admin';
            $data->date = Carbon::now();
            $data->updated_by = Auth::id();
            if ($data->save()) {
                return response()->json([
                    'status' => true,
                    'message' => 'Order status change successfully.',
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Something went wrong.',
                ]);
            }
        } catch (\Throwable $th) {

            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ]);
        }
    }
    public function allStatus()
    {


        return OrderStatus::allOrderStatus();
    }
    public function orderStatusList()
    {
    }

    public function postOrder(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'address_id' => "required|numeric",
            'shipping_charge' => "required|numeric",
            'total_amount' => "required",
            'coupon_code' => "nullable|string|max:255",
            'discount' => "nullable|numeric",
            'payment_type' => "required",
            'products' => 'required|array',
            'products.*.id' => 'required|numeric',
            'products.*.quantity' => 'required|numeric',
            'products.*.sale_price' => 'required|numeric',
        ]);


        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }
        // dd($request->all());
        foreach ($request->products as $product) {
            if ($product['id'] > 0) {
                $pid = $product['id'];
                // $paid = $product['productAttrId'];
                $stock = ProductInventory::getStock($pid, "");
                $pquantity = $product['quantity'];
                $pName = Product::getProductName($pid);
                if ($pquantity > $stock) {

                    if ($stock > 0) {
                        return response([
                            'status' => false,
                            'message' => $pName . ' Quantity is grater then Stock! Please decrease quantity.',
                        ], 404);
                    } else {
                        return response([
                            'status' => false,
                            'message' => $pName . ' is not available in Stock! Please remove product.',
                        ], 404);
                    }
                }
            }
        }

        $sale_price = 0;
        foreach ($request->products as $product) {
            $sale_price  +=  $product['sale_price'] * $product['quantity'];
        }
        // if ((float)$sale_price !=  (float)$request->total_amount) {
        //     return response([
        //         'status' => false,
        //         'message' => 'Price is not match!',
        //     ], 404);
        // }

        if ($request->coupon_code) {


            $voucher = Voucher::where(['coupon_code' => $request->coupon_code, 'status' => 1])
                ->whereDate('start_date', '<=', Carbon::now())
                ->whereDate('end_date', '>=', Carbon::now())
                ->first();
            if ($voucher) {
                // if ($voucher->discountby == 'amount') {
                //     $v_by = 'amount';
                //     $v_amount = $voucher->discount_amount;
                // }
                // if ($voucher->discountby == 'percentage') {
                //     $v_by = 'percentage';
                //     $v_amount = $voucher->discount_percentage;
                // }

            } else {
                return response([
                    'status' => false,
                    'message' => 'Coupon not found.'
                ]);
            }
        }




        try {
            DB::beginTransaction();
            $maxIndex = Order::maxIn();
            $date = Carbon::now();
            $currenDate = $date->timestamp;
            $orderNo = 'EC-' . substr($currenDate, -4) . '' . Str::padLeft($maxIndex, 10, '0');
            $order = new Order();
            $order->user_id = $userId = Auth::user() ? Auth::user()->id : null;
            $order->order_no = $orderNo;
            $order->max_index = $maxIndex;
            $order->vat = 0;
            $order->shipping_charge = ceil($request->shipping_charge);
            $order->discount = $request->discount ? $request->discount : 0;
            $order->coupon_code = $request->coupon_code ?? null;
            $order->total_amount = (float)$total = (($request->total_amount + $order->shipping_charge) - $order->discount);
            $order->created_by = $userId;
            $order->user_address_id = $request->address_id;

            if ($order->save()) {
                $isTransectionFail = false;
                $orderId = $order->id;
                $i = 0;

                // $data = DB::table('carts')->where('session_id', '=', $request->sessionId)->update(['is_ordered' => 1]);
                foreach ($request->products as $product) {
                    if ($product['id'] != 0) {
                        $orderDetails = new OrderDetail();
                        $orderDetails->order_id = $orderId;
                        // $orderDetails->order_no = $orderNo;
                        $orderDetails->product_id = $product['id'];
                        // $orderDetails->attribute_id = $product['productAttrId'];
                        $orderDetails->discount_type = '';
                        $orderDetails->quantity = $product['quantity'];
                        $orderDetails->discount_amount = 0;
                        $orderDetails->vat = 0;
                        $orderDetails->product_price = $product['sale_price'];
                        $netPrice = ((($orderDetails->quantity * $orderDetails->product_price) - $orderDetails->discount_amount) + $orderDetails->vat);
                        $orderDetails->net_price = $netPrice;
                        //  $orderDetails->warranty_id = Product::getWarrantyId($product['Id']);

                        if (!$orderDetails->save()) {
                            $isTransectionFail = true;
                        }

                        $proInv = new ProductInventory();
                        $proInv->product_id = $product['id'];
                        // $proInv->product_attribute_id = $product['productAttrId'];
                        $proInv->stock_in = 0;
                        $proInv->stock_out = $product['quantity'];
                        $proInv->ref_type = ProductInventory::ORDER;
                        $proInv->reference = 'Place Order by Customer';
                        $proInv->date = date("Y-m-d");
                        $proInv->created_by = null;

                        if ($proInv->save()) {
                        } else {
                            $isTransectionFail = true;
                        }
                    }
                }



                $orderStatus = new OrderStatus();
                $orderStatus->order_id = $orderId;
                $orderStatus->status = OrderStatus::PENDING;
                $orderStatus->remarks = "Order Submitted by Consumer";
                $orderStatus->date = $date = date("Y-m-d H:i:s");
                $orderStatus->updated_by = null;
                if (!$orderStatus->save()) {
                    $isTransectionFail = true;
                }

                $paymentDetails = new PaymentDetail();
                $paymentDetails->order_id = $orderId;
                // $paymentDetails->order_no = $orderNo;
                // $paymentDetails->user_id = $userId;
                if ($request->payment_type == 'COD') {
                    $paymentDetails->payment_type = PaymentDetail::CASH_ON_DELIVERY;
                    $paymentDetails->transaction_no = "";
                }
                if ($request->payment_type == 'BKASH') {
                    $paymentDetails->payment_type = PaymentDetail::BKASH;
                    $paymentDetails->transaction_no = "";
                }
                if ($request->payment_type == 'NAGAD') {
                    $paymentDetails->payment_type = PaymentDetail::NAGAD;
                    $paymentDetails->transaction_no = "";
                }
                if ($request->payment_type == 'SSL') {
                    $paymentDetails->payment_type = PaymentDetail::SSL_COMMERZ;
                    $paymentDetails->transaction_no = "";
                }

                if (!$paymentDetails->save()) {
                    $isTransectionFail = true;
                }


                if ($isTransectionFail) {
                    DB::rollBack();
                    return response()->json([
                        'status' => false,
                        'message' => 'Error occurred while creating Order!',
                    ], 404);
                } else {
                    DB::commit();

                    //                     $url = "http://66.45.237.70/maskingapi.php";
                    //                     $number = $phoneNo;
                    //                     $text = "Dear Customer,
                    // Your order placed successfully.your order number is " . $orderNo;
                    //                     $data = array(
                    //                         'username' => "motionview",
                    //                         'password' => "ASDFG12345",
                    //                         'senderid' => "Motion View",
                    //                         'number' => "$number",
                    //                         'message' => "$text"
                    //                     );

                    //                     $smsResponse = Http::asForm()->post($url, $data);

                    //                     $mailTo = Auth::user() ? Auth::user()->email : '';
                    // $cc = 'miyad.mh@gmail.com';
                    // $bcc = 'miyad.mh@gmail.com';
                    // $mailInfo = new \stdClass();
                    // $mailInfo->order_no = $orderNo;
                    // $mailInfo->cus_name = $cus_name;
                    // $mailInfo->total = $total;
                    // $mailInfo->phone_no = $phoneNo;
                    // $mailInfo->products = $request->products;
                    // $mailInfo->discount = $request->discount ? $request->discount : 0;
                    // $mailInfo->shippingFee = $request->shippingFee;
                    // $mailInfo->address = $cus_address;
                    // $mailInfo->date = $date;
                    // $mailResponse = $this->send($mailInfo, $mailTo, $cc, $bcc, $orderNo);

                    return response()->json([
                        'status' => true,
                        'order_no' => $orderNo,
                        'amount' => $total,
                        // 'name' => $cus_name,
                        // 'phone' => $phoneNo,
                        // 'address' => $cus_address,
                        // 'email' => $mailTo,
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

    public function getOrderHistory(Request $request)
    {
        $user_id = Auth::user()->id;
        if ($user_id > 0) {
            $data = Order::where('user_id', '=', $user_id)->orderBy('id', 'DESC')->paginate(10);
            return OrderHistoryResource::collection($data);
        }
    }
}
