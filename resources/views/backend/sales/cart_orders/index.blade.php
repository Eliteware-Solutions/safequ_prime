@extends('backend.layouts.app')

@section('content')

    <div class="card">
        <form class="" action="" id="sort_orders" method="GET">
            <div class="card-header row gutters-5">
                <div class="row gutters-5">
                    <h5 class="mb-md-0 h6">{{ translate('Cart Orders') }}</h5>
                </div>
                <div class="row gutters-5 mt-2">
                    <div class="col-lg-2 mb-2 mb-md-0"></div>

                    <!-- Change Status Modal -->
                    <div class="col-lg-6 mb-2 mb-md-0">
                        <div class="form-group mb-0">
                            <input type="text" class="aiz-date-range form-control" value="{{ $date }}" name="date"
                                   id="filter_date" placeholder="{{ translate('Filter by date') }}"
                                   data-format="DD-MM-Y" data-separator=" to " data-advanced-range="true"
                                   autocomplete="off">
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="form-group mb-0">
                            <button type="submit" class="btn btn-primary">{{ translate('Filter') }}</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <table class="table aiz-table mb-0">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>{{translate('Order Date')}}</th>
                        <th>{{translate('Customer')}}</th>
                        <th>{{translate('Community')}}</th>
                        <th>{{translate('Product')}}</th>
                        <th>{{translate('Quantity')}}</th>
                        <th>{{translate('Price')}}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($carts as $key => $order)
                        @php
                            $seller = \App\Models\User::where('id', $order->owner_id)->first();
                            $sellerName = (isset($seller) ? $seller->name : '-');
                            $totalPrice = floatval($order->price) + floatval($order->tax) + floatval($order->shipping_cost);
                        @endphp
                        <tr>
                            <td>{{ ($key+1) }}</td>
                            <td>
                                {{ date('d-m-Y', strtotime($order->created_at)) }}
                            </td>
                            <td>{{ $order->user->name }}</td>
                            <td>{{ $sellerName }}</td>
                            <td>{{ (isset($order->product) ? $order->product->name : '--') }}</td>
                            <td>{{ $order->quantity }}</td>
                            <td>{{ single_price($totalPrice) }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

                <div class="aiz-pagination">
                    {{ $carts->appends(request()->input())->links() }}
                </div>

            </div>
        </form>
    </div>

@endsection

@section('modal')
    @include('modals.delete_modal')
@endsection
