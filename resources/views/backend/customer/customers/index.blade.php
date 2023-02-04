@extends('backend.layouts.app')

@section('content')

    <div class="aiz-titlebar text-left mt-2 mb-3">
        <div class="row align-items-center">
            <div class="col-auto">
                <h1 class="h3">{{translate('All Customers')}}</h1>
            </div>
            <div class="col text-right">
                <a href="{{ route('customers.add') }}" class="btn btn-circle btn-info">
                    <span>{{translate('Add New Customer')}}</span>
                </a>
            </div>
        </div>
    </div>


    <div class="card">
        <form class="" id="sort_customers" action="" method="GET">
            <div class="card-header row gutters-5">
                <div class="col">
                    <h5 class="mb-0 h6">{{translate('Customers')}}</h5>
                </div>

                <div class="dropdown mb-2 mb-md-0">
                    <button class="btn border dropdown-toggle" type="button" data-toggle="dropdown">
                        {{translate('Bulk Action')}}
                    </button>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a class="dropdown-item" href="#" onclick="bulk_delete()">{{translate('Delete selection')}}</a>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group mb-0">
                        <input type="text" class="form-control" id="search" name="search"
                               @isset($sort_search) value="{{ $sort_search }}"
                               @endisset placeholder="{{ translate('Type email or name & Enter') }}">
                    </div>
                </div>

                <div class="col-auto">
                    <div class="form-group mb-0">
                        <button type="button" class="btn btn-primary" onclick="exportCustomerExcel()">{{ translate('Export') }}</button>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <table class="table aiz-table mb-0">
                    <thead>
                    <tr>
                        <!--<th data-breakpoints="lg">#</th>-->
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
                        <th>{{translate('Name')}}</th>
                        <th>{{translate('Community')}}</th>
                        <th>{{translate('Phone')}}</th>
                        <th>{{translate('Wallet Balance')}}</th>
                        <th>{{translate('Referred By')}}</th>
                        <th>{{translate('Pending Bills')}}</th>
                        <th class="text-center">{{translate('Options')}}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($users as $key => $user)
                        @if ($user != null)
                            <tr>
                            <!--<td>{{ ($key+1) + ($users->currentPage() - 1)*$users->perPage() }}</td>-->
                                <td>
                                    <div class="form-group">
                                        <div class="aiz-checkbox-inline">
                                            <label class="aiz-checkbox">
                                                <input type="checkbox" class="check-one" name="id[]"
                                                       value="{{$user->id}}">
                                                <span class="aiz-square-check"></span>
                                            </label>
                                        </div>
                                    </div>
                                </td>
                                <td>@if($user->banned == 1) <i class="fa fa-ban text-danger"
                                                               aria-hidden="true"></i> @endif {{$user->name}}</td>
                                <td>
                                    @if($user->user_community != null)
                                        {{ $user->user_community->name }}
                                    @else
                                        {{ '--' }}
                                    @endif
                                </td>
                                <td>{{$user->phone}}</td>
                                <td>{{single_price($user->balance)}}</td>
                                <td>
                                    @if($user->referred_user != null)
                                        {{ $user->referred_user->name }}
                                    @else
                                        {{ '--' }}
                                    @endif
                                </td>
                                <td>{{ single_price($user->pending_bill) }}</td>
                                <td class="text-center">
                                    <a class="btn btn-soft-primary btn-icon btn-circle btn-sm"
                                       href="{{route('customers.edit', ['id'=>$user->id] )}}"
                                       title="{{ translate('Edit') }}">
                                        <i class="las la-edit"></i>
                                    </a>
                                    <a class="btn btn-soft-primary btn-icon btn-circle btn-sm"
                                       href="{{route('customers.detail', ['id'=> $user->id] )}}"
                                       title="{{ translate('Edit') }}">
                                        <i class="las la-eye"></i>
                                    </a>
                                    <a onclick="show_make_wallet_recharge_modal('{{encrypt($user->id)}}')"
                                       class="btn btn-soft-primary btn-icon btn-circle btn-sm"
                                       title="{{ translate('Wallet') }}">
                                        <i class="las la-wallet"></i>
                                    </a>
                                    {{-- @if($user->banned != 1)
                                        <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm"
                                           onclick="confirm_ban('{{route('customers.ban', encrypt($user->id))}}');"
                                           title="{{ translate('Ban this Customer') }}">
                                            <i class="las la-user-slash"></i>
                                        </a>
                                    @else
                                        <a href="#" class="btn btn-soft-success btn-icon btn-circle btn-sm"
                                           onclick="confirm_unban('{{route('customers.ban', encrypt($user->id))}}');"
                                           title="{{ translate('Unban this Customer') }}">
                                            <i class="las la-user-check"></i>
                                        </a>
                                    @endif --}}
                                    <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete"
                                       data-href="{{route('customers.destroy', $user->id)}}"
                                       title="{{ translate('Delete') }}">
                                        <i class="las la-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                    </tbody>
                </table>
                <div class="aiz-pagination">
                    {{ $users->appends(request()->input())->links() }}
                </div>
            </div>
        </form>
    </div>


    <div class="modal fade" id="confirm-ban">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title h6">{{translate('Confirmation')}}</h5>
                    <button type="button" class="close" data-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>{{translate('Do you really want to ban this Customer?')}}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">{{translate('Cancel')}}</button>
                    <a type="button" id="confirmation" class="btn btn-primary">{{translate('Proceed!')}}</a>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="confirm-unban">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title h6">{{translate('Confirmation')}}</h5>
                    <button type="button" class="close" data-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>{{translate('Do you really want to unban this Customer?')}}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">{{translate('Cancel')}}</button>
                    <a type="button" id="confirmationunban" class="btn btn-primary">{{translate('Proceed!')}}</a>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('modal')
    @include('modals.delete_modal')

    <!-- offline payment Modal -->

    <div class="modal fade" id="offline_wallet_recharge_modal" tabindex="-1" role="dialog"
         aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ translate('Recharge Wallet') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
                </div>
                <div id="offline_wallet_recharge_modal_body">
                    <form class="" action="{{route('admin_wallet_recharge')}}" method="post"
                          enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" id="user_id" name="user_id">
                        <div class="modal-body gry-bg px-3 pt-3 mx-auto">
                            <div id="manual_payment_data">

                                <div class="card mb-3 p-3">
                                    <div class="row mt-3">
                                        <div class="col-md-3">
                                            <label>Amount <span class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-md-9">
                                            <input type="number" lang="en" class="form-control mb-3" min="0" step="0.01"
                                                   name="amount" placeholder="Amount" required="">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-3">
                                            <label>Reason <span class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-md-9">
                                            <textarea class="form-control mb-3" name="reason"
                                                      placeholder="reason" required=""></textarea>
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
                $('.check-one:checkbox').each(function () {
                    this.checked = true;
                });
            } else {
                $('.check-one:checkbox').each(function () {
                    this.checked = false;
                });
            }

        });

        function sort_customers(el) {
            $('#sort_customers').submit();
        }

        function confirm_ban(url) {
            $('#confirm-ban').modal('show', {backdrop: 'static'});
            document.getElementById('confirmation').setAttribute('href', url);
        }

        function confirm_unban(url) {
            $('#confirm-unban').modal('show', {backdrop: 'static'});
            document.getElementById('confirmationunban').setAttribute('href', url);
        }

        function bulk_delete() {
            var data = new FormData($('#sort_customers')[0]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{route('bulk-customer-delete')}}",
                type: 'POST',
                data: data,
                cache: false,
                contentType: false,
                processData: false,
                success: function (response) {
                    if (response == 1) {
                        location.reload();
                    }
                }
            });
        }

        function show_make_wallet_recharge_modal(user_id) {
            $('#offline_wallet_recharge_modal #user_id').val(user_id);
            $('#offline_wallet_recharge_modal').modal('show', {backdrop: 'static'});
        }

        function exportCustomerExcel() {
            let search = $('#search').val();
            let url = "{{route('customer_export.excel', ':search')}}";
            url = url.replace(':search', '&search='+search);

            window.location.href = url;
        }
    </script>
@endsection
