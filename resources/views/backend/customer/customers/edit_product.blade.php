@extends('backend.layouts.app')

@php
    $disabled = '';
    $type = '';
    if ($order_type == 'cart_order') {
        $disabled = 'disabled';
        $type = 'cart';
    }
@endphp

@section('content')
    <div class="aiz-titlebar text-left mt-2 mb-3">
        <h5 class="mb-0 h6">{{ translate('Add New Order') }}</h5>
    </div>

    <div class="">
        <form class="form form-horizontal mar-top" action="{{ route('customers.edit_customer_order') }}" method="POST"
            enctype="multipart/form-data" id="choice_form">
            <div class="row gutters-5">
                <div class="col-lg-12">
                    @csrf
                    <input type="hidden" name="user_id" value="{{ $user->id }}">

                    @if ($type)
                        <input type="hidden" name="cart_id" value="{{ $order->id }}">
                    @else
                        <input type="hidden" name="order_id" value="{{ $order->id }}">
                    @endif

                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 h6">{{ translate('Order Information') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group row">
                                <label class="col-md-3 col-from-label">{{ translate('Customer') }} <span
                                        class="text-danger">*</span></label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="name"
                                        placeholder="{{ translate('Customer Name') }}" value="{{ $user->name }}" disabled>
                                </div>
                            </div>
                            <div class="form-group row" id="payment_status">
                                <label class="col-md-3 col-from-label">{{ translate('Payment Status') }} <span
                                        class="text-danger">*</span></label>
                                <div class="col-md-8">
                                    <select class="form-control aiz-selectpicker" name="payment_status" id="payment_status"
                                        data-live-search="true" required onchange="checkPaymentDetails(this)" {{ $disabled }}>
                                        <option value="unpaid">{{ translate('Un-Paid') }}</option>
                                        <option value="paid">{{ translate('Paid') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 col-from-label">{{ translate('Payment Type') }}</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="payment_type" id="payment_type"
                                        placeholder="{{ translate('G-Pay etc..') }}" {{ $disabled }}>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 col-from-label">{{ translate('Transaction Id') }}</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="transaction_id" id="transaction_id"
                                        placeholder="{{ translate('GP123456 etc..') }}" {{ $disabled }}>
                                </div>
                            </div>
                            <div class="form-group row" id="owner_id">
                                <label class="col-md-3 col-from-label">{{ translate('Community') }} <span
                                        class="text-danger">*</span></label>
                                <div class="col-md-8">
                                    <select class="form-control aiz-selectpicker" name="owner_id" id="owner_id" required>
                                        <option>Select Community</option>
                                        @foreach ($shop as $val)
                                            <option value="{{ $val->id }}"
                                                @if ($order->owner_id == $val->id) selected @endif>{{ $val->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 col-from-label">
                                    {{ translate('Products') }} <span class="text-danger">*</span>
                                </label>
                                <div class="col-md-8">
                                    <div class="qunatity-price">
                                        <div class="row gutters-5">
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <select class="form-control aiz-selectpicker" name="proudct"
                                                        data-live-search="true" required onchange="loadProductUnit(this);">
                                                        <option value="">Select Product</option>
                                                        @php
                                                            $qty = '';
                                                            $unit = '';
                                                        @endphp

                                                        @foreach ($active_products as $product)
                                                            <option value="{{ $product->id }}"
                                                                data-unit_label="{{ $product->unit_label }}"
                                                                @if ($order->product_stock_id == $product->id) selected @endif>
                                                                @php
                                                                    $variation = '';
                                                                    if ($product->product->variation) {
                                                                        $variation = ' [' . $product->product->variation . ']';
                                                                    }
                                                                @endphp
                                                                {{ $product->product->name . $variation }}
                                                            </option>
                                                            @php
                                                            if ($order->product_stock_id == $product->id) {
                                                                $qty = $order->quantity;
                                                                $unit = $product->unit_label;
                                                            }
                                                            @endphp
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="form-group">
                                                    <input type="text" class="form-control prod_qty" value="{{ $qty }}"
                                                        placeholder="{{ translate('Qty') }}" name="prod_qty" required>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="form-group">
                                                    <input type="text" class="form-control prod_unit" value="{{ $unit }}"
                                                        placeholder="{{ translate('Unit') }}" name="price_per_unit">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>


                <div class="col-12">
                    <div class="btn-toolbar float-right mb-3" role="toolbar" aria-label="Toolbar with button groups">
                        @if ($order_type == 'cart_order')
                            <input type="submit" name="add_cart" class="btn btn-primary mr-4"
                                value="{{ translate('Update cart') }}">
                        @else
                            <div class="btn-group" role="group" aria-label="Second group">
                                <input type="submit" name="add_order" class="btn btn-success"
                                    value="{{ translate('Place Order') }}">
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
        "use strict";

        $('form').bind('submit', function(e) {
            // Disable the submit button while evaluating if the form should be submitted
            $("button[type='submit']").prop('disabled', true);

            let valid = true;

            if (!valid) {
                e.preventDefault();

                // Reactivate the button if the form was not submitted
                $("button[type='submit']").button.prop('disabled', false);
            }
        });

        function checkPaymentDetails(obj) {
            if ($(obj).val() == 'paid') {
                $('#payment_type').attr('required', true);
                $('#transaction_id').attr('required', true);
            } else {
                $('#payment_type').removeAttr('required');
                $('#transaction_id').removeAttr('required');
            }
        }

        function loadProductUnit(Obj) {
            let unit_label = $(Obj).find(':selected').data('unit_label');
            $(Obj).parent().parent().parent().parent().find('.prod_unit').val(unit_label);
            $(Obj).parent().parent().parent().parent().find('.prod_qty').val(1);
        }
    </script>
@endsection
