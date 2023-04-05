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

class RegularCustomersExport implements FromCollection, WithMapping, WithHeadings
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

        $orders = Order::select(DB::raw('COUNT(orders.id) as total_orders'), 'users.name as user_name', 'users.phone', 'users.email', 'users.address', 'orders.*')
            ->join('users', 'users.id', '=', 'orders.user_id')
            ->whereRaw(" DATE_FORMAT(orders.created_at, '%Y-%m-%d') >= '".date('Y-m-d', strtotime($from_date))."' ")
            ->whereRaw(" DATE_FORMAT(orders.created_at, '%Y-%m-%d') <= '".date('Y-m-d', strtotime($to_date))."' ")
            ->whereRaw(" (added_by_admin = 1 OR (orders.payment_status = 'paid' AND added_by_admin = 0)) ")
            ->groupBy('orders.user_id')
            ->orderBy('total_orders', $order_by_count);

        if ($this->search != null) {
            $orders = $orders->whereRaw(" (users.name LIKE '%".$this->search."%' OR users.phone LIKE '%".$this->search."%' OR users.email LIKE '%".$this->search."%') ");
        }

        return $orders->get();
    }

    public function headings(): array
    {
        return [
            'Name',
            'Phone',
            'Email',
            'Total Orders',
            'Address',
        ];
    }

    /**
     * @var Order $order
     */
    public function map($order): array
    {
        $name = isset($order->user_name) ? $order->user_name : '--';
        $phone = isset($order->phone) ? str_replace('+91', '', $order->phone) : '--';
        $email = isset($order->email) ? $order->email : '--';
        $totalOrders = isset($order->total_orders) ? $order->total_orders : '--';
        $address = isset($order->address) ? $order->address : '--';

        return [
            $name,
            $phone,
            $email,
            $totalOrders,
            $address
        ];
    }
}
