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

class CustomerStackBarChartExport implements FromCollection, WithMapping, WithHeadings
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
            $month_start_date = date("$year-$month-01");
            $loop_month = date("$year-$month");
            $month_last_date = date("Y-m-t", strtotime($loop_month));

            $obj->total_users = '0';
            $obj->total_new_users = '0';
            $obj->total_repeat_users = '0';

            if (strtotime($loop_month) <= strtotime(date('Y-m'))) {
                $total_users_data = User::select(DB::raw('COUNT(id) as total_users'))->whereRaw(" user_type = 'customer' AND banned = 0 AND DATE(users.created_at) <= '$month_last_date'")->first();
                $obj->total_users = intval($total_users_data->total_users);

                $total_new_users_data = User::select(DB::raw('COUNT(id) as total_new_users'))->whereRaw(" user_type = 'customer' AND banned = 0 AND YEAR(users.created_at) = $year AND MONTH(users.created_at) = $month ")->first();
                $obj->total_new_users = intval($total_new_users_data->total_new_users);

                $sales_chart_data = Order::select('orders.user_id')
                    ->whereRaw(" DATE(orders.created_at) >= '$month_start_date' AND DATE(orders.created_at) <= '$month_last_date' AND (added_by_admin = 1 OR (payment_status = 'paid' AND added_by_admin = 0)) AND user_id IN (SELECT orders.user_id  FROM `orders` WHERE DATE(orders.created_at) < '$month_start_date'
AND (added_by_admin = 1 OR (payment_status = 'paid' AND added_by_admin = 0)) GROUP BY orders.user_id) ")
                    ->groupBy('orders.user_id')
                    ->get();
                $obj->total_repeat_users = $sales_chart_data->count();
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
     * @var Order $customer_data
     */
    public function map($customer_data): array
    {
        return [
            $customer_data->label_txt,
            $customer_data->total_users,
            $customer_data->total_new_users,
            $customer_data->total_repeat_users
        ];
    }
}
