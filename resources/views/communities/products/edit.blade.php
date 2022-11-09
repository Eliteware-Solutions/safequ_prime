@extends('backend.layouts.app')

@section('content')
    <div class="aiz-titlebar text-left mt-2 mb-3">
        <h5 class="mb-0 h6">{{ translate('Edit Community Product') }}</h5>
    </div>
    <div class="">
        <form class="form form-horizontal mar-top" action="{{route('community_product_update', $product_stock->id)}}"
              method="POST" enctype="multipart/form-data" id="choice_form">
            <div class="row gutters-5">
                <div class="col-lg-8">
                    <input name="_method" type="hidden" value="POST">
                    <input type="hidden" name="id" value="{{ $product_stock->id }}">
                    @csrf
                    <div class="card">
                        <div class="card-body">
                            <div class="form-group row">
                                <label class="col-md-3 col-from-label">{{translate('Product')}} <span
                                            class="text-danger">*</span></label>
                                <div class="col-md-8">
                                    <select class="form-control aiz-selectpicker" name="product_id" id="product_id"
                                            data-live-search="true" required>
                                        <option value="">{{ translate('Select Product') }}</option>
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}"
                                                    @if($product_stock->product_id == $product->id) selected @endif>{{ $product->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row" id="seller">
                                <label class="col-md-3 col-from-label">{{translate('Community')}} <span
                                            class="text-danger">*</span></label>
                                <div class="col-md-8">
                                    <select class="form-control aiz-selectpicker" name="seller_id" id="seller_id"
                                            data-live-search="true" required>
                                        <option value="">{{ translate('Select Community') }}</option>
                                        <option value="0">{{ translate('All') }}</option>
                                        @foreach ($sellers as $seller)
                                            <option value="{{ $seller->id }}"
                                                    @if($product_stock->seller_id == $seller->id) selected @endif>
                                                {{ $seller->user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 control-label"
                                       for="purchase_date_range">{{translate('Purchase Date Range')}} <span
                                            class="text-danger">*</span></label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control aiz-date-range" name="purchase_date_range"
                                           placeholder="{{translate('Purchase Date Range')}}"
                                           @if($product_stock->purchase_start_date && $product_stock->purchase_end_date) value="{{ date('d-m-Y H:i:s', strtotime($product_stock->purchase_start_date)).' TO '.date('d-m-Y H:i:s', strtotime($product_stock->purchase_end_date)) }}"
                                           @endif data-time-picker="true" data-format="DD-MM-YYYY HH:mm:ss"
                                           data-separator=" TO " autocomplete="off" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 h6">{{translate('Product price + stock')}}</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group row">
                                <label class="col-lg-3 col-from-label">{{translate('Unit price')}}</label>
                                <div class="col-lg-6">
                                    <input type="text" placeholder="{{translate('Unit price')}}" name="unit_price"
                                           class="form-control" value="{{$product_stock->price}}" required>
                                </div>
                            </div>

                            <div id="show-hide-div">
                                <div class="form-group row" id="quantity">
                                    <label class="col-lg-3 col-from-label">{{translate('Quantity')}}</label>
                                    <div class="col-lg-6">
                                        <input type="number" lang="en" value="{{ $product_stock->qty }}" step="1"
                                               placeholder="{{translate('Quantity')}}" name="current_stock"
                                               class="form-control">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 col-from-label">
                                        {{translate('SKU')}}
                                    </label>
                                    <div class="col-md-6">
                                        <input type="text" placeholder="{{ translate('SKU') }}"
                                               value="{{ $product_stock->sku }}" name="sku" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 col-from-label">
                                    {{translate('Wholesale Prices')}}
                                </label>
                                <div class="col-md-6">
                                    <div class="qunatity-price">
                                        @foreach ($product_stock->wholesalePrices as $wholesalePrice)
                                            <div class="row gutters-5">
                                                <div class="col-3">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control"
                                                               placeholder="{{translate('Min QTY')}}"
                                                               name="wholesale_min_qty[]"
                                                               value="{{ $wholesalePrice->min_qty }}" required>
                                                    </div>
                                                </div>
                                                <div class="col-3">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control"
                                                               placeholder="{{ translate('Max QTY') }}"
                                                               name="wholesale_max_qty[]"
                                                               value="{{ $wholesalePrice->max_qty }}" required>
                                                    </div>
                                                </div>
                                                <div class="col">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control"
                                                               placeholder="{{ translate('Price per piece') }}"
                                                               name="wholesale_price[]"
                                                               value="{{ $wholesalePrice->price }}" required>
                                                    </div>
                                                </div>
                                                <div class="col-auto">
                                                    <button type="button"
                                                            class="mt-1 btn btn-icon btn-circle btn-sm btn-soft-danger"
                                                            data-toggle="remove-parent" data-parent=".row">
                                                        <i class="las la-times"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button
                                            type="button"
                                            class="btn btn-soft-secondary btn-sm"
                                            data-toggle="add-more"
                                            data-content='<div class="row gutters-5">
                                        <div class="col-3">
                                            <div class="form-group">
                                                <input type="text" class="form-control" placeholder="{{translate('Min Qty')}}" name="wholesale_min_qty[]" required>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-group">
                                                <input type="text" class="form-control" placeholder="{{ translate('Max Qty') }}" name="wholesale_max_qty[]" required>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="form-group">
                                                <input type="text" class="form-control" placeholder="{{ translate('Price per piece') }}" name="wholesale_price[]" required>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <button type="button" class="mt-1 btn btn-icon btn-circle btn-sm btn-soft-danger" data-toggle="remove-parent" data-parent=".row">
                                                <i class="las la-times"></i>
                                            </button>
                                        </div>
                                    </div>'
                                            data-target=".qunatity-price">
                                        {{ translate('Add More') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 h6">{{translate('Estimate Shipping Time')}}</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group mb-3">
                                <label for="name">
                                    {{translate('Shipping Days')}}
                                </label>
                                <div class="input-group">
                                    <input type="number" class="form-control" name="est_shipping_days"
                                           value="{{ $product_stock->est_shipping_days }}" min="1" step="1"
                                           placeholder="{{translate('Shipping Days')}}">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"
                                              id="inputGroupPrepend">{{translate('Days')}}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row pt-3">
                                <label class="col-md-6 col-from-label">{{translate('Best Selling Product')}}</label>
                                <div class="col-md-6">
                                    <label class="aiz-switch aiz-switch-success mb-0">
                                        <input type="checkbox" name="is_best_selling" {{ $product_stock->is_best_selling ? 'checked' : '' }} value="1">
                                        <span></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="mb-3 text-right">
                        <button type="submit" name="button"
                                class="btn btn-info">{{ translate('Update Product') }}</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

@endsection
