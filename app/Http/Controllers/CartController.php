<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\ProductStock;
use App\Models\Shop;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Cart;
use App\Models\User;
use Auth;
use Session;
use Cookie;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $user_data = array();
        $shop = array();
        if (auth()->user() != null) {
            $user_id = auth()->user()->id;
            $user_data = auth()->user();
            if ($request->session()->get('temp_user_id')) {
                Cart::where('temp_user_id', $request->session()->get('temp_user_id'))
                    ->update(
                        [
                            'user_id'      => $user_id,
                            'temp_user_id' => null
                        ]
                    );

                Session::forget('temp_user_id');
            }
            $carts = Cart::with('product_stock')->where('user_id', $user_id)->get()->sortBy('product_stock.purchase_end_date');
        } else {
            $temp_user_id = $request->session()->get('temp_user_id');
            // $carts = Cart::where('temp_user_id', $temp_user_id)->get();
            $carts = ($temp_user_id != null) ? Cart::where('temp_user_id', $temp_user_id)->get() : [];
        }

        foreach($carts as $prd) {
            $prd['delivery'] = $this->get_delivery_day($prd->product->parent_category->slug);
        }

        $shops = Shop::get();

        return view('frontend.view_cart', compact('carts', 'user_data', 'shops'));
    }

    // Get Products Delivery Day
    public function get_delivery_day($category)
    {
        if ($category == 'fruit') {
            if (date('D') == 'Sun') {
                return "Will be delivered tomorrow";
            }

            if (date('His') < '130000') {
                return "Will be delivered today";
            } else {
                if (date('D') != 'Sat') {
                    return "Will be delivered tomorrow";
                } else {
                    return "Will be delivered on Monday";
                }
            }
        } else if ($category == 'vegetables') {
            if (date('wHis') < '2130000') {
                return "Will be delivered on Wednesday";
            } else if (date('wHis') < '5130000') {
                return "Will be delivered on Saturday";
            } else {
                return "Will be delivered on Wednesday";
            }
        }
    }

    public function userOrderCart($id)
    {
        if ($id) {
            $decode_data = base64_decode($id);
            $decode_data_ary = explode('#', $decode_data);
            if (isset($decode_data_ary[1]) && !empty($decode_data_ary[1])) {
                $decode_data_ary2 = explode('$', $decode_data_ary[1]);
                if (isset($decode_data_ary2[0]) && intval($decode_data_ary2[0]) > 0) {
                    $user_id = $decode_data_ary2[0];
                    $user = User::where(['id' => $user_id, 'user_type' => 'customer'])->first();
                    if ($user) {
                        auth()->login($user, true);
                        return redirect()->route('cart');
                    } else {
                        return redirect()->route('user.login');
                    }
                } else {
                    return redirect()->route('user.login');
                }
            } else {
                return redirect()->route('user.login');
            }
        } else {
            return redirect()->route('user.login');
        }
    }

    public function showCartModal(Request $request)
    {
        $product = Product::find($request->id);
        return view('frontend.partials.addToCart', compact('product'));
    }

    public function showCartModalAuction(Request $request)
    {
        $product = Product::find($request->id);
        return view('auction.frontend.addToCartAuction', compact('product'));
    }

    public function addToCart(Request $request)
    {
        $product = Product::find($request->id);
        $carts = array();
        $data = array();

        if (auth()->user() != null) {
            $user_id = Auth::user()->id;
            $data['user_id'] = $user_id;
            $carts = Cart::where('user_id', $user_id)->get();
        } else {
            if ($request->session()->get('temp_user_id')) {
                $temp_user_id = $request->session()->get('temp_user_id');
            } else {
                $temp_user_id = bin2hex(random_bytes(10));
                $request->session()->put('temp_user_id', $temp_user_id);
            }
            $data['temp_user_id'] = $temp_user_id;
            $carts = Cart::where('temp_user_id', $temp_user_id)->get();
        }

        $data['product_id'] = $product->id;
        $data['product_stock_id'] = $request->product_stock_id;
        $data['owner_id'] = $product->user_id;

        $str = '';
        $tax = 0;
        if ($product->auction_product == 0) {
            /*if($product->digital != 1 && $request->quantity < $product->min_qty) {
                return array(
                    'status' => 0,
                    'cart_count' => count($carts),
                    'modal_view' => view('frontend.partials.minQtyNotSatisfied', [ 'min_qty' => $product->min_qty ])->render(),
                    'nav_cart_view' => view('frontend.partials.cart')->render(),
                );
            }*/

            //check the color enabled or disabled for the product
            if ($request->has('color')) {
                $str = $request['color'];
            }

            if ($product->digital != 1) {
                //Gets all the choice values of customer choice option and generate a string like Black-S-Cotton
                foreach (json_decode(Product::find($request->id)->choice_options) as $key => $choice) {
                    if ($str != null) {
                        $str .= '-' . str_replace(' ', '', $request['attribute_id_' . $choice->attribute_id]);
                    } else {
                        $str .= str_replace(' ', '', $request['attribute_id_' . $choice->attribute_id]);
                    }
                }
            }

            $data['variation'] = $str;

            $product_stock = $product->stocks->where('id', $data['product_stock_id'])->first();
            $price = $product_stock->price;

            if ($product->wholesale_product) {
                $wholesalePrice = $product_stock->wholesalePrices->where('min_qty', '<=', $request->quantity)->where('max_qty', '>=', $request->quantity)->first();
                if ($wholesalePrice) {
                    $price = $wholesalePrice->price;
                }
            }

            $quantity = $product_stock->qty;

            /*if($quantity < $request['quantity']){
                return array(
                    'status' => 0,
                    'cart_count' => count($carts),
                    'modal_view' => view('frontend.partials.outOfStockCart')->render(),
                    'nav_cart_view' => view('frontend.partials.cart')->render(),
                );
            }*/

            //discount calculation
            $discount_applicable = false;

            if ($product->discount_start_date == null) {
                $discount_applicable = true;
            } elseif (
                strtotime(date('d-m-Y H:i:s')) >= $product->discount_start_date &&
                strtotime(date('d-m-Y H:i:s')) <= $product->discount_end_date
            ) {
                $discount_applicable = true;
            }

            if ($discount_applicable) {
                if ($product->discount_type == 'percent') {
                    $price -= ($price * $product->discount) / 100;
                } elseif ($product->discount_type == 'amount') {
                    $price -= $product->discount;
                }
            }

            //calculation of taxes
            foreach ($product->taxes as $product_tax) {
                if ($product_tax->tax_type == 'percent') {
                    $tax += ($price * $product_tax->tax) / 100;
                } elseif ($product_tax->tax_type == 'amount') {
                    $tax += $product_tax->tax;
                }
            }

            $data['quantity'] = $request->quantity;
            $data['price'] = $price;
            $data['tax'] = $tax;
            //$data['shipping'] = 0;
            $data['shipping_cost'] = 0;
            $data['shipping_type'] = 'home_delivery';
            $data['product_referral_code'] = null;
            $data['cash_on_delivery'] = $product->cash_on_delivery;
            $data['digital'] = $product->digital;

            if ($request->quantity == null) {
                $data['quantity'] = 1;
            }

            if (Cookie::has('referred_product_id') && Cookie::get('referred_product_id') == $product->id) {
                $data['product_referral_code'] = Cookie::get('product_referral_code');
            }

            if ($carts && count($carts) > 0) {
                $foundInCart = false;

                foreach ($carts as $key => $cartItem) {
                    $cart_product = Product::where('id', $cartItem['product_id'])->first();
                    if ($cart_product->auction_product == 1) {
                        return array(
                            'status'        => 0,
                            'cart_count'    => count($carts),
                            'cart_total'    => 0,
                            'modal_view'    => view('frontend.partials.auctionProductAlredayAddedCart')->render(),
                            'nav_cart_view' => view('frontend.partials.cart')->render(),
                        );
                    }

                    if ($cartItem['product_id'] == $request->id && $cartItem['product_stock_id'] == $request->product_stock_id) {
//                        $product_stock = $cart_product->stocks->where('variant', $str)->first();
                        $product_stock = $cart_product->stocks->where('id', $request->product_stock_id)->first();
                        $quantity = $product_stock->qty;
                        /*if($quantity < $cartItem['quantity'] + $request['quantity']){
                            return array(
                                'status' => 0,
                                'cart_count' => count($carts),
                                'modal_view' => view('frontend.partials.outOfStockCart')->render(),
                                'nav_cart_view' => view('frontend.partials.cart')->render(),
                            );
                        }*/

//                        if (($str != null && $cartItem['variation'] == $str) || $str == null) {
                        if ($product_stock) {
                            $foundInCart = true;
                            // $cartItem['quantity'] += $request['quantity'];
                            $cartItem['quantity'] = $request->quantity;

                            if ($cart_product->wholesale_product) {
                                $wholesalePrice = $product_stock->wholesalePrices->where('min_qty', '<=', $request->quantity)->where('max_qty', '>=', $request->quantity)->first();
                                if ($wholesalePrice) {
                                    $price = $wholesalePrice->price;
                                }
                            }

                            $cartItem['price'] = $price;
                            $cartItem->save();
                        }
                    }
                }

                if (!$foundInCart) {
                    Cart::create($data);
                }
            } else {
                Cart::create($data);
            }

            if (auth()->user() != null) {
                $user_id = Auth::user()->id;
                $carts = Cart::where('user_id', $user_id)->get();
            } else {
                $temp_user_id = $request->session()->get('temp_user_id');
                $carts = Cart::where('temp_user_id', $temp_user_id)->get();
            }

            $carts = $this->manageCartCoupon($carts);

            $cartTotalAmount = 0;
            foreach ($carts as $cartItem) {
                $cartTotalAmount = $cartTotalAmount + ($cartItem['price'] + $cartItem['tax']) * $cartItem['quantity'];
            }

            $product_shipping_cost = calculateShippingCost($carts);
            $cartTotalAmount += $product_shipping_cost;

            if ($carts && $carts->sum('discount') > 0) {
                $cartTotalAmount -= $carts->sum('discount');
            }

            return array(
                'status'     => 1,
                'cart_count' => count($carts),
                'cart_total' => $cartTotalAmount,
                // 'modal_view' => view('frontend.partials.addedToCart', compact('product', 'data'))->render(),
                // 'nav_cart_view' => view('frontend.partials.cart')->render(),
            );
        } else {
            $price = $product->bids->max('amount');

            foreach ($product->taxes as $product_tax) {
                if ($product_tax->tax_type == 'percent') {
                    $tax += ($price * $product_tax->tax) / 100;
                } elseif ($product_tax->tax_type == 'amount') {
                    $tax += $product_tax->tax;
                }
            }

            $data['quantity'] = 1;
            $data['price'] = $price;
            $data['tax'] = $tax;
            $data['shipping_type'] = 'home_delivery';
            $data['shipping_cost'] = 0;
            $data['product_referral_code'] = null;
            $data['cash_on_delivery'] = $product->cash_on_delivery;
            $data['digital'] = $product->digital;

            if (count($carts) == 0) {
                Cart::create($data);
            }
            if (auth()->user() != null) {
                $user_id = Auth::user()->id;
                $carts = Cart::where('user_id', $user_id)->get();
            } else {
                $temp_user_id = $request->session()->get('temp_user_id');
                $carts = Cart::where('temp_user_id', $temp_user_id)->get();
            }
            calculateShippingCost($carts);

            $carts = $this->manageCartCoupon($carts);

            return array(
                'status'        => 1,
                'cart_count'    => count($carts),
                'cart_total'    => 0,
                'modal_view'    => view('frontend.partials.addedToCart', compact('product', 'data'))->render(),
                'nav_cart_view' => view('frontend.partials.cart')->render(),
            );
        }
    }

    public function manageCartCoupon($cartsData)
    {
        if ($cartsData && isset($cartsData[0]) && !empty($cartsData[0])) {
            if ($cartsData[0]->coupon_applied > 0 && $cartsData[0]->coupon_code && $cartsData[0]->owner_id > 0) {
                $coupon_code = $cartsData[0]->coupon_code;
                $owner_id = $cartsData[0]->owner_id;
                $coupon = Coupon::where('code', $coupon_code)->first();
                if ($coupon != null) {
                    if (strtotime(date('d-m-Y')) >= $coupon->start_date && strtotime(date('d-m-Y')) <= $coupon->end_date) {
                        $useCoupon = false;
                        if (Auth::user()) {
                            $userCouponUsage = CouponUsage::where('user_id', Auth::user()->id)->where('coupon_id', $coupon->id)->first();
                            if ($userCouponUsage == null) {
                                $useCoupon = true;
                            }
                        } else {
                            if (session('temp_user_id')) {
                                $useCoupon = true;
                            }
                        }

                        if ($useCoupon) {
                            $flag = true;
                            $coupon_details = json_decode($coupon->details);
                            $carts = $cartsData;
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

                            $updateCartCouponData = array();
                            if ($flag) {
                                $updateCartCouponData['discount'] = $coupon_discount / count($carts);
                                $updateCartCouponData['coupon_code'] = $coupon_code;
                                $updateCartCouponData['coupon_applied'] = 1;
                            } else {
                                $updateCartCouponData['discount'] = 0;
                                $updateCartCouponData['coupon_code'] = '';
                                $updateCartCouponData['coupon_applied'] = 0;
                            }

                            $where = array();
                            if (Auth::user()) {
                                $where = array('user_id' => Auth::user()->id);
                            } elseif(session('temp_user_id')) {
                                $where = array('temp_user_id' => session('temp_user_id'));
                            }
                            Cart::where($where)
                                ->where('owner_id', $owner_id)
                                ->update($updateCartCouponData);
                        }
                    }
                }
            }
        }

        $where = array();
        if (Auth::user()) {
            $where = array('user_id' => Auth::user()->id);
        } elseif(session('temp_user_id')) {
            $where = array('temp_user_id' => session('temp_user_id'));
        }
        $carts = Cart::where($where)->get();
        return $carts;
    }

    public function bulkAddToCart(Request $request)
    {
        Cart::where('user_id', auth()->user()->id)->delete();
        if ($request->data) {
            foreach ($request->data as $val) {
                $req_quantity = floatval($val['qty']);
                $product_id = intval($val['product_id']);
                $product_stock_id = intval($val['product_stock_id']);

                $product = Product::find($product_id);
                $carts = array();
                $data = array();

                if (auth()->user() != null) {
                    $user_id = Auth::user()->id;
                    $data['user_id'] = $user_id;
                    $carts = Cart::where('user_id', $user_id)->get();
                } else {
                    if ($request->session()->get('temp_user_id')) {
                        $temp_user_id = $request->session()->get('temp_user_id');
                    } else {
                        $temp_user_id = bin2hex(random_bytes(10));
                        $request->session()->put('temp_user_id', $temp_user_id);
                    }
                    $data['temp_user_id'] = $temp_user_id;
                    $carts = Cart::where('temp_user_id', $temp_user_id)->get();
                }

                $data['product_id'] = $product->id;
                $data['product_stock_id'] = $product_stock_id;
                $data['owner_id'] = $product->user_id;

                $str = '';
                $tax = 0;

                if ($product->digital != 1) {
                    //Gets all the choice values of customer choice option and generate a string like Black-S-Cotton
                    foreach (json_decode(Product::find($product_id)->choice_options) as $key => $choice) {
                        if ($str != null) {
                            $str .= '-' . str_replace(' ', '', $request['attribute_id_' . $choice->attribute_id]);
                        } else {
                            $str .= str_replace(' ', '', $request['attribute_id_' . $choice->attribute_id]);
                        }
                    }
                }

                $data['variation'] = $str;

                $product_stock = $product->stocks->where('id', $data['product_stock_id'])->first();
                $price = $product_stock->price;

                if ($product->wholesale_product) {
                    $wholesalePrice = $product_stock->wholesalePrices->where('min_qty', '<=', $req_quantity)->where('max_qty', '>=', $req_quantity)->first();
                    if ($wholesalePrice) {
                        $price = $wholesalePrice->price;
                    }
                }

                $quantity = $product_stock->qty;

                //discount calculation
                $discount_applicable = false;

                if ($product->discount_start_date == null) {
                    $discount_applicable = true;
                } elseif (
                    strtotime(date('d-m-Y H:i:s')) >= $product->discount_start_date &&
                    strtotime(date('d-m-Y H:i:s')) <= $product->discount_end_date
                ) {
                    $discount_applicable = true;
                }

                if ($discount_applicable) {
                    if ($product->discount_type == 'percent') {
                        $price -= ($price * $product->discount) / 100;
                    } elseif ($product->discount_type == 'amount') {
                        $price -= $product->discount;
                    }
                }

                //calculation of taxes
                foreach ($product->taxes as $product_tax) {
                    if ($product_tax->tax_type == 'percent') {
                        $tax += ($price * $product_tax->tax) / 100;
                    } elseif ($product_tax->tax_type == 'amount') {
                        $tax += $product_tax->tax;
                    }
                }

                $data['quantity'] = $req_quantity;
                $data['price'] = $price;
                $data['tax'] = $tax;
                //$data['shipping'] = 0;
                $data['shipping_cost'] = 0;
                $data['shipping_type'] = 'home_delivery';
                $data['product_referral_code'] = null;
                $data['cash_on_delivery'] = $product->cash_on_delivery;
                $data['digital'] = $product->digital;

                if ($req_quantity == null) {
                    $data['quantity'] = 1;
                }

                if (Cookie::has('referred_product_id') && Cookie::get('referred_product_id') == $product->id) {
                    $data['product_referral_code'] = Cookie::get('product_referral_code');
                }

                if ($carts && count($carts) > 0) {
                    $foundInCart = false;

                    foreach ($carts as $key => $cartItem) {
                        $cart_product = Product::where('id', $cartItem['product_id'])->first();

                        if ($cartItem['product_id'] == $product_id && $cartItem['product_stock_id'] == $product_stock_id) {
                            $product_stock = $cart_product->stocks->where('variant', $str)->first();
                            $quantity = $product_stock->qty;

                            if (($str != null && $cartItem['variation'] == $str) || $str == null) {
                                $foundInCart = true;

                                $cartItem['quantity'] += $req_quantity;

                                if ($cart_product->wholesale_product) {
                                    $wholesalePrice = $product_stock->wholesalePrices->where('min_qty', '<=', $req_quantity)->where('max_qty', '>=', $req_quantity)->first();
                                    if ($wholesalePrice) {
                                        $price = $wholesalePrice->price;
                                    }
                                }

                                $cartItem['price'] = $price;

                                $cartItem->save();
                            }
                        }
                    }
                    if (!$foundInCart) {
                        Cart::create($data);
                    }
                } else {
                    Cart::create($data);
                }

                if (auth()->user() != null) {
                    $user_id = Auth::user()->id;
                    $carts = Cart::where('user_id', $user_id)->get();
                } else {
                    $temp_user_id = $request->session()->get('temp_user_id');
                    $carts = Cart::where('temp_user_id', $temp_user_id)->get();
                }
                calculateShippingCost($carts);
            }

            return array(
                'status'     => 1
            );
        } else {
            return array(
                'status'     => 0
            );
        }
    }

    //removes from Cart
    public function removeFromCart(Request $request)
    {
        $cur_cart = Cart::where('id', $request->id)->first();
        if ($cur_cart) {
            Cart::destroy($request->id);
        } elseif (isset($request->product_id) && isset($request->product_stock_id)) {
            $cartData = array();
            if (auth()->user() != null) {
                $user_id = Auth::user()->id;
                $cartData = Cart::where(['user_id' => $user_id, 'product_id' => $request->product_id, 'product_stock_id' => $request->product_stock_id])->first();
            } else {
                $temp_user_id = $request->session()->get('temp_user_id');
                $cartData = Cart::where(['temp_user_id' => $temp_user_id, 'product_id' => $request->product_id, 'product_stock_id' => $request->product_stock_id])->first();
            }
            if ($cartData) {
                Cart::destroy($cartData->id);
            }
        }

        $user_data = array();
        $shop = array();
        if (auth()->user() != null) {
            $user_id = Auth::user()->id;
            $carts = Cart::where('user_id', $user_id)->get();
            $user_data = Auth::user();

            $shop = Shop::where('user_id', intval($user_data->joined_community_id))->first();
        } else {
            $temp_user_id = $request->session()->get('temp_user_id');
            $carts = Cart::where('temp_user_id', $temp_user_id)->get();
        }

        $cartTotalAmount = 0;
        foreach ($carts as $cartItem) {
            $cartTotalAmount = $cartTotalAmount + ($cartItem['price'] + $cartItem['tax']) * $cartItem['quantity'];
        }

        $product_shipping_cost = calculateShippingCost($carts);
        $cartTotalAmount += $product_shipping_cost;

        if ($carts && $carts->sum('discount') > 0) {
            $cartTotalAmount -= $carts->sum('discount');
        }

        $carts = $this->manageCartCoupon($carts);

        foreach($carts as $prd) {
            $prd['delivery'] = $this->get_delivery_day($prd->product->parent_category->slug);
        }

        return array(
            'cart_count'    => count($carts),
            'cart_total'    => $cartTotalAmount,
            'cart_view'     => view('frontend.partials.cart_details', compact('carts', 'user_data', 'shop'))->render(),
            'nav_cart_view' => view('frontend.partials.cart')->render(),
        );
    }

    //updated the quantity for a cart item
    public function updateQuantity(Request $request)
    {
        $cartItem = Cart::findOrFail($request->id);
        $user_data = array();
        if ($cartItem['id'] == $request->id) {
            $product = Product::find($cartItem['product_id']);
            $product_stock = $product->stocks->where('id', $cartItem['product_stock_id'])->first();
            $quantity = $product_stock->qty;
            $price = $product_stock->price;

            /*if ($quantity >= $request->quantity) {
                $cartItem['quantity'] = $request->quantity;
            } else {
                $request->quantity = $cartItem['quantity'];
            }*/

            if ($product->wholesale_product) {
                $wholesalePrice = $product_stock->wholesalePrices->where('min_qty', '<=', $request->quantity)->where('max_qty', '>=', $request->quantity)->first();
                if ($wholesalePrice) {
                    $price = $wholesalePrice->price;
                }
            }

            //discount calculation
            $discount_applicable = false;

            if ($product->discount_start_date == null) {
                $discount_applicable = true;
            } elseif (
                strtotime(date('d-m-Y H:i:s')) >= $product->discount_start_date &&
                strtotime(date('d-m-Y H:i:s')) <= $product->discount_end_date
            ) {
                $discount_applicable = true;
            }

            if ($discount_applicable) {
                if ($product->discount_type == 'percent') {
                    $price -= ($price * $product->discount) / 100;
                } elseif ($product->discount_type == 'amount') {
                    $price -= $product->discount;
                }
            }

            $cartItem['quantity'] = $request->quantity;

            /*if($quantity >= $request->quantity) {
                if($request->quantity >= $product->min_qty){
                    $cartItem['quantity'] = $request->quantity;
                }
            }*/

            /*if($product->wholesale_product){
                $wholesalePrice = $product_stock->wholesalePrices->where('min_qty', '<=', $request->quantity)->where('max_qty', '>=', $request->quantity)->first();
                if($wholesalePrice){
                    $price = $wholesalePrice->price;
                }
            }*/

            $cartItem['price'] = $price;
            $cartItem->save();
        }

        if (auth()->user() != null) {
            $user_id = Auth::user()->id;
            $carts = Cart::where('user_id', $user_id)->get();
            $user_data = Auth::user();
        } else {
            $temp_user_id = $request->session()->get('temp_user_id');
            $carts = Cart::where('temp_user_id', $temp_user_id)->get();
        }
        calculateShippingCost($carts);

        $carts = $this->manageCartCoupon($carts);

        foreach($carts as $prd) {
            $prd['delivery'] = $this->get_delivery_day($prd->product->parent_category->slug);
        }

        return array(
            'cart_count'    => count($carts),
            'cart_view'     => view('frontend.partials.cart_details', compact('carts', 'user_data'))->render(),
            'nav_cart_view' => view('frontend.partials.cart')->render(),
        );
    }

    public function cartCount(Request $request)
    {
        $cart_count = 0;
        if (auth()->user() != null) {
            $user_id = Auth::user()->id;
            $carts = Cart::where('user_id', $user_id)->get();
            $cart_count = $carts->count();
        } elseif (session('temp_user_id')) {
            $carts = Cart::where('temp_user_id', session('temp_user_id'))->get();
            $cart_count = $carts->count();
        }

        $cart_total = 0;
        foreach ($carts as $cartItem) {
            $cart_total = $cart_total + ($cartItem['price'] + $cartItem['tax']) * $cartItem['quantity'];
        }

        $product_shipping_cost = calculateShippingCost($carts);
        $cart_total += $product_shipping_cost;

        if ($carts && $carts->sum('discount') > 0) {
            $cart_total -= $carts->sum('discount');
        }

        return array(
            'cart_count' => $cart_count,
            'cart_total' => $cart_total
        );
    }

    public function addToCustomerCart(Request $request)
    {
        $carts = array();
        $data = array();

        $user_id = $request->user_id;
        $data['user_id'] = $user_id;
        $carts = Cart::where('user_id', $user_id)->get();

        $str = '';
        $tax = 0;
        $prod_qty = $request->prod_qty;
        foreach ($request->proudct as $key => $val) {
            $product_stock = ProductStock::with('product')->findOrFail($val);

            $data['product_id'] = $product_stock->product_id;
            $data['product_stock_id'] = $val;
            $data['owner_id'] = $request->owner_id;

            $price = $product_stock->price;

            // if ($product_stock->product->wholesale_product) {
            //     $wholesalePrice = $product_stock->wholesalePrices->where('min_qty', '<=', $prod_qty[$key])->where('max_qty', '>=', $prod_qty[$key])->first();
            //     if ($wholesalePrice) {
            //         $price = $wholesalePrice->price;
            //     }
            // }

            if (trim($request->custom_price[$key]) != '') {
                $price = $request->custom_price[$key];
            } else {
                $wholesalePrice = $product_stock->wholesalePrices->where('min_qty', '<=', $prod_qty[$key])->where('max_qty', '>=', $prod_qty[$key])->first();
                if ($wholesalePrice) {
                    $price = $wholesalePrice->price;
                }
            }

            //discount calculation
            $discount_applicable = false;

            if ($product_stock->product->discount_start_date == null) {
                $discount_applicable = true;
            } elseif (
                strtotime(date('d-m-Y H:i:s')) >= $product_stock->product->discount_start_date &&
                strtotime(date('d-m-Y H:i:s')) <= $product_stock->product->discount_end_date
            ) {
                $discount_applicable = true;
            }

            if ($discount_applicable) {
                if ($product_stock->product->discount_type == 'percent') {
                    $price -= ($price * $product_stock->product->discount) / 100;
                } elseif ($product_stock->product->discount_type == 'amount') {
                    $price -= $product_stock->product->discount;
                }
            }

            //calculation of taxes
            foreach ($product_stock->product->taxes as $product_tax) {
                if ($product_tax->tax_type == 'percent') {
                    $tax += ($price * $product_tax->tax) / 100;
                } elseif ($product_tax->tax_type == 'amount') {
                    $tax += $product_tax->tax;
                }
            }

            $data['quantity'] = $prod_qty[$key];
            $data['price'] = $price;
            $data['custom_price'] = $price;
            $data['tax'] = $tax;
            $data['shipping_cost'] = 0;
            $data['shipping_type'] = 'home_delivery';
            $data['product_referral_code'] = null;
            $data['cash_on_delivery'] = $product_stock->product->cash_on_delivery;
            $data['digital'] = $product_stock->product->digital;

            if ($carts && count($carts) > 0) {
                $foundInCart = false;

                foreach ($carts as $cartItem) {
                    $cart_product = Product::where('id', $cartItem['product_id'])->first();

                    if ($cartItem['product_id'] == $product_stock->product_id && $cartItem['product_stock_id'] == $val) {
                        $product_stock = $cart_product->stocks->where('variant', $str)->first();
                        if (($str != null && $cartItem['variation'] == $str) || $str == null) {
                            $foundInCart = true;

                            $cartItem['quantity'] += $prod_qty[$key];

                            if (trim($request->custom_price[$key]) != '') {
                                $price = $request->custom_price[$key];
                                $cartItem['custom_price'] = $price;
                            } else {
                                $wholesalePrice = $product_stock->wholesalePrices->where('min_qty', '<=', $prod_qty[$key])->where('max_qty', '>=', $prod_qty[$key])->first();
                                if ($wholesalePrice) {
                                    $price = $wholesalePrice->price;
                                }
                                $cartItem['custom_price'] = NULL;
                            }

                            $cartItem['price'] = $price;

                            $cartItem->save();
                        }
                    }
                }

                if (!$foundInCart) {
                    Cart::create($data);
                }
            } else {
                Cart::create($data);
            }

            calculateShippingCost($carts);
        }
    }

    public function editItemInCustomerCart(Request $request)
    {
        $carts = array();
        $data = array();

        $user_id = $request->user_id;
        $data['user_id'] = $user_id;
        $carts = Cart::where('id', $request->cart_id)->get();

        $str = '';
        $tax = 0;

        $prod_qty = intval($request->prod_qty);

        $product_stock = ProductStock::with('product')->findOrFail($request->proudct);

        $data['product_id'] = $product_stock->product_id;
        $data['product_stock_id'] = $request->proudct;
        $data['owner_id'] = $request->owner_id;

        $price = $product_stock->price;

        if (trim($request->custom_price) != '') {
            $price = $request->custom_price;
        } else {
            $wholesalePrice = $product_stock->wholesalePrices->where('min_qty', '<=', $prod_qty)->where('max_qty', '>=', $prod_qty)->first();
            if ($wholesalePrice) {
                $price = $wholesalePrice->price;
            }
        }

        //discount calculation
        $discount_applicable = false;

        if ($product_stock->product->discount_start_date == null) {
            $discount_applicable = true;
        } elseif (
            strtotime(date('d-m-Y H:i:s')) >= $product_stock->product->discount_start_date &&
            strtotime(date('d-m-Y H:i:s')) <= $product_stock->product->discount_end_date
        ) {
            $discount_applicable = true;
        }

        if ($discount_applicable) {
            if ($product_stock->product->discount_type == 'percent') {
                $price -= ($price * $product_stock->product->discount) / 100;
            } elseif ($product_stock->product->discount_type == 'amount') {
                $price -= $product_stock->product->discount;
            }
        }

        //calculation of taxes
        foreach ($product_stock->product->taxes as $product_tax) {
            if ($product_tax->tax_type == 'percent') {
                $tax += ($price * $product_tax->tax) / 100;
            } elseif ($product_tax->tax_type == 'amount') {
                $tax += $product_tax->tax;
            }
        }

        $data['quantity'] = $prod_qty;
        $data['price'] = $price;
        $data['custom_price'] = $price;
        $data['tax'] = $tax;
        $data['shipping_cost'] = 0;
        $data['shipping_type'] = 'home_delivery';
        $data['product_referral_code'] = null;
        $data['cash_on_delivery'] = $product_stock->product->cash_on_delivery;
        $data['digital'] = $product_stock->product->digital;

        if ($carts && count($carts) > 0) {
            $foundInCart = false;

            foreach ($carts as $cartItem) {
                $cart_product = Product::where('id', $cartItem['product_id'])->first();

                if ($cartItem['product_id'] == $product_stock->product_id && $cartItem['product_stock_id'] == intval($request->proudct)) {
                    $product_stock = $cart_product->stocks->where('variant', $str)->first();

                    if (($str != null && $cartItem['variation'] == $str) || $str == null) {
                        $foundInCart = true;

                        $cartItem['quantity'] = $prod_qty;

                        if (trim($request->custom_price) != '') {
                            $price = $request->custom_price;
                            $cartItem['custom_price'] = $price;
                        } else {
                            $wholesalePrice = $product_stock->wholesalePrices->where('min_qty', '<=', $prod_qty)->where('max_qty', '>=', $prod_qty)->first();
                            if ($wholesalePrice) {
                                $price = $wholesalePrice->price;
                            }
                            $cartItem['custom_price'] = NULL;
                        }

                        $cartItem['price'] = $price;

                        $cartItem->save();
                    }
                }
            }

            if (!$foundInCart) {
                Cart::create($data);
            }
        } else {
            Cart::create($data);
        }

        calculateShippingCost($carts);
    }
}
