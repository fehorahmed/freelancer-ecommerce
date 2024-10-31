<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\ProductInventory;
use App\Models\UserAddress;
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        //
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        //
    }

    public function postOrder(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'address_id' => "required|numeric",
            'shipping_charge' => "required|numeric",
            'totalAmount' => "required",
            'paymentType' => "required",
            'products' => 'required|array',
            'products.id' => 'required|numeric',
            'products.quantity' => 'required|numeric',
            'products.sale_price' => 'required|numeric',
        ]);


        if ($validator->fails()) {
            return response()->json([
                'success' => false,
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
                            'success' => false,
                            'message' => $pName . ' Quantity is grater then Stock! Please decrease quantity.',
                        ], 404);
                    } else {
                        return response([
                            'success' => false,
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
        if ((float)$sale_price !=  (float)$request->totalAmount) {
            return response([
                'success' => false,
                'message' => 'Price is not match!',
            ],404);
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
            $order->total_amount = (float)$total = (($request->totalAmount + $order->shipping_charge) - $order->discount);
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
                        $orderDetails->order_no = $orderNo;
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
                $orderStatus->order_no = $orderNo;
                $orderStatus->status_id = Status::PENDING;
                $orderStatus->remarks = "Order Submitted by Consumer";
                $orderStatus->date = $date = date("Y-m-d H:i:s");
                $orderStatus->updated_by = null;
                if (!$orderStatus->save()) {
                    $isTransectionFail = true;
                }

                $paymentDetails = new PaymentDetails();
                $paymentDetails->order_id = $orderId;
                $paymentDetails->order_no = $orderNo;
                $paymentDetails->user_id = $userId;
                if ($request->paymentType == 'COD') {
                    $paymentDetails->payment_type = PaymentDetails::CASH_ON_DELIVERY;
                    $paymentDetails->transaction_no = "";
                }
                if ($request->paymentType == 'BKASH') {
                    $paymentDetails->payment_type = PaymentDetails::BKASH;
                    $paymentDetails->transaction_no = "";
                }
                if ($request->paymentType == 'NAGAD') {
                    $paymentDetails->payment_type = PaymentDetails::NAGAD;
                    $paymentDetails->transaction_no = "";
                }
                if ($request->paymentType == 'SSL') {
                    $paymentDetails->payment_type = PaymentDetails::SSL_COMMERZ;
                    $paymentDetails->transaction_no = "";
                }


                if (!$paymentDetails->save()) {
                    $isTransectionFail = true;
                }


                if ($isTransectionFail) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'status_code' => 200,
                        'message' => 'Error occurred while creating Order!',
                    ]);
                } else {
                    DB::commit();

                    $url = "http://66.45.237.70/maskingapi.php";
                    $number = $phoneNo;
                    $text = "Dear Customer,
Your order placed successfully.your order number is " . $orderNo;
                    $data = array(
                        'username' => "motionview",
                        'password' => "ASDFG12345",
                        'senderid' => "Motion View",
                        'number' => "$number",
                        'message' => "$text"
                    );

                    $smsResponse = Http::asForm()->post($url, $data);

                    $mailTo = Auth::user() ? Auth::user()->email : '';
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
                        'success' => true,
                        'status_code' => 200,
                        'order_no' => $orderNo,
                        'amount' => $total,
                        'name' => $cus_name,
                        'phone' => $phoneNo,
                        'address' => $cus_address,
                        'email' => $mailTo,
                        'message' => 'Order submitted successfully!',
                    ]);
                }
            } else {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'status_code' => 200,
                    'message' => 'Error occurred while creating Order!',
                ]);
            }

            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
        }
    }
}
