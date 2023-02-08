@extends('backend.layouts.app')

@section('content')

    <div class="card">
        <div class="card-header">
            <h1 class="h2 fs-16 mb-0">{{ translate('Order Details') }}</h1>
        </div>
        <div class="card-body">
            <div class="row gutters-5">
                <div class="col text-center text-md-left">
                </div>
            @php
                $delivery_status = $order->delivery_status;
                $payment_status = $order->payment_status;
            @endphp

            <!--Assign Delivery Boy-->
                @if (addon_is_activated('delivery_boy'))
                    <div class="col-md-3 ml-auto">
                        <label for="assign_deliver_boy">{{translate('Assign Deliver Boy')}}</label>
                        @if($delivery_status == 'pending' || $delivery_status == 'confirmed' || $delivery_status == 'picked_up')
                            <select class="form-control aiz-selectpicker" data-live-search="true"
                                    data-minimum-results-for-search="Infinity" id="assign_deliver_boy">
                                <option value="">{{translate('Select Delivery Boy')}}</option>
                                @foreach($delivery_boys as $delivery_boy)
                                    <option value="{{ $delivery_boy->id }}"
                                            @if($order->assign_delivery_boy == $delivery_boy->id) selected @endif>
                                        {{ $delivery_boy->name }}
                                    </option>
                                @endforeach
                            </select>
                        @else
                            <input type="text" class="form-control" value="{{ optional($order->delivery_boy)->name }}"
                                   disabled>
                        @endif
                    </div>
                @endif

                <div class="col-md-3 ml-auto">
                    <label for="update_payment_status">{{translate('Payment Status')}}</label>
                    <select class="form-control aiz-selectpicker" data-minimum-results-for-search="Infinity"
                            id="update_payment_status">
                        <option value="unpaid"
                                @if ($payment_status == 'unpaid') selected @endif>{{translate('Unpaid')}}</option>
                        <option value="paid"
                                @if ($payment_status == 'paid') selected @endif>{{translate('Paid')}}</option>
                    </select>
                </div>
                <div class="col-md-3 ml-auto">
                    <label for="update_delivery_status">{{translate('Delivery Status')}}</label>
                    @if($delivery_status != 'delivered' && $delivery_status != 'cancelled' && $delivery_status != 'replaced')
                        <select class="form-control aiz-selectpicker" data-minimum-results-for-search="Infinity"
                                id="update_delivery_status">
                            <option value="pending"
                                    @if ($delivery_status == 'pending') selected @endif>{{translate('Pending')}}</option>
                            <option value="confirmed"
                                    @if ($delivery_status == 'confirmed') selected @endif>{{translate('Confirmed')}}</option>
                            <option value="picked_up"
                                    @if ($delivery_status == 'picked_up') selected @endif>{{translate('Picked Up')}}</option>
                            <option value="on_the_way"
                                    @if ($delivery_status == 'on_the_way') selected @endif>{{translate('On The Way')}}</option>
                            <option value="delivered"
                                    @if ($delivery_status == 'delivered') selected @endif>{{translate('Delivered')}}</option>
                            <option value="cancelled"
                                    @if ($delivery_status == 'cancelled') selected @endif>{{translate('Cancel')}}</option>
                            @if ($delivery_status == 'delivered')
                                <option value="replaced"
                                        @if ($delivery_status == 'replaced') selected @endif>{{translate('Replaced')}}</option>
                            @endif
                        </select>
                    @else
                        <input type="text" class="form-control" value="{{ ucwords($delivery_status) }}" disabled>
                    @endif
                </div>
                <div class="col-md-3 ml-auto">
                    <label for="update_tracking_code">{{translate('Tracking Code (optional)')}}</label>
                    <input type="text" class="form-control" id="update_tracking_code"
                           value="{{ $order->tracking_code }}">
                </div>
            </div>
            <div class="mb-3">
                @php
                    $removedXML = '<?xml version="1.0" encoding="UTF-8"?>';
                @endphp
                {!! str_replace($removedXML,"", QrCode::size(100)->generate($order->code)) !!}
            </div>
            <div class="row gutters-5">
                <div class="col text-center text-md-left">
                    <address>
                        <strong class="text-main">{{ json_decode($order->shipping_address)->name }}</strong><br>
                        {{ json_decode($order->shipping_address)->email }}<br>
                        {{ json_decode($order->shipping_address)->phone }}<br>
                        {{ json_decode($order->shipping_address)->address }}
                        , {{ json_decode($order->shipping_address)->city }}
                        , {{ json_decode($order->shipping_address)->postal_code }}<br>
                        {{ json_decode($order->shipping_address)->country }}
                    </address>
                    @if ($order->manual_payment && is_array(json_decode($order->manual_payment_data, true)))
                        <br>
                        <strong class="text-main">{{ translate('Payment Information') }}</strong><br>
                        {{ translate('Name') }}: {{ json_decode($order->manual_payment_data)->name }}
                        , {{ translate('Amount') }}
                        : {{ single_price(json_decode($order->manual_payment_data)->amount) }}
                        , {{ translate('TRX ID') }}: {{ json_decode($order->manual_payment_data)->trx_id }}
                        <br>
                        <a href="{{ uploaded_asset(json_decode($order->manual_payment_data)->photo) }}" target="_blank"><img
                                src="{{ uploaded_asset(json_decode($order->manual_payment_data)->photo) }}" alt=""
                                height="100"></a>
                    @endif
                </div>
                <div class="col-md-4 ml-auto">
                    <table>
                        <tbody>
                        <tr>
                            <td class="text-main text-bold">{{translate('Order #')}}</td>
                            <td class="text-right text-info text-bold">    {{ $order->code }}</td>
                        </tr>
                        <tr>
                            <td class="text-main text-bold">{{translate('Order Status')}}</td>
                            <td class="text-right">
                                @if($delivery_status == 'delivered')
                                    <span
                                        class="badge badge-inline badge-success">{{ translate(ucfirst(str_replace('_', ' ', $delivery_status))) }}</span>
                                @else
                                    <span
                                        class="badge badge-inline badge-info">{{ translate(ucfirst(str_replace('_', ' ', $delivery_status))) }}</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-main text-bold">{{translate('Order Date')}}    </td>
                            <td class="text-right">{{ date('d-m-Y h:i A', $order->date) }}</td>
                        </tr>
                        <tr>
                            <td class="text-main text-bold">
                                {{translate('Total amount')}}
                            </td>
                            <td class="text-right">
                                {{ single_price($order->grand_total) }}
                            </td>
                        </tr>
                        <tr>
                            <td class="text-main text-bold">{{translate('Payment method')}}</td>
                            <td class="text-right">{{ translate(ucfirst(str_replace('_', ' ', $order->payment_type))) }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <hr class="new-section-sm bord-no">
            <div class="row">
                @if($delivery_status == 'delivered')
                    <div class="col-auto">
                        <div class="form-group">
                            <button type="button" class="btn btn-primary" onclick="replaceOrder()">{{ translate('Replace Order') }}</button>
                        </div>
                    </div>
                @endif

                <div class="col-lg-12 table-responsive">
                    <table class="table table-bordered aiz-table invoice-summary" id="order-details-table">
                        <thead>
                        <tr class="bg-trans-dark">
                            @if($delivery_status == 'delivered')
                                <th width="5%">
                                    <div class="form-group">
                                        <div class="aiz-checkbox-inline">
                                            <label class="aiz-checkbox">
                                                <input type="checkbox" class="check-all">
                                                <span class="aiz-square-check"></span>
                                            </label>
                                        </div>
                                    </div>
                                </th>
                            @endif
                            <th class="min-col">#</th>
                            <th width="10%">{{translate('Photo')}}</th>
                            <th class="text-uppercase">{{translate('Description')}}</th>
                            <th class="min-col text-center text-uppercase">{{translate('Qty')}}</th>
                            <th class="min-col text-right text-uppercase">{{translate('Price')}}</th>
                            <th class="min-col text-right text-uppercase">{{translate('Total')}}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($order->orderDetails as $key => $orderDetail)

                            <tr>
                                @if($delivery_status == 'delivered')
                                    <td>
                                        <div class="form-group">
                                            <div class="aiz-checkbox-inline">
                                                <label class="aiz-checkbox">
                                                    <input type="checkbox" class="check-one" name="id[]"
                                                           value="{{$orderDetail->id}}" @if($orderDetail->delivery_status == 'replaced') disabled @endif>
                                                    <span class="aiz-square-check"></span>
                                                </label>
                                            </div>
                                        </div>
                                    </td>
                                @endif
                                <td>{{ $key+1 }}</td>
                                <td>
                                    @if ($orderDetail->product != null && $orderDetail->product->auction_product == 0)
                                        <p><img height="50"
                                                src="{{ uploaded_asset($orderDetail->product->thumbnail_img) }}"></p>
                                    @elseif ($orderDetail->product != null && $orderDetail->product->auction_product == 1)
                                        <p><img height="50"
                                                src="{{ uploaded_asset($orderDetail->product->thumbnail_img) }}"></p>
                                    @else
                                        <strong>{{ translate('N/A') }}</strong>
                                    @endif
                                </td>
                                <td>
                                    @if ($orderDetail->product != null && $orderDetail->product->auction_product == 0)
                                        <strong><p
                                                class="text-muted">{{ $orderDetail->product->getTranslation('name') }}</p>
                                        </strong>
                                        <small><b>Variation: </b>{{ $orderDetail->product->variation }}</small><br/>
                                        <small><b>Location: </b>{{ $orderDetail->product->manufacturer_location }}
                                        </small><br/>
                                        <small><b>Status: </b>{{ translate($orderDetail->delivery_status) }}
                                        </small>
                                    @elseif ($orderDetail->product != null && $orderDetail->product->auction_product == 1)
                                        <strong><p
                                                class="text-muted">{{ $orderDetail->product->getTranslation('name') }}</p>
                                        </strong>
                                    @else
                                        <strong>{{ translate('Product Unavailable') }}</strong>
                                    @endif
                                </td>
                                @php
                                    if(floatval($orderDetail->product->min_qty) < 1){
                                        $qty_unit =  (1000 * floatval($orderDetail->product->min_qty)) . ' ' . $orderDetail->product->secondary_unit;
                                    } else {
                                        $qty_unit = ($orderDetail->quantity * floatval($orderDetail->product->min_qty)) . ' ' . $orderDetail->product->unit;
                                    }
                                @endphp
                                <td class="text-center">{{ $orderDetail->quantity }}</td>
                                <td class="text-right">{{ single_price($orderDetail->price/$orderDetail->quantity) . ' / ' . $qty_unit }}</td>
                                <td class="text-right">{{ single_price($orderDetail->price) }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="clearfix float-right">
                <table class="table">
                    <tbody>
                    <tr>
                        <td>
                            <strong class="text-muted">{{translate('Sub Total')}} :</strong>
                        </td>
                        <td class="text-right">
                            {{ single_price($order->orderDetails->sum('price')) }}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <strong class="text-muted">{{translate('Tax')}} :</strong>
                        </td>
                        <td class="text-right">
                            {{ single_price($order->orderDetails->sum('tax')) }}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <strong class="text-muted">{{translate('Shipping')}} :</strong>
                        </td>
                        <td class="text-right">
                            {{ single_price($order->orderDetails->sum('shipping_cost')) }}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <strong class="text-muted">{{translate('Coupon')}} :</strong>
                        </td>
                        <td class="text-right">
                            {{ single_price($order->coupon_discount) }}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <strong class="text-muted">{{translate('TOTAL')}} :</strong>
                        </td>
                        <td class="text-muted h5">
                            {{ single_price($order->grand_total) }}
                        </td>
                    </tr>
                    </tbody>
                </table>
                <div class="text-right no-print">
                    <a href="{{ route('invoice.download', $order->id) }}" type="button"
                       class="btn btn-icon btn-light"><i class="las la-print"></i></a>
                </div>
            </div>

        </div>
    </div>
@endsection

@section('modal')
    @include('modals.delete_modal')

    <!-- Payment Transaction Details Modal -->
    <div class="modal fade" id="payment_transaction_modal" tabindex="-1" role="dialog"
         aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ translate('Recharge Wallet') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
                </div>
                <div id="offline_wallet_recharge_modal_body">
                    <form class="" action="{{route('orders.update_payment_status')}}" method="post"
                          enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" id="order_id" name="order_id">
                        <input type="hidden" id="added_by_admin" name="added_by_admin">
                        <input type="hidden" id="status" name="status">
                        <input type="hidden" id="form_type" name="form_type" value="modal">
                        <div class="modal-body gry-bg px-3 pt-3 mx-auto">
                            <div id="manual_payment_data">

                                <div class="card mb-3 p-3">
                                    <div class="row mt-3">
                                        <div class="col-md-3">
                                            <label>Payment Type <span class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control mb-3" name="payment_type" id="payment_type"
                                                   placeholder="{{ translate('G-Pay etc..') }}" required>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-3">
                                            <label>Transaction Id <span class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control" name="transaction_id" id="transaction_id"
                                                   placeholder="{{ translate('GP123456 etc..') }}" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group text-right">
                                    <button type="submit" class="btn btn-sm btn-primary transition-3d-hover mr-1">
                                        Confirm
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script type="text/javascript">
        $(document).on("change", ".check-all", function () {
            if (this.checked) {
                // Iterate each checkbox
                if (!$('.check-one:checkbox').attr('disabled')) {
                    $('.check-one:checkbox').each(function () {
                        this.checked = true;
                    });
                }
            } else {
                $('.check-one:checkbox').each(function () {
                    this.checked = false;
                });
            }
        });

        $('#assign_deliver_boy').on('change', function () {
            var order_id = {{ $order->id }};
            var delivery_boy = $('#assign_deliver_boy').val();
            $.post('{{ route('orders.delivery-boy-assign') }}', {
                _token: '{{ @csrf_token() }}',
                order_id: order_id,
                delivery_boy: delivery_boy
            }, function (data) {
                AIZ.plugins.notify('success', '{{ translate('Delivery boy has been assigned') }}');
            });
        });

        $('#update_delivery_status').on('change', function () {
            var order_id = {{ $order->id }};
            var status = $('#update_delivery_status').val();
            $.post('{{ route('orders.update_delivery_status') }}', {
                _token: '{{ @csrf_token() }}',
                order_id: order_id,
                status: status
            }, function (data) {
                AIZ.plugins.notify('success', '{{ translate('Delivery status has been updated') }}');

                setTimeout(function () {
                    window.location.reload();
                }, 1000);
            });
        });

        $('#update_payment_status').on('change', function () {
            let order_id = {{ $order->id }};
            let added_by_admin = {{ intval($order->added_by_admin) }};
            let status = $('#update_payment_status').val();
            if (added_by_admin == 1 && status == 'paid') {
                $('#payment_transaction_modal #order_id').val(order_id);
                $('#payment_transaction_modal #status').val(status);
                $('#payment_transaction_modal #added_by_admin').val(added_by_admin);
                $('#payment_transaction_modal').modal('show', {backdrop: 'static'});
            } else {
                $.post('{{ route('orders.update_payment_status') }}', {
                    _token: '{{ @csrf_token() }}',
                    order_id: order_id,
                    status: status,
                    form_type: 'ajax',
                    status: added_by_admin
                }, function (data) {
                    AIZ.plugins.notify('success', '{{ translate('Payment status has been updated') }}');
                });
            }
        });

        $('#update_tracking_code').on('change', function () {
            var order_id = {{ $order->id }};
            var tracking_code = $('#update_tracking_code').val();
            $.post('{{ route('orders.update_tracking_code') }}', {
                _token: '{{ @csrf_token() }}',
                order_id: order_id,
                tracking_code: tracking_code
            }, function (data) {
                AIZ.plugins.notify('success', '{{ translate('Order tracking code has been updated') }}');
            });
        });

        function replaceOrder() {
            var order_detail_ids = $("#order-details-table input:checkbox:checked").map(function(){
                if ($(this).val() != 'on') return $(this).val();
            }).get();

            if (order_detail_ids.length == 0) {
                AIZ.plugins.notify('danger', '{{ translate('Please select product.') }}');
                return false;
            } else {
                let order_id = {{ $order->id }};
                $.post('{{ route('orders.replace_delivered_order') }}', {
                    _token: '{{ @csrf_token() }}',
                    order_id: order_id,
                    status: 'replaced',
                    order_detail_ids: order_detail_ids
                }, function (data) {
                    AIZ.plugins.notify('success', '{{ translate('Order submitted for replacement') }}');

                    setTimeout(function () {
                        window.location.reload();
                    }, 1000);
                });
            }
        }
    </script>
@endsection
