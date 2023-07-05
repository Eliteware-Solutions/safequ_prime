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
        $this->payment_status = $request->payment_status;
        $this->filter_date = $request->filter_date;
        $this->search = $request->search;
    }

    public function collection()
    {
        ini_set('memory_limit', -1);
        $orders = OrderDetail::orderBy('id', 'desc');
        if ($this->payment_status != 'unpaid') {
            $orders = $orders->whereHas('order', function ($query) {
                $query->whereRaw("(added_by_admin = 1 OR (payment_status = 'paid' AND added_by_admin = 0))");
            });
        }

        if ($this->search != null) {
            $orders = $orders->whereHas('order', function ($query) {
                $query->where('code', 'like', '%' . $this->search . '%');
            });
        }
        if ($this->delivery_status != null) {
            $orders = $orders->where('delivery_status', $this->delivery_status);
        }
        if ($this->payment_status != null) {
            $orders = $orders->whereHas('order', function ($query) {
                $query->where('added_by_admin', 1)->where('payment_status', $this->payment_status);
            });
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

        if (isset($order->product) && isset($order->product->min_qty)) {
            $qty_unit_main = $order->product->min_qty;
            if (floatval($order->product->min_qty) < 1) {
                $qty_unit_main = (1000 * floatval($order->product->min_qty));
            }
            $product_name = $order->product->name;
            $product_variation = $order->product->variation;
            $product_secondary_unit = $order->product->secondary_unit;
        } else {
            $qty_unit_main = 0;
            $product_name = '--';
            $product_variation = '--';
            $product_secondary_unit = '--';
        }

        return [
            $code,
            $order->created_at,
            $deliveryDate,
            $userName,
            $userPhone,
            $communityName,
            $flatNo,
            $product_name,
            $parentCategory,
            $subCategory,
            $product_variation,
            $farmLocation,
            $order->quantity,
            number_format($qty_unit_main, 0) . ' ' . $product_secondary_unit,
            number_format(floatval($order->price / $order->quantity), 2),
            $order->price,
            $order->payment_status,
            $paymentType,
            $order->delivery_status,
            $addedByAdmin
        ];
    }
}
