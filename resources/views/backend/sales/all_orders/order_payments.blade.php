@extends('backend.layouts.app')

@section('content')

    <div class="card">
        <form class="" action="" id="sort_orders" method="GET">
            <div class="card-header row gutters-5">
                <div class="row gutters-5">
                    <h5 class="mb-md-0 h6">{{ translate('Order Payments') }}</h5>
                </div>
                <div class="row gutters-5 mt-2">
<!--                    <div class="dropdown mb-2 mb-md-0">
                        <button class="btn border dropdown-toggle" type="button" data-toggle="dropdown">
                            {{ translate('Bulk Action') }}
                        </button>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="#" onclick="bulk_delete()">
                                {{ translate('Delete selection') }}</a>
                        </div>
                    </div>-->

                    <!-- Change Status Modal -->
<!--                    <div class="col-lg-5 mb-2 mb-md-0">
                        <div class="form-group mb-0">
                            <input type="text" class="aiz-date-range form-control" value=""
                                   name="date" id="filter_date" placeholder="{{ translate('Filter by date') }}"
                                   data-format="DD-MM-Y" data-separator=" to " data-advanced-range="true" autocomplete="off">
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="form-group mb-0">
                            <button type="submit" class="btn btn-primary">{{ translate('Filter') }}</button>
                            <button type="button" class="btn btn-primary"
                                    onclick="exportExcel()">{{ translate('Export') }}</button>
                        </div>
                    </div>-->
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
                        <th>{{ translate('Date') }}</th>
                        <th>{{ translate('Payment Id') }}</th>
                        <th data-breakpoints="md"> {{ translate('Amount') }}</th>
                        <th data-breakpoints="md">{{ translate('Status') }}</th>
                        <th data-breakpoints="md">{{ translate('Method') }}</th>
                        <th data-breakpoints="md">{{ translate('Description') }}</th>
                        <th data-breakpoints="md">{{ translate('Customer') }}</th>
<!--                        <th data-breakpoints="md">{{ translate('Notes') }}</th>-->
                        <th class="text-right" width="20%">{{ translate('options') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($payments as $key => $payment)
                        <tr>
                            {{-- <td>
                                {{ $key + 1 + ($orders->currentPage() - 1) * $orders->perPage() }}
                            </td> --}}
                            <td>
                                <div class="form-group">
                                    <div class="aiz-checkbox-inline">
                                        <label class="aiz-checkbox">
                                            <input type="checkbox" class="check-one" name="id[]"
                                                   value="{{ $payment['id'] }}">
                                            <span class="aiz-square-check"></span>
                                        </label>
                                    </div>
                                </div>
                            </td>
                            <td>{{ date('d-m-Y', strtotime($payment['created_at'])) }}</td>
                            <td>{{ $payment['payment_id'] }}</td>
                            <td>{!! single_price($payment['amount']) !!}</td>
                            <td>{{ $payment['status'] }}</td>
                            <td>{{ $payment['method'] }}</td>
                            <td>{{ $payment['description'] }}</td>
                            <td>
                                {{ $payment['email'] }} </br>
                                {{ $payment['contact'] }}
                            </td>
<!--                            <td>{{ $payment['notes'] }}</td>-->
                            <td class="text-right">
<!--                                <a href="#"
                                   class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete"
                                   data-href="{{ route('orders.destroy', $payment['id']) }}"
                                   title="{{ translate('Delete') }}">
                                    <i class="las la-trash"></i>
                                </a>-->
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

                <div class="aiz-pagination">
                    {{ $all_payments->appends(request()->input())->links() }}
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
            let delivery_status = $('#delivery_status').val();
            let filter_date = $('#filter_date').val();
            let search = $('#search').val();
            let payment_status = $('#payment_status').val();
            let url = "{{ route('order_export.excel', ':delivery_status:payment_status:filter_date:search') }}";
            url = url.replace(':delivery_status', 'delivery_status=' + delivery_status);
            url = url.replace(':payment_status', '&payment_status=' + payment_status);
            url = url.replace(':filter_date', '&filter_date=' + filter_date);
            url = url.replace(':search', '&search=' + search);

            window.location.href = url;
        }
    </script>
@endsection
