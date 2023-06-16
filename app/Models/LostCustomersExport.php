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

class LostCustomersExport implements FromCollection, WithMapping, WithHeadings
{
    public $filter_date, $search, $filter_date_two;

    public function __construct($request)
    {
        $this->filter_date = $request->filter_date;
        $this->filter_date_two = $request->filter_date_two;
        $this->search = $request->search;
    }

    public function collection()
    {
        ini_set('memory_limit', -1);
        $order_by_count = 'desc';
        $from_date = date('01-m-Y', strtotime('-1 month'));
        $to_date = date('t-m-Y', strtotime('-1 month'));
        $from_date_two = date('01-m-Y');
        $to_date_two = date('t-m-Y');

        if ($this->filter_date != null) {
            $req_date = explode('to', $this->filter_date);
            $from_date = date('d-m-Y', strtotime($req_date[0]));
            $to_date = date('d-m-Y', strtotime($req_date[1]));
        }

        if ($this->filter_date_two != null) {
            $req_date_two = explode('to', $this->filter_date_two);
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

        if ($this->search != null) {
            $users = $users->whereRaw(" (users.name LIKE '%".$this->search."%' OR users.phone LIKE '%".$this->search."%' OR users.email LIKE '%".$this->search."%') ");
        }

        return $users->get();
    }

    public function headings(): array
    {
        return [
            'Name',
            'Phone',
            'Email',
            'Last Order',
        ];
    }

    /**
     * @var User $user
     */
    public function map($user): array
    {
        $name = isset($user->name) ? $user->name : '--';
        $phone = isset($user->phone) ? str_replace('+91', '', $user->phone) : '--';
        $email = isset($user->email) ? $user->email : '--';
        $lastOrder = isset($user->last_order) && trim($user->last_order->code) ? $user->last_order->code : '--';

        return [
            $name,
            $phone,
            $email,
            $lastOrder,
        ];
    }
}
