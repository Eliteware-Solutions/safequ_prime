<?php

namespace App\Models;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\User;
use DB;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;

class BestSaleProductsExport implements FromCollection, WithMapping, WithHeadings
{
    public $filter_date, $search, $order_by_count;

    public function __construct($request)
    {
        $this->filter_date = $request->filter_date;
        $this->search = $request->search;
        $this->order_by_count = $request->order_by_count;
    }

    public function collection()
    {
        ini_set('memory_limit', -1);
        $order_by_count = 'desc';
        $from_date = date('d-m-Y', strtotime(' -90 days'));
        $to_date = date('d-m-Y');

        if ($this->filter_date != null) {
            $req_date = explode('to', $this->filter_date);
            $from_date = date('d-m-Y', strtotime($req_date[0]));
            $to_date = date('d-m-Y', strtotime($req_date[1]));
        }

        if ($this->order_by_count != null) {
            $order_by_count = $this->order_by_count;
        }

        $order_details = OrderDetail::select(DB::raw('COUNT(order_details.product_id) as order_count'), DB::raw('SUM(quantity) as total_qty'), 'products.name as product_name', 'products.thumbnail_img', 'order_details.*')
            ->join('orders', 'orders.id', '=', 'order_details.order_id')
            ->join('products', 'products.id', '=', 'order_details.product_id')
            ->whereRaw(" DATE_FORMAT(order_details.created_at, '%Y-%m-%d') >= '".date('Y-m-d', strtotime($from_date))."' ")
            ->whereRaw(" DATE_FORMAT(order_details.created_at, '%Y-%m-%d') <= '".date('Y-m-d', strtotime($to_date))."' ")
            ->whereRaw(" (added_by_admin = 1 OR (orders.payment_status = 'paid' AND added_by_admin = 0)) ")
            ->groupBy('order_details.product_id')
            ->orderBy('order_count', $order_by_count);

        if ($this->search != null) {
            $order_details = $order_details->where('products.name', 'like', '%' . $this->search . '%');
        }

        return $order_details->get();
    }

    public function headings(): array
    {
        return [
            'Name',
            'Total Orders',
            'Total Qty',
            'Unit'
        ];
    }

    /**
     * @var Order $order_detail
     */
    public function map($order_detail): array
    {
        $name = isset($order_detail->product_name) ? $order_detail->product_name : '--';
        $totalOrders = isset($order_detail->order_count) ? $order_detail->order_count : '--';
        $totalQty = isset($order_detail->total_qty) ? $order_detail->total_qty : '--';
        $unit = '';
        if($order_detail->total_qty * floatval($order_detail->product->min_qty) < 1){
            $unit =  (1000 * floatval($order_detail->product->min_qty)) . ' ' . $order_detail->product->secondary_unit;
        } elseif (floatval($order_detail->product->min_qty) > 1) {
            $unit = ($order_detail->total_qty * floatval($order_detail->product->min_qty)) . ' ' . $order_detail->product->unit;
        } else {
            $unit = $order_detail->product->unit;
        }

        return [
            $name,
            $totalOrders,
            $totalQty,
            $unit
        ];
    }
}
