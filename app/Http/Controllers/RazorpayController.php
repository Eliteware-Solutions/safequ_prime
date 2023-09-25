<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\User;
use Illuminate\Http\Request;

use Session;
use Redirect;
use App\Models\CombinedOrder;
use App\Models\Seller;
use Razorpay\Api\Api;
use Illuminate\Support\Facades\Input;
use App\Models\CustomerPackage;
use App\Models\SellerPackage;
use App\Http\Controllers\CustomerPackageController;
use Auth;
use Illuminate\Support\Facades\Log;
use App\Models\PaymentWebhook;
use Twilio\TwiML\Voice\Pay;

class RazorpayController extends Controller
{
    public function payWithRazorpay($request)
    {
        if (Session::has('payment_type')) {
            if (Session::get('payment_type') == 'cart_payment') {
                $user = Auth::user();
                $combined_order = CombinedOrder::with('user')->findOrFail(Session::get('combined_order_id'));
                $wallet_amount = 0;
                if ($request->partial_payment == 'on') {
                    $wallet_amount = $user->balance;
                }

                $notes = [];
                $i = 1;
                foreach ($combined_order->orders as $val) {
                    $notes['ord_' . $i] = json_encode(array("order_id" => "$val->id", "order_code" => "$val->code", "payment_for" => "order", "payment_method" => "Cart Checkout", 'receipt' => $combined_order->id));
                    $i++;
                }
                $api = new Api(env('RAZOR_KEY'), env('RAZOR_SECRET'));

                $orderData = [
                    'receipt'         => $combined_order->id,
                    'amount'          => round(($combined_order->grand_total - $wallet_amount) * 100), // 2000 rupees in paise
                    'currency'        => 'INR',
                    'payment_capture' => 1 // auto capture
                ];

                $razorpayOrder = $api->order->create($orderData);

                $razorpayOrderId = $razorpayOrder['id'];

                return view('frontend.razor_wallet.order_payment_Razorpay', compact('combined_order', 'wallet_amount', 'notes', 'razorpayOrderId'));
            } elseif (Session::get('payment_type') == 'wallet_payment') {
                return view('frontend.razor_wallet.wallet_payment_Razorpay');
            } elseif (Session::get('payment_type') == 'customer_package_payment') {
                return view('frontend.razor_wallet.customer_package_payment_Razorpay');
            } elseif (Session::get('payment_type') == 'seller_package_payment') {
                return view('frontend.razor_wallet.seller_package_payment_Razorpay');
            }
        }
    }

    public function payment(Request $request)
    {
        //Input items of form
        $input = $request->all();
        //get API Configuration
        $api = new Api(env('RAZOR_KEY'), env('RAZOR_SECRET'));

        //Fetch payment information by razorpay_payment_id
        $payment = $api->payment->fetch($input['razorpay_payment_id']);
        $payment_done_at = (isset($payment['created_at']) && $payment['created_at'] != '') ? $payment['created_at'] : '';

        if (count($input) && !empty($input['razorpay_payment_id'])) {
            $payment_detalis = null;
            try {
                if (isset($payment['status']) && $payment['status'] != 'captured') {
                    $response = $api->payment->fetch($input['razorpay_payment_id'])->capture(array('amount' => $payment['amount']));
                } else {
                    $response = $payment;
                }

                if (Session::get('payment_type') == 'cart_payment') {
                    $payment_detalis = json_encode(array('id' => $response['id'], 'method' => $response['method'], 'amount' => $response['amount'], 'wallet_amount' => floatval($input['wallet_amount']), 'currency' => $response['currency'], 'payment_done_at' => $payment_done_at));
                } else {
                    $payment_detalis = json_encode(array('id' => $response['id'], 'method' => $response['method'], 'amount' => $response['amount'], 'currency' => $response['currency'], 'payment_done_at' => $payment_done_at));
                }
                //
            } catch (\Exception $e) {
                // return  $e->getMessage();
                \Session::put('error', $e->getMessage());
                // return redirect()->back();

                if ($payment->status == 'failed') {
                    $payment_detalis = json_encode(array('id' => $payment['id'], 'method' => $payment['method'], 'amount' => $payment['amount'], 'currency' => $payment['currency'], 'error' => $payment->error_reason, 'error_msg' => $payment->error_description, 'payment_done_at' => $payment_done_at));
                }

                $checkoutController = new CheckoutController;
                $return_msg = $checkoutController->checkout_failed(Session::get('combined_order_id'), $payment_detalis);

                flash($return_msg)->error();
                return redirect()->route('cart');
            }

            // Do something here for store payment details in database...
            if (Session::has('payment_type')) {
                if (Session::get('payment_type') == 'cart_payment') {
                    if (floatval($input['wallet_amount']) > 0) {
                        $user = Auth::user();
                        $user->balance -= floatval($input['wallet_amount']);
                        $user->save();
                    }
                    $checkoutController = new CheckoutController;
                    return $checkoutController->checkout_done(Session::get('combined_order_id'), $payment_detalis, floatval($input['wallet_amount']));
                } elseif (Session::get('payment_type') == 'wallet_payment') {
                    $walletController = new WalletController;
                    return $walletController->wallet_payment_done(Session::get('payment_data'), $payment_detalis);
                } elseif (Session::get('payment_type') == 'customer_package_payment') {
                    $customer_package_controller = new CustomerPackageController;
                    return $customer_package_controller->purchase_payment_done(Session::get('payment_data'), $payment);
                } elseif (Session::get('payment_type') == 'seller_package_payment') {
                    $seller_package_controller = new SellerPackageController;
                    return $seller_package_controller->purchase_payment_done(Session::get('payment_data'), $payment);
                }
            }
        }
    }

    public function link_payment_success(Request $request)
    {
        $order = array();
        $reference_id = $request->razorpay_payment_link_reference_id;
        $reference_id_ary = explode("#", $reference_id);
        if (isset($reference_id_ary[0])) {
            $order_id = $reference_id_ary[0];

            $order = Order::findOrFail($order_id);
        }

        return view('frontend.link_payment_order_confirmed', compact('order'));
    }

    public function payment_link_webhook(Request $request)
    {
        Log::error('Payment Link Webhook Data Start');
        Log::error($request);
        Log::error('Payment Link Webhook Data Ends');

        if ($request) {
            if ($request['event'] == 'payment_link.paid') {
                if ($request['event'] && $request['payload']['payment']) {
                    $payment = $request['payload']['payment'];
                    $notes = $payment['entity']['notes'];
                    if (!empty($notes)) {
                        $payment_for = $payment['entity']['notes']['payment_for'];
                        if ($payment_for == 'order') { // When Payment done from Order Link
                            $order_id = $payment['entity']['notes']['order_id'];
                            $order = Order::findOrFail($order_id);
                            if ($order->payment_status != 'paid') {
                                $payment_detalis = json_encode(array('id' => $payment['entity']['id'], 'method' => $payment['entity']['method'], 'amount' => $payment['entity']['base_amount'], 'wallet_amount' => 0, 'currency' => $payment['entity']['currency']));

                                $order->payment_status = 'paid';
                                $order->payment_details = $payment_detalis;
                                if (isset($request['created_at']) && $request['created_at'] != '') {
                                    $order->payment_datetime = date('Y-m-d H:i:s', $request['created_at']);
                                }
                                $order->save();

                                OrderDetail::where('order_id', $order_id)->update([
                                    'payment_status' => 'paid'
                                ]);
                            }
                        } elseif ($payment_for == 'customer_pending_bill') { // When Payment done from Customer Pending Bill Link
                            $user_id = $payment['entity']['notes']['user_id'];
                            $user = User::findOrFail($user_id);

                            if ($user) {
                                $orders = Order::where(array('user_id' => $user_id, 'payment_status' => 'unpaid'))->get();
                                foreach ($orders as $order) {
                                    $payment_detalis = json_encode(array('id' => $payment['entity']['id'], 'method' => $payment['entity']['method'], 'amount' => floatval($order->grand_total) * 100, 'wallet_amount' => 0, 'currency' => $payment['entity']['currency']));

                                    $order->payment_status = 'paid';
                                    $order->payment_details = $payment_detalis;
                                    if (isset($request['created_at']) && $request['created_at'] != '') {
                                        $order->payment_datetime = date('Y-m-d H:i:s', $request['created_at']);
                                    }
                                    $order->save();

                                    OrderDetail::where('order_id', $order->id)->update([
                                        'payment_status' => 'paid'
                                    ]);
                                }

                                $user->pending_bill_url = null;
                                $user->pending_url_amt = 0;
                                $user->save();
                            }
                        }
                    }
                }
            }
        }

        return response()->json('success', 200);
    }

    public function web_payment_webhook(Request $request)
    {
        Log::error('Web Webhook Data Start');
        Log::error($request);
        Log::error('Web Webhook Data Ends');

        if ($request) {
            if ($request['event'] == 'payment.captured') {
                $payment = $request['payload']['payment'];
                $notes = $payment['entity']['notes'];
                foreach ($notes as $note) {
                    $val = json_decode($note);
                    $order_id = $val->order_id;
                    $order = Order::findOrFail($order_id);
                    if ($order->payment_status != 'paid') {
                        $payment_detalis = json_encode(array('id' => $payment['entity']['id'], 'method' => $payment['entity']['method'], 'amount' => $payment['entity']['base_amount'], 'wallet_amount' => 0, 'currency' => $payment['entity']['currency']));

                        $order->payment_status = 'paid';
                        $order->payment_details = $payment_detalis;
                        if (isset($request['created_at']) && $request['created_at'] != '') {
                            $order->payment_datetime = date('Y-m-d H:i:s', $request['created_at']);
                        }
                        $order->save();

                        OrderDetail::where('order_id', $order_id)->update([
                            'payment_status' => 'paid'
                        ]);
                    }
                }
            }
        }

        $webhookData = array();
        $webhookData['entity'] = $request['entity'];
        $webhookData['event'] = $request['account_id'];
        $webhookData['contains'] = $request['contains'];
        $webhookData['payload'] = $request['payload'];
        $webhookData['created_at'] = $request['created_at'];

        $insertData = array();
        $insertData['type'] = 'web';
        $insertData['webhook_data'] = json_encode($webhookData);
        PaymentWebhook::create($insertData);

        return response()->json('success', 200);
    }

    public function user_bill_payment_link_success(Request $request)
    {
        $user = array();
        return view('frontend.user_pending_bill_success', compact('user'));
    }

    public function payAdminOrderWithRazorpay($request)
    {
        if (Session::has('payment_type')) {
            if (Session::get('payment_type') == 'cart_payment') {
                $user = Auth::user();
                $order_data = Order::findOrFail($request->order_id);
                $combined_order = CombinedOrder::with('user')->findOrFail(Session::get('combined_order_id'));
                $wallet_amount = 0;
                if ($request->partial_payment == 'on') {
                    $wallet_amount = $user->balance;
                }

                $notes = [];
                $i = 1;
                foreach ($combined_order->orders as $val) {
                    $notes['ord_' . $i] = json_encode(array("order_id" => "$val->id", "order_code" => "$val->code", "payment_for" => "order", "payment_method" => "Cart Checkout", 'receipt' => $combined_order->id));
                    $i++;
                }
                $api = new Api(env('RAZOR_KEY'), env('RAZOR_SECRET'));

                $orderData = [
                    'receipt'         => $combined_order->id,
                    'amount'          => round(($order_data->grand_total - $wallet_amount) * 100), // 2000 rupees in paise
                    'currency'        => 'INR',
                    'payment_capture' => 1 // auto capture
                ];

                $razorpayOrder = $api->order->create($orderData);

                $razorpayOrderId = $razorpayOrder['id'];

                return view('frontend.razor_wallet.admin_order_payment_Razorpay', compact('combined_order', 'wallet_amount', 'notes', 'razorpayOrderId', 'order_data'));
            }
        }
    }
}
