<?php

namespace App\Models;

use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use DB;

class OrderBreakStackBarChartExport implements FromCollection, WithMapping, WithHeadings
{
    public $year, $search;

    public function __construct($request)
    {
        $this->year = $request->year;
    }

    public function collection()
    {
        ini_set('memory_limit', -1);

        $year = $this->year;
        $result = array();
        $labels = array(1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec');
        for ($month = 1; $month <= 12; $month++) {
            $obj = new \stdClass();
            $obj->label_txt = $labels[$month];
            if ($month < 10) {
                $month = 0 . $month;
            }
            $loop_month = date("$year-$month");
            $month_start_date = date("$year-$month-01");
            $month_last_date = date("Y-m-t", strtotime($loop_month));
            $prev_month_first_date = date("Y-m-01", strtotime($loop_month . ' -1 month'));
            $prev_month_last_date = date("Y-m-t", strtotime($loop_month . ' -1 month'));

            $obj->admin_orders = '0';
            $obj->user_orders = '0';
            $obj->total_orders = '0';

            if (strtotime($loop_month) <= strtotime(date('Y-m'))) {
                $total_orders = 0;

                $admin_orders_data = Order::select(DB::raw('count(orders.id) as total_admin_orders'))
                    ->whereRaw(" DATE(orders.created_at) >= '$month_start_date' AND DATE(orders.created_at) <= '$month_last_date' AND added_by_admin = 1 ")
                    ->first();
                $obj->admin_orders = $admin_orders_data->total_admin_orders;
                $total_orders += $admin_orders_data->total_admin_orders;

                $users_order_data = Order::select(DB::raw('count(orders.id) as total_admin_orders'))
                    ->whereRaw(" DATE(orders.created_at) >= '$month_start_date' AND DATE(orders.created_at) <= '$month_last_date' AND payment_status = 'paid' AND added_by_admin = 0 ")
                    ->first();
                $obj->user_orders = $users_order_data->total_admin_orders;
                $total_orders += $users_order_data->total_admin_orders;

                $obj->total_orders = $total_orders;
            }

            $result[] = $obj;
        }

        return new \Illuminate\Support\Collection($result);
    }

    public function headings(): array
    {
        return [
            'Label',
            'Admin Orders',
            'App Orders',
            'Total Orders'
        ];
    }

    /**
     * @var Order $order_data
     */
    public function map($order_data): array
    {
        return [
            $order_data->label_txt,
            $order_data->admin_orders,
            $order_data->user_orders,
            $order_data->total_orders
        ];
    }
}
