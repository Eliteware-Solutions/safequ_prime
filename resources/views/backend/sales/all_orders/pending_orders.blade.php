@extends('backend.layouts.app')

@section('content')

    <div class="card">
        <form class="" action="" id="sort_orders" method="GET">
            <div class="card-header row gutters-5">
                <div class="row gutters-5">
                    <h5 class="mb-md-0 h6">{{ translate('Pending Orders') }}</h5>
                </div>
                <div class="row gutters-5 mt-2">
                    <div class="dropdown mb-2 mb-md-0">
                        <button class="btn border dropdown-toggle" type="button" data-toggle="dropdown">
                            {{ translate('Bulk Action') }}
                        </button>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="#" onclick="bulk_delete()">
                                {{ translate('Delete selection') }}</a>

                            <a class="dropdown-item" href="#" onclick="change_status()">
                                {{ translate('Order Delivered') }}</a>

                            {{-- <a class="dropdown-item" href="#" data-toggle="modal" data-target="#exampleModal">
                                <i class="las la-sync-alt"></i>{{ translate('Change Order Status') }}
                            </a> --}}
                        </div>
                    </div>

                    <!-- Change Status Modal -->
                    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog"
                         aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">
                                        {{ translate('Choose an order status') }}
                                    </h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <select class="form-control aiz-selectpicker mb-5" onchange="change_status()"
                                            data-minimum-results-for-search="Infinity" id="update_delivery_status">
                                        <option value="pending">{{ translate('Pending') }}</option>
                                        <option value="delivered">{{ translate('Delivered') }}</option>
                                        <option value="cancelled">{{ translate('Cancel') }}</option>
                                    </select>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <button type="button" class="btn btn-primary">Save changes</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 mb-2 mb-md-0">
                        <select class="form-control aiz-selectpicker" name="payment_status" id="payment_status">
                            <option value="">{{ translate('Payment Status') }}</option>
                            <option value="payment_initiated" @if ($payment_status == 'payment_initiated') selected @endif>
                                {{ translate('Payment Initiated') }}</option>
                            <option value="unpaid" @if ($payment_status == 'unpaid') selected @endif>
                                {{ translate('Unpaid') }}</option>
                            <option value="failed" @if ($payment_status == 'failed') selected @endif>
                                {{ translate('Failed') }}</option>
                        </select>
                    </div>
<!--                    <div class="col-lg-2 mb-2 mb-md-0">
                        <select class="form-control aiz-selectpicker" name="delivery_status" id="delivery_status">
                            <option value="">{{ translate('Filter by Delivery Status') }}</option>
                            <option value="pending" @if ($delivery_status == 'pending') selected @endif>
                                {{ translate('Pending') }}</option>
                            <option value="confirmed" @if ($delivery_status == 'confirmed') selected @endif>
                                {{ translate('Confirmed') }}</option>
                            <option value="picked_up" @if ($delivery_status == 'picked_up') selected @endif>
                                {{ translate('Picked Up') }}</option>
                            <option value="on_the_way" @if ($delivery_status == 'on_the_way') selected @endif>
                                {{ translate('On The Way') }}</option>
                            <option value="delivered" @if ($delivery_status == 'delivered') selected @endif>
                                {{ translate('Delivered') }}</option>
                            <option value="cancelled" @if ($delivery_status == 'cancelled') selected @endif>
                                {{ translate('Cancel') }}</option>
                        </select>
                    </div>-->
                    <div class="col-lg-3 mb-2 mb-md-0">
                        <div class="form-group mb-0">
                            <input type="text" class="aiz-date-range form-control" value="{{ $date }}"
                                   name="date" id="filter_date" placeholder="{{ translate('Filter by date') }}"
                                   data-format="DD-MM-Y" data-separator=" to " data-advanced-range="true" autocomplete="off">
                        </div>
                    </div>
                    <div class="col-lg-2 mb-2 mb-md-0">
                        <div class="form-group mb-0">
                            <input type="text" class="form-control" id="search" name="search"
                                   @isset($sort_search) value="{{ $sort_search }}"
                                   @endisset
                                   placeholder="{{ translate('Type Order code & hit Enter') }}">
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="form-group mb-0">
                            <button type="submit" class="btn btn-primary">{{ translate('Filter') }}</button>
                            <button type="button" class="btn btn-primary"
                                    onclick="exportExcel()">{{ translate('Export') }}</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <table class="table aiz-table mb-0">
                    <thead>
                    <tr>
                        <!--<th>#</th>-->
                        <th>
                            <div class="form-group">
                                <div class="aiz-checkbox-inline">
                                    <label class="aiz-checkbox">
                                        <input type="checkbox" class="check-all">
                                        <span class="aiz-square-check"></span>
                                    </label>
                                </div>
                            </div>
                        </th>
                        <th>{{ translate('Order Date') }}</th>
                        <th>{{ translate('Order Code') }}</th>
                        <th data-breakpoints="md"># {{ translate('Products') }}</th>
                        <th data-breakpoints="md">{{ translate('Customer') }}</th>
                        <th data-breakpoints="md">{{ translate('Amount') }}</th>
                        <th data-breakpoints="md">{{ translate('Delivery Status') }}</th>
                        <th data-breakpoints="md">{{ translate('Payment Status') }}</th>
                        @if (addon_is_activated('refund_request'))
                            <th>{{ translate('Refund') }}</th>
                        @endif
                        <th class="text-right" width="20%">{{ translate('options') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($orders as $key => $order)
                        <tr>
                            {{-- <td>
                                {{ $key + 1 + ($orders->currentPage() - 1) * $orders->perPage() }}
                            </td> --}}
                            <td>
                                <div class="form-group">
                                    <div class="aiz-checkbox-inline">
                                        <label class="aiz-checkbox">
                                            <input type="checkbox" class="check-one" name="id[]"
                                                   value="{{ $order->id }}">
                                            <span class="aiz-square-check"></span>
                                        </label>
                                    </div>
                                </div>
                            </td>
                            <td>{{ date('d-m-Y', strtotime($order->created_at)) }}</td>
                            <td>{{ $order->code }}</td>
                            <td>{{ count($order->orderDetails) }}</td>
                            <td>
                                @if ($order->user != null)
                                    {{ $order->user->name }}
                                @else
                                    Guest ({{ $order->guest_id }})
                                @endif
                            </td>
                            <td>{!! single_price($order->grand_total + $order->service_charge) !!}</td>
                            <td>
                                @php
                                    $status = $order->delivery_status;
                                    if ($order->delivery_status == 'cancelled') {
                                        $status = '<span class="badge badge-inline badge-danger">' . translate('Cancel') . '</span>';
                                    }

                                @endphp
                                {!! $status !!}
                            </td>
                            <td>
                                @if ($order->payment_status == 'payment_initiated')
                                    <span
                                        class="badge badge-inline badge-info">{{ translate('Payment Initiated') }}</span>
                                @elseif($order->payment_status == 'paid')
                                    <span class="badge badge-inline badge-success">{{ translate('Paid') }}</span>
                                @elseif($order->payment_status == 'unpaid')
                                    <span class="badge badge-inline badge-danger">{{ translate('Unpaid') }}</span>
                                @else
                                    <span class="badge badge-inline badge-danger">{{ translate('Failed') }}</span>
                                @endif
                            </td>
                            @if (addon_is_activated('refund_request'))
                                <td>
                                    @if (count($order->refund_requests) > 0)
                                        {{ count($order->refund_requests) }} {{ translate('Refund') }}
                                    @else
                                        {{ translate('No Refund') }}
                                    @endif
                                </td>
                            @endif
                            <td class="text-right">
                                @if ($order->payment_status != 'paid' && $order->added_by_admin == 1)
                                    <a class="btn btn-soft-success btn-icon btn-circle btn-sm"
                                       href="javascript:void(0)" onclick="copyAdminOrderPaymentUrl(this)"
                                       data-url="{{ route('cart.adminOrderPayment', [ 'id' => base64_encode(Str::random(3).'#'.$order->user_id.'$'.Str::random(3)), 'order_id' => $order->id ] ) }}"
                                       title="{{ translate('Generate Payment Link') }}">
                                        <i class="las la-money-bill"></i>
                                    </a>
                                <!--                                        <a class="btn btn-soft-success btn-icon btn-circle btn-sm"
                                            href="javascript:void(0)" onclick="copyPaymentUrl(this)"
                                            data-url="{{ $order->razorpay_payment_link }}" data-id="{{ $order->id }}"
                                            title="{{ translate('Generate Payment Link') }}">
                                            <i class="las la-money-bill"></i>
                                        </a>-->
                                @endif
                                <a class="btn btn-soft-primary btn-icon btn-circle btn-sm"
                                   href="{{ route('all_orders.show', encrypt($order->id)) }}"
                                   title="{{ translate('View') }}">
                                    <i class="las la-eye"></i>
                                </a>
                                <a class="btn btn-soft-info btn-icon btn-circle btn-sm"
                                   href="{{ route('invoice.download', $order->id) }}"
                                   title="{{ translate('Download Invoice') }}">
                                    <i class="las la-download"></i>
                                </a>
                                <a href="#"
                                   class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete"
                                   data-href="{{ route('orders.destroy', $order->id) }}"
                                   title="{{ translate('Delete') }}">
                                    <i class="las la-trash"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

                <div class="aiz-pagination">
                    {{ $orders->appends(request()->input())->links() }}
                </div>

            </div>
        </form>
    </div>
@endsection

@section('modal')
    @include('modals.delete_modal')
@endsection

@section('script')
    <script type="text/javascript">
        $(document).on("change", ".check-all", function() {
            if (this.checked) {
                // Iterate each checkbox
                $('.check-one:checkbox').each(function() {
                    this.checked = true;
                });
            } else {
                $('.check-one:checkbox').each(function() {
                    this.checked = false;
                });
            }

        });

        function change_status() {
            var data = new FormData($('#sort_orders')[0]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ route('bulk-order-status') }}",
                type: 'POST',
                data: data,
                cache: false,
                contentType: false,
                processData: false,
                success: function(response) {
                    if (response == 1) {
                        location.reload();
                    }
                }
            });
        }

        function bulk_delete() {
            var data = new FormData($('#sort_orders')[0]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ route('bulk-order-delete') }}",
                type: 'POST',
                data: data,
                cache: false,
                contentType: false,
                processData: false,
                success: function(response) {
                    if (response == 1) {
                        location.reload();
                    }
                }
            });
        }

        function exportExcel() {
            // let delivery_status = $('#delivery_status').val();
            let filter_date = $('#filter_date').val();
            let search = $('#search').val();
            let payment_status = $('#payment_status').val();
            let url = "{{ route('pending_order_export.excel', ':payment_status:filter_date:search') }}";
            // url = url.replace(':delivery_status', 'delivery_status=' + delivery_status);
            url = url.replace(':payment_status', '&payment_status=' + payment_status);
            url = url.replace(':filter_date', '&filter_date=' + filter_date);
            url = url.replace(':search', '&search=' + search);

            window.location.href = url;
        }

        function copyAdminOrderPaymentUrl(e) {
            let url = $(e).data('url');
            let $temp = $("<input>");
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

        function copyPaymentUrl(e) {
            let url = $(e).data('url');
            let orderId = $(e).data('id');
            if (!url) {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ route('order-payment-link') }}",
                    type: 'POST',
                    data: {
                        id: orderId
                    },
                    success: function(response) {
                        if (response.status == 1) {
                            url = response.payment_link;
                            let $temp = $("<input>");
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
                    }
                });
            } else {
                let $temp = $("<input>");
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
        }
    </script>
@endsection
