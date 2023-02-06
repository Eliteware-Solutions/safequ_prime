<?php

namespace App\Http\Controllers;

use App\Models\Order;
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
                return view('frontend.razor_wallet.order_payment_Razorpay', compact('combined_order', 'wallet_amount'));
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

        if (count($input) && !empty($input['razorpay_payment_id'])) {
            $payment_detalis = null;
            try {
                $response = $api->payment->fetch($input['razorpay_payment_id'])->capture(array('amount' => $payment['amount']));
                if (Session::get('payment_type') == 'cart_payment') {
                    $payment_detalis = json_encode(array('id' => $response['id'], 'method' => $response['method'], 'amount' => $response['amount'], 'wallet_amount' => floatval($input['wallet_amount']), 'currency' => $response['currency']));
                } else {
                    $payment_detalis = json_encode(array('id' => $response['id'], 'method' => $response['method'], 'amount' => $response['amount'], 'currency' => $response['currency']));
                }
            } catch (\Exception $e) {
//                return  $e->getMessage();
                \Session::put('error', $e->getMessage());
                return redirect()->back();
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
        Log::error('Webhook Data Start');
        Log::error($request);
        Log::error('Webhook Data Ends');
        if ($request) {
            if ($request['event'] == 'payment_link.paid') {
                if ($request['event'] && $request['payload']['payment']) {
                    $payment = $request['payload']['payment'];
                    $order_id = $payment['entity']['notes']['order_id'];
                    $order = Order::findOrFail($order_id);
                    if ($order->payment_status != 'paid') {
                        $payment_detalis = json_encode(array('id' => $payment['entity']['id'], 'method' => $payment['entity']['method'], 'amount' => $payment['entity']['base_amount'], 'wallet_amount' => 0, 'currency' => $payment['entity']['currency']));

                        $order->payment_status = 'paid';
                        $order->payment_details = $payment_detalis;
                        $order->save();
                    }
                }
            }
        }

        return true;
    }
}
