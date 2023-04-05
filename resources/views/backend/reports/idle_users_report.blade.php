@extends('backend.layouts.app')

@section('content')

    <div class="aiz-titlebar text-left mt-2 mb-3">
        <div class=" align-items-center">
            <h1 class="h3">{{translate('Idle Customers Report')}}</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 mx-auto">
            <div class="card">
                <form action="{{ route('idle-users.report') }}" method="GET">
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
                            <select class="form-control aiz-selectpicker" name="order_by_users" id="order_by_users">
                                <option value="" @if ($order_by_users == '') selected @endif>
                                    {{ translate('None') }}</option>
                                <option value="desc" @if ($order_by_users == 'desc') selected @endif>
                                    {{ translate('High - Low') }}</option>
                                <option value="asc" @if ($order_by_users == 'asc') selected @endif>
                                    {{ translate('Low - High') }}</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-md btn-primary" type="submit">
                                {{ translate('Filter') }}
                            </button>
                            <button type="button" class="btn btn-primary" onclick="exportExcelIdleCustomers()">
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
                            <th>{{ translate('Phone') }}</th>
                            <th>{{ translate('Email')}}</th>
                            <th>{{ translate('Last Order')}}</th>
                            <th>{{ translate('Address')}}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($users as $key => $user)
                            <tr>
                                <td>{{ ($key+1) + ($users->currentPage() - 1)*$users->perPage() }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->phone }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @if($user->last_order)
                                        {{ $user->last_order->code }} </br>
                                        {{ date('d-m-Y', strtotime($user->last_order->created_at)) }}
                                    @endif
                                </td>
                                <td>{{ $user->address }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <div class="aiz-pagination mt-4">
                        {{ $users->appends(request()->input())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script type="text/javascript">
        function exportExcelIdleCustomers() {
            let filter_date = $('#filter_date').val();
            let search = $('#search').val();
            let order_by_users = $('#order_by_users').val();
            let url = "{{ route('idle_users.excel', ':filter_date:search:order_by_users') }}";
            url = url.replace(':filter_date', '&filter_date=' + filter_date);
            url = url.replace(':search', '&search=' + search);
            url = url.replace(':order_by_users', '&order_by_users=' + order_by_users);

            window.location.href = url;
        }
    </script>
@endsection
