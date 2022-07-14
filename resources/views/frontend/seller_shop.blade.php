@extends('frontend.layouts.app',['header_show' => true, 'header2' => true, 'footer' => true])

@if (isset($category_id))
    @php
        $meta_title = \App\Models\Category::find($category_id)->meta_title;
        $meta_description = \App\Models\Category::find($category_id)->meta_description;
    @endphp
@elseif (isset($brand_id))
    @php
        $meta_title = \App\Models\Brand::find($brand_id)->meta_title;
        $meta_description = \App\Models\Brand::find($brand_id)->meta_description;
    @endphp
@else
    @php
        $meta_title = get_setting('meta_title');
        $meta_description = get_setting('meta_description');
    @endphp
@endif

@section('meta_title'){{ $meta_title }}@stop
@section('meta_description'){{ $meta_description }}@stop

@section('meta')
    <!-- Schema.org markup for Google+ -->
    <meta itemprop="name" content="{{ $meta_title }}">
    <meta itemprop="description" content="{{ $meta_description }}">

    <!-- Twitter Card data -->
    <meta name="twitter:title" content="{{ $meta_title }}">
    <meta name="twitter:description" content="{{ $meta_description }}">

    <!-- Open Graph data -->
    <meta property="og:title" content="{{ $meta_title }}"/>
    <meta property="og:description" content="{{ $meta_description }}"/>
@endsection

@section('content')

    <main class="main-tag mt-0 promain">

        <div class="breadcrumbs high">
            <div class="container">
                <h5 class="mb-0 fw700 text-white text-uppercase">Your Community - {{ $shop->name }}</h5>
            </div>
        </div>

        <!-- Cards -->
        <div class="content  bgcream-product ">
            <div class="container px-0">
                <div class="row justify-content-center ">
                    <input type="hidden" id="cart_data" value="{{ json_encode($cart) }}">

                    @if ($categories && (count($products_purchase_expired) > 0 || count($products_purchase_started) > 0))
                        <div class="col-12 pb-md-5 pb-4 px-2">
                            <div class="srch-fltr-card mb-md-0 mb-2">
                                <ul class="item-tags pb-3 mb-0 flex-acenter-jbtw">
                                    <li class="active_filter filter-button" data-filter="all"> All</li>

                                    @foreach ($categories as $key => $cat)
                                        <li class="filter-button mr-1" data-filter="{{ $cat['filter'] }}">
                                            {{ $cat['name'] }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif

                    @if (count($products_purchase_expired) > 0)
                        @foreach ($products_purchase_expired as $expired_product)
                            <div
                                    class="col-lg-4 col-md-6 col-sm-8 px-2 pb-4 timeout-card filter {{ $expired_product->product->category->slug }} ">
                                <!-- Item Cards -->
                                <div class="item-card p-3">

                                    <div class="d-flex justify-content-start align-items-center">
                                        <div class="img-name pr-2">
                                            <div class="item-img item-img-sm text-center">
                                                <img src="{{ uploaded_asset($expired_product->product->photos) }}"
                                                     onerror="this.onerror=null;this.src='{{ static_asset('assets/img/avatar-place.png;')}}'"
                                                     alt="{{ $expired_product->product->name }}"/>
                                            </div>
                                        </div>
                                        <div>
                                            <p class="fw500 fsize14 title-txt mb-1">{{ $expired_product->product->name }}</p>
                                            <p class="mb-0 fsize12 body-txt ordered-qty">
                                                <i class="fad fa-tractor fsize16"></i> <b> Farm location: </b>
                                                {{ $expired_product->product->manufacturer_location }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="item-data text-center py-3">
                                        <div class="progress-div">
                                            <div class="progress">
                                                <p class="mb-0 fsize13 text-white">
                                                    Ran out of time
                                                    <span
                                                            class="fsize13 text-white">({{ $expired_product->orderDetails->sum('quantity') }}{{ $expired_product->unit }}
                                                        ordered)</span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    @if (count($expired_product->orders->unique('user_id')) > 0)
                                        <a href="javascript:void(0)" data-toggle="modal"
                                           data-target="#orderListModal_{{ $expired_product->id }}">
                                            <div class="card-members">
                                                <div class="mbr-img pr-3">
                                                    @foreach ($expired_product->orders->unique('user_id') as $i => $order)
                                                        @if ($i < 5)
                                                            <img
                                                                    src="{{ uploaded_asset($order->user->avatar_original) }}"
                                                                    onerror="this.onerror=null;this.src='{{ static_asset('assets/img/avatar-default.webp;') }}'">
                                                        @endif
                                                    @endforeach
                                                </div>
                                                <div class="mbr-cnt pl-2">
                                                    <p class="mb-0 text-primary fsize13">ordered</p>
                                                </div>
                                            </div>
                                        </a>
                                    @endif
                                </div>
                            </div>

                            {{-- Users Order list Modal --}}
                            <div class="modal fade orderListModal"
                                 id="orderListModal_{{ $expired_product->id }}"
                                 tabindex="-1" aria-labelledby="orderListModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Who Have Ordered</h5>
                                            <div class="close-btn text-right">
                                                <a href="javascript:void(0)" class="fw900"
                                                   data-dismiss="modal">X</a>
                                            </div>
                                        </div>
                                        <div class="modal-body">
                                            @foreach ($expired_product->orderDetails as $orderDetail)
                                                <div class="item-details px-sm-3">
                                                    <div class="order-list">
                                                        <div class="item-card p-3 mb-3">
                                                            <div
                                                                    class="d-flex justify-content-between align-items-center">
                                                                <div class="pr-2">
                                                                    <p class="fw600 fsize15 title-txt mb-1">
                                                                        {{ $orderDetail->order->user->name }}</p>
                                                                    <p class="mb-0 lh-17">
                                                                        <span class="fsize13 body-txt ordered-qty">
                                                                            @php
                                                                                $qty_unit = ($orderDetail->quantity * floatval($expired_product->product->min_qty)) . ' ' . $expired_product->product->unit;
                                                                                if($orderDetail->quantity * floatval($expired_product->product->min_qty) < 1){
                                                                                    $qty_unit = (1000 * floatval($expired_product->product->min_qty)) . ' ' . $expired_product->product->secondary_unit;
                                                                                }
                                                                            @endphp
                                                                            {{ $qty_unit }}
                                                                        </span>
                                                                        <span class="fsize13 body-txt ordered-qty">
                                                                            &nbsp;&bull;&nbsp;
                                                                            {{ date('d F, Y H:i', $orderDetail->order->date) }}
                                                                        </span>
                                                                    </p>
                                                                </div>
                                                                <div class="user-img-sm m-0">
                                                                    <img
                                                                            src="{{ uploaded_asset($orderDetail->order->user->avatar_original) }}"
                                                                            onerror="this.onerror=null;this.src='{{ static_asset('assets/img/avatar-default.webp;') }}';">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif

                    @if (count($products_purchase_started) > 0)
                        <div class="container position-relative">
                            <div class="middlesec row">

                                @foreach ($products_purchase_started as $product)
                                    @php
                                        $cart_qty = 0;
                                        if (count($cart) > 0 && isset($cart[$product->id])) {
                                            $cart_qty = $cart[$product->id]['qty'];
                                        }

                                        $product_total = 0;
                                        if (count($cart) > 0 && isset($cart[$product->id])) {
                                            $product_total = $cart[$product->id]['total'];
                                        }

                                        $product_price = $product->price;
                                        if (count($cart) > 0 && isset($cart[$product->id])) {
                                            $product_price = $cart[$product->id]['price'];
                                        }

                                        $qty_unit_main = $product->product->unit;
                                        if (floatval($product->product->min_qty) < 1) {
                                                $qty_unit_main = (1000 * floatval($product->product->min_qty)) . ' ' . $product->product->secondary_unit;
                                        }
                                    @endphp
                                    <div class="col-lg-12 px-0 pb-2 filter {{ $product->product->category->slug }} ">
                                        <!-- Item Cards -->
                                        <div class="mobile_hr_card">
                                            <input type="hidden" id="total_{{ $product->id }}" class="product_total"
                                                   value="{{ $product_total }}">
                                            <input type="hidden" id="secondary_unit_{{ $product->id }}"
                                                   value="{{ $qty_unit_main }}">

                                            <div class="shop-datail">
                                                <div class="shop-datail">
                                                    <div class="mainimg">
                                                        <img src="{{ uploaded_asset($product->product->photos) }}"
                                                             onerror="this.onerror=null;this.src='{{ static_asset('assets/img/avatar-place.png') }}';"
                                                             class="img-fluid" alt="{{ $product->product->name }}">
                                                    </div>
                                                    <div>
                                                        <div>
                                                            <h3>{{ $product->product->name }}</h3>
                                                            <p id="unit_display_{{ $product->id }}">
                                                                {!! single_price_web($product_price) !!}
                                                                / {{ $qty_unit_main }}
                                                            </p>
                                                            @if($product->product->manufacturer_location)
                                                                <p class="font-italic"><b>Farm
                                                                        Location:</b> {{$product->product->manufacturer_location}}
                                                                </p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="countitem">
                                                    <div class="input-group w-auto counterinput">
                                                        <input type="button" value="-"
                                                               class="button-minus   icon-shape icon-sm  lftcount"
                                                               data-field="quantity"
                                                               data-product_id="{{ $product->product->id }}"
                                                               data-product_stock_id="{{ $product->id }}">
                                                        <input type="number" step="1" max="10" value="{{ $cart_qty }}"
                                                               name="quantity" id="quantity"
                                                               class="quantity-field border-0 text-center w-25">
                                                        <input type="button" value="+"
                                                               class="button-plus icon-shape icon-sm lh-0 rgtcount"
                                                               data-field="quantity"
                                                               data-product_id="{{ $product->product->id }}"
                                                               data-product_stock_id="{{ $product->id }}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if (count($products_purchase_expired) == 0 && count($products_purchase_started) == 0)
                        <div class="row pt-5">
                            <div class="col-lg-12 mx-auto">
                                <img src="{{ static_asset('assets/img/product-not-found.jpg') }}"
                                     class="mw-100 mx-auto">
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </div>

        <div class="container">
            <div class="row ">
                <div class="col-12 px-0">
                    <div class="sticky-bottom sticky2">
                        <a href="javascript:void(0)" id="checkout-btn"
                           class="sticky-button-bottom @if($checkout_total == 0) pointer-none @endif"
                           onclick="addProductToCart();">
                            checkout
                            <span id="checkout-amount">( {!! single_price_web($checkout_total) !!} )</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="sticky-stopper"></div>
        <!-- Item Modal -->
        <div class="modal fade itemModal" id="itemModal" data-backdrop="static" tabindex="-1"
             aria-labelledby="itemModalLabel" aria-hidden="true">
        </div>

        <!-- Item Modal -->
        <a href="https://wa.me/917498107182" target="_blank">
            <div class="wp-help-btn flex-acenter-jbtw">
                <span class="fw500"> Help </span> <i class="fab fa-whatsapp"></i>
            </div>
        </a>

    </main>

@endsection

@section('script')

    <script>
        tmpCart = [];

        function addToCart(url) {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                type: 'POST',
                data: {},
                cache: false,
                contentType: false,
                processData: false,
                success: function (response) {
                    $('#itemModal').html('');
                    $('#itemModal').html(response);
                    $('#itemModal').modal('show');
                }
            });
        }

        // Tooltip
        $(function () {
            $('[data-toggle="tooltip"]').tooltip()
        })

        $(document).ready(function () {
            if ($('#cart_data').val()) {
                tmpCart = JSON.parse($('#cart_data').val());
            }

            // Remaining time
            $(".remaining-time").each(function () {
                const currDiv = $(this);

                // Set the date we're counting down to
                var countDownDate = new Date(currDiv.data("time")).getTime();

                // Update the count down every 1 second
                var x = setInterval(function () {
                    // Get today's date and time
                    var now = new Date().getTime();

                    // Find the distance between now and the count down date
                    var distance = countDownDate - now;

                    // Time calculations for days, hours, minutes and seconds
                    var days = Math.floor(distance / (1000 * 60 * 60 * 24));
                    var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 *
                        60));
                    var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    var seconds = Math.floor((distance % (1000 * 60)) / 1000);

                    currDiv.find(".cnt").removeClass("active disabled");
                    currDiv.find(".days").text(days > 0 ? days : "00");
                    currDiv.find(".hours").text(hours > 0 ? hours : "00");
                    currDiv.find(".minutes").text(minutes > 0 ? minutes : "00");
                    currDiv.find(".seconds").text(seconds > 0 ? seconds : "00");

                    if (days > 0) {
                        currDiv.find(".days").parent().addClass("active");
                    } else if (days <= 0 && hours > 0) {
                        currDiv.find(".days").parent().addClass("disabled");
                        currDiv.find(".hours").parent().addClass("active");
                    } else if (days <= 0 && hours <= 0 && minutes > 0) {
                        currDiv.find(".days").parent().addClass("disabled");
                        currDiv.find(".hours").parent().addClass("disabled");
                        currDiv.find(".minutes").parent().addClass("active");
                    } else if (days <= 0 && hours <= 0 && minutes > 0) {
                        currDiv.find(".days").parent().addClass("disabled");
                        currDiv.find(".hours").parent().addClass("disabled");
                        currDiv.find(".minutes").parent().addClass("disabled");
                        currDiv.find(".seconds").parent().addClass("active");
                    } else {
                        currDiv.find(".cnt").addClass("disabled");
                    }

                    $(".preloader_div").hide();
                    $(".remaining-time").show();
                }, 1000);
            });

            $(".progress-bar").each(function () {
                let width = 0;
                let progressCnt = 0;
                let target = $(this).data("target");
                let unit = $(this).data("unit");
                let progress = $(this).data("progress");

                let progressComplete = parseInt((progress * 100) / target);

                const count = setInterval(() => {
                    if (width != progressComplete) {
                        width++;
                        progressCnt++;
                        $(this).css("opacity", "1");
                        (width <= 100) ? $(this).css("width", width + "%") : '';
                        $(this).text(progress + ' ' + unit);
                        /*if (progressCnt <= progress) {
                            $(this).text(progress + ' ' + unit);
                        }*/
                    } else {
                        clearInterval(count);
                    }
                }, 15);
            });

            $(".filter-button").click(function () {
                $('.filter-button').removeClass('active_filter');
                $(this).addClass('active_filter');
                var value = $(this).attr('data-filter');

                if (value == "all") {
                    //$('.filter').removeClass('hidden');
                    $('.filter').show();
                } else {
//            $('.filter[filter-item="'+value+'"]').removeClass('hidden');
//            $(".filter").not('.filter[filter-item="'+value+'"]').addClass('hidden');
                    $(".filter").not('.' + value).hide();
                    $('.filter').filter('.' + value).show();
                }
            });

            $('.input-group').on('click', '.button-plus', function (e) {
                incrementValue(e);
            });

            $('.input-group').on('click', '.button-minus', function (e) {
                decrementValue(e);
            });

            // Sticky Bottom
            var inner = $(".sticky-bottom");
            var elementPosTop = inner.position().top;
            var viewportHeight = $(window).height();
            $(window).on('scroll', function () {
                var scrollPos = $(window).scrollTop();
                var elementFromTop = elementPosTop - scrollPos;
                var bgcreheight = $('.middlesec').height();
                bgcreheight = bgcreheight - 500;

                if (bgcreheight >= scrollPos) {
                    inner.addClass("sticky2");
                } else {
                    inner.removeClass("sticky2");
                }
            });

        });

        $(window).scroll(function () {
            if ($(this).scrollTop() > 50) {
                $('.srch-fltr-card').addClass('newClass');
            } else {
                $('.srch-fltr-card').removeClass('newClass');
            }
        });

        function incrementValue(e) {
            e.preventDefault();
            var fieldName = $(e.target).data('field');
            var productId = $(e.target).data('product_id');
            var productStockId = $(e.target).data('product_stock_id');
            var parent = $(e.target).closest('div');
            var currentVal = parseInt(parent.find('input[name=' + fieldName + ']').val(), 10);
            var qty = 0;

            if (!isNaN(currentVal)) {
                qty = currentVal + 1;
                parent.find('input[name=' + fieldName + ']').val(currentVal + 1);
            } else {
                parent.find('input[name=' + fieldName + ']').val(0);
            }

            getVariantPrice(qty, productId, productStockId);
        }

        function decrementValue(e) {
            e.preventDefault();
            var fieldName = $(e.target).data('field');
            var productId = $(e.target).data('product_id');
            var productStockId = $(e.target).data('product_stock_id');
            var parent = $(e.target).closest('div');
            var currentVal = parseInt(parent.find('input[name=' + fieldName + ']').val(), 10);
            var qty = 0;

            if (!isNaN(currentVal) && currentVal > 0) {
                qty = currentVal - 1;
                parent.find('input[name=' + fieldName + ']').val(currentVal - 1);
            } else {
                parent.find('input[name=' + fieldName + ']').val(0);
            }

            getVariantPrice(qty, productId, productStockId);
        }

        function getVariantPrice(qty, productId, productStockId) {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: '{{ route('products.variant_price') }}',
                data: {id: productId, stock_id: productStockId, quantity: qty},
                success: function (data) {
                    if (data.total_price != '') {
                        $('#total_' + productStockId).val(data.total_price);
                    } else {
                        $('#total_' + productStockId).val(0);
                    }
                    $('#unit_display_' + productStockId).html('');
                    $('#unit_display_' + productStockId).html(data.unit_price + ' / ' + $('#secondary_unit_' + productStockId).val());

                    calculateCheckOutTotal(qty, productId, productStockId);
                }
            });
        }

        function calculateCheckOutTotal(qty, productId, productStockId) {
            let checkoutTotal = 0.00;
            $('.product_total').each(function (k, value) {
                checkoutTotal = (parseFloat($(value).val()) + parseFloat(checkoutTotal)).toFixed(2);
            });
            if (checkoutTotal > 0) {
                $('#checkout-btn').removeClass('pointer-none');
            } else {
                $('#checkout-btn').addClass('pointer-none');
            }
            $('#checkout-amount').html('');
            $('#checkout-amount').html('( <ins class="currency-symbol">₹</ins> ' + checkoutTotal + ' )');

            if (checkoutTotal > 0) {
                tmpCart[productStockId] = {'qty': qty, 'product_id': productId, 'product_stock_id': productStockId};
            } else {
                tmpCart = [];
            }
        }

        function addProductToCart() {
            tmpCart = $.map(tmpCart, function (value, index) {
                return [value];
            });

            let cartData = [];
            $(tmpCart).each(function (k, tmp) {
                if (tmp) {
                    cartData.push(tmp);
                }
            });

            if (cartData.length > 0) {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '{{ route('cart.bulkAddToCart') }}',
                    data: {data: cartData},
                    success: function (data) {
                        if (data.status == 1) {
                            window.location.replace("{{ route('cart') }}");
                        } else {
                            AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                        }
                    }
                });
            }
        }

    </script>
@endsection
