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

class OrderAcqStackBarChartExport implements FromCollection, WithMapping, WithHeadings
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

            $obj->total_users = '0';
            $obj->total_new_users = '0';
            $obj->total_repeat_users = '0';

            if (strtotime($loop_month) <= strtotime(date('Y-m'))) {
                $total_customers = 0;

                $repeat_user_order_data = Order::select('orders.user_id')
                    ->whereRaw(" DATE(orders.created_at) >= '$month_start_date' AND DATE(orders.created_at) <= '$month_last_date' AND (added_by_admin = 1 OR (payment_status = 'paid' AND added_by_admin = 0)) AND user_id IN (SELECT orders.user_id  FROM `orders` WHERE DATE(orders.created_at) >= '$prev_month_first_date' AND DATE(orders.created_at) <= '$prev_month_last_date'
AND (added_by_admin = 1 OR (payment_status = 'paid' AND added_by_admin = 0)) GROUP BY orders.user_id) ")
                    ->groupBy('orders.user_id')
                    ->get();
                $obj->total_repeat_users = $repeat_user_order_data->count();
                $total_customers += $repeat_user_order_data->count();

                $new_user_order_data = Order::select('orders.user_id')
                    ->whereRaw(" DATE(orders.created_at) >= '$month_start_date' AND DATE(orders.created_at) <= '$month_last_date' AND (added_by_admin = 1 OR (payment_status = 'paid' AND added_by_admin = 0)) AND user_id NOT IN (SELECT orders.user_id  FROM `orders` WHERE DATE(orders.created_at) <= '$prev_month_last_date'
AND (added_by_admin = 1 OR (payment_status = 'paid' AND added_by_admin = 0)) GROUP BY orders.user_id) ")
                    ->groupBy('orders.user_id')
                    ->get();
                $obj->total_new_users = $new_user_order_data->count();
                $total_customers += $new_user_order_data->count();

                $obj->total_users = $total_customers;
            }

            $result[] = $obj;
        }

        return new \Illuminate\Support\Collection($result);
    }

    public function headings(): array
    {
        return [
            'Label',
            'Total Customers',
            'New Customers',
            'Repeat Customers'
        ];
    }

    /**
     * @var Order $order_data
     */
    public function map($order_data): array
    {
        return [
            $order_data->label_txt,
            $order_data->total_users,
            $order_data->total_new_users,
            $order_data->total_repeat_users
        ];
    }
}
