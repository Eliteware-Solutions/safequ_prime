<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\SalesLineChartExport;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use Artisan;
use Cache;
use CoreComponentRepository;
use DB;
use Excel;

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
        // $cached_data = Cache::remember('cached_data', 3600, function () use ($to, $from, $root_categories) {
        if ($request->date != null) {
            $from = explode(" to ", $request->date)[0];
            $to = explode(" to ", $request->date)[1];
        }

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

        $item['total_customers'] = User::where('user_type', 'customer')->count();

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

        //     return $item;
        // });
        $cached_data = $item;

        $cur_year = date('Y');

        return view('backend.dashboard', compact('root_categories', 'cached_data', 'from', 'to', 'cur_year'));
    }

    function clearCache(Request $request)
    {
        Artisan::call('cache:clear');
        flash(translate('Cache cleared successfully'))->success();
        return back();
    }

    public function sales_line_chart(Request $request)
    {
        $cur_year = $request->year;
        $chart_type = $request->chart_type;
        $totalSalesAmountAry = array();
        $totalSalesAmountPriceAry = array();
        $labels = array();
        if ($chart_type == 'month') {
            $labels = array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
            for ($month = 1; $month <= 12; $month++) {
                $sales_total_amount = 0;
                $sales_chart_data = Order::select(DB::raw('SUM(orders.grand_total) as total_amt'), DB::raw('SUM(orders.coupon_discount) as total_discount'))->whereRaw(" YEAR(orders.created_at) = $cur_year AND MONTH(orders.created_at) = $month ")->first();
                if (floatval($sales_chart_data->total_discount) > 0) {
                    $sales_total_amount = floatval($sales_chart_data->total_amt) - floatval($sales_chart_data->total_discount);
                } else {
                    $sales_total_amount = floatval($sales_chart_data->total_amt);
                }
                $totalSalesAmountAry[] = $sales_total_amount;
                $totalSalesAmountPriceAry[] = single_price($sales_total_amount);
            }
        } else { // Last 7 days data
            for ($i = 7; $i > 0; $i--) {
                $date = date('Y-m-d', strtotime("-$i days"));
                $labels[] = date('d-m-Y', strtotime("-$i days"));
                $sales_total_amount = 0;
                $sales_chart_data = Order::select(DB::raw('SUM(orders.grand_total) as total_amt'), DB::raw('SUM(orders.coupon_discount) as total_discount'))->whereRaw(" DATE(orders.created_at) = '$date' ")->first();

                if (floatval($sales_chart_data->total_discount) > 0) {
                    $sales_total_amount = floatval($sales_chart_data->total_amt) - floatval($sales_chart_data->total_discount);
                } else {
                    $sales_total_amount = floatval($sales_chart_data->total_amt);
                }
                $totalSalesAmountAry[] = $sales_total_amount;
                $totalSalesAmountPriceAry[] = single_price($sales_total_amount);
            }
        }

        $sales_order_chart = [
            'labels'   => $labels,
            'datasets' => [
                [
                    'label'           => 'Sales Order',
                    'backgroundColor' => 'transparent',
                    'borderColor'     => '#4f7dff',
                    'data'            => $totalSalesAmountAry,
                    'extraData'       => $totalSalesAmountPriceAry,
                ],
            ],
        ];

        return response()->json(array('data' => $sales_order_chart));
    }

    public function customer_bar_chart(Request $request)
    {
        $year = $request->year;
        $chart_type = $request->chart_type;
        $totalUsersAry = array();
        $totalNewUsersAry = array();
        $totalRepeatUsersAry = array();
        $labels = array();
        if ($chart_type == 'month') {
            $labels = array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
            for ($month = 1; $month <= 12; $month++) {
                if ($month < 10) {
                    $month = 0 . $month;
                }
                $month_start_date = date("$year-$month-01");
                $loop_month = date("$year-$month");
                $month_last_date = date("Y-m-t", strtotime($loop_month));

                if (strtotime($loop_month) <= strtotime(date('Y-m'))) {
                    $total_users_data = User::select(DB::raw('COUNT(id) as total_users'))->whereRaw(" user_type = 'customer' AND banned = 0 AND DATE(users.created_at) <= '$month_last_date'")->first();
                    $totalUsersAry[] = intval($total_users_data->total_users);

                    $total_new_users_data = User::select(DB::raw('COUNT(id) as total_new_users'))->whereRaw(" user_type = 'customer' AND banned = 0 AND YEAR(users.created_at) = $year AND MONTH(users.created_at) = $month ")->first();
                    $totalNewUsersAry[] = intval($total_new_users_data->total_new_users);

                    $sales_chart_data = Order::select('orders.user_id')
                                ->whereRaw(" DATE(orders.created_at) >= '$month_start_date' AND DATE(orders.created_at) <= '$month_last_date' AND (added_by_admin = 1 OR (payment_status = 'paid' AND added_by_admin = 0)) AND user_id IN (SELECT orders.user_id  FROM `orders` WHERE DATE(orders.created_at) < '$month_start_date'
AND (added_by_admin = 1 OR (payment_status = 'paid' AND added_by_admin = 0)) GROUP BY orders.user_id) ")
                                ->groupBy('orders.user_id')
                                ->get();

                    $totalRepeatUsersAry[] = $sales_chart_data->count();
                } else {
                    $totalUsersAry[] = 0;
                    $totalNewUsersAry[] = 0;
                    $totalRepeatUsersAry[] = 0;
                }
            }
        } else { // For weekly

        }

        $users_bar_chart = [
            'labels'   => $labels,
            'datasets' => [
                [
                    'label'           => 'Total Customers',
                    'backgroundColor' => '#044c78',
                    'borderColor'     => '#044c78',
                    'data'            => $totalUsersAry,
                    'stack'           => 'Stack 0',
                ],
                [
                    'label'           => 'New Customers',
                    'backgroundColor' => '#47a6c0',
                    'borderColor'     => '#47a6c0',
                    'data'            => $totalNewUsersAry,
                    'stack'           => 'Stack 0',
                ],
                [
                    'label'           => 'Repeat Customers',
                    'backgroundColor' => '#feac54',
                    'borderColor'     => '#feac54',
                    'data'            => $totalRepeatUsersAry,
                    'stack'           => 'Stack 0',
                ]
            ],
        ];

        return response()->json(array('data' => $users_bar_chart));
    }

    public function order_acq_bar_chart(Request $request)
    {
        $year = $request->year;
        $totalUsersAry = array();
        $totalNewUsersAry = array();
        $totalRepeatUsersAry = array();
        $totalUsersAry = array();
        $labels = array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');

        for ($month = 1; $month <= 12; $month++) {
            if ($month < 10) {
                $month = 0 . $month;
            }
            $loop_month = date("$year-$month");
            $month_start_date = date("$year-$month-01");
            $month_last_date = date("Y-m-t", strtotime($loop_month));
            $prev_month_first_date = date("Y-m-01", strtotime($loop_month . ' -1 month'));
            $prev_month_last_date = date("Y-m-t", strtotime($loop_month . ' -1 month'));

            if (strtotime($loop_month) <= strtotime(date('Y-m'))) {
                $total_customers = 0;
                $repeat_user_order_data = Order::select('orders.user_id')
                    ->whereRaw(" DATE(orders.created_at) >= '$month_start_date' AND DATE(orders.created_at) <= '$month_last_date' AND (added_by_admin = 1 OR (payment_status = 'paid' AND added_by_admin = 0)) AND user_id IN (SELECT orders.user_id  FROM `orders` WHERE DATE(orders.created_at) >= '$prev_month_first_date' AND DATE(orders.created_at) <= '$prev_month_last_date'
AND (added_by_admin = 1 OR (payment_status = 'paid' AND added_by_admin = 0)) GROUP BY orders.user_id) ")
                    ->groupBy('orders.user_id')
                    ->get();
                $totalRepeatUsersAry[] = $repeat_user_order_data->count();
                $total_customers += $repeat_user_order_data->count();

                $new_user_order_data = Order::select('orders.user_id')
                    ->whereRaw(" DATE(orders.created_at) >= '$month_start_date' AND DATE(orders.created_at) <= '$month_last_date' AND (added_by_admin = 1 OR (payment_status = 'paid' AND added_by_admin = 0)) AND user_id NOT IN (SELECT orders.user_id  FROM `orders` WHERE DATE(orders.created_at) <= '$prev_month_last_date'
AND (added_by_admin = 1 OR (payment_status = 'paid' AND added_by_admin = 0)) GROUP BY orders.user_id) ")
                    ->groupBy('orders.user_id')
                    ->get();
                $totalNewUsersAry[] = $new_user_order_data->count();
                $total_customers += $new_user_order_data->count();

                $totalUsersAry[] = $total_customers;
            } else {
                $totalNewUsersAry[] = 0;
                $totalRepeatUsersAry[] = 0;
                $totalUsersAry[] = 0;
            }
        }

        $order_acq_bar_chart = [
            'labels'   => $labels,
            'datasets' => [
                [
                    'label'           => 'Repeat Customers',
                    'backgroundColor' => '#4bb3dc',
                    'borderColor'     => '#4bb3dc',
                    'data'            => $totalRepeatUsersAry,
                    'stack'           => 'Stack 0',
                ],
                [
                    'label'           => 'New Customers',
                    'backgroundColor' => '#e37674',
                    'borderColor'     => '#e37674',
                    'data'            => $totalNewUsersAry,
                    'stack'           => 'Stack 0',
                ],
                [
                    'label'           => 'Total Customers',
                    'backgroundColor' => '#4ab0aa',
                    'borderColor'     => '#4ab0aa',
                    'data'            => $totalUsersAry,
                    'stack'           => 'Stack 1',
                ]
            ],
        ];

        return response()->json(array('data' => $order_acq_bar_chart));
    }

    public function orders_line_chart(Request $request)
    {
        $cur_year = $request->year;
        $chart_type = $request->chart_type;
        $totalOrdersAry = array();
        $labels = array();
        if ($chart_type == 'month') {
            $labels = array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
            for ($month = 1; $month <= 12; $month++) {
                $total_orders_data = Order::select(DB::raw('count(orders.id) as total_orders'))->whereRaw(" YEAR(orders.created_at) = $cur_year AND MONTH(orders.created_at) = $month AND (added_by_admin = 1 OR (payment_status = 'paid' AND added_by_admin = 0)) ")->first();
                $totalOrdersAry[] = $total_orders_data->total_orders;
            }
        } else { // Last 7 days data
            for ($i = 7; $i > 0; $i--) {
                $date = date('Y-m-d', strtotime("-$i days"));
                $labels[] = date('d-m-Y', strtotime("-$i days"));
                $total_orders_data = Order::select(DB::raw('count(orders.id) as total_orders'))->whereRaw(" DATE(orders.created_at) = '$date' AND (added_by_admin = 1 OR (payment_status = 'paid' AND added_by_admin = 0)) ")->first();

                $totalOrdersAry[] = $total_orders_data->total_orders;
            }
        }

        $total_order_chart = [
            'labels'   => $labels,
            'datasets' => [
                [
                    'label'           => 'Total Order',
                    'backgroundColor' => 'transparent',
                    'borderColor'     => '#ff6361',
                    'data'            => $totalOrdersAry,
                ],
            ],
        ];

        return response()->json(array('data' => $total_order_chart));
    }

    public function order_break_bar_chart(Request $request)
    {
        $year = $request->year;
        $totalUserOrdersAry = array();
        $totalAdminOrdersAry = array();
        $totalOrdersAry = array();
        $labels = array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');

        for ($month = 1; $month <= 12; $month++) {
            if ($month < 10) {
                $month = 0 . $month;
            }
            $loop_month = date("$year-$month");
            $month_start_date = date("$year-$month-01");
            $month_last_date = date("Y-m-t", strtotime($loop_month));
            $prev_month_first_date = date("Y-m-01", strtotime($loop_month . ' -1 month'));
            $prev_month_last_date = date("Y-m-t", strtotime($loop_month . ' -1 month'));

            if (strtotime($loop_month) <= strtotime(date('Y-m'))) {
                $total_orders = 0;
                $admin_orders_data = Order::select(DB::raw('count(orders.id) as total_admin_orders'))
                    ->whereRaw(" DATE(orders.created_at) >= '$month_start_date' AND DATE(orders.created_at) <= '$month_last_date' AND added_by_admin = 1 ")
                    ->first();
                $totalAdminOrdersAry[] = $admin_orders_data->total_admin_orders;
                $total_orders += $admin_orders_data->total_admin_orders;

                $users_order_data = Order::select(DB::raw('count(orders.id) as total_admin_orders'))
                    ->whereRaw(" DATE(orders.created_at) >= '$month_start_date' AND DATE(orders.created_at) <= '$month_last_date' AND payment_status = 'paid' AND added_by_admin = 0 ")
                    ->first();
                $totalUserOrdersAry[] = $users_order_data->total_admin_orders;
                $total_orders += $users_order_data->total_admin_orders;

                $totalOrdersAry[] = $total_orders;
            } else {
                $totalUserOrdersAry[] = 0;
                $totalAdminOrdersAry[] = 0;
                $totalOrdersAry[] = 0;
            }
        }

        $order_break_bar_chart = [
            'labels'   => $labels,
            'datasets' => [
                [
                    'label'           => 'Admin Orders',
                    'backgroundColor' => '#bc5090',
                    'borderColor'     => '#bc5090',
                    'data'            => $totalAdminOrdersAry,
                    'stack'           => 'Stack 0',
                ],
                [
                    'label'           => 'App Orders',
                    'backgroundColor' => '#58508d',
                    'borderColor'     => '#58508d',
                    'data'            => $totalUserOrdersAry,
                    'stack'           => 'Stack 0',
                ],
                [
                    'label'           => 'Total Orders',
                    'backgroundColor' => '#ff7c43',
                    'borderColor'     => '#ff7c43',
                    'data'            => $totalOrdersAry,
                    'stack'           => 'Stack 1',
                ]
            ],
        ];

        return response()->json(array('data' => $order_break_bar_chart));
    }

    public function sales_line_chart_export(Request $request)
    {
        return Excel::download(new SalesLineChartExport($request), 'sales_line_chart.xlsx');
    }

}
