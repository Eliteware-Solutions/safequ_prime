@extends('frontend.layouts.app',['header_show' => true, 'header2' => true, 'footer' => false])

@section('content')
    <main class="main-tag mt-0 cart-main-tag">

        <div class="breadcrumbs">
            <div class="container">
                <h5 class="mb-0 fw700 text-white text-uppercase">Cart Details</h5>
            </div>
        </div>

        <div class="content pb-5" id="cart_summary">

            <input type="hidden" id="item_count" value="{{ count($carts) }}">

            <div class="container py-4">
                <div class="row justify-content-center">
                    <div class="col-lg-6 col-md-7 px-1">
                        @if(count($carts) > 0)
                            <h6 class="fw600 title-txt pb-2 mb-2">My Exotic Farm Fresh Order List</h6>

                            @if ($user_data && $user_data->address != '')
                                <div class="delivery-addr p-3 flex-astart-jstart mb-3">
                                    <input type="checkbox" name="delivery_address" id="delivery_address" class="mr-2"
                                           checked/>
                                    <span class="check-box"></span>

                                    <label for="delivery_address" class="body-txt mb-0">
                                        {{ $user_data->address . ' ' . $user_data->city . ' ' . $user_data->state . ' ' . $user_data->postal_code }}
                                    </label>
                                </div>
                            @else
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
                            $total = 0;
                            $shipping = 0;
                        @endphp
                        @foreach ($carts as $key => $cartItem)
                            @php
                                $product = \App\Models\Product::find($cartItem['product_id']);
                                $product_stock = $product->stocks->where('id', $cartItem['product_stock_id'])->first();

                                $product_shipping_cost = $cartItem['shipping_cost'];
                                $shipping += $product_shipping_cost;

                                $sub_total = ($cartItem['price'] + $cartItem['tax']) * $cartItem['quantity'];
                                $total = $total + ($cartItem['price'] + $cartItem['tax']) * $cartItem['quantity'];
                                if(floatval($product->min_qty) < 1){
                                    $product->unit = floatval($product->min_qty) * 1000 .' '.$product->secondary_unit;
                                }
                            @endphp
                            <div class="crtord-itm-card mb-4 p-3">
                                <div class="img-name w-100">
                                    <div class="p-0 mxw-85px">
                                        <div class="item-img text-center">
                                            <img src="{{ uploaded_asset($product->photos) }}"
                                                 onerror="this.onerror=null;this.src='{{ static_asset('assets/img/avatar-place.png') }}';"
                                                 alt="{{ $product->name }}"/>
                                        </div>
                                    </div>
                                    <div class="pl-3 w-100">
                                        <h6 class="fw700">{{ $product->name }}</h6>
                                        <div class="pt-2 d-flex">
                                            <p class="body-txt mb-0">
                                                <span class="act-price fw700">
                                                    {!! single_price_web($sub_total) !!}
                                                </span>
                                                <i class="body-txt fsize12">&nbsp; <br class="sm"/>
                                                    ({!! single_price_web($cartItem['price']) !!} / {{ $product->unit }}
                                                    )
                                                </i>
                                            </p>
                                            <div class="action">
                                                <div class="item-count flex-acenter-jbtw">
                                                    <button class="btn"
                                                            onclick="this.parentNode.querySelector('input[type=number]').stepDown();"
                                                            type="button" data-field="quantity[{{ $cartItem['id'] }}]"
                                                            data-cart_id="{{ $cartItem['id'] }}">
                                                        <i class="fa fa-minus"></i>
                                                    </button>
                                                    <input class="quantity" min="1"
                                                           name="quantity[{{ $cartItem['id'] }}]"
                                                           value="{{ $cartItem['quantity'] }}" type="number"
                                                           id="quantity_{{ $cartItem['id'] }}" readonly/>
                                                    <button class="btn"
                                                            onclick="this.parentNode.querySelector('input[type=number]').stepUp();"
                                                            type="button" data-field="quantity[{{ $cartItem['id'] }}]"
                                                            data-cart_id="{{ $cartItem['id'] }}">
                                                        <i class="fa fa-plus"></i>
                                                    </button>
                                                </div>
                                                <h6 class="mb-0 text-danger cursor-pointer fw700 p-2 ml-2"
                                                    onclick="removeFromCartView(event, {{ $cartItem['id'] }})">X</h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        @php
                            $total += $shipping;
                            if ($carts->sum('discount') > 0){
                                $total -= $carts->sum('discount');
                            }
                        @endphp
                        @if ($total == 0)
                            <div class="row">
                                <div class="col-xl-8 mx-auto">
                                    <div class="shadow-sm bg-white p-4 rounded">
                                        <div class="text-center p-3">
                                            <i class="las la-frown la-3x opacity-60 mb-3"></i>
                                            <h3 class="h4 fw-700">{{ translate('Your Cart is empty') }}</h3>
                                        </div>
                                    </div>
                                    <div class="pt-4 text-center">
                                        @if($user_data && intval($user_data->joined_community_id) > 0)
                                            <a href="{{ route('shop.visit', $shop->slug) }}">
                                                <button class="btn primary-btn btn-round px-5">
                                                    Continue Shopping &nbsp;&nbsp;
                                                    <i class="fal fa-long-arrow-right text-white"></i>
                                                </button>
                                            </a>
                                        @else
                                            <a href="{{ route('home') }}">
                                                <button class="btn primary-btn btn-round px-5">
                                                    Continue Shopping &nbsp;&nbsp;
                                                    <i class="fal fa-long-arrow-right text-white"></i>
                                                </button>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if ($total > 0)
                        <!-- Amount -->
                            <div class="row">
                                <div class="col-12 text-center">
                                    <a href="{{ route('shop.visit', session()->get('shop_slug')) }}" class="add_more_product_btn">+ Add More Products</a>
                                </div>
                            </div>
                            <div class="payings py-4">
                                <hr class="b-1">
                                @if($shipping > 0)
                                    <h6>
                                        <ins class="fw500">Shipping cost :</ins>
                                        <ins class="fw500 text-right"> {!! single_price_web($shipping) !!} </ins>
                                    </h6>
                                @endif
                                @if ($carts->sum('discount') > 0)
                                    <h6>
                                        <ins class="fw500">{{translate('Coupon Discount')}} :</ins>
                                        <ins class="fw500 text-right">
                                            - {!! single_price_web($carts->sum('discount')) !!} </ins>
                                    </h6>
                                @endif
                                <h5>
                                    <ins class="fw700">Total :</ins>
                                    <ins class="fw700 text-right"
                                         id="basic_amount"> {!! single_price_web($total) !!} </ins>
                                </h5>
                            </div>

                            @if (Auth::check() && get_setting('coupon_system') == 1)
                                @if ($carts[0]['discount'] > 0)
                                    <div class="mb-3">
                                        <form class="" id="remove-coupon-form" enctype="multipart/form-data">
                                            @csrf
                                            <input type="hidden" name="owner_id" value="{{ $carts[0]['owner_id'] }}">
                                            <div class="input-group">
                                                <div class="form-control">{{ $carts[0]['coupon_code'] }}</div>
                                                <div class="input-group-append">
                                                    <button type="button" id="coupon-remove"
                                                            class="btn text-white primary-color-bg b-1 border-primary">{{translate('Change Coupon')}}</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                @else
                                    <div class="mb-3">
                                        <form class="" id="apply-coupon-form" enctype="multipart/form-data">
                                            @csrf
                                            <input type="hidden" name="owner_id" value="{{ $carts[0]['owner_id'] }}">
                                            <div class="input-group">
                                                <input type="text" class="form-control" name="code"
                                                       onkeydown="return event.key != 'Enter';"
                                                       placeholder="{{translate('Have coupon code? Enter here')}}"
                                                       required>
                                                <div class="input-group-append">
                                                    <button type="button" id="coupon-apply"
                                                            class="btn text-white primary-color-bg b-1 border-primary">{{translate('Apply')}}</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                @endif
                            @endif

                            <form action="{{ route('payment.checkout') }}" class="form-default" role="form"
                                  method="POST"
                                  id="checkout-form">
                                @csrf

                                @if (count($carts) > 0)
                                    <input type="hidden" name="owner_id" value="{{ $carts[0]['owner_id'] }}">
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
                                                       @if(Auth::user()->balance == 0) disabled @else checked @endif/>
                                                <span class="check-box"></span>

                                                <label for="partial_payment" class="body-txt mb-0">
                                                    <span class="align-middle body-txt">Use SafeQu balance <ins
                                                                class="fw600 body-txt">{!! single_price_web(Auth::user()->balance) !!} </ins></span>
                                                </label>
                                            </div>
                                            <input type="hidden" id="payable_amount"
                                                   value='{!! single_price_web($total - Auth::user()->balance) !!}'>
                                        @else
                                            <div class="other-gatewy p-3 mb-3">
                                                <label for="pay-option2" class="label-radio mb-0 py-2 d-block">
                                                    <input type="radio" id="pay-option2" name="payment_option"
                                                           value="wallet" tabindex="1" checked/>
                                                    <span class="align-middle body-txt">SafeQu balance</span>
                                                    <br>
                                                    <span class="align-middle body-txt cart_wallet_bal">
                                                    Available <ins
                                                                class="fw600 body-txt">{!! single_price_web(Auth::user()->balance) !!} </ins> for Payment
                                                    </span>
                                                </label>
                                            </div>
                                        @endif

                                        <div class="other-gatewy p-3 mb-3">
                                            <label for="pay-option1" class="label-radio mb-0 py-2 d-block">
                                                <input type="radio" id="pay-option1" name="payment_option" tabindex="1"
                                                       value="razorpay"
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
                                            <span class="fw500 h5"
                                                  id="total_amount">{!! single_price_web($total) !!}</span>
                                            &nbsp;

                                        </h5>
                                    </div>
                                    <div>
                                        @if ($user_data && $user_data->address == '')
                                            <a href="javascript:void(0)" data-toggle="tooltip" data-placement="top"
                                               title="Please complete your profile before attempting to make payment">
                                                <i class="fad fa-info-circle primary-color-dark animated faa-tada align-middle"></i>
                                            </a>
                                        @endif
                                        <button type="button" id="btn_pay_now"
                                                class="ml-2 btn primary-btn btn-round py-1"
                                                onclick="submitOrder(this)"
                                                @if (count($carts) == 0 || $user_data->address == '') disabled @endif>
                                            Pay Now
                                        </button>
                                    </div>
                                </div>

                            </form>
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
        $(function () {
            $('[data-toggle="tooltip"]').tooltip()
        })

        $(document).ready(function () {
            updateNavCart($('#item_count').val());

            $('.item-count button').on('click', function () {
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

                (qty < 10) ? $("#itm-cnt").removeClass("d2 d3") : "";

                $('#btn_pay_now').attr('disabled', 'disabled');
                updateQuantity(cart_id, qty);
            })
        })

        function submitOrder(el) {
            $(el).prop('disabled', true);
            if ($('#delivery_address').is(":checked")) {
                $('#checkout-form').submit();
            } else {
                AIZ.plugins.notify('danger', '{{ translate('You need to select the address') }}');
                $(el).prop('disabled', false);
            }
        }

        function updateQuantity(cart_id, qty) {
            $.post('{{ route('cart.updateQuantity') }}', {
                _token: AIZ.data.csrf,
                id: cart_id,
                quantity: qty
            }, function (data) {
                updateNavCart(data.cart_count);
                $('#cart_summary').html('');
                $('#cart_summary').html(data.cart_view);
            });
        }

        function removeFromCartView(e, key) {
            e.preventDefault();
            $.post('{{ route('cart.removeFromCart') }}', {
                _token: AIZ.data.csrf,
                id: key
            }, function (data) {
                updateNavCart(data.cart_count);
                $('#cart_summary').html(data.cart_view);
                AIZ.plugins.notify('success', "{{ translate('Item has been removed from cart') }}");
            });
        }

        $(document).on("click", "#coupon-apply", function () {
            var data = new FormData($('#apply-coupon-form')[0]);

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                method: "POST",
                url: "{{route('checkout.apply_coupon_code')}}",
                data: data,
                cache: false,
                contentType: false,
                processData: false,
                success: function (data, textStatus, jqXHR) {
                    AIZ.plugins.notify(data.response_message.response, data.response_message.message);
                    $("#cart_summary").html(data.html);
                }
            })
        });

        $(document).on("click", "#coupon-remove", function () {
            var data = new FormData($('#remove-coupon-form')[0]);

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                method: "POST",
                url: "{{route('checkout.remove_coupon_code')}}",
                data: data,
                cache: false,
                contentType: false,
                processData: false,
                success: function (data, textStatus, jqXHR) {
                    $("#cart_summary").html(data);
                }
            });
        });

        if ($('#partial_payment').is(':checked')) {
            $('.total h5').append('<span class="fw500 mb-0 strikethrough">' + $('#total_amount').html() + '</span>');
            $('#total_amount').html($('#payable_amount').val());
        }

        $('#partial_payment').on('change', function () {
            if (this.checked == true) {
                $('.total h5').append('<span class="fw500 mb-0 strikethrough">' + $('#total_amount').html() + '</span>');
                $('#total_amount').html($('#payable_amount').val());
            } else {
                $('.total h5 .strikethrough').remove();
                $('#total_amount').html($('#basic_amount').html());
            }
        });
    </script>
@endsection
