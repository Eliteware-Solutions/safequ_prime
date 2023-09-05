<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\V2\AuthController;
use Illuminate\Foundation\Auth\RegistersUsers;
use App\Models\Customer;
use App\Models\Seller;
use App\Models\User;
use App\Models\Wallet;
use App\Utility\PayfastUtility;
use Illuminate\Http\Request;
use Auth;
use App\Models\Category;
use App\Models\Cart;
use App\Http\Controllers\PaypalController;
use App\Http\Controllers\InstamojoController;
use App\Http\Controllers\StripePaymentController;
use App\Http\Controllers\PublicSslCommerzPaymentController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaytmController;
use App\Models\Order;
use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\Address;
use App\Models\CombinedOrder;
use Illuminate\Support\Facades\Hash;
use Session;
use App\Utility\PayhereUtility;
use App\Utility\NotificationUtility;

class CheckoutController extends Controller
{
    use RegistersUsers;

    public function __construct()
    {
        //
    }

    //check the selected payment gateway and redirect to that controller accordingly
    public function checkout(Request $request)
    {
        if ($request->payment_option != null) {
            $carts_details = array();
            if (!Auth::check()) {
                $user = User::where('phone', '+91' . $request->phone)->first();

                if ($user) {
                    if (isset($request->hdn_coupon_code)) {
                        $coupon = Coupon::where('code', $request->hdn_coupon_code)->first();
                        $coupon_usage = CouponUsage::where('coupon_id', $coupon->id)->where('user_id', $user->id)->first();
                        if ($coupon_usage) {
                            flash(translate('You already used this coupon!'))->warning();
                            return back();
                        }
                    }
                    $this->guard()->login($user);
                } else {
                    $user = User::create([
                        'name' => $request->name,
                        // 'address' => $request->flat_no . ', ' . $request->address,
                        'address' => $request->address,
                        'city' => $request->city,
                        'email' => $request->email,
                        'email_verified_at' => date("Y-m-d H:i:s"),
                        'password' => Hash::make($request->email),
                        'phone' => '+91' . $request->phone,
                        'balance' => env('WELCOME_BONUS_AMOUNT'),
                        'joined_community_id' => 0,
                    ]);

                    $user->referral_key = md5($user->id);
                    $user->save();
                    $this->guard()->login($user);

                    $customer = new Customer;
                    $customer->user_id = $user->id;
                    $customer->save();

                    $wallet = new Wallet;
                    $wallet->user_id = $user->id;
                    $wallet->amount = env('WELCOME_BONUS_AMOUNT');
                    $wallet->payment_method = 'bonus';
                    $wallet->payment_details = json_encode(array('id' => $user->id, 'amount' => env('WELCOME_BONUS_AMOUNT'), 'method' => 'bonus'));
                    $wallet->save();
                }

                Cart::where('temp_user_id', session('temp_user_id'))
                    ->update([
                        'user_id' => $user->id,
                        // 'temp_user_id' => null
                    ]);

                Session::forget('temp_user_id');

                $carts_details = Cart::where('user_id', $user->id)
                    ->get();
            } else {
                $carts_details = Cart::where('user_id', Auth::user()->id)
                    ->get();
            }

            $return_msg = '';
            $validateCart = true;
            foreach ($carts_details as $cart_data) {
                if (isset($cart_data->product) && $cart_data->product->published == 0) {
                    $return_msg = $cart_data->product->name . ' is no longer available, kindly remove from cart';
                    $validateCart = false;
                    break;
                }
            }

            if (!$validateCart) {
                flash($return_msg)->error();
                return redirect()->route('cart');
            } else {
                // dd($request);
                (new OrderController)->store($request);

                $request->session()->put('payment_type', 'cart_payment');

                $data['combined_order_id'] = $request->session()->get('combined_order_id');
                $request->session()->put('payment_data', $data);

                if ($request->session()->get('combined_order_id') != null) {
                    if ($request->payment_option == 'paypal') {
                        $paypal = new PaypalController;
                        return $paypal->getCheckout();
                    } elseif ($request->payment_option == 'stripe') {
                        $stripe = new StripePaymentController;
                        return $stripe->stripe();
                    } elseif ($request->payment_option == 'mercadopago') {
                        $mercadopago = new MercadopagoController;
                        return $mercadopago->paybill();
                    } elseif ($request->payment_option == 'sslcommerz') {
                        $sslcommerz = new PublicSslCommerzPaymentController;
                        return $sslcommerz->index($request);
                    } elseif ($request->payment_option == 'instamojo') {
                        $instamojo = new InstamojoController;
                        return $instamojo->pay($request);
                    } elseif ($request->payment_option == 'razorpay') {
                        $razorpay = new RazorpayController;
                        return $razorpay->payWithRazorpay($request);
                    } elseif ($request->payment_option == 'payku') {
                        return (new PaykuController)->create($request);
                    } elseif ($request->payment_option == 'voguepay') {
                        $voguePay = new VoguePayController;
                        return $voguePay->customer_showForm();
                    } elseif ($request->payment_option == 'ngenius') {
                        $ngenius = new NgeniusController();
                        return $ngenius->pay();
                    } elseif ($request->payment_option == 'iyzico') {
                        $iyzico = new IyzicoController();
                        return $iyzico->pay();
                    } elseif ($request->payment_option == 'nagad') {
                        $nagad = new NagadController;
                        return $nagad->getSession();
                    } elseif ($request->payment_option == 'bkash') {
                        $bkash = new BkashController;
                        return $bkash->pay();
                    } elseif ($request->payment_option == 'aamarpay') {
                        $aamarpay = new AamarpayController;
                        return $aamarpay->index();
                    } elseif ($request->payment_option == 'flutterwave') {
                        $flutterwave = new FlutterwaveController();
                        return $flutterwave->pay();
                    } elseif ($request->payment_option == 'mpesa') {
                        $mpesa = new MpesaController();
                        return $mpesa->pay();
                    } elseif ($request->payment_option == 'paystack') {
                        if (addon_is_activated('otp_system') && !Auth::user()->email) {
                            flash(translate('Your email should be verified before order'))->warning();
                            return redirect()->route('cart')->send();
                        }
                        $paystack = new PaystackController;
                        return $paystack->redirectToGateway($request);
                    } elseif ($request->payment_option == 'payhere') {
                        $combined_order = CombinedOrder::findOrFail($request->session()->get('combined_order_id'));

                        $combined_order_id = $combined_order->id;
                        $amount = $combined_order->grand_total;
                        $first_name = json_decode($combined_order->shipping_address)->name;
                        $last_name = 'X';
                        $phone = json_decode($combined_order->shipping_address)->phone;
                        $email = json_decode($combined_order->shipping_address)->email;
                        $address = json_decode($combined_order->shipping_address)->address;
                        $city = json_decode($combined_order->shipping_address)->city;

                        return PayhereUtility::create_checkout_form($combined_order_id, $amount, $first_name, $last_name, $phone, $email, $address, $city);
                    } elseif ($request->payment_option == 'payfast') {
                        $combined_order = CombinedOrder::findOrFail($request->session()->get('combined_order_id'));

                        $combined_order_id = $combined_order->id;
                        $amount = $combined_order->grand_total;

                        return PayfastUtility::create_checkout_form($combined_order_id, $amount);
                    } elseif ($request->payment_option == 'paytm') {
                        if (Auth::user()->phone == null) {
                            flash('Please add phone number to your profile')->warning();
                            return redirect()->route('profile');
                        }

                        $paytm = new PaytmController;
                        return $paytm->index();
                    } elseif ($request->payment_option == 'toyyibpay') {
                        return (new ToyyibpayController)->createbill();
                    } else if ($request->payment_option == 'authorizenet') {
                        $authorize_net = new AuthorizeNetController();
                        return $authorize_net->pay();
                    } elseif ($request->payment_option == 'cash_on_delivery') {
                        flash(translate("Your order has been placed successfully"))->success();
                        return redirect()->route('order_confirmed');
                    } elseif ($request->payment_option == 'wallet') {
                        $user = Auth::user();
                        $combined_order = CombinedOrder::findOrFail($request->session()->get('combined_order_id'));
                        if ($user->balance >= $combined_order->grand_total) {
                            $user->balance -= $combined_order->grand_total;
                            $user->save();

                            $payment = json_encode(array('payment_done_at' => strtotime(date('Y-m-d H:i:s'))));
                            return $this->checkout_done($request->session()->get('combined_order_id'), $payment);
                        }
                    } else {
                        $combined_order = CombinedOrder::findOrFail($request->session()->get('combined_order_id'));
                        foreach ($combined_order->orders as $order) {
                            $order->manual_payment = 1;
                            $order->save();
                        }
                        flash(translate('Your order has been placed successfully. Please submit payment information from purchase history'))->success();
                        return redirect()->route('order_confirmed');
                    }
                }
            }
        } else {
            flash(translate('Select Payment Option.'))->warning();
            return back();
        }
    }

    //redirects to this method after a successful checkout
    public function checkout_done($combined_order_id, $payment, $wallet_amount = 0)
    {
        $paymentData = json_decode($payment, true);
        $combined_order = CombinedOrder::findOrFail($combined_order_id);

        foreach ($combined_order->orders as $key => $order) {
            $order = Order::findOrFail($order->id);
            $order->payment_status = 'paid';
            $order->payment_details = $payment;

            if (isset($paymentData['payment_done_at']) && $paymentData['payment_done_at'] != '') {
                $order->payment_datetime = date('Y-m-d H:i:s', $paymentData['payment_done_at']);
            }

            $order->save();

            // If Order is done from Wallet then make transaction entry in Wallet of Debit
            if ($key == 0 && ($order->payment_type == "wallet" || $wallet_amount > 0)) {
                $amount = $order->grand_total;
                if ($wallet_amount > 0) {
                    $amount = $wallet_amount;
                }
                $wallet = new Wallet;
                $wallet->user_id = $order->user_id;
                $wallet->amount = '-' . $amount;
                $wallet->payment_method = 'order';
                $wallet->payment_details = json_encode(array('id' => $order->id, 'code' => $order->code, 'amount' => $amount, 'method' => 'order'));
                $wallet->save();
            }

            calculateCommissionAffilationClubPoint($order);

            NotificationUtility::sendOrderPlacedNotification($order);
        }

        Session::put('combined_order_id', $combined_order_id);
        return redirect()->route('order_confirmed');
    }

    public function get_shipping_info(Request $request)
    {
        $carts = Cart::where('user_id', Auth::user()->id)->get();
        //        if (Session::has('cart') && count(Session::get('cart')) > 0) {
        if ($carts && count($carts) > 0) {
            $categories = Category::all();
            return view('frontend.shipping_info', compact('categories', 'carts'));
        }
        flash(translate('Your cart is empty'))->success();
        return back();
    }

    public function store_shipping_info(Request $request)
    {
        if ($request->address_id == null) {
            flash(translate("Please add shipping address"))->warning();
            return back();
        }

        $carts = Cart::where('user_id', Auth::user()->id)->get();

        foreach ($carts as $key => $cartItem) {
            $cartItem->address_id = $request->address_id;
            $cartItem->save();
        }

        return view('frontend.delivery_info', compact('carts'));
        // return view('frontend.payment_select', compact('total'));
    }

    public function store_delivery_info(Request $request)
    {
        $carts = Cart::where('user_id', Auth::user()->id)
            ->get();

        if ($carts->isEmpty()) {
            flash(translate('Your cart is empty'))->warning();
            return redirect()->route('home');
        }

        $shipping_info = Address::where('id', $carts[0]['address_id'])->first();
        $total = 0;
        $tax = 0;
        $shipping = 0;
        $subtotal = 0;

        if ($carts && count($carts) > 0) {
            foreach ($carts as $key => $cartItem) {
                $product = \App\Models\Product::find($cartItem['product_id']);
                $tax += $cartItem['tax'] * $cartItem['quantity'];
                $subtotal += $cartItem['price'] * $cartItem['quantity'];

                if ($request['shipping_type_' . $product->user_id] == 'pickup_point') {
                    $cartItem['shipping_type'] = 'pickup_point';
                    $cartItem['pickup_point'] = $request['pickup_point_id_' . $product->user_id];
                } else {
                    $cartItem['shipping_type'] = 'home_delivery';
                }
                $cartItem['shipping_cost'] = 0;
                if ($cartItem['shipping_type'] == 'home_delivery') {
                    $cartItem['shipping_cost'] = getShippingCost($carts, $key);
                }

                if (isset($cartItem['shipping_cost']) && is_array(json_decode($cartItem['shipping_cost'], true))) {

                    foreach (json_decode($cartItem['shipping_cost'], true) as $shipping_region => $val) {
                        if ($shipping_info['city'] == $shipping_region) {
                            $cartItem['shipping_cost'] = (float)($val);
                            break;
                        } else {
                            $cartItem['shipping_cost'] = 0;
                        }
                    }
                } else {
                    if (
                        !$cartItem['shipping_cost'] ||
                        $cartItem['shipping_cost'] == null ||
                        $cartItem['shipping_cost'] == 'null'
                    ) {

                        $cartItem['shipping_cost'] = 0;
                    }
                }

                $shipping += $cartItem['shipping_cost'];
                $cartItem->save();
            }
            $total = $subtotal + $tax + $shipping;
            return view('frontend.payment_select', compact('carts', 'shipping_info', 'total'));
        } else {
            flash(translate('Your Cart was empty'))->warning();
            return redirect()->route('home');
        }
    }

    public function apply_coupon_code(Request $request)
    {
        $coupon = Coupon::where('code', $request->code)->first();
        $response_message = array();
        $user_data = Auth::user();
        if ($coupon != null) {
            if (strtotime(date('d-m-Y')) >= $coupon->start_date && strtotime(date('d-m-Y')) <= $coupon->end_date) {
                $useCoupon = false;
                if (Auth::user()) {
                    $userCouponUsage = CouponUsage::where('user_id', Auth::user()->id)->where('coupon_id', $coupon->id)->first();
                    if ($userCouponUsage == null) {
                        $useCoupon = true;
                    }
                } else {
                    if ($request->session()->get('temp_user_id')) {
                        $useCoupon = true;
                    }
                }
                if ($useCoupon) {
                    $flag = true;
                    $coupon_details = json_decode($coupon->details);

                    $carts = array();
                    if (Auth::user()) {
                        $carts = Cart::where('user_id', Auth::user()->id)
                            ->where('owner_id', $request->owner_id)
                            ->get();
                    } elseif ($request->session()->get('temp_user_id')) {
                        $carts = Cart::where('temp_user_id', $request->session()->get('temp_user_id'))
                            ->where('owner_id', $request->owner_id)
                            ->get();
                    }

                    $coupon_discount = 0;
                    if ($coupon->type == 'cart_base') {
                        $subtotal = 0;
                        $tax = 0;
                        $shipping = 0;
                        foreach ($carts as $key => $cartItem) {
                            $subtotal += $cartItem['price'] * $cartItem['quantity'];
                            $tax += $cartItem['tax'] * $cartItem['quantity'];
                            $shipping += $cartItem['shipping_cost'];
                        }
                        $sum = $subtotal + $tax + $shipping;

                        if ($sum >= $coupon_details->min_buy) {
                            if ($coupon->discount_type == 'percent') {
                                $coupon_discount = ($sum * $coupon->discount) / 100;
                                if ($coupon_discount > $coupon_details->max_discount) {
                                    $coupon_discount = $coupon_details->max_discount;
                                }
                            } elseif ($coupon->discount_type == 'amount') {
                                $coupon_discount = $coupon->discount;
                            }
                        } else {
                            $flag = false;
                            $response_message['response'] = 'warning';
                            $response_message['message'] = translate('Minimum cart value should be ' . single_price_web($coupon_details->min_buy) . ' to apply this coupon.');
                        }
                    } elseif ($coupon->type == 'product_base') {
                        foreach ($carts as $key => $cartItem) {
                            foreach ($coupon_details as $key => $coupon_detail) {
                                if ($coupon_detail->product_id == $cartItem['product_id']) {
                                    if ($coupon->discount_type == 'percent') {
                                        $coupon_discount += ($cartItem['price'] * $coupon->discount / 100) * $cartItem['quantity'];
                                    } elseif ($coupon->discount_type == 'amount') {
                                        $coupon_discount += $coupon->discount * $cartItem['quantity'];
                                    }
                                }
                            }
                        }
                    }

                    if ($flag) {
                        $where = array();
                        if (Auth::user()) {
                            $where = array('user_id' => Auth::user()->id);
                        } elseif ($request->session()->get('temp_user_id')) {
                            $where = array('temp_user_id' => $request->session()->get('temp_user_id'));
                        }
                        Cart::where($where)
                            ->where('owner_id', $request->owner_id)
                            ->update(
                                [
                                    'discount'       => $coupon_discount / count($carts),
                                    'coupon_code'    => $request->code,
                                    'coupon_applied' => 1
                                ]
                            );

                        $response_message['response'] = 'success';
                        $response_message['message'] = translate('Coupon has been applied');
                    }
                } else {
                    $response_message['response'] = 'warning';
                    $response_message['message'] = translate('You already used this coupon!');
                }
            } else {
                $response_message['response'] = 'warning';
                $response_message['message'] = translate('Coupon expired!');
            }
        } else {
            $response_message['response'] = 'danger';
            $response_message['message'] = translate('Invalid coupon!');
        }

        $carts = array();
        if (Auth::user()) {
            $carts = Cart::where('user_id', Auth::user()->id)
                ->get();
        } elseif ($request->session()->get('temp_user_id')) {
            $carts = Cart::where('temp_user_id', $request->session()->get('temp_user_id'))
                ->get();
        }
        //        $shipping_info = Address::where('id', $carts[0]['address_id'])->first();

        $returnHTML = view('frontend.partials.cart_details', compact('coupon', 'carts', 'user_data'))->render();
        return response()->json(array('response_message' => $response_message, 'html' => $returnHTML));
    }

    public function remove_coupon_code(Request $request)
    {
        $where = array();
        if (Auth::user()) {
            $where = array('user_id' => Auth::user()->id);
        } elseif ($request->session()->get('temp_user_id')) {
            $where = array('temp_user_id' => $request->session()->get('temp_user_id'));
        }
        Cart::where($where)
            ->update(
                [
                    'discount'       => 0.00,
                    'coupon_code'    => '',
                    'coupon_applied' => 0
                ]
            );

        $coupon = Coupon::where('code', $request->code)->first();
        if (Auth::user()) {
            $carts = Cart::where('user_id', Auth::user()->id)
                ->get();
        } elseif ($request->session()->get('temp_user_id')) {
            $carts = Cart::where('temp_user_id', $request->session()->get('temp_user_id'))
                ->get();
        }

        //        $shipping_info = Address::where('id', $carts[0]['address_id'])->first();
        $user_data = Auth::user();
        return view('frontend.partials.cart_details', compact('coupon', 'carts', 'user_data'));
    }

    public function apply_club_point(Request $request)
    {
        if (addon_is_activated('club_point')) {

            $point = $request->point;

            if (Auth::user()->point_balance >= $point) {
                $request->session()->put('club_point', $point);
                flash(translate('Point has been redeemed'))->success();
            } else {
                flash(translate('Invalid point!'))->warning();
            }
        }
        return back();
    }

    public function remove_club_point(Request $request)
    {
        $request->session()->forget('club_point');
        return back();
    }

    public function order_confirmed()
    {
        $combined_order = CombinedOrder::findOrFail(Session::get('combined_order_id'));

        Cart::where('user_id', $combined_order->user_id)
            ->delete();

        //Session::forget('club_point');
        //Session::forget('combined_order_id');

        return view('frontend.order_confirmed', compact('combined_order'));
    }
}
