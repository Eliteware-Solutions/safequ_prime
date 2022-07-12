<?php

namespace App\Http\Controllers;

use App\Models\CustomersExport;
use App\Models\OrderDetail;
use App\Models\ProductStock;
use App\Models\Seller;
use App\Models\Shop;
use App\Models\Wallet;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\User;
use App\Models\Order;
use Excel;
use Illuminate\Support\Facades\Hash;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sort_search = null;
        $users = User::where('user_type', 'customer')->where('email_verified_at', '!=', null)->orderBy('created_at', 'desc');
        if ($request->has('search')) {
            $sort_search = $request->search;
            $users->where(function ($q) use ($sort_search) {
                $q->where('name', 'like', '%' . $sort_search . '%')->orWhere('email', 'like', '%' . $sort_search . '%');
            });
        }
        $users = $users->paginate(15);
        return view('backend.customer.customers.index', compact('users', 'sort_search'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required',
            'email' => 'required|unique:users|email',
            'phone' => 'required|unique:users',
        ]);

        $response['status'] = 'Error';

        $user = User::create($request->all());

        $customer = new Customer;

        $customer->user_id = $user->id;
        $customer->save();

        if (isset($user->id)) {
            $html = '';
            $html .= '<option value="">
                        ' . translate("Walk In Customer") . '
                    </option>';
            foreach (Customer::all() as $key => $customer) {
                if ($customer->user) {
                    $html .= '<option value="' . $customer->user->id . '" data-contact="' . $customer->user->email . '">
                                ' . $customer->user->name . '
                            </option>';
                }
            }

            $response['status'] = 'Success';
            $response['html'] = $html;
        }

        echo json_encode($response);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        User::destroy($id);
        flash(translate('Customer has been deleted successfully'))->success();
        return redirect()->route('customers.index');
    }

    public function bulk_customer_delete(Request $request)
    {
        if ($request->id) {
            foreach ($request->id as $customer_id) {
                $this->destroy($customer_id);
            }
        }

        return 1;
    }

    public function login($id)
    {
        $user = User::findOrFail(decrypt($id));

        auth()->login($user, true);

        return redirect()->route('dashboard');
    }

    public function ban($id)
    {
        $user = User::findOrFail(decrypt($id));

        if ($user->banned == 1) {
            $user->banned = 0;
            flash(translate('Customer UnBanned Successfully'))->success();
        } else {
            $user->banned = 1;
            flash(translate('Customer Banned Successfully'))->success();
        }

        $user->save();

        return back();
    }

    public function customer_detail($id)
    {
        $user = User::findOrFail($id);

        if ($user) {
            $cart_orders = $user->carts;
            $order_details = OrderDetail::with('order')->whereHas('order', function ($query) use ($id) {
                $query->where('user_id', $id);
            })->orderBy('created_at','desc')->paginate(10, ['*'], 'orders');
            $wallet_history = Wallet::where('user_id', $id)->orderBy('created_at','desc')->paginate(10, ['*'], 'wallet');

            return view('backend.customer.customers.details', compact('user', 'cart_orders', 'order_details', 'wallet_history'));
        } else {
            return back();
        }
    }

    public function add_customer_product($id)
    {
        $user = User::findOrFail($id);

        if ($user) {
            $shop = Shop::where('user_id', $user->joined_community_id)->first();

            if ($shop != null) {
                $seller = Seller::where('user_id', $shop->user_id)->first();
                $products_purchase_started = isset($seller->products_purchase_started) ? $seller->products_purchase_started : [];
                $products_purchase_expired = isset($seller->products_purchase_expired) ? $seller->products_purchase_expired : [];

                $active_products = [];
                foreach ($products_purchase_started as $product) {
                    $unit = '';
                    if (floatval($product->product->min_qty) >= 1) {
                        $unit = floatval(1000 * $product->product->min_qty) . ' ' . $product->product->secondary_unit;
                    } else {
                        $unit = floatval(1000 * $product->product->min_qty) . ' ' . $product->product->secondary_unit;
                    }

                    $unit = single_price($product->price) . ' / ' . $unit;
                    $product->unit_label = $unit;
                    $active_products[] = $product;
                }

                foreach ($products_purchase_expired as $product) {
                    $unit = '';
                    if (floatval($product->product->min_qty) >= 1) {
                        $unit = floatval(1000 * $product->product->min_qty) . ' ' . $product->product->secondary_unit;
                    } else {
                        $unit = floatval(1000 * $product->product->min_qty) . ' ' . $product->product->secondary_unit;
                    }

                    $unit = single_price($product->price) . '/' . $unit;
                    $product->unit_label = $unit;
                    $active_products[] = $product;
                }

                return view('backend.customer.customers.add_product', compact('user', 'shop', 'seller', 'active_products'));
            } else {
                return back();
            }
        } else {
            return back();
        }
    }

    public function add_customer_order(Request $request)
    {
        $qtyAvailable = true;
        $msg = '';
        $prod_qty = $request->prod_qty;
        foreach ($request->proudct as $key => $val) {
            $productStock = ProductStock::find($val);
            if (floatval($prod_qty[$key]) > floatval($productStock->qty)) {
                $msg = 'Available quantity for ' . $productStock->product->name . ' is less then required quantity';
                $qtyAvailable = false;
                break;
            }
        }

        if ($qtyAvailable == true) {
            if ($request->add_order)
                (new OrderController)->save_order_from_backend($request);
            else
                (new CartController)->addToCustomerCart($request);

            flash(translate('Order has been added.'))->success();
            return redirect()->route('customers.detail', $request->user_id);
        } else {
            flash($msg)->error();
            return back();
        }

    }

    public function add_customer()
    {
        $sellers = Seller::all();

        return view('backend.customer.customers.add_customer', compact('sellers'));
    }

    public function store_customer(Request $request)
    {
        $user = User::where('phone', '+' . $request->country_code . $request->phone)->first();

        if (!$user) {
            if (!empty($request->email)) {
                $user = User::where('email', $request->email)->first();
                if ($user) {
                    flash('Customer with same email already present.')->error();
                    return back();
                }
            }

            $user = User::create([
                'name'                => $request->name,
                'phone'               => '+' . $request->country_code . $request->phone,
                'password'            => Hash::make(123456),
                'verification_code'   => rand(1000, 9999),
                'balance'             => env('WELCOME_BONUS_AMOUNT'),
                'joined_community_id' => $request->community_id,
                'email'               => $request->email,
                'email_verified_at'   => date('Y-m-d H:i:s'),
                'address'             => $request->address
            ]);

            $user->referral_key = md5($user->id);
            $user->save();

            $customer = new Customer;
            $customer->user_id = $user->id;
            $customer->save();

            flash(translate('Customer has been added.'))->success();
            return redirect('admin/customers');
        } else {
            flash('Customer with same phone number already present.')->error();
            return back();
        }
    }

    public function edit_customer($id)
    {
        $user = User::findOrFail($id);

        if ($user) {
            $sellers = Seller::all();

            return view('backend.customer.customers.edit_customer', compact('user', 'sellers'));
        } else {
            flash('Customer not found')->error();
            return back();
        }
    }

    public function update_customer(Request $request)
    {
        $user_present = User::where('id', '!=', $request->user_id)->where('phone', '+' . $request->country_code . $request->phone)->first();

        if (!$user_present) {
            if (!empty($request->email)) {
                $user = User::where('email', $request->email)->where('id', '!=', $request->user_id)->first();
                if ($user) {
                    flash('Customer with same email already present.')->error();
                    return back();
                }
            }

            $user = User::where('id', $request->user_id)->first();

            $user->name = $request->name;
            $user->phone = '+' . $request->country_code . $request->phone;
            $user->joined_community_id = $request->community_id;
            $user->email = $request->email;
            $user->address = $request->address;
            $user->save();

            flash(translate('Customer has been added.'))->success();
            return redirect('admin/customers');
        } else {
            flash('Customer with same phone number already present.')->error();
            return back();
        }
    }

    public function export(Request $request)
    {
        return Excel::download(new CustomersExport($request), 'customers.xlsx');
    }
}
