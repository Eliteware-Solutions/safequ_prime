@extends('backend.layouts.app')

@section('content')

    <div class="aiz-titlebar text-left mt-2 mb-3">
        <div class=" align-items-center">
            <h1 class="h3">{{translate('Regular Customers Report')}}</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 mx-auto">
            <div class="card">
                <form action="{{ route('regular-users.report') }}" method="GET">
                    <div class="card-header row gutters-5">
                        <div class="col-md-3">
                            <div class="form-group mb-0">
                                <input type="text" class="aiz-date-range form-control"
                                       value="{{$from_date}} to {{$to_date}}"
                                       name="filter_date"
                                       id="filter_date" placeholder="{{ translate('Filter by date') }}"
                                       data-format="DD-MM-Y" data-separator=" to " data-advanced-range="true"
                                       autocomplete="off">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-0">
                                <input type="text" class="form-control form-control-sm" id="search" name="search"
                                       @isset($search) value="{{ $search }}"
                                       @endisset placeholder="{{ translate('Type & Enter') }}">
                            </div>
                        </div>
                        <div class="col-lg-2 mb-2 mb-md-0">
                            <select class="form-control aiz-selectpicker" name="order_by_count" id="order_by_count">
                                <option value="desc" @if ($order_by_count == 'desc') selected @endif>
                                    {{ translate('High - Low') }}</option>
                                <option value="asc" @if ($order_by_count == 'asc') selected @endif>
                                    {{ translate('Low - High') }}</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-md btn-primary" type="submit">
                                {{ translate('Filter') }}
                            </button>
                            <button type="button" class="btn btn-primary" onclick="exportExcelRegularCustomers()">
                                {{ translate('Export') }}
                            </button>
                        </div>
                    </div>
                </form>
                <div class="card-body">
                    <table class="table aiz-table mb-0">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ translate('Name')}}</th>
                            <th>{{ translate('Phone')}}</th>
                            <th>{{ translate('Email')}}</th>
                            <th>{{ translate('Total Orders') }}</th>
                            <th>{{ translate('Address')}}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($orders as $key => $order)
                            <tr>
                                <td>{{ $key+1 }}</td>
                                <td>{{ $order->user_name }}</td>
                                <td>{{ $order->phone }}</td>
                                <td>{{ $order->email }}</td>
                                <td>{{ $order->total_orders }}</td>
                                <td>{{ $order->address }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <div class="aiz-pagination mt-4">
                        {{ $orders->appends(request()->input())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script type="text/javascript">
        function exportExcelRegularCustomers() {
            let filter_date = $('#filter_date').val();
            let search = $('#search').val();
            let order_by_count = $('#order_by_count').val();
            let url = "{{ route('regular-users.excel', ':filter_date:search:order_by_count') }}";
            url = url.replace(':filter_date', '&filter_date=' + filter_date);
            url = url.replace(':search', '&search=' + search);
            url = url.replace(':order_by_count', '&order_by_count=' + order_by_count);

            window.location.href = url;
        }
    </script>
@endsection
