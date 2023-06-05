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

class AcquireCustomersExport implements FromCollection, WithMapping, WithHeadings
{
    public $filter_date, $search;

    public function __construct($request)
    {
        $this->filter_date = $request->filter_date;
        $this->search = $request->search;
    }

    public function collection()
    {
        ini_set('memory_limit', -1);
        $order_by_count = 'desc';
        $from_date = date('01-m-Y');
        $to_date = date('t-m-Y');

        if ($this->filter_date != null) {
            $req_date = explode('to', $this->filter_date);
            $from_date = date('d-m-Y', strtotime($req_date[0]));
            $to_date = date('d-m-Y', strtotime($req_date[1]));
        }

        /*if ($this->order_by_count != null) {
            $order_by_count = $this->order_by_count;
        }*/

        $users = User::whereRaw(" DATE(users.created_at) >= '".date('Y-m-d', strtotime($from_date))."' ")
            ->whereRaw(" DATE(users.created_at) <= '".date('Y-m-d', strtotime($to_date))."' ")
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
