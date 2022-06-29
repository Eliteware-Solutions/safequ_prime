<?php

namespace App\Models;

use App\Models\Order;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OrdersExport implements FromCollection, WithMapping, WithHeadings
{
    public $delivery_status, $filter_date, $search;

    public function __construct($request)
    {
        $this->delivery_status = $request->delivery_status;
        $this->filter_date = $request->filter_date;
        $this->search = $request->search;
    }

    public function collection()
    {
        $orders = OrderDetail::orderBy('id', 'desc');
        if ($this->search != null) {
            $orders = $orders->whereHas('order', function ($query) {
                $query->where('code', 'like', '%' . $this->search . '%');
            });
        }
        if ($this->delivery_status != null) {
            $orders = $orders->where('delivery_status', $this->delivery_status);
        }
        if ($this->filter_date != null) {
            $orders = $orders->where('created_at', '>=', date('Y-m-d', strtotime(explode(" to ", $this->filter_date)[0])))->where('created_at', '<=', date('Y-m-d', strtotime(explode(" to ", $this->filter_date)[1])));
        }

        return $orders->get();
    }

    public function headings(): array
    {
        return [
            'OrderCode',
            'Date',
            'DeliveryDate',
            'Name',
            'Phone',
            'Community',
            'FlatNo',
            'Product',
            'Category',
            'Sub Category',
            'Variation',
            'FarmLocation',
            'Qty',
            'Unit',
            'Price',
            'TotalPrice',
            'PaymentStatus',
            'PaymentType',
            'DeliveryStatus',
            'Added By Admin',
        ];
    }

    /**
     * @var Order $order
     */
    public function map($order): array
    {
        $userName = isset($order->order) && isset($order->order->user) ? $order->order->user->name : '--';
        $code = isset($order->order) ? $order->order->code : '--';
        $paymentType = isset($order->order) ? $order->order->payment_type : '--';
        $addedByAdmin = isset($order->order) && $order->order->added_by_admin == 1 ? 'true' : 'false';
        $userPhone = isset($order->order) && isset($order->order->user) ? str_replace('+91', '', $order->order->user->phone) : '--';
        $communityName = isset($order->product) && isset($order->product->user) ? $order->product->user->name : $order->seller_id;
        $flatNo = isset($order->order) && isset($order->order->user) ? $order->order->user->address : '--';
        $parentCategory = isset($order->product) && isset($order->product->parent_category) ? $order->product->parent_category->name : '--';
        $subCategory = isset($order->product) && isset($order->product->category_id) ? $order->product->category->name : '--';
        $farmLocation = isset($order->product) ? $order->product->manufacturer_location : '--';

        $deliveryDate = '--';
        if (isset($order->product_stock) && isset($order->product_stock->purchase_end_date)) {
            $deliveryDate = date('d-m-Y', strtotime($order->product_stock->purchase_end_date . '+' . intval($order->product_stock->est_shipping_days) . ' days'));
        } elseif ($order->is_archived == 1 && isset($order->archive_product_stock) && isset($order->archive_product_stock->purchase_end_date)) {
            $deliveryDate = date('d-m-Y', strtotime($order->archive_product_stock->purchase_end_date . '+' . intval($order->archive_product_stock->est_shipping_days) . ' days'));
        }

        $qty_unit_main = $order->product->min_qty;
        if (floatval($order->product->min_qty) < 1) {
            $qty_unit_main = (1000 * floatval($order->product->min_qty));
        }

        return [
            $code,
            $order->created_at,
            $deliveryDate,
            $userName,
            $userPhone,
            $communityName,
            $flatNo,
            $order->product->name,
            $parentCategory,
            $subCategory,
            $order->product->variation,
            $farmLocation,
            $order->quantity,
            number_format($qty_unit_main, 0) . ' ' . $order->product->secondary_unit,
            number_format(floatval($order->price / $order->quantity), 2),
            $order->price,
            $order->payment_status,
            $paymentType,
            $order->delivery_status,
            $addedByAdmin
        ];
    }
}
