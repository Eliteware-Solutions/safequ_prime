<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use Artisan;
use Cache;
use CoreComponentRepository;

class AdminController extends Controller
{
    /**
     * Show the admin dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function admin_dashboard(Request $request)
    {
        CoreComponentRepository::initializeCache();
        $root_categories = Category::where('level', 0)->get();
        $from = date('01-m-Y');
        $to = date('t-m-Y');
//        $cached_data = Cache::remember('cached_data', 3600, function () use ($to, $from, $root_categories) {
        if ($request->date != null) {
            $from = explode(" to ", $request->date)[0];
            $to = explode(" to ", $request->date)[1];
        }
//        dd($from,$to);
        $num_of_sale_data = null;
        $qty_data = null;
        foreach ($root_categories as $key => $category) {
            $category_ids = \App\Utility\CategoryUtility::children_ids($category->id);
            $category_ids[] = $category->id;

            $products = Product::with('stocks')->whereIn('category_id', $category_ids)->get();
            $qty = 0;
            $sale = 0;
            foreach ($products as $key => $product) {
                $sale += $product->num_of_sale;
                foreach ($product->stocks as $key => $stock) {
                    $qty += $stock->qty;
                }
            }
            $qty_data .= $qty . ',';
            $num_of_sale_data .= $sale . ',';
        }
        $item['num_of_sale_data'] = $num_of_sale_data;
        $item['qty_data'] = $qty_data;

        $item['total_customers'] = User::where('user_type', 'customer')->where('email_verified_at', '!=', null)->count();

        $item['total_orders'] = Order::whereDate('created_at', '>=', date('Y-m-d', strtotime($from)))->whereDate('created_at', '<=', date('Y-m-d', strtotime($to)))->where(function ($query) {
            $query->where('payment_status', 'paid')->orWhere(function ($query) {
                $query->where('added_by_admin', 1)->where('payment_status', 'unpaid');
            });
        })->count();

        $item['total_sales'] = Order::whereDate('created_at', '>=', date('Y-m-d', strtotime($from)))->whereDate('created_at', '<=', date('Y-m-d', strtotime($to)))->where(function ($query) {
            $query->where('payment_status', 'paid')->orWhere(function ($query) {
                $query->where('added_by_admin', 1)->where('payment_status', 'unpaid');
            });
        })->sum('grand_total');

        $item['total_pending_payment'] = Order::where('payment_status', 'unpaid')->where('added_by_admin', 1)->whereDate('created_at', '>=', date('Y-m-d', strtotime($from)))->whereDate('created_at', '<=', date('Y-m-d', strtotime($to)))->sum('grand_total');

        $item['community_data'] = Shop::whereIn('user_id', verified_sellers_id())->with([
            'unpaid_orders' => function ($query) use ($from, $to) {
                $query->whereDate('created_at', '>=', date('Y-m-d', strtotime($from)))->whereDate('created_at', '<=', date('Y-m-d', strtotime($to)));
            }, 'orders'     => function ($query) use ($from, $to) {
                $query->whereDate('created_at', '>=', date('Y-m-d', strtotime($from)))->whereDate('created_at', '<=', date('Y-m-d', strtotime($to)));
            }, 'delivered_orders'     => function ($query) use ($from, $to) {
                $query->whereDate('created_at', '>=', date('Y-m-d', strtotime($from)))->whereDate('created_at', '<=', date('Y-m-d', strtotime($to)));
            }
        ])->withCount('customers')->get();

//            return $item;
//        });
        $cached_data = $item;
        return view('backend.dashboard', compact('root_categories', 'cached_data', 'from', 'to'));
    }

    function clearCache(Request $request)
    {
        Artisan::call('cache:clear');
        flash(translate('Cache cleared successfully'))->success();
        return back();
    }
}
