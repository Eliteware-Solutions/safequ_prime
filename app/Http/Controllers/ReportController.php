<?php

namespace App\Http\Controllers;

use App\Models\AcquireCustomersExport;
use App\Models\BestSaleProductsExport;
use App\Models\IdleCustomersExport;
use App\Models\LostCustomersExport;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\RegularCustomersExport;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\CommissionHistory;
use App\Models\Wallet;
use App\Models\Seller;
use App\Models\User;
use App\Models\Search;
use Auth;
use Excel;
use DB;

class ReportController extends Controller
{
    public function stock_report(Request $request)
    {
        $sort_by =null;
        $products = Product::orderBy('created_at', 'desc');
        if ($request->has('category_id')){
            $sort_by = $request->category_id;
            $products = $products->where('category_id', $sort_by);
        }
        $products = $products->paginate(15);
        return view('backend.reports.stock_report', compact('products','sort_by'));
    }

    public function in_house_sale_report(Request $request)
    {
        $sort_by =null;
        $products = Product::orderBy('num_of_sale', 'desc')->where('added_by', 'admin');
        if ($request->has('category_id')){
            $sort_by = $request->category_id;
            $products = $products->where('category_id', $sort_by);
        }
        $products = $products->paginate(15);
        return view('backend.reports.in_house_sale_report', compact('products','sort_by'));
    }

    public function seller_sale_report(Request $request)
    {
        $sort_by =null;
        $sellers = Seller::orderBy('created_at', 'desc');
        if ($request->has('verification_status')){
            $sort_by = $request->verification_status;
            $sellers = $sellers->where('verification_status', $sort_by);
        }
        $sellers = $sellers->paginate(10);
        return view('backend.reports.seller_sale_report', compact('sellers','sort_by'));
    }

    public function idle_users_report(Request $request)
    {
        $search = null;
        $order_by_users = '';
        $from_date = date('d-m-Y', strtotime(' -90 days'));
        $to_date = date('d-m-Y');

        if ($request->filter_date != null) {
            $req_date = explode('to', $request->filter_date);
            $from_date = date('d-m-Y', strtotime($req_date[0]));
            $to_date = date('d-m-Y', strtotime($req_date[1]));
        }

        $users = User::select(DB::raw('(SELECT orders.created_at FROM orders WHERE orders.user_id = users.id ORDER BY orders.created_at desc LIMIT 1) as last_order_date'), 'users.*')
                ->whereRaw(" user_type='customer' AND users.id NOT IN (SELECT orders.user_id FROM `orders` WHERE DATE_FORMAT(orders.created_at, '%Y-%m-%d') >= '".date('Y-m-d', strtotime($from_date))."' AND DATE_FORMAT(orders.created_at, '%Y-%m-%d') <= '".date('Y-m-d', strtotime($to_date))."' GROUP BY orders.user_id) ");

        if (trim($request->order_by_users) != '') {
            $order_by_users = $request->order_by_users;
            if ($request->order_by_users == 'desc') {
                $users = $users->orderBy('last_order_date', 'desc');
            } else {
                $users = $users->orderByRaw("COALESCE(last_order_date, 'zz') asc");
            }
        } else {
            $users = $users->orderBy('users.name', 'asc');
        }

        if ($request->search != null) {
            $search = $request->search;
            $users = $users
                ->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('email', 'like', '%' . $request->search . '%')
                ->orWhere('phone', 'like', '%' . $request->search . '%');
        }

        $users = $users->paginate(20);
        return view('backend.reports.idle_users_report', compact('users', 'from_date', 'to_date', 'search', 'order_by_users'));
    }

    public function idle_users_export(Request $request)
    {
        return Excel::download(new IdleCustomersExport($request), 'idle_users.xlsx');
    }

    public function best_sale_products(Request $request)
    {
        $search = null;
        $order_by_count = 'desc';
        $from_date = date('d-m-Y', strtotime(' -90 days'));
        $to_date = date('d-m-Y');

        if ($request->filter_date != null) {
            $req_date = explode('to', $request->filter_date);
            $from_date = date('d-m-Y', strtotime($req_date[0]));
            $to_date = date('d-m-Y', strtotime($req_date[1]));
        }

        if ($request->order_by_count != null) {
            $order_by_count = $request->order_by_count;
        }

        $order_details = OrderDetail::select(DB::raw('COUNT(order_details.product_id) as order_count'), DB::raw('SUM(quantity) as total_qty'), 'products.name as product_name', 'products.thumbnail_img', 'order_details.*')
                            ->join('orders', 'orders.id', '=', 'order_details.order_id')
                            ->join('products', 'products.id', '=', 'order_details.product_id')
                            ->whereRaw(" DATE_FORMAT(order_details.created_at, '%Y-%m-%d') >= '".date('Y-m-d', strtotime($from_date))."' ")
                            ->whereRaw(" DATE_FORMAT(order_details.created_at, '%Y-%m-%d') <= '".date('Y-m-d', strtotime($to_date))."' ")
                            ->whereRaw(" (added_by_admin = 1 OR (orders.payment_status = 'paid' AND added_by_admin = 0)) ")
                            ->groupBy('order_details.product_id')
                            ->orderBy('order_count', $order_by_count);

        if ($request->search != null) {
            $search = $request->search;
            $order_details = $order_details->where('products.name', 'like', '%' . $request->search . '%');
        }

        $order_details = $order_details->paginate(20);
        return view('backend.reports.best_sale_products_report', compact('order_details', 'from_date', 'to_date', 'search', 'order_by_count'));
    }

    public function best_sale_products_export(Request $request)
    {
        return Excel::download(new BestSaleProductsExport($request), 'best_sale_products.xlsx');
    }

    public function regular_users_report(Request $request)
    {
        $search = null;
        $order_by_count = 'desc';
        $from_date = date('d-m-Y', strtotime(' -90 days'));
        $to_date = date('d-m-Y');

        if ($request->filter_date != null) {
            $req_date = explode('to', $request->filter_date);
            $from_date = date('d-m-Y', strtotime($req_date[0]));
            $to_date = date('d-m-Y', strtotime($req_date[1]));
        }

        if ($request->order_by_count != null) {
            $order_by_count = $request->order_by_count;
        }

        $orders = Order::select(DB::raw('COUNT(orders.id) as total_orders'), 'users.name as user_name', 'users.phone', 'users.email', 'users.address', 'orders.*')
            ->join('users', 'users.id', '=', 'orders.user_id')
            ->whereRaw(" DATE(orders.created_at) >= '".date('Y-m-d', strtotime($from_date))."' ")
            ->whereRaw(" DATE(orders.created_at) <= '".date('Y-m-d', strtotime($to_date))."' ")
            ->whereRaw(" (added_by_admin = 1 OR (orders.payment_status = 'paid' AND added_by_admin = 0)) ")
            ->groupBy('orders.user_id')
            ->orderBy('total_orders', $order_by_count)
            ->orderBy('users.name', 'asc');

        if ($request->search != null) {
            $search = $request->search;
            $orders = $orders->whereRaw(" (users.name LIKE '%".$request->search."%' OR users.phone LIKE '%".$request->search."%' OR users.email LIKE '%".$request->search."%') ");
        }

        $orders = $orders->paginate(20);
        return view('backend.reports.regular_customers_report', compact('orders', 'from_date', 'to_date', 'search', 'order_by_count'));
    }

    public function regular_users_export(Request $request)
    {
        return Excel::download(new RegularCustomersExport($request), 'regular_customers.xlsx');
    }

    public function acquired_users_report(Request $request)
    {
        $search = null;
        $order_by_count = 'desc';
        $from_date = date('01-m-Y');
        $to_date = date('t-m-Y');

        if ($request->filter_date != null) {
            $req_date = explode('to', $request->filter_date);
            $from_date = date('d-m-Y', strtotime($req_date[0]));
            $to_date = date('d-m-Y', strtotime($req_date[1]));
        }

        /*if ($request->order_by_count != null) {
            $order_by_count = $request->order_by_count;
        }*/

        $users = User::whereRaw(" DATE(users.created_at) >= '".date('Y-m-d', strtotime($from_date))."' ")
                    ->whereRaw(" DATE(users.created_at) <= '".date('Y-m-d', strtotime($to_date))."' ")
                    ->orderBy('users.name', 'asc');

        if ($request->search != null) {
            $search = $request->search;
            $users = $users->whereRaw(" (users.name LIKE '%".$request->search."%' OR users.phone LIKE '%".$request->search."%' OR users.email LIKE '%".$request->search."%') ");
        }

        $users = $users->paginate(20);
        return view('backend.reports.acquired_customers_report', compact('users', 'from_date', 'to_date', 'search', 'order_by_count'));
    }

    public function acquired_users_export(Request $request)
    {
        return Excel::download(new AcquireCustomersExport($request), 'new_customers.xlsx');
    }

    public function lost_users_report(Request $request)
    {
        $search = null;
        $order_by_count = 'desc';
        $from_date = date('01-m-Y', strtotime('-1 month'));
        $to_date = date('t-m-Y', strtotime('-1 month'));
        $from_date_two = date('01-m-Y');
        $to_date_two = date('t-m-Y');

        if ($request->filter_date != null) {
            $req_date = explode('to', $request->filter_date);
            $from_date = date('d-m-Y', strtotime($req_date[0]));
            $to_date = date('d-m-Y', strtotime($req_date[1]));
        }

        if ($request->filter_date_two != null) {
            $req_date_two = explode('to', $request->filter_date_two);
            $from_date_two = date('d-m-Y', strtotime($req_date_two[0]));
            $to_date_two = date('d-m-Y', strtotime($req_date_two[1]));
        }

        $orders_from = Order::select('orders.user_id')->whereRaw(" DATE(orders.created_at) >= '".date('Y-m-d', strtotime($from_date))."' ")
            ->whereRaw(" DATE(orders.created_at) <= '".date('Y-m-d', strtotime($to_date))."' ")
            ->whereRaw(" (added_by_admin = 1 OR (orders.payment_status = 'paid' AND added_by_admin = 0)) ")
            ->groupBy('orders.user_id')
            ->get();

        $users_from_ary = array();
        foreach ($orders_from as $val) {
            array_push($users_from_ary, $val->user_id);
        }

        $orders_to = Order::select('orders.user_id')->whereRaw(" DATE(orders.created_at) >= '".date('Y-m-d', strtotime($from_date_two))."' ")
            ->whereNotIn('user_id', $users_from_ary)
            ->whereRaw(" DATE(orders.created_at) <= '".date('Y-m-d', strtotime($to_date_two))."' ")
            ->whereRaw(" (added_by_admin = 1 OR (orders.payment_status = 'paid' AND added_by_admin = 0)) ")
            ->groupBy('orders.user_id')
            ->get();

        $users_to_ary = array();
        foreach ($orders_to as $val) {
            array_push($users_to_ary, $val->user_id);
        }

        $lost_users = array_diff($users_from_ary, $users_to_ary);

        $users = User::whereIn('id', $lost_users)
            ->orderBy('users.name', 'asc');

        if ($request->search != null) {
            $search = $request->search;
            $users = $users->whereRaw(" (users.name LIKE '%".$request->search."%' OR users.phone LIKE '%".$request->search."%' OR users.email LIKE '%".$request->search."%') ");
        }

        $users = $users->paginate(20);
        return view('backend.reports.lost_customers_report', compact('users', 'from_date', 'to_date', 'search', 'order_by_count', 'from_date_two', 'to_date_two'));
    }

    public function lost_users_export(Request $request)
    {
        return Excel::download(new LostCustomersExport($request), 'lost_customers.xlsx');
    }

    public function wish_report(Request $request)
    {
        $sort_by =null;
        $products = Product::orderBy('created_at', 'desc');
        if ($request->has('category_id')){
            $sort_by = $request->category_id;
            $products = $products->where('category_id', $sort_by);
        }
        $products = $products->paginate(10);
        return view('backend.reports.wish_report', compact('products','sort_by'));
    }

    public function user_search_report(Request $request){
        $searches = Search::orderBy('count', 'desc')->paginate(10);
        return view('backend.reports.user_search_report', compact('searches'));
    }

    public function commission_history(Request $request) {
        $seller_id = null;
        $date_range = null;

        if(Auth::user()->user_type == 'seller') {
            $seller_id = Auth::user()->id;
        } if($request->seller_id) {
            $seller_id = $request->seller_id;
        }

        $commission_history = CommissionHistory::orderBy('created_at', 'desc');

        if ($request->date_range) {
            $date_range = $request->date_range;
            $date_range1 = explode(" / ", $request->date_range);
            $commission_history = $commission_history->where('created_at', '>=', $date_range1[0]);
            $commission_history = $commission_history->where('created_at', '<=', $date_range1[1]);
        }
        if ($seller_id){

            $commission_history = $commission_history->where('seller_id', '=', $seller_id);
        }

        $commission_history = $commission_history->paginate(10);
        if(Auth::user()->user_type == 'seller') {
            return view('frontend.user.seller.reports.commission_history_report', compact('commission_history', 'seller_id', 'date_range'));
        }
        return view('backend.reports.commission_history_report', compact('commission_history', 'seller_id', 'date_range'));
    }

    public function wallet_transaction_history(Request $request) {
        $user_id = null;
        $date_range = null;

        if($request->user_id) {
            $user_id = $request->user_id;
        }

        $users_with_wallet = User::whereIn('id', function($query) {
            $query->select('user_id')->from(with(new Wallet)->getTable());
        })->get();

        $wallet_history = Wallet::orderBy('created_at', 'desc');

        if ($request->date_range) {
            $date_range = $request->date_range;
            $date_range1 = explode(" / ", $request->date_range);
            $wallet_history = $wallet_history->where('created_at', '>=', $date_range1[0]);
            $wallet_history = $wallet_history->where('created_at', '<=', $date_range1[1]);
        }
        if ($user_id){
            $wallet_history = $wallet_history->where('user_id', '=', $user_id);
        }

        $wallets = $wallet_history->paginate(10);

        return view('backend.reports.wallet_history_report', compact('wallets', 'users_with_wallet', 'user_id', 'date_range'));
    }
}
