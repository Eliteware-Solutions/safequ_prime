@extends('backend.layouts.app')

@section('content')
    <div class="row gutters-10">

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0 fs-14">{{ translate('Information') }}</h6>
                </div>
                <div class="card-body">
                    <p><b>Name: </b> {{ $user->name }}</p>
                    <p><b>Email: </b> {{ $user->email }}</p>
                    <p><b>Phone: </b> {{ $user->phone }}</p>
                    <p><b>Address: </b> {{ $user->address }}</p>
                    <a href="javascript:void(0)" class="" onclick="copyUrl(this)"
                        data-url="{{ route('referral.registration', $user->referral_key) }}">
                        <button type="submit" class="btn btn-primary"><i class="las la-clipboard mr-2"></i>
                            {{ translate('Copy Referral Link') }}</button>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="row gutters-10">
                <div class="col-6">
                    <div class="bg-grad-2 text-white rounded-lg mb-4 overflow-hidden">
                        <div class="px-3 pt-3">
                            <div class="opacity-50">
                                <span class="fs-12 d-block">{{ translate('Wallet') }}</span>
                                {{ translate('Balance') }}
                            </div>
                            <div class="h3 fw-700 mb-3">
                                {{ single_price($user->balance) }}
                            </div>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
                            <path fill="rgba(255,255,255,0.3)" fill-opacity="1"
                                d="M0,128L34.3,112C68.6,96,137,64,206,96C274.3,128,343,224,411,250.7C480,277,549,235,617,213.3C685.7,192,754,192,823,181.3C891.4,171,960,149,1029,117.3C1097.1,85,1166,43,1234,58.7C1302.9,75,1371,149,1406,186.7L1440,224L1440,320L1405.7,320C1371.4,320,1303,320,1234,320C1165.7,320,1097,320,1029,320C960,320,891,320,823,320C754.3,320,686,320,617,320C548.6,320,480,320,411,320C342.9,320,274,320,206,320C137.1,320,69,320,34,320L0,320Z">
                            </path>
                        </svg>
                    </div>
                </div>
                <div class="col-6">
                    <div class="bg-grad-3 text-white rounded-lg mb-4 overflow-hidden">
                        <div class="px-3 pt-3">
                            <div class="opacity-50">
                                <span class="fs-12 d-block">{{ translate('Total') }}</span>
                                {{ translate('Orders') }}
                            </div>
                            <div class="h3 fw-700 mb-3">{{ $user->orders->count() }}</div>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
                            <path fill="rgba(255,255,255,0.3)" fill-opacity="1"
                                d="M0,128L34.3,112C68.6,96,137,64,206,96C274.3,128,343,224,411,250.7C480,277,549,235,617,213.3C685.7,192,754,192,823,181.3C891.4,171,960,149,1029,117.3C1097.1,85,1166,43,1234,58.7C1302.9,75,1371,149,1406,186.7L1440,224L1440,320L1405.7,320C1371.4,320,1303,320,1234,320C1165.7,320,1097,320,1029,320C960,320,891,320,823,320C754.3,320,686,320,617,320C548.6,320,480,320,411,320C342.9,320,274,320,206,320C137.1,320,69,320,34,320L0,320Z">
                            </path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="aiz-titlebar text-left mt-2 mb-3">
        <div class="align-items-center">
            <div class="text-md-right">
                <a href="{{ route('customers.add_product', $user->id) }}" class="btn btn-circle btn-info">
                    <span>{{ translate('Add New Order') }}</span>
                </a>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">{{ translate('Cart Orders') }}</h5>
        </div>
        <div class="card-body">
            <table class="table aiz-table mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ translate('Order Date') }}</th>
                        <th>{{ translate('Community') }}</th>
                        <th>{{ translate('Product') }}</th>
                        <th>{{ translate('Quantity') }}</th>
                        <th>{{ translate('Price') }}</th>
                        <th class="text-center">{{ translate('Options') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($cart_orders as $key => $order)
                        @php
                            $seller = \App\Models\User::where('id', $order->owner_id)->first();
                            $sellerName = isset($seller) ? $seller->name : '-';
                            $totalPrice = floatval($order->price) + floatval($order->tax) + floatval($order->shipping_cost);
                        @endphp
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>
                                {{ date('d-m-Y', strtotime($order->created_at)) }}
                            </td>
                            <td>{{ $sellerName }}</td>
                            <td>{{ isset($order->product) ? $order->product->name : '--' }}</td>
                            <td>{{ $order->quantity }}</td>
                            <td>{{ single_price($totalPrice) }}</td>
                            <td class="text-center">
                                <a class="btn btn-soft-primary btn-icon btn-circle btn-sm"
                                    href="{{ route('customers.edit_product', ['type' => 'cart_order', 'user_id' => $user->id, 'ord_id' => $order->id]) }}"
                                    title="{{ translate('Edit') }}">
                                    <i class="las la-edit"></i>
                                </a>
                                <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete"
                                    data-href="{{ route('customers.delete_cart_item', ['user_id' => $user->id, 'ord_id' => $order->id]) }}"
                                    title="{{ translate('Delete') }}">
                                    <i class="las la-trash"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">{{ translate('Order History') }}</h5>
        </div>
        <div class="card-body">
            <table class="table aiz-table mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ translate('Order No') }}</th>
                        <th>{{ translate('Community') }}</th>
                        <th>{{ translate('Product') }}</th>
                        <th>{{ translate('Quantity') }}</th>
                        <th>{{ translate('Price') }}</th>
                        <th>{{ translate('Delivery Status') }}</th>
                        <th>{{ translate('Payment Status') }}</th>
                        <th class="text-center">{{ translate('Options') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order_details as $key => $detail)
                        @php
                            $seller = \App\Models\User::where('id', $detail->seller_id)->first();
                            $sellerName = isset($seller) ? $seller->name : '-';
                            $totalPrice = floatval($detail->price) + floatval($detail->tax) + floatval($detail->shipping_cost);
                            if (isset($detail->product_stock->product) && !empty($detail->product_stock->product)) {
                                $productName = $detail->product_stock->product->name;
                            } elseif (isset($detail->archive_product_stock) && isset($detail->archive_product_stock->product) && !empty($detail->archive_product_stock->product)) {
                                $productName = $detail->archive_product_stock->product->name;
                            } else {
                                $productName = '--';
                            }
                        @endphp
                        <tr>
                            <td>{{ $key + 1 + ($order_details->currentPage() - 1) * $order_details->perPage() }} </td>
                            <td>
                                {{ $detail->order->code }}
                                <br />
                                {{ date('d-m-Y', strtotime($detail->created_at)) }}
                            </td>
                            <td>{{ $sellerName }}</td>
                            <td>{{ $productName }}</td>
                            <td>{{ $detail->quantity }}</td>
                            <td>{{ single_price($totalPrice) }}</td>
                            <td>{{ ucwords($detail->delivery_status) }}</td>
                            <td>
                                @if ($detail->payment_status == 'paid')
                                    <span class="badge badge-inline badge-success">{{translate('Paid')}}</span>
                                @else
                                    <span class="badge badge-inline badge-danger">{{translate('Unpaid')}}</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if ($detail->payment_status == 'unpaid')
                                    <a class="btn btn-soft-primary btn-icon btn-circle btn-sm"
                                        href="{{ route('customers.edit_product', ['type' => 'order', 'user_id' => $user->id, 'ord_id' => $detail->order_id]) }}"
                                        title="{{ translate('Edit') }}">
                                        <i class="las la-edit"></i>
                                    </a>
                                    <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete"
                                        data-href="{{ route('customers.delete_order_item', ['user_id' => $user->id, 'order_detail_id' => $detail->id]) }}"
                                        title="{{ translate('Delete') }}">
                                        <i class="las la-trash"></i>
                                    </a>
                                @else
                                    <a class="btn btn-soft-info btn-icon btn-circle btn-sm"
                                        href="{{ route('customers.edit_product', ['type' => 'order', 'user_id' => $user->id, 'ord_id' => $detail->order_id]) }}"
                                        title="{{ translate('Edit') }}">
                                        <i class="las la-edit"></i>
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="aiz-pagination">
                {{ $order_details->appends(request()->input())->links() }}
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">{{ translate('Wallet Tansaction') }}</h5>
        </div>
        <div class="card-body">
            <table class="table aiz-table mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ translate('Date') }}</th>
                        <th>{{ translate('Amount') }}</th>
                        <th>{{ translate('Payment Method') }}</th>
                        <th>Transaction Type</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($wallet_history as $key => $wallet)
                        @php
                            $payment_details = json_decode($wallet->payment_details);
                        @endphp
                        <tr>
                            <td>{{ $key + 1 + ($wallet_history->currentPage() - 1) * $wallet_history->perPage() }}</td>
                            <td>{{ date('d-m-Y', strtotime($wallet->created_at)) }}</td>
                            <td>{{ single_price(abs($wallet->amount)) }}</td>
                            <td>
                                {{ ucfirst(str_replace('_', ' ', $wallet->payment_method)) }}
                                @if ($wallet->payment_method == 'order')
                                    <p>OrderNo: {{ $payment_details->code }}</p>
                                @endif
                            </td>
                            <td>{{ $wallet->amount > 0 ? 'Credit' : 'Debit' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="aiz-pagination mt-4">
                {{ $wallet_history->links() }}
            </div>
        </div>
    </div>
@endsection

@section('modal')
    @include('modals.delete_modal')
@endsection

@section('script')
    <script type="text/javascript">
        function copyUrl(e) {
            var url = $(e).data('url');
            var $temp = $("<input>");
            $("body").append($temp);
            $temp.val(url).select();
            try {
                document.execCommand("copy");
                AIZ.plugins.notify('success', '{{ translate('Link copied to clipboard') }}');
            } catch (err) {
                AIZ.plugins.notify('danger', '{{ translate('Oops, unable to copy') }}');
            }
            $temp.remove();
        }
    </script>
@endsection
