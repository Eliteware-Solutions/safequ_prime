<?php

namespace App\Http\Controllers;

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
            $shop = Shop::where('user_id', $user_data->joined_community_id)->first();
        } else {
            $temp_user_id = $request->session()->get('temp_user_id');
            // $carts = Cart::where('temp_user_id', $temp_user_id)->get();
            $carts = ($temp_user_id != null) ? Cart::where('temp_user_id', $temp_user_id)->get() : [];

            $shop = Shop::findOrfail(Cookie::get('local_shop_id'));
        }

        return view('frontend.view_cart', compact('carts', 'user_data', 'shop'));
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
                            'modal_view'    => view('frontend.partials.auctionProductAlredayAddedCart')->render(),
                            'nav_cart_view' => view('frontend.partials.cart')->render(),
                        );
                    }

                    if ($cartItem['product_id'] == $request->id && $cartItem['product_stock_id'] == $request->product_stock_id) {
                        $product_stock = $cart_product->stocks->where('variant', $str)->first();
                        $quantity = $product_stock->qty;
                        /*if($quantity < $cartItem['quantity'] + $request['quantity']){
                            return array(
                                'status' => 0,
                                'cart_count' => count($carts),
                                'modal_view' => view('frontend.partials.outOfStockCart')->render(),
                                'nav_cart_view' => view('frontend.partials.cart')->render(),
                            );
                        }*/

                        if (($str != null && $cartItem['variation'] == $str) || $str == null) {
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
            calculateShippingCost($carts);
            return array(
                'status'     => 1,
                'cart_count' => count($carts),
                //                'modal_view' => view('frontend.partials.addedToCart', compact('product', 'data'))->render(),
                //                'nav_cart_view' => view('frontend.partials.cart')->render(),
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
            return array(
                'status'        => 1,
                'cart_count'    => count($carts),
                'modal_view'    => view('frontend.partials.addedToCart', compact('product', 'data'))->render(),
                'nav_cart_view' => view('frontend.partials.cart')->render(),
            );
        }
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
        Cart::destroy($request->id);
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

        calculateShippingCost($carts);

        return array(
            'cart_count'    => count($carts),
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

        return array(
            'cart_count' => $cart_count
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

            if ($product_stock->product->wholesale_product) {
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

                            if ($cart_product->wholesale_product) {
                                $wholesalePrice = $product_stock->wholesalePrices->where('min_qty', '<=', $prod_qty[$key])->where('max_qty', '>=', $prod_qty[$key])->first();
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

        if ($product_stock->product->wholesale_product) {
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

                        if ($cart_product->wholesale_product) {
                            $wholesalePrice = $product_stock->wholesalePrices->where('min_qty', '<=', $prod_qty)->where('max_qty', '>=', $prod_qty)->first();
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

        calculateShippingCost($carts);
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
        if ($product_stock->product->wholesale_product) {
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
                        if ($cart_product->wholesale_product) {
                            $wholesalePrice = $product_stock->wholesalePrices->where('min_qty', '<=', $prod_qty)->where('max_qty', '>=', $prod_qty)->first();
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
        calculateShippingCost($carts);
    }
}
