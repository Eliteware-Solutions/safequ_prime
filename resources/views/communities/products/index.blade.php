@extends('backend.layouts.app')

@section('content')

    <div class="aiz-titlebar text-left mt-2 mb-3">
        <div class="row align-items-center">
            <div class="col-auto">
                <h1 class="h3">{{translate('Community Products')}}</h1>
            </div>
            <div class="col text-right">
                <a href="{{ route('community_product_create') }}" class="btn btn-circle btn-info">
                    <span>{{translate('Add New Product')}}</span>
                </a>
            </div>
        </div>
    </div>
    <br>

    <div class="card">
        <form class="" id="sort_products" action="" method="GET">
            <div class="card-header row gutters-5">
                <div class="col">
                    <h5 class="mb-md-0 h6">{{ translate('All Product') }}</h5>
                </div>

                <div class="dropdown mb-2 mb-md-0">
                    <button class="btn border dropdown-toggle" type="button" data-toggle="dropdown">
                        {{translate('Bulk Action')}}
                    </button>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a class="dropdown-item" href="#" onclick="bulk_delete()"> {{translate('Delete selection')}}</a>
                    </div>
                </div>

                <div class="col-md-2 ml-auto">
                    <select class="form-control form-control-sm aiz-selectpicker mb-2 mb-md-0" id="user_id"
                            name="user_id" onchange="sort_products()">
                        <option value="">{{ translate('All Sellers') }}</option>
                        @foreach (\App\Models\Seller::all() as $key => $seller)
                            @if ($seller->user != null && $seller->user->shop != null)
                                <option value="{{ $seller->id }}"
                                        @if ($seller->id == $seller_id) selected @endif>{{ $seller->user->shop->name }}
                                    ({{ $seller->user->name }})
                                </option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 ml-auto " style="display: none;">
                    <select class="form-control form-control-sm aiz-selectpicker mb-2 mb-md-0" name="type" id="type"
                            onchange="sort_products()">
                        <option value="">{{ translate('Sort By') }}</option>
                        <option value="rating,desc"
                                @isset($col_name , $query) @if($col_name == 'rating' && $query == 'desc') selected @endif @endisset>{{translate('Rating (High > Low)')}}</option>
                        <option value="rating,asc"
                                @isset($col_name , $query) @if($col_name == 'rating' && $query == 'asc') selected @endif @endisset>{{translate('Rating (Low > High)')}}</option>
                        <option value="num_of_sale,desc"
                                @isset($col_name , $query) @if($col_name == 'num_of_sale' && $query == 'desc') selected @endif @endisset>{{translate('Num of Sale (High > Low)')}}</option>
                        <option value="num_of_sale,asc"
                                @isset($col_name , $query) @if($col_name == 'num_of_sale' && $query == 'asc') selected @endif @endisset>{{translate('Num of Sale (Low > High)')}}</option>
                        <option value="unit_price,desc"
                                @isset($col_name , $query) @if($col_name == 'unit_price' && $query == 'desc') selected @endif @endisset>{{translate('Base Price (High > Low)')}}</option>
                        <option value="unit_price,asc"
                                @isset($col_name , $query) @if($col_name == 'unit_price' && $query == 'asc') selected @endif @endisset>{{translate('Base Price (Low > High)')}}</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <div class="form-group mb-0">
                        <input type="text" class="form-control form-control-sm" id="search" name="search"
                               @isset($sort_search) value="{{ $sort_search }}"
                               @endisset placeholder="{{ translate('Type & Enter') }}">
                    </div>
                </div>
            </div>

            <div class="card-body">
                <table class="table aiz-table mb-0">
                    <thead>
                    <tr>
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
                        <th data-breakpoints="sm">{{translate('Community')}}</th>
                        <th data-breakpoints="sm">{{translate('Purchase Dates')}}</th>
                        <th data-breakpoints="sm">{{translate('Info')}}</th>
                        <th data-breakpoints="md">{{translate('Total Stock')}}</th>
                        <th data-breakpoints="sm" class="text-right">{{translate('Options')}}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($products as $key => $product)
                        <tr>
                            <td>
                                <div class="form-group d-inline-block">
                                    <label class="aiz-checkbox">
                                        <input type="checkbox" class="check-one" name="id[]" value="{{$product->id}}">
                                        <span class="aiz-square-check"></span>
                                    </label>
                                </div>
                            </td>
                            <td>
                                <div class="row gutters-5">
<!--                                    <div class="col-auto">
                                        <img src="{{ uploaded_asset($product->product->thumbnail_img)}}" alt="Image"
                                             class="size-50px img-fit">
                                    </div>-->
                                    <div class="col">
                                        <span class="text-muted text-truncate-2">{{ $product->product->getTranslation('name') }}</span>
                                        <strong>{{translate('Variation')}}
                                            :</strong> {{ $product->product->variation }}
                                    </div>
                                </div>
                            </td>
                            <td>{{ (isset($product->seller) ? $product->seller->user->name : '--')  }}</td>
                            <td>
                                <strong>{{translate('From')}} :</strong> {{ $product->purchase_start_date }} </br>
                                <strong>{{translate('To')}} :</strong> {{ $product->purchase_end_date }}
                            </td>
                            <td>
                                <strong>{{translate('Num of Sale')}}
                                    :</strong> {{ $product->product->num_of_sale }} {{translate('times')}} </br>
                                <strong>{{translate('Base Price')}}
                                    :</strong> {{ single_price($product->product->unit_price) }}
                            </td>
                            <td>
                                @php
                                    $qty = 0;
                                    if($product->product->variant_product) {
                                        foreach ($product->product->stocks as $key => $stock) {
                                            $qty += $stock->qty;
                                            echo $stock->variant.' - '.$stock->qty.'<br>';
                                        }
                                    }
                                    else {
                                        //$qty = $product->current_stock;
                                        $qty = $product->qty;
                                        echo $qty;
                                    }
                                @endphp
                                @if($qty <= $product->product->low_stock_quantity)
                                    <span class="badge badge-inline badge-danger">Low</span>
                                @endif
                            </td>
                            <td class="text-right">
                                <a class="btn btn-soft-primary btn-icon btn-circle btn-sm"
                                   href="{{route('community_product_edit', ['id'=>$product->id] )}}"
                                   title="{{ translate('Edit') }}">
                                    <i class="las la-edit"></i>
                                </a>
                                <a class="btn btn-soft-warning btn-icon btn-circle btn-sm"
                                   href="{{route('community_product_duplicate', ['id'=>$product->id] )}}"
                                   title="{{ translate('Duplicate') }}">
                                    <i class="las la-copy"></i>
                                </a>
                                <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete"
                                   data-href="{{route('community_product_destroy', $product->id)}}"
                                   title="{{ translate('Delete') }}">
                                    <i class="las la-trash"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <div class="aiz-pagination">
                    {{ $products->appends(request()->input())->links() }}
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

        "use strict";

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

        function update_todays_deal(el) {
            if (el.checked) {
                var status = 1;
            } else {
                var status = 0;
            }
            $.post('{{ route('products.todays_deal') }}', {
                _token: '{{ csrf_token() }}',
                id: el.value,
                status: status
            }, function (data) {
                if (data == 1) {
                    AIZ.plugins.notify('success', '{{ translate('Todays Deal updated successfully') }}');
                } else {
                    AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
            });
        }

        function update_published(el) {
            if (el.checked) {
                var status = 1;
            } else {
                var status = 0;
            }
            $.post('{{ route('products.published') }}', {
                _token: '{{ csrf_token() }}',
                id: el.value,
                status: status
            }, function (data) {
                if (data == 1) {
                    AIZ.plugins.notify('success', '{{ translate('Published products updated successfully') }}');
                } else {
                    AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
            });
        }

        function update_approved(el) {
            if (el.checked) {
                var approved = 1;
            } else {
                var approved = 0;
            }
            $.post('{{ route('products.approved') }}', {
                _token: '{{ csrf_token() }}',
                id: el.value,
                approved: approved
            }, function (data) {
                if (data == 1) {
                    AIZ.plugins.notify('success', '{{ translate('Product approval update successfully') }}');
                } else {
                    AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
            });
        }

        function update_featured(el) {
            if (el.checked) {
                var status = 1;
            } else {
                var status = 0;
            }
            $.post('{{ route('products.featured') }}', {
                _token: '{{ csrf_token() }}',
                id: el.value,
                status: status
            }, function (data) {
                if (data == 1) {
                    AIZ.plugins.notify('success', '{{ translate('Featured products updated successfully') }}');
                } else {
                    AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
            });
        }

        function sort_products(el) {
            $('#sort_products').submit();
        }

        function bulk_delete() {
            var data = new FormData($('#sort_products')[0]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{route('bulk-community-product-delete')}}",
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

    </script>
@endsection
