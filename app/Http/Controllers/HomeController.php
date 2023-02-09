<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\ProductStock;
use Illuminate\Http\Request;
use Auth;
use Hash;
use App\Models\Category;
use App\Models\FlashDeal;
use App\Models\Brand;
use App\Models\Product;
use App\Models\PickupPoint;
use App\Models\CustomerPackage;
use App\Models\User;
use App\Models\Seller;
use App\Models\Shop;
use App\Models\Order;
use App\Models\BusinessSetting;
use App\Models\Coupon;
use Cookie;
use Illuminate\Support\Str;
use App\Mail\SecondEmailVerifyMailManager;
use App\Models\AffiliateConfig;
use App\Models\Page;
use Mail;
use Illuminate\Auth\Events\PasswordReset;
use Cache;


class HomeController extends Controller
{
    /**
     * Show the application frontend home.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Auth::check() && intval(Auth::user()->joined_community_id) > 0) {
            $shop = Shop::where('user_id', Auth::user()->joined_community_id)->first();
            return redirect()->route('shop.visit', $shop->slug);
        }

        $featured_categories = Cache::rememberForever('featured_categories', function () {
            return Category::where('featured', 1)->get();
        });

        $todays_deal_products = Cache::rememberForever('todays_deal_products', function () {
            return filter_products(Product::where('published', 1)->where('todays_deal', '1'))->get();
        });

        $newest_products = Cache::remember('newest_products', 3600, function () {
            return filter_products(Product::latest())->limit(12)->get();
        });

        $communities = Shop::limit(10)->get();

        /* ----------------------
        Best selling products from Lodha Park only ( Seller Id = 2 )
        ---------------------- */

        $local_shop_id = Cookie::get('local_shop_id');
        $local_shop_slug = Cookie::get('local_shop_slug');

        $local_shop_id = (intval($local_shop_id) > 0 ? $local_shop_id : 0);

        $parentCategories = Category::where('parent_id', 0)->get();

        $customer_favourites = array();
        $customer_favourites = ProductStock::where(['is_best_selling' => 1, 'seller_id' => 0])->inRandomOrder()->limit(10)->get();

        $flash_deal = FlashDeal::where('end_date', '>', strtotime(date('d-m-Y H:i:s')))->where('status', 1)->first();

        $deals_of_the_day = array();
        if ($flash_deal) {
            foreach ($flash_deal->flash_deal_products as $deal_products) {
                if ($deal_products->deal_products->productStock) {
                    $deals_of_the_day[$deal_products->deal_products->id] = $deal_products->deal_products->productStock;
                }
            }
        }


        // $best_selling_products = array();
        $all_products = array();
        foreach ($parentCategories as $cat) {
            // $best_selling_products[$cat->id] = ProductStock::whereHas('product', function ($query) use ($cat) {
            //     $query->where('parent_category_id', $cat->id);
            // })->where('is_best_selling', 1)->where('seller_id', $local_shop_id)->inRandomOrder()->limit(8)->get();
            $all_products[$cat->id] = ProductStock::whereHas('product', function ($query) use ($cat) {
                $query->where('parent_category_id', $cat->id);
            })->where('seller_id', 0)->inRandomOrder()->limit(10)->get();
        }

        $our_full_range_of_products = array();
        foreach ($all_products as $prd) {
            foreach ($prd as $val) {
                $our_full_range_of_products[] = $val;
            }
        }

        if ($local_shop_id > 0) {
            $seller = Seller::findOrfail($local_shop_id);
        } else {
            $seller = Seller::findOrfail(2);
        }

        $cart = [];
        if (session('temp_user_id')) {
            $cart_data = $this->get_product_cart();
            if ($cart_data) {
                $cart = $cart_data['cart'];
            }
        }

        return view('frontend.index', compact('featured_categories', 'todays_deal_products', 'newest_products', 'communities', 'parentCategories', 'all_products', 'customer_favourites', 'our_full_range_of_products', 'deals_of_the_day', 'seller', 'cart', 'local_shop_id', 'local_shop_slug'));
    }

    public function set_local_community(Request $request)
    {
        $shop = Shop::findOrfail($request->id);
        if ($shop) {
            Cookie::queue('local_shop_id', $request->id, 3600);
            Cookie::queue('local_shop_slug', $shop->slug, 3600);

            return array(
                'status'     => 1,
                'shop_slug' => $shop->slug,
            );
        } else {
            return array(
                'status'     => 0,
            );
        }
    }

    public function login()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }
        $community_id = 0;
        return view('frontend.user_login', compact('community_id'));
    }

    public function community_user_login($id)
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }
        $community_id = $id;
        return view('frontend.user_login', compact('community_id'));
    }

    public function registration(Request $request)
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }
        /*if ($request->has('referral_code') && addon_is_activated('affiliate_system')) {
            try {
                $affiliate_validation_time = AffiliateConfig::where('type', 'validation_time')->first();
                $cookie_minute = 30 * 24;
                if ($affiliate_validation_time) {
                    $cookie_minute = $affiliate_validation_time->value * 60;
                }

                Cookie::queue('referral_code', $request->referral_code, $cookie_minute);
                $referred_by_user = User::where('referral_code', $request->product_referral_code)->first();

                $affiliateController = new AffiliateController;
                $affiliateController->processAffiliateStats($referred_by_user->id, 1, 0, 0, 0);
            } catch (\Exception $e) {

            }
        }*/
        $referer_user_id = 0;
        return view('frontend.user_registration', compact('referer_user_id'));
    }

    public function referral_user_register($userKey)
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }
        $referer_user = ($userKey ? $userKey : 0);
        $refer_user = User::where('referral_key', $referer_user)->first();
        if ($refer_user) {
            $referer_user_id = $refer_user->id;
            return view('frontend.user_registration', compact('referer_user_id'));
        } else {
            return redirect()->route('home');
        }
    }

    public function cart_login(Request $request)
    {
        $user = null;
        if ($request->get('phone') != null) {
            $user = User::whereIn('user_type', ['customer', 'seller'])->where('phone', "+{$request['country_code']}{$request['phone']}")->first();
        } elseif ($request->get('email') != null) {
            $user = User::whereIn('user_type', ['customer', 'seller'])->where('email', $request->email)->first();
        }

        if ($user != null) {
            if (Hash::check($request->password, $user->password)) {
                if ($request->has('remember')) {
                    auth()->login($user, true);
                } else {
                    auth()->login($user, false);
                }
            } else {
                flash(translate('Invalid email or password!'))->warning();
            }
        } else {
            flash(translate('Invalid email or password!'))->warning();
        }
        return back();
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Show the customer/seller dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboard()
    {
        if (Auth::user()->user_type == 'seller') {
            return view('frontend.user.seller.dashboard');
        } elseif (Auth::user()->user_type == 'customer') {
            return view('frontend.user.customer.dashboard');
        } elseif (Auth::user()->user_type == 'delivery_boy') {
            return view('delivery_boys.frontend.dashboard');
        } else {
            abort(404);
        }
    }

    public function profile(Request $request)
    {
        // if(Auth::user()->user_type == 'delivery_boy'){
        //     return view('delivery_boys.frontend.profile');
        // }
        // else{
        return view('frontend.user.profile');
        // }
    }

    public function userProfileUpdate(Request $request)
    {
        if (env('DEMO_MODE') == 'On') {
            flash(translate('Sorry! the action is not permitted in demo '))->error();
            return back();
        }
        $user = Auth::user();
        if (filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
            if (User::where('email', $request->email)->where('id', '!=', $user->id)->first() != null) {
                flash(translate('Email already exists.'))->error();
                return back();
            }
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->address = $request->address;
        //        $user->city = $request->city;
        //        $user->state = $request->state;
        //        $user->country = $request->country;
        //        $user->postal_code = $request->postal_code;

        // if($request->new_password != null && ($request->new_password == $request->confirm_password)){
        //     $user->password = Hash::make($request->new_password);
        // }

        $user->avatar_original = $request->photo;

        $seller = $user->seller;

        if ($seller) {
            $seller->cash_on_delivery_status = $request->cash_on_delivery_status;
            $seller->bank_payment_status = $request->bank_payment_status;
            $seller->bank_name = $request->bank_name;
            $seller->bank_acc_name = $request->bank_acc_name;
            $seller->bank_acc_no = $request->bank_acc_no;
            $seller->bank_routing_no = $request->bank_routing_no;

            $seller->save();
        }

        $user->save();

        flash(translate('Your Profile has been updated successfully!'))->success();
        if (session('link') != null) {
            return redirect(session('link'));
        } else {
            return back();
        }
    }

    public function flash_deal_details($slug)
    {
        $flash_deal = FlashDeal::where('slug', $slug)->first();
        if ($flash_deal != null)
            return view('frontend.flash_deal_details', compact('flash_deal'));
        else {
            abort(404);
        }
    }

    public function load_featured_section()
    {
        return view('frontend.partials.featured_products_section');
    }

    public function load_best_selling_section()
    {
        return view('frontend.partials.best_selling_section');
    }

    public function load_auction_products_section()
    {
        if (!addon_is_activated('auction')) {
            return;
        }
        return view('auction.frontend.auction_products_section');
    }

    public function load_home_categories_section()
    {
        return view('frontend.partials.home_categories_section');
    }

    public function load_best_sellers_section()
    {
        return view('frontend.partials.best_sellers_section');
    }

    public function trackOrder(Request $request)
    {
        if ($request->has('order_code')) {
            $order = Order::where('code', $request->order_code)->first();
            if ($order != null) {
                return view('frontend.track_order', compact('order'));
            }
        }
        return view('frontend.track_order');
    }

    public function products()
    {
        return view('frontend.products_list');
    }

    public function product(Request $request, $slug)
    {
        $detailedProduct = Product::with('reviews', 'brand', 'stocks', 'user', 'user.shop')->where('auction_product', 0)->where('slug', $slug)->where('approved', 1)->first();

        if ($detailedProduct != null && $detailedProduct->published) {
            if ($request->has('product_referral_code') && addon_is_activated('affiliate_system')) {

                $affiliate_validation_time = AffiliateConfig::where('type', 'validation_time')->first();
                $cookie_minute = 30 * 24;
                if ($affiliate_validation_time) {
                    $cookie_minute = $affiliate_validation_time->value * 60;
                }
                Cookie::queue('product_referral_code', $request->product_referral_code, $cookie_minute);
                Cookie::queue('referred_product_id', $detailedProduct->id, $cookie_minute);

                $referred_by_user = User::where('referral_code', $request->product_referral_code)->first();

                $affiliateController = new AffiliateController;
                $affiliateController->processAffiliateStats($referred_by_user->id, 1, 0, 0, 0);
            }
            if ($detailedProduct->digital == 1) {
                return view('frontend.digital_product_details', compact('detailedProduct'));
            } else {
                return view('frontend.product_details', compact('detailedProduct'));
            }
        }
        abort(404);
    }

    public function shop()
    {
        $categories = [];
        $parentCategories = Category::where('parent_id', 0)->with('childrenCategories')->get();

        if ($parentCategories) {
            foreach ($parentCategories as $parCat) {
                $catFilter = [];
                $catFilter[$parCat->slug] = $parCat->slug;
                if (!empty($parCat->childrenCategories)) {
                    foreach ($parCat->childrenCategories as $childCat) {
                        $catFilter[$childCat->slug] = $childCat->slug;

                        if (!empty($childCat->categories)) {
                            foreach ($childCat->categories as $chilCat2) {
                                $catFilter[$chilCat2->slug] = $chilCat2->slug;
                            }
                        }
                    }
                }
                $categories[$parCat->slug]['name'] = $parCat->name;
                $categories[$parCat->slug]['filter'] = implode(",.", $catFilter);
            }
        }

        $categorizedProd = array();
        foreach ($parentCategories as $cat) {
            $categorizedProd[$cat->id] = ProductStock::whereHas('product', function ($query) use ($cat) {
                $query->where('parent_category_id', $cat->id);
            })->where('seller_id', 0)->inRandomOrder()->limit(10)->get();
        }

        $all_products = array();
        foreach ($categorizedProd as $prd) {
            foreach ($prd as $val) {
                $all_products[] = $val;
            }
        }

        $cart = [];
        $checkout_total = 0;
        if (session('temp_user_id')) {
            $cart_data = $this->get_product_cart();
            if ($cart_data) {
                $cart = $cart_data['cart'];
                $checkout_total = $cart_data['checkout_total'];
            }
        }

        return view('frontend.seller_shop', compact('categories', 'all_products', 'categorizedProd', 'cart', 'checkout_total'));
    }

    // public function shop($slug)
    // {
    //     $shop = Shop::where('slug', $slug)->first();

    //     if ($shop != null) {
    //         request()->session()->put('shop_slug', $shop->slug);
    //         request()->session()->put('shop_name', $shop->name);

    //         $seller = Seller::where('user_id', $shop->user_id)->first();
    //         $products_purchase_started = isset($seller->products_purchase_started) ? $seller->products_purchase_started : [];
    //         $products_purchase_expired = isset($seller->products_purchase_expired) ? $seller->products_purchase_expired : [];

    //         $categories = [];
    //         $parentCategories = Category::where('parent_id', 0)
    //             ->with('childrenCategories')
    //             ->get();
    //         if ($parentCategories) {
    //             foreach ($parentCategories as $parCat) {
    //                 $catFilter = [];
    //                 $catFilter[$parCat->slug] = $parCat->slug;
    //                 if (!empty($parCat->childrenCategories)) {
    //                     foreach ($parCat->childrenCategories as $childCat) {
    //                         $catFilter[$childCat->slug] = $childCat->slug;

    //                         if (!empty($childCat->categories)) {
    //                             foreach ($childCat->categories as $chilCat2) {
    //                                 $catFilter[$chilCat2->slug] = $chilCat2->slug;
    //                             }
    //                         }
    //                     }
    //                 }
    //                 $categories[$parCat->slug]['name'] = $parCat->name;
    //                 $categories[$parCat->slug]['filter'] = implode(",.", $catFilter);
    //             }
    //         }

    //         $cart = [];
    //         $checkout_total = 0;
    //         if (session('temp_user_id')) {
    //             $cart_data = $this->get_product_cart();
    //             if ($cart_data) {
    //                 $cart = $cart_data['cart'];
    //                 $checkout_total = $cart_data['checkout_total'];
    //             }
    //         }

    //         return view('frontend.seller_shop', compact('shop', 'products_purchase_started', 'products_purchase_expired', 'categories', 'cart', 'checkout_total'));
    //     }
    //     abort(404);
    // }

    public function get_product_cart()
    {
        $result = array();
        $cart = array();
        $checkout_total = 0;
        if (Auth::check()) {
            $cart_data = Cart::where('user_id', auth()->user()->id)->get();
        } else {
            $cart_data = Cart::where('temp_user_id', session('temp_user_id'))->get();
        }
        foreach ($cart_data as $cart_val) {
            $cart[$cart_val->product_stock_id]['qty'] = $cart_val->quantity;
            $cart[$cart_val->product_stock_id]['product_id'] = $cart_val->product_id;
            $cart[$cart_val->product_stock_id]['product_stock_id'] = $cart_val->product_stock_id;

            $price = $cart_val->price;
            if ($cart_val->product_stock) {
                $wholesalePrice = $cart_val->product_stock->wholesalePrices->where('min_qty', '<=', $cart_val->quantity)->where('max_qty', '>=', $cart_val->quantity)->first();
                if ($wholesalePrice) {
                    $price = $wholesalePrice->price;
                }
            }
            $cart[$cart_val->product_stock_id]['price'] = $price;
            $cart[$cart_val->product_stock_id]['total'] = floatval($cart_val->quantity * $price);
            $checkout_total = floatval($cart_val->quantity * $price) + $checkout_total;
        }

        $result['cart'] = $cart;
        $result['checkout_total'] = $checkout_total;
        return $result;
    }

    public function filter_shop($slug, $type)
    {
        $shop = Shop::where('slug', $slug)->first();
        if ($shop != null && $type != null) {
            return view('frontend.seller_shop', compact('shop', 'type'));
        }
        abort(404);
    }

    public function all_categories(Request $request)
    {
        //        $categories = Category::where('level', 0)->orderBy('name', 'asc')->get();
        $categories = Category::where('level', 0)->orderBy('order_level', 'desc')->get();
        return view('frontend.all_category', compact('categories'));
    }

    public function all_brands(Request $request)
    {
        $categories = Category::all();
        return view('frontend.all_brand', compact('categories'));
    }

    public function show_product_upload_form(Request $request)
    {
        $seller = Auth::user()->seller;
        if (addon_is_activated('seller_subscription')) {
            if ($seller->seller_package && $seller->seller_package->product_upload_limit > $seller->user->products()->count()) {
                $categories = Category::where('parent_id', 0)
                    ->where('digital', 0)
                    ->with('childrenCategories')
                    ->get();
                return view('frontend.user.seller.product_upload', compact('categories'));
            } else {
                flash(translate('Upload limit has been reached. Please upgrade your package.'))->warning();
                return back();
            }
        }
        $categories = Category::where('parent_id', 0)
            ->where('digital', 0)
            ->with('childrenCategories')
            ->get();
        return view('frontend.user.seller.product_upload', compact('categories'));
    }

    public function show_product_edit_form(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $lang = $request->lang;
        $tags = json_decode($product->tags);
        $categories = Category::where('parent_id', 0)
            ->where('digital', 0)
            ->with('childrenCategories')
            ->get();
        return view('frontend.user.seller.product_edit', compact('product', 'categories', 'tags', 'lang'));
    }

    public function show_product_details($id)
    {
        $product = ProductStock::with('product')->findOrFail($id);
        return view('frontend.cart_modal', compact('product'));
    }

    public function seller_product_list(Request $request)
    {
        $search = null;
        $products = Product::where('user_id', Auth::user()->id)->where('digital', 0)->orderBy('created_at', 'desc');
        if ($request->has('search')) {
            $search = $request->search;
            $products = $products->where('name', 'like', '%' . $search . '%');
        }
        $products = $products->paginate(10);
        return view('frontend.user.seller.products', compact('products', 'search'));
    }

    public function home_settings(Request $request)
    {
        return view('home_settings.index');
    }

    public function top_10_settings(Request $request)
    {
        foreach (Category::all() as $key => $category) {
            if (is_array($request->top_categories) && in_array($category->id, $request->top_categories)) {
                $category->top = 1;
                $category->save();
            } else {
                $category->top = 0;
                $category->save();
            }
        }

        foreach (Brand::all() as $key => $brand) {
            if (is_array($request->top_brands) && in_array($brand->id, $request->top_brands)) {
                $brand->top = 1;
                $brand->save();
            } else {
                $brand->top = 0;
                $brand->save();
            }
        }

        flash(translate('Top 10 categories and brands have been updated successfully'))->success();
        return redirect()->route('home_settings.index');
    }

    public function variant_price(Request $request)
    {
        $product = Product::find($request->id);
        $str = '';
        $quantity = 0;
        $tax = 0;
        $max_limit = 0;

        if ($request->has('color')) {
            $str = $request['color'];
        }

        if (json_decode($product->choice_options) != null) {
            foreach (json_decode($product->choice_options) as $key => $choice) {
                if ($str != null) {
                    $str .= '-' . str_replace(' ', '', $request['attribute_id_' . $choice->attribute_id]);
                } else {
                    $str .= str_replace(' ', '', $request['attribute_id_' . $choice->attribute_id]);
                }
            }
        }

        $qty_unit = $product->unit;
        if (floatval($product->min_qty) < 1) {
            if (($request->quantity * floatval($product->min_qty)) < 1) {
                $qty_unit = (1000 * $request->quantity * floatval($product->min_qty)) . ' ' . $product->secondary_unit;
            } else {
                $qty_unit = ($request->quantity * floatval($product->min_qty)) . ' ' . $product->unit;
            }
        }

        $product_stock = $product->stocks->where('id', $request->stock_id)->first();
        $price = $product_stock->price;

        if ($product->wholesale_product) {
            $wholesalePrice = $product_stock->wholesalePrices->where('min_qty', '<=', $request->quantity)->where('max_qty', '>=', $request->quantity)->first();
            if ($wholesalePrice) {
                $price = $wholesalePrice->price;
            }
        }

        $quantity = $product_stock->qty;
        $max_limit = $product_stock->qty;

        if ($quantity >= 1 && $product->min_qty <= $quantity) {
            $in_stock = 1;
        } else {
            $in_stock = 0;
        }

        //Product Stock Visibility
        if ($product->stock_visibility_state == 'text') {
            if ($quantity >= 1 && $product->min_qty < $quantity) {
                $quantity = translate('In Stock');
            } else {
                $quantity = translate('Out Of Stock');
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

        // taxes
        foreach ($product->taxes as $product_tax) {
            if ($product_tax->tax_type == 'percent') {
                $tax += ($price * $product_tax->tax) / 100;
            } elseif ($product_tax->tax_type == 'amount') {
                $tax += $product_tax->tax;
            }
        }

        $price += $tax;

        return array(
            'unit_price'  => single_price_web($price),
            'price'       => single_price_web($price * $request->quantity),
            'quantity'    => $quantity,
            'qty_unit'    => $qty_unit,
            'digital'     => $product->digital,
            'variation'   => $str,
            'max_limit'   => $max_limit,
            'in_stock'    => $in_stock,
            'total_qty'   => $request->quantity,
            'total_price' => round(floatval($request->quantity * $price), 3)
        );
    }

    public function sellerpolicy()
    {
        $page = Page::where('type', 'seller_policy_page')->first();
        return view("frontend.policies.sellerpolicy", compact('page'));
    }

    public function returnpolicy()
    {
        $page = Page::where('type', 'return_policy_page')->first();
        return view("frontend.policies.returnpolicy", compact('page'));
    }

    public function supportpolicy()
    {
        $page = Page::where('type', 'support_policy_page')->first();
        return view("frontend.policies.supportpolicy", compact('page'));
    }

    public function terms()
    {
        $page = Page::where('type', 'terms_conditions_page')->first();
        return view("frontend.policies.terms", compact('page'));
    }

    public function privacypolicy()
    {
        $page = Page::where('type', 'privacy_policy_page')->first();
        return view("frontend.policies.privacypolicy", compact('page'));
    }

    public function get_pick_up_points(Request $request)
    {
        $pick_up_points = PickupPoint::all();
        return view('frontend.partials.pick_up_points', compact('pick_up_points'));
    }

    public function get_category_items(Request $request)
    {
        $category = Category::findOrFail($request->id);
        return view('frontend.partials.category_elements', compact('category'));
    }

    public function premium_package_index()
    {
        $customer_packages = CustomerPackage::all();
        return view('frontend.user.customer_packages_lists', compact('customer_packages'));
    }

    public function seller_digital_product_list(Request $request)
    {
        $products = Product::where('user_id', Auth::user()->id)->where('digital', 1)->orderBy('created_at', 'desc')->paginate(10);
        return view('frontend.user.seller.digitalproducts.products', compact('products'));
    }

    public function show_digital_product_upload_form(Request $request)
    {
        $seller = Auth::user()->seller;
        if (addon_is_activated('seller_subscription')) {
            if ($seller->seller_package && $seller->seller_package->product_upload_limit > $seller->user->products()->count()) {
                $categories = Category::where('digital', 1)->get();
                return view('frontend.user.seller.digitalproducts.product_upload', compact('categories'));
            } else {
                flash(translate('Upload limit has been reached. Please upgrade your package.'))->warning();
                return back();
            }
        }
        $categories = Category::where('digital', 1)->get();
        return view('frontend.user.seller.digitalproducts.product_upload', compact('categories'));
    }

    public function show_digital_product_edit_form(Request $request, $id)
    {
        $categories = Category::where('digital', 1)->get();
        $lang = $request->lang;
        $product = Product::find($id);
        return view('frontend.user.seller.digitalproducts.product_edit', compact('categories', 'product', 'lang'));
    }

    // Ajax call
    public function new_verify(Request $request)
    {
        $email = $request->email;
        if (isUnique($email) == '0') {
            $response['status'] = 2;
            $response['message'] = 'Email already exists!';
            return json_encode($response);
        }

        $response = $this->send_email_change_verification_mail($request, $email);
        return json_encode($response);
    }


    // Form request
    public function update_email(Request $request)
    {
        $email = $request->email;
        if (isUnique($email)) {
            $this->send_email_change_verification_mail($request, $email);
            flash(translate('A verification mail has been sent to the mail you provided us with.'))->success();
            return back();
        }

        flash(translate('Email already exists!'))->warning();
        return back();
    }

    public function send_email_change_verification_mail($request, $email)
    {
        $response['status'] = 0;
        $response['message'] = 'Unknown';

        $verification_code = Str::random(32);

        $array['subject'] = 'Email Verification';
        $array['from'] = env('MAIL_FROM_ADDRESS');
        $array['content'] = 'Verify your account';
        $array['link'] = route('email_change.callback') . '?new_email_verificiation_code=' . $verification_code . '&email=' . $email;
        $array['sender'] = Auth::user()->name;
        $array['details'] = "Email Second";

        $user = Auth::user();
        $user->new_email_verificiation_code = $verification_code;
        $user->save();

        try {
            Mail::to($email)->queue(new SecondEmailVerifyMailManager($array));

            $response['status'] = 1;
            $response['message'] = translate("Your verification mail has been Sent to your email.");
        } catch (\Exception $e) {
            // return $e->getMessage();
            $response['status'] = 0;
            $response['message'] = $e->getMessage();
        }

        return $response;
    }

    public function email_change_callback(Request $request)
    {
        if ($request->has('new_email_verificiation_code') && $request->has('email')) {
            $verification_code_of_url_param = $request->input('new_email_verificiation_code');
            $user = User::where('new_email_verificiation_code', $verification_code_of_url_param)->first();

            if ($user != null) {

                $user->email = $request->input('email');
                $user->new_email_verificiation_code = null;
                $user->save();

                auth()->login($user, true);

                flash(translate('Email Changed successfully'))->success();
                return redirect()->route('dashboard');
            }
        }

        flash(translate('Email was not verified. Please resend your mail!'))->error();
        return redirect()->route('dashboard');
    }

    public function reset_password_with_code(Request $request)
    {
        if (($user = User::where('email', $request->email)->where('verification_code', $request->code)->first()) != null) {
            if ($request->password == $request->password_confirmation) {
                $user->password = Hash::make($request->password);
                $user->email_verified_at = date('Y-m-d h:m:s');
                $user->save();
                event(new PasswordReset($user));
                auth()->login($user, true);

                flash(translate('Password updated successfully'))->success();

                if (auth()->user()->user_type == 'admin' || auth()->user()->user_type == 'staff') {
                    return redirect()->route('admin.dashboard');
                }
                return redirect()->route('home');
            } else {
                flash("Password and confirm password didn't match")->warning();
                return redirect()->route('password.request');
            }
        } else {
            flash("Verification code mismatch")->error();
            return redirect()->route('password.request');
        }
    }


    public function all_flash_deals()
    {
        $today = strtotime(date('Y-m-d H:i:s'));

        $data['all_flash_deals'] = FlashDeal::where('status', 1)
            ->where('start_date', "<=", $today)
            ->where('end_date', ">", $today)
            ->orderBy('created_at', 'desc')
            ->get();

        return view("frontend.flash_deal.all_flash_deal_list", $data);
    }

    public function all_seller(Request $request)
    {
        $shops = Shop::whereIn('user_id', verified_sellers_id())
            ->paginate(15);

        return view('frontend.shop_listing', compact('shops'));
    }

    public function all_coupons(Request $request)
    {
        $coupons = Coupon::where('start_date', '<=', strtotime(date('d-m-Y')))->where('end_date', '>=', strtotime(date('d-m-Y')))->paginate(15);
        return view('frontend.coupons', compact('coupons'));
    }

    public function inhouse_products(Request $request)
    {
        $products = filter_products(Product::where('added_by', 'admin'))->with('taxes')->paginate(12)->appends(request()->query());
        return view('frontend.inhouse_products', compact('products'));
    }
}
