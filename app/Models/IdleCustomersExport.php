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

class IdleCustomersExport implements FromCollection, WithMapping, WithHeadings
{
    public $filter_date, $search, $order_by_users;

    public function __construct($request)
    {
        $this->filter_date = $request->filter_date;
        $this->search = $request->search;
        $this->order_by_users = $request->order_by_users;
    }

    public function collection()
    {
        ini_set('memory_limit', -1);
        $from_date = date('d-m-Y', strtotime(' -90 days'));
        $to_date = date('d-m-Y');

        if ($this->filter_date != null) {
            $req_date = explode('to', $this->filter_date);
            $from_date = date('d-m-Y', strtotime($req_date[0]));
            $to_date = date('d-m-Y', strtotime($req_date[1]));
        }

        $users = User::select(DB::raw('(SELECT orders.created_at FROM orders WHERE orders.user_id = users.id ORDER BY orders.created_at desc LIMIT 1) as last_order_date'), 'users.*')
                    ->whereRaw(" user_type='customer' AND users.id NOT IN (SELECT orders.user_id FROM `orders` WHERE DATE_FORMAT(orders.created_at, '%Y-%m-%d') >= '".date('Y-m-d', strtotime($from_date))."' AND DATE_FORMAT(orders.created_at, '%Y-%m-%d') <= '".date('Y-m-d', strtotime($to_date))."' GROUP BY orders.user_id) ");

        if (trim($this->order_by_users) != '') {
            if ($this->order_by_users == 'desc') {
                $users = $users->orderBy('last_order_date', 'desc');
            } else {
                $users = $users->orderByRaw("COALESCE(last_order_date, 'zz') asc");
            }
        } else {
            $users = $users->orderBy('users.name', 'asc');
        }

        if ($this->search != null) {
            $users = $users
                ->where('name', 'like', '%' . $this->search . '%')
                ->orWhere('email', 'like', '%' . $this->search . '%')
                ->orWhere('phone', 'like', '%' . $this->search . '%');
        }

        return $users->get();
    }

    public function headings(): array
    {
        return [
            'Name',
            'Phone',
            'Email',
            'Address',
            'Last Order Code',
            'Last Order Date'
        ];
    }

    /**
     * @var Order $user
     */
    public function map($user): array
    {
        $name = isset($user->name) ? $user->name : '--';
        $phone = isset($user->phone) ? str_replace('+91', '', $user->phone) : '--';
        $email = isset($user->email) ? $user->email : '--';
        $address = isset($user->address) ? $user->address : '--';
        $lastOrderCode = isset($user->last_order) && isset($user->last_order->code) ? $user->last_order->code : '--';
        $lastOderDate = isset($user->last_order) && isset($user->last_order->created_at) ? date('d-m-Y', strtotime($user->last_order->created_at)) : '--';

        return [
            $name,
            $phone,
            $email,
            $address,
            $lastOrderCode,
            $lastOderDate
        ];
    }
}
