@extends('frontend.layouts.app', ['new_header' => false, 'header_show' => true, 'header2' => false, 'footer' => true, 'new_footer' => false])

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
    <meta property="og:title" content="{{ $meta_title }}" />
    <meta property="og:description" content="{{ $meta_description }}" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.0/font/bootstrap-icons.css">
@endsection

@section('content')

    <main class="main-tag promain">
        <div class="breadcrumbs high">
            <div class="container">
                <h5 class="mb-0 fw700 text-white text-uppercase">{{ translate('Products') }}</h5>
            </div>
        </div>
        <div class="content  bgcream-product ">
            <div class="container px-0">
                <div class="row justify-content-center ">
                    <input type="hidden" id="cart_data" value="{{ json_encode($cart) }}">

                    @if ($categories)

                        <section class="lodha_nestedtab">
                            <div class="container">
                                <!-- Tabs -->
                                <div class="hedtab mb-3 trnsn-300-sm">
                                    <div class="search-lg">
                                        {{-- <h4 class="fw700 title-txt ">Our
                                            <ins class="primary-color fw700">Products</ins>
                                        </h4> --}}
                                        <input type="text" name="searchProduct" id="searchProduct" placeholder="Search.." class="form-control" onkeyup="findProduct(this)">
                                    </div>
                                    <ul class="nav nav-tabs" id="tabs">
                                        <li><a href="#" data-toggle="tab" data-filter="all" class="filter-button active"
                                                onclick="filterCategory($(this))">All</a></li>
                                        @php $i=0; @endphp
                                        @foreach ($categories as $key => $cat)
                                            @if ($cat['name'] != 'Flowers')
                                                <li>
                                                    <a href="#" data-toggle="tab" data-filter="{{ $cat['filter'] }}"
                                                        class="filter-button"
                                                        onclick="filterCategory($(this))">
                                                        {{ $cat['name'] }}
                                                    </a>
                                                </li>
                                            @endif
                                            @php $i++; @endphp
                                        @endforeach
                                    </ul>
                                    <div class="search-sm pt-3">
                                        <input type="text" name="searchProduct" id="searchProduct" placeholder="Search.." class="form-control" onkeyup="findProduct(this)">
                                    </div>
                                    <!-- Tabs -->
                                </div>

                                @if (count($all_products) > 0)
                                    <div class="row pb-4 onFx-pd" style="padding-top: 28px">
                                        @foreach ($all_products as $product)
                                            @if ($product->product->category->slug != 'flowers')
                                                @php
                                                    $cart_qty = 0;
                                                    $cart_id = 0;
                                                    if (count($cart) > 0 && isset($cart[$product->id])) {
                                                        $cart_qty = $cart[$product->id]['qty'];
                                                        $cart_id = $cart[$product->id]['cart_id'];
                                                    }
                                                    $addCartQty = $cart_qty + 1;

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
                                                        $qty_unit_main = 1000 * floatval($product->product->min_qty) . ' ' . $product->product->secondary_unit;
                                                    }
                                                @endphp
                                                <div class="col-lg-6 filter pb-4 {{ $product->product->category->slug }} searchCard">
                                                    <div class="searchText" style="display: none">{{ strtolower($product->product->name) }}</div>
                                                    <div class="tab_horizontal_card p-3 m-0 position-relative">
                                                        @if ($product->product->todays_deal == 1)
                                                            <div class="deal-type">Flat {{ $product->product->discount }}%
                                                                Off</div>
                                                        @endif
                                                        <div class="tab_hori_inr">
                                                            <div>
                                                                <img src="{{ uploaded_asset($product->product->photos) }}"
                                                                    onerror="this.onerror=null;this.src='{{ static_asset('assets/img/no-image-found.jpg') }}';"
                                                                    class="img-rounded top_img"
                                                                    alt="{{ $product->product->name }}">
                                                            </div>
                                                            <div class="  tab_horizontal_card_detail  ">
                                                                @if ($product->product->manufacturer_location)
                                                                    <span>{{ $product->product->manufacturer_location }}</span>
                                                                @endif
                                                                <p class="titlecard">
                                                                    {{ $product->product->name }} </p>
                                                                @if ($product->product->todays_deal == 1)
                                                                    <p class="prd-disc-pricing mb-1">
                                                                        <s>{!! single_price_web($product_price) !!} /
                                                                            {{ $qty_unit_main }}</s>
                                                                    </p>
                                                                    <p class="price">
                                                                        {!! single_price_web($product_price - round(($product_price * $product->product->discount) / 100, 2)) !!} /
                                                                        {{ $qty_unit_main }}
                                                                    </p>
                                                                @else
                                                                    <p class="price">{!! single_price_web($product_price) !!} /
                                                                        {{ $qty_unit_main }}</p>
                                                                @endif
                                                                <div class="cartbtn">
                                                                    <img src="./public/assets/img/carts.svg" class=" cart"
                                                                        alt="cart">
                                                                    <a href="javacript:;" class="cartbtn"> Add
                                                                        to Cart</a>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="countitem">
                                                            <div class="input-group w-auto counterinput">
                                                                <input type="button" value="-"
                                                                    class="button-minus  icon-shape icon-sm lftcount"
                                                                    data-field="quantity"
                                                                    data-product_id="{{ $product->product->id }}"
                                                                    data-product_stock_id="{{ $product->id }}"
                                                                    data-cart_id="{{ $cart_id }}"
                                                                    onclick="decrementValue($(this))">
                                                                <input type="number" step="1" min="0"
                                                                    max="10" value="{{ $cart_qty }}"
                                                                    name="quantity" id="quantity"
                                                                    class="quantity-field border-0 text-center w-25">
                                                                <input type="button" value="+"
                                                                    class="button-plus icon-shape icon-sm lh-0 rgtcount"
                                                                    data-field="quantity"
                                                                    data-product_id="{{ $product->product->id }}"
                                                                    data-product_stock_id="{{ $product->id }}"
                                                                    onclick="incrementValue($(this))">
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>
                                            @endif
                                        @endforeach

                                        {{-- <div class="col-md-12">
                                            <a href="#" class="loadbtn">
                                                <img src="./public/assets/img/Vector.png"
                                                    class="proces mr-2" alt="proces">
                                                Load More
                                            </a>
                                        </div> --}}
                                    </div>

                                    <div class="row pt-5" id="noProductFoundFilter" style="display: none;">
                                        <div class="col-lg-6 mx-auto">
                                            <img src="{{ static_asset('assets/img/product-not-found.jpg') }}"
                                                class="mw-100 mx-auto">
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </section>
                    @endif

                    @if (count($all_products) == 0)
                        <div class="row pt-5">
                            <div class="col-lg-12 mx-auto">
                                <img src="{{ static_asset('assets/img/product-not-found.jpg') }}" class="mw-100 mx-auto">
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </div>

        {{-- <div class="container">
            <div class="row ">
                <div class="col-12 px-0">
                    <div class="sticky-bottom">
                        <a href="javascript:void(0)" id="checkout-btn"
                            class="sticky-button-bottom my-lg-4 @if ($checkout_total == 0) pointer-none @endif"
                            onclick="addProductToCart();">
                            checkout
                            <span id="checkout-amount">( {!! single_price_web($checkout_total) !!} )</span>
                        </a>
                    </div>
                </div>
            </div>
        </div> --}}

        <div class="sticky-stopper"></div>

        {{-- <div class="modal fade itemModal" id="itemModal" data-backdrop="static" tabindex="-1"
            aria-labelledby="itemModalLabel" aria-hidden="true"></div> --}}


        <a href="https://wa.me/917498107182" target="_blank">
            <div class="wp-help-btn flex-acenter-jbtw">
                <span class="fw500"> Help </span> <i class="fab fa-whatsapp"></i>
            </div>
        </a>

    </main>

@endsection

@section('script')
    <script>
        let tmpCart = [];

        // Tooltip
        $(function() {
            $('[data-toggle="tooltip"]').tooltip()
        })

        $(document).ready(function() {
            $('#noProductFoundFilter').hide();

            if ($('#cart_data').val()) {
                tmpCart = JSON.parse($('#cart_data').val());
            }

            // Remaining time
            $(".remaining-time").each(function() {
                const currDiv = $(this);

                // Set the date we're counting down to
                var countDownDate = new Date(currDiv.data("time")).getTime();

                // Update the count down every 1 second
                var x = setInterval(function() {
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

            $(".progress-bar").each(function() {
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
                        (width <= 100) ? $(this).css("width", width + "%"): '';
                        $(this).text(progress + ' ' + unit);
                    } else {
                        clearInterval(count);
                    }
                }, 15);
            });

            // $(".active").click();
        });

        var filterTabWidth = $('.hedtab').outerWidth();
        var filterTabHeight = $('.hedtab').outerHeight();

        $(document).scroll(function() {
            let scroll = $(this).scrollTop();
            let header = $("header");
            let filterTab = $('.hedtab');

            if (scroll >= 0.1) {
                header.addClass("fixedHeader");
                $('.main-tag').css('padding-top', header.height())

            } else {
                header.removeClass("fixedHeader");
                $('.main-tag').css('padding-top', 0)
            }

            if ($(document).width() >= 768) {
                if (scroll >= 102) {
                    if (!filterTab.hasClass('fixed-div')) {
                        filterTab.addClass('fixed-div')
                        filterTab.css({
                            top: 136,
                            width: filterTabWidth
                        })

                        $('.onFx-pd').css('padding-top', (filterTabHeight + 6))
                    } else {
                        return false
                    }
                } else {
                    filterTab.removeClass('fixed-div')
                    filterTab.css({
                        top: 'inherit',
                        width: "100%"
                    })
                    $('.onFx-pd').css('padding-top', 28)
                }
            } else {
                if (scroll >= 146) {
                    if (!filterTab.hasClass('fixed-div')) {
                        filterTab.addClass('fixed-div')
                        filterTab.css({
                            top: 90,
                            width: "100%",
                            left: 0
                        })

                        $('.onFx-pd').css('padding-top', (filterTabHeight + 4))

                    } else {
                        return false
                    }
                } else {
                    filterTab.removeClass('fixed-div')
                    filterTab.css({
                        top: 'inherit',
                        width: "100%",
                        left: 'inherit'
                    })
                    $('.onFx-pd').css('padding-top', 28)
                }
            }

            // ---------------------------------------
            if (scroll > 50) {
                $('.srch-fltr-card').addClass('newClass');
            } else {
                $('.srch-fltr-card').removeClass('newClass');
            }
        });

        $(window).on("load", function() {
            $(".alltabs").clone().prependTo(".rightDiv");
        });

        $(".tabdots2").mouseover(function() {
            old_src = $(this).attr("./public/assets/img/inrtab2dot.svg");
            $(this).attr("src", "./public/assets/img/dot2.svg");
        }).mouseout(function() {
            $(this).attr("src", "./public/assets/img/inrtab2dot.svg");
        });

        $(".tabdots1").mouseover(function() {
            old_src = $(this).attr("./public/assets/img/dots2hvr.png");
            $(this).attr("src", "./public/assets/img/inrtab1dot.svg.");
        }).mouseout(function() {
            $(this).attr("src", "./public/assets/img/dots2hvr.png");
        });

        function filterCategory(obj) {
            $('#noProductFoundFilter').hide();
            $(obj).removeClass('active_filter');
            $(obj).addClass('active_filter');
            let value = $(obj).attr('data-filter');

            if (value == "all") {
                $('.filter').show();
            } else {
                $(".filter").not('.' + value).hide();
                $('.filter').filter('.' + value).show();
                if (document.querySelectorAll(".filter " + value).length == 0) {
                    $('#noProductFoundFilter').show();
                } else {
                    $('#noProductFoundFilter').hide();
                }
            }
        }

        function incrementValue(obj) {
            let fieldName = $(obj).data('field');
            let productId = $(obj).data('product_id');
            let productStockId = $(obj).data('product_stock_id');
            let parent = $(obj).closest('div');
            let currentVal = parseInt(parent.find('input[name=' + fieldName + ']').val(), 10);
            let qty = 0;

            if (!isNaN(currentVal)) {
                qty = currentVal + 1;
                parent.find('input[name=' + fieldName + ']').val(currentVal + 1);
            } else {
                parent.find('input[name=' + fieldName + ']').val(0);
            }

            addToCart(productId, productStockId, qty);
        }

        function decrementValue(obj) {
            let fieldName = $(obj).data('field');
            let productId = $(obj).data('product_id');
            let productStockId = $(obj).data('product_stock_id');
            let cartId = $(obj).data('cart_id');
            let parent = $(obj).closest('div');
            let currentVal = parseInt(parent.find('input[name=' + fieldName + ']').val(), 10);
            let qty = 0;

            if (!isNaN(currentVal) && currentVal > 0) {
                qty = currentVal - 1;
                parent.find('input[name=' + fieldName + ']').val(currentVal - 1);
            } else {
                parent.find('input[name=' + fieldName + ']').val(0);
            }

            if (qty > 0) {
                addToCart(productId, productStockId, qty);
            } else {
                removeFromCart(cartId, productId, productStockId);
            }
        }

        function removeFromCart(cartId, productId, productStockId) {
            $.post('{{ route('cart.removeFromCart') }}', {
                _token: AIZ.data.csrf,
                id: cartId,
                product_id: productId,
                product_stock_id: productStockId
            }, function(data) {
                updateNavCart(data.cart_count);
                // $('#cart_summary').html(data.cart_view);
                AIZ.plugins.notify('success', "{{ translate('Item has been removed from cart') }}");
            });
        }

        function getVariantPrice(qty, productId, productStockId) {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: '{{ route('products.variant_price') }}',
                data: {
                    id: productId,
                    stock_id: productStockId,
                    quantity: qty
                },
                success: function(data) {
                    if (data.total_price != '') {
                        $('#total_' + productStockId).val(data.total_price);
                    } else {
                        $('#total_' + productStockId).val(0);
                    }
                    $('#unit_display_' + productStockId).html('');
                    $('#unit_display_' + productStockId).html(data.unit_price + ' / ' + $(
                        '#secondary_unit_' +
                        productStockId).val());

                    calculateCheckOutTotal(qty, productId, productStockId);
                }
            });
        }

        function calculateCheckOutTotal(qty, productId, productStockId) {
            let checkoutTotal = 0.00;
            $('.product_total').each(function(k, value) {
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
                tmpCart[productStockId] = {
                    'qty': qty,
                    'product_id': productId,
                    'product_stock_id': productStockId
                };
            } else {
                tmpCart = [];
            }
        }

        function addProductToCart() {
            tmpCart = $.map(tmpCart, function(value, index) {
                return [value];
            });

            let cartData = [];
            $(tmpCart).each(function(k, tmp) {
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
                    data: {
                        data: cartData
                    },
                    success: function(data) {
                        if (data.status == 1) {
                            window.location.replace("{{ route('cart') }}");
                        } else {
                            AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                        }
                    }
                });
            }
        }

        function findProduct(text) {
            $('.searchCard').hide()
            let searchText = $(text).val().toLowerCase()
            $('.searchText:contains("' + searchText + '")').parent().show()
        }
    </script>

@endsection
