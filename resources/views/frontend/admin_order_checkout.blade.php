@extends('frontend.layouts.app', ['new_header' => false, 'header_show' => false, 'header2' => false, 'footer' => false, 'new_footer' => false])

@section('meta_title', get_setting('website_name') . ' | Order Payment')

@section('content')
    <main class="main-tag mt-0 cart-main-tag">

        <div class="breadcrumbs cart-bcr">
            <div class="container text-center pt-3">
                <h4 class="mb-0 fw700 text-white text-uppercase">Order Payment</h4>
            </div>
        </div>

        <div class="content pb-5" id="cart_summary">

            <input type="hidden" id="item_count" value="{{ count($order->orderDetails) }}">

            <div class="container py-4">
                <div class="row justify-content-center">
                    <div class="col-md-8 px-1">
                        @if ($order)
                            <h6 class="fw600 title-txt pb-2 mb-2">My Exotic Farm Fresh Order List</h6>

                            @if ($user_data && isset($user_data->address) && $user_data->address != '')
                                <div class="delivery-addr p-3 flex-astart-jstart mb-3">
                                    <input type="checkbox" name="delivery_address" id="delivery_address" class="mr-2"
                                        checked />
                                    <span class="check-box"></span>

                                    <label for="delivery_address" class="body-txt mb-0">
                                        {{ $user_data->address . ' ' . $user_data->city . ' ' . $user_data->state . ' ' . $user_data->postal_code }}
                                    </label>
                                </div>
                            @elseif (auth()->user())
                                <div class="text-center">
                                    <a href="{{ route('profile') }}">
                                        <button class="btn primary-btn btn-round py-1"> Add Address</button>
                                    </a>
                                </div>
                                <hr>
                            @endif
                        @endif
                        <br>
                        <!-- Item Card -->

                        @php
                            $total = $order->grand_total;
                            $shipping = 0;
                            $shipSubtotal = 0;
                        @endphp
                        @foreach ($order->orderDetails as $key => $detail)
                            @php
                                $product = \App\Models\Product::find($detail->product_id);
                                $product_stock = $product->stocks->where('id', $detail->product_stock_id)->first();
                                
                                $product_shipping_cost = $detail->shipping_cost;
                                $shipping += $product_shipping_cost;
                                
                                $sub_total = $detail->price + $detail->tax;
                                if (floatval($product->min_qty) < 1) {
                                    $product->unit = floatval($product->min_qty) * 1000 . ' ' . $product->secondary_unit;
                                }
                                $unit_price = floatval($detail->price) > 0 && $detail->price / $detail->quantity > 0 ? $detail->price / $detail->quantity : $product->unit_price;
                            @endphp
                            <div class="crtord-itm-card mb-4 py-3 px-2">
                                <div class="img-name w-100">
                                    <div class="p-0">
                                        <div class="item-img text-center">
                                            <img src="{{ uploaded_asset($product->photos) }}"
                                                onerror="this.onerror=null;this.src='{{ static_asset('assets/img/no-image-found.jpg') }}';"
                                                alt="{{ $product->name }}" />
                                        </div>
                                    </div>
                                    <div class="pl-3 w-100">
                                        <h6 class="fw700">{{ $product->name }}</h6>
                                        <div class="pt-2 d-flex">
                                            <p class="body-txt mb-0">
                                                <span class="act-price fw700">
                                                    {!! single_price_web($sub_total) !!}
                                                </span>
                                                <i class="body-txt fsize12">&nbsp; <br class="sm" />
                                                    ({!! single_price_web($unit_price) !!} / {{ $product->unit }})
                                                </i>
                                            </p>
                                            <div class="action">
                                                <div class="item-count flex-acenter-jbtw">
                                                    <input class="quantity" min="1"
                                                        name="quantity[{{ $detail->id }}]" value="{{ $detail->quantity }}"
                                                        type="number" id="quantity_{{ $detail->id }}" readonly />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        @if ($total == 0)
                            <div class="row">
                                <div class="col-xl-8 mx-auto">
                                    <div class="shadow-sm bg-white p-4 rounded">
                                        <div class="text-center p-3">
                                            <i class="las la-frown la-3x opacity-60 mb-3"></i>
                                            <h3 class="h4 fw-700">{{ translate('No Orders') }}</h3>
                                        </div>
                                    </div>
                                    <div class="pt-4 text-center">
                                        <a href="{{ route('shop.visit') }}">
                                            <button class="btn primary-btn btn-round px-5">
                                                Continue Shopping &nbsp;&nbsp;
                                                <i class="fal fa-long-arrow-right text-white"></i>
                                            </button>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if ($total > 0)
                            <!-- Amount -->
                            <div class="payings py-4">
                                @if ($shipping > 0)
                                    @php
                                        $shipSubtotal = $total - $shipping;
                                    @endphp
                                    <small>
                                        <i class="fw500">
                                            <sup>**</sup>Add products worth
                                            <span class="blinking">{!! single_price_web(abs(get_setting('ship_cost_min_price') - $shipSubtotal)) !!}</span> to avail free delivery
                                        </i>
                                    </small>
                                @endif
                                <hr class="b-1">
                                @if ($shipping > 0)
                                    <h6>
                                        <ins class="fw500">Shipping cost :</ins>
                                        <ins class="fw500 text-right"> {!! single_price_web($shipping) !!} </ins>
                                    </h6>
                                    <h6>
                                        <ins class="fw500">Sub cost :</ins>
                                        <ins class="fw500 text-right"> {!! single_price_web($shipSubtotal) !!} </ins>
                                    </h6>
                                @else
                                    <h6>
                                        <ins class="fw500">Shipping cost :</ins>
                                        <ins class="fw500 text-right"> FREE </ins>
                                    </h6>
                                @endif

                                @if ($order->service_charge > 0)
                                    <h6>
                                        <ins class="fw500">Service Charge :</ins>
                                        <ins class="fw500 text-right"> {!! single_price_web($order->service_charge) !!}</ins>
                                    </h6>
                                @endif

                                <h5 class="mt-3">
                                    <ins class="fw700">Total :</ins>
                                    <ins class="fw700 text-right" id="basic_amount"> {!! single_price_web($total + $order->service_charge) !!} </ins>
                                </h5>
                            </div>


                            <!-- Checkout Form -->
                            @if (Auth::user())
                                <form action="{{ route('admin.order.payment.checkout') }}" class="form-default"
                                    role="form" method="POST" id="checkout-form-login">
                                    @csrf

                                    @if ($order)
                                        <input type="hidden" name="order_id" value="{{ $order->id }}">
                                        <input type="hidden" name="owner_id" value="{{ $order->seller_id }}">
                                    @endif

                                    <!-- Payment Method -->
                                    <div class="pay-method pb-4">
                                        @if (Auth::user())
                                            <p class="fsize12">Complete your payment easily using the below options to
                                                confirm your farm fresh order:</p>
                                            @if ($total > Auth::user()->balance)
                                                <div class="delivery-addr p-3 flex-astart-jstart mb-3">
                                                    <input type="checkbox" name="partial_payment" id="partial_payment"
                                                        class="mr-2"
                                                        @if (Auth::user()->balance == 0) disabled
                                                           @else checked @endif />
                                                    <span class="check-box"></span>

                                                    <label for="partial_payment" class="body-txt mb-0">
                                                        <span class="align-middle body-txt">Use SafeQu balance <ins
                                                                class="fw600 body-txt">{!! single_price_web(Auth::user()->balance) !!}
                                                            </ins></span>
                                                    </label>
                                                </div>
                                                <input type="hidden" id="payable_amount" value='{!! single_price_web($total - Auth::user()->balance) !!}'>
                                            @else
                                                <div class="other-gatewy p-3 mb-3">
                                                    <label for="pay-option2" class="label-radio mb-0 py-2 d-block">
                                                        <input type="radio" id="pay-option2" name="payment_option"
                                                            value="wallet" tabindex="1" checked />
                                                        <span class="align-middle body-txt">SafeQu balance</span>
                                                        <br>
                                                        <span class="align-middle body-txt cart_wallet_bal">
                                                            Available <ins class="fw600 body-txt">{!! single_price_web(Auth::user()->balance) !!}
                                                            </ins> for Payment
                                                        </span>
                                                    </label>
                                                </div>
                                            @endif

                                            <div class="other-gatewy p-3 mb-3">
                                                <label for="pay-option1" class="label-radio mb-0 py-2 d-block">
                                                    <input type="radio" id="pay-option1" name="payment_option"
                                                        tabindex="1" value="razorpay"
                                                        @if ($total > Auth::user()->balance) checked @endif />
                                                    <span class="align-middle body-txt">
                                                        PayTM / G-Pay / UPI / Net Banking
                                                    </span>
                                                </label>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="p-3 pay-btn bt-1 flex-acenter-jbtw">
                                        <div class="total">
                                            <p class="fsize15 mb-1 body-txt w-100">Total:</p>
                                            <h5 class="mb-0">
                                                <span class="fw500 h5" id="total_amount">{!! single_price_web($total) !!}</span>
                                                &nbsp;

                                            </h5>
                                        </div>
                                        <div>
                                            @if ($user_data && isset($user_data->address) && $user_data->address == '')
                                                <a href="javascript:void(0)" data-toggle="tooltip" data-placement="top"
                                                    title="Please complete your profile before attempting to make payment">
                                                    <i
                                                        class="fad fa-info-circle primary-color-dark animated faa-tada align-middle"></i>
                                                </a>
                                            @endif
                                            <button type="button" id="btn_pay_now"
                                                class="ml-2 btn primary-btn btn-round py-1"
                                                onclick="submitLoginOrder(this)"
                                                @if (count($order->orderDetails) == 0) disabled @endif>
                                                Pay Now
                                            </button>
                                        </div>
                                    </div>

                                </form>
                            @endif

                        @endif
                    </div>
                </div>
            </div>
        </div>

    </main>
@endsection

@section('modal')
@endsection

@section('script')
    <script type="text/javascript">
        // Tooltip
        $(function() {
            $('[data-toggle="tooltip"]').tooltip()
        })

        $(document).scroll(function() {
            let scroll = $(this).scrollTop();
            let header = $("header");

            if (scroll >= 0.1) {
                header.addClass("fixedHeader");
                $('.main-tag').css('padding-top', header.height())

            } else {
                header.removeClass("fixedHeader");
                $('.main-tag').css('padding-top', 0)
            }
        })

        $(document).ready(function() {
            updateNavCart($('#item_count').val());

            $('.item-count button').on('click', function() {
                let cart_id = $(this).data('cart_id');
                let qty = parseInt($("#quantity_" + cart_id).val());

                $("#itm-cnt").text(qty);

                if (qty >= 10) {
                    $("#itm-cnt").removeClass("d2 d3");
                    $("#itm-cnt").addClass("d2");
                }

                if (qty >= 100) {
                    $("#itm-cnt").removeClass("d2 d3");
                    $("#itm-cnt").addClass("d3");
                }

                (qty < 10) ? $("#itm-cnt").removeClass("d2 d3"): "";

                $('#btn_pay_now').attr('disabled', 'disabled');
            })
        })

        /*function submitOrder(el) {
            $('#checkout-form').submit();
        }*/

        function submitLoginOrder(el) {
            $(el).prop('disabled', true);
            if ($('#delivery_address').is(":checked")) {
                $('#checkout-form-login').submit();
            } else {
                AIZ.plugins.notify('danger', '{{ translate('You need to select the address') }}');
                $(el).prop('disabled', false);
            }
        }

        if ($('#partial_payment').is(':checked')) {
            $('.total h5').append('<span class="fw500 mb-0 strikethrough">' + $('#total_amount').html() + '</span>');
            $('#total_amount').html($('#payable_amount').val());
        }

        $('#partial_payment').on('change', function() {
            if (this.checked == true) {
                $('.total h5').append('<span class="fw500 mb-0 strikethrough">' + $('#total_amount').html() +
                    '</span>');
                $('#total_amount').html($('#payable_amount').val());
            } else {
                $('.total h5 .strikethrough').remove();
                $('#total_amount').html($('#basic_amount').html());
            }
        });
    </script>
@endsection
