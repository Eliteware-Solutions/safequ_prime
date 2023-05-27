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

class OrdersLineChartExport implements FromCollection, WithMapping, WithHeadings
{
    public $year, $search, $chart_type;

    public function __construct($request)
    {
        $this->year = $request->year;
        $this->chart_type = $request->chart_type;
    }

    public function collection()
    {
        ini_set('memory_limit', -1);

        $cur_year = $this->year;
        $chart_type = $this->chart_type;
        $orders_data = array();
        if ($chart_type == 'month') {
            $orders_data = Order::select(DB::raw('count(orders.id) as total_orders'), DB::raw('MONTH(orders.created_at) as order_month'), DB::raw('MONTHNAME(orders.created_at) as label_txt'))->whereRaw(" YEAR(orders.created_at) = $cur_year AND (added_by_admin = 1 OR (payment_status = 'paid' AND added_by_admin = 0)) ")->groupBy('order_month');
        } else {
            $last_date = date('Y-m-d', strtotime("-7 days"));
            $orders_data = Order::select(DB::raw('count(orders.id) as total_orders'), DB::raw('DATE(orders.created_at) as label_txt'))->whereRaw(" DATE(orders.created_at) >= '$last_date' AND (added_by_admin = 1 OR (payment_status = 'paid' AND added_by_admin = 0)) ")->groupBy('label_txt');
        }

        return $orders_data->get();
    }

    public function headings(): array
    {
        return [
            'Label',
            'Total Orders'
        ];
    }

    /**
     * @var Order $orders_data
     */
    public function map($orders_data): array
    {
        return [
            $orders_data->label_txt,
            $orders_data->total_orders
        ];
    }
}
