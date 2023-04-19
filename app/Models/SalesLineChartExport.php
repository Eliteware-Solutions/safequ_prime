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

class SalesLineChartExport implements FromCollection, WithMapping, WithHeadings
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
        $sales_data = array();
        if ($chart_type == 'month') {
            $sales_data = Order::select(DB::raw('SUM(orders.grand_total) as total_amt'), DB::raw('SUM(orders.coupon_discount) as total_discount'), DB::raw('MONTH(orders.created_at) as order_month'), DB::raw('MONTHNAME(orders.created_at) as label_txt'))->whereRaw(" YEAR(orders.created_at) = $cur_year ")->groupBy('order_month');
        } else {
            $last_date = date('Y-m-d', strtotime("-7 days"));
            $sales_data = Order::select(DB::raw('SUM(orders.grand_total) as total_amt'), DB::raw('SUM(orders.coupon_discount) as total_discount'), DB::raw('DATE(orders.created_at) as label_txt'))->whereRaw(" DATE(orders.created_at) >= '$last_date' ")->groupBy('label_txt');
        }

        return $sales_data->get();
    }

    public function headings(): array
    {
        return [
            'Label',
            'Total Sales'
        ];
    }

    /**
     * @var Order $sales_data
     */
    public function map($sales_data): array
    {
        $sales_total_amount = 0;
        if (floatval($sales_data->total_discount) > 0) {
            $sales_total_amount = floatval($sales_data->total_amt) - floatval($sales_data->total_discount);
        } else {
            $sales_total_amount = floatval($sales_data->total_amt);
        }

        return [
            $sales_data->label_txt,
            $sales_total_amount
        ];
    }
}
