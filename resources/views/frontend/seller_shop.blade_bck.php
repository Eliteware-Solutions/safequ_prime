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

<main class="main-tag mt-0">

    <div class="breadcrumbs high">
        <div class="container">
            <h5 class="mb-0 fw700 text-white text-uppercase">Your Community - {{ $shop->name }}</h5>
        </div>
    </div>

    <!-- Cards -->
    <div class="content pb-5">
        <div class="container">
            <div class="row justify-content-center">

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
                <div class="col-lg-4 col-md-6 col-sm-8 px-2 pb-4 timeout-card filter {{ $expired_product->product->category->slug }} ">
                    <!-- Item Cards -->
                    <div class="item-card p-3">

                        <div class="d-flex justify-content-start align-items-center">
                            <div class="img-name pr-2">
                                <div class="item-img item-img-sm text-center">
                                    <img src="{{ uploaded_asset($expired_product->product->photos) }}"
                                         onerror="this.onerror=null;this.src='{{ static_asset('assets/img/avatar-place.png') }}';"
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
                                    <img src="{{ uploaded_asset($order->user->avatar_original) }}"
                                         onerror="this.onerror=null;this.src='{{ static_asset('assets/img/avatar-default.webp') }}';">
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
                                            <div class="d-flex justify-content-between align-items-center">
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
                                                    <img src="{{ uploaded_asset($orderDetail->order->user->avatar_original) }}"
                                                         onerror="this.onerror=null;this.src='{{ static_asset('assets/img/avatar-default.webp') }}';">
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
                @foreach ($products_purchase_started as $product)
                @php
                $qty_unit_main = $product->product->unit;
                if (floatval($product->product->min_qty) < 1) {
                $qty_unit_main = (1000 * floatval($product->product->min_qty)) . ' ' . $product->product->secondary_unit;
                }
                @endphp
                <div class="col-lg-4 col-md-6 col-sm-8 px-2 pb-4 filter {{ $product->product->category->slug }} ">
                    <!-- Item Cards -->
                    <div class="item-card">
                        <div class="card-top">
                            <div class="pricing text-center">
                                <span class="text-white">Price / {{ $qty_unit_main }}</span>
                                <h6 class="mb-0 mt-2 mx-auto">
                                    {!! single_price_web($product->price) !!}
                                </h6>
                            </div>
                            <div class="item-img text-center">
                                <img src="{{ uploaded_asset($product->product->photos) }}"
                                     onerror="this.onerror=null;this.src='{{ static_asset('assets/img/avatar-place.png') }}';"
                                     alt="{{ $product->product->name }}"/>
                            </div>
                            <div class="nxt-delivery">
                                <span class="text-white">Next Delivery</span>
                                <h6 class="mb-0 mt-2 text-center mx-auto">
                                    {{ date('d', strtotime($product->purchase_end_date. '+' . intval($product->est_shipping_days) . ' days')) }}
                                    <br>
                                    <ins>{{ date('M', strtotime($product->purchase_end_date. '+' . intval($product->est_shipping_days) . ' days')) }}</ins>
                                </h6>
                            </div>
                        </div>
                        <div class="item-data text-center pt-5 px-3">
                            <div class="px-2">
                                <h6 class="pt-1 fw700 mb-1">{{ $product->product->name }}</h6>
                                <p class="fw600f fsize13 body-txt mb-2">
                                    Variety: {{ $product->product->variation }}</p>
                                {{-- <p class="rating-stars"> --}}
                                    {{-- {{ renderStarRating($product->rating) }} --}}
                                    {{-- </p> --}}
                                <p class="body-txt fsize13 font-italic pb-1">
                                    <i class="fad fa-tractor fsize16"></i> <b> Farm location: </b>
                                    {{ $product->product->manufacturer_location }}
                                </p>
                                <hr>
                            </div>
                            <div style="display: none;">
                                <p class="fw700 px-2">Time Remaining</p>

                                <!-- Preloader -->
                                <div class="flex-astart-jcenter preloader_div pb-2 px-2">
                                    @for ($i = 0; $i < 4; $i++)
                                    <div>
                                        <div class="timing mb-0">
                                            <div>
                                                <h3 class="mb-0"></h3>
                                            </div>
                                        </div>
                                        <p class="mb-0"></p>
                                    </div>
                                    @endfor
                                </div>
                                <!-- Preloader -->

                                <div class="remaining-time pb-2 px-2"
                                     data-time="{{ date('m-d-Y H:i:s', strtotime($product->purchase_end_date)) }}"
                                     style="display: none">
                                    <div class="timing">
                                        <div class="cnt">
                                            <h3 class="mb-0 days ">00</h3>
                                        </div>
                                        <span>Days</span>
                                    </div>
                                    <div class="timing">
                                        <div class="cnt">
                                            <h3 class="mb-0 hours">00</h3>
                                        </div>
                                        <span>Hours</span>
                                    </div>
                                    <div class="timing">
                                        <div class="cnt">
                                            <h3 class="mb-0 minutes">00</h3>
                                        </div>
                                        <span>Minutes</span>
                                    </div>
                                    <div class="timing">
                                        <div class="cnt">
                                            <h3 class="mb-0 seconds">00</h3>
                                        </div>
                                        <span>Seconds</span>
                                    </div>
                                </div>

                                <div class="order-progress text-center pt-3 px-2">
                                    <p class="fw600 target-qty">Available Harvest: {{ $product->qty }}
                                        {{ $product->product->unit }}&nbsp;
                                        <a href="javascript:void(0)" data-toggle="tooltip"
                                           data-placement="top"
                                           title="Unlock special community benefits when the available harvest is booked out by your community.">
                                            <i class="fad fa-info-circle animated faa-tada align-middle"></i>
                                        </a>
                                    </p>
                                </div>
                                <div class="progress-div mb-4">
                                    <div class="progress">
                                        <div class="progress-bar" data-target="{{ $product->qty }}"
                                             data-progress="{{ $product->orderDetails->sum('quantity')*$product->product->min_qty }}"
                                             data-unit="{{ $product->product->unit }}">
                                        </div>
                                    </div>
                                </div>

                                @if (count($product->orders->where('payment_status','paid')->unique('user_id')) > 0)
                                <a href="javascript:void(0)" data-toggle="modal"
                                   data-target="#orderListModal_{{ $product->id }}">
                                    <div class="card-members pb-3">
                                        <div class="mbr-img pr-3">
                                            @foreach ($product->product->orders->where('payment_status','paid')->unique('user_id') as $i => $order)
                                            @if ($i < 5)
                                            <img src="{{ uploaded_asset($order->user->avatar_original) }}"
                                                 onerror="this.onerror=null;this.src='{{ static_asset('assets/img/avatar-default.webp') }}';">
                                            @endif
                                            @endforeach
                                        </div>
                                        <div class="mbr-cnt pl-2">
                                            <p class="mb-0 text-primary fsize13">have already
                                                ordered</p>
                                        </div>
                                    </div>
                                </a>
                                @endif
                            </div>
                            @if ($product->product->tags)
                            @php
                            $tagsAry = explode(',', $product->product->tags);
                            $cnt = count($tagsAry);
                            @endphp

                            <ul class="item-tags pb-3 mb-0 flex-acenter-jbtw">
                                @foreach ($tagsAry as $tag)
                                <li class="fsize13 {{$cnt > 1 ? 'mr-1': ''}}">
                                    <i class="fas fsize15 fa-check-circle mr-1"></i> {{ $tag }}
                                </li>
                                @endforeach
                            </ul>
                            @endif

                        </div>
                        <div class="card-bottom">
                            <button class="btn text-uppercase text-white fw600 w-100"
                                    onclick="addToCart('{{ route('products-details', $product    ->id) }}');">
                                <i class="fas fa-shopping-cart text-white fsize18"></i>
                                &nbsp; Add to cart
                            </button>
                        </div>
                    </div>
                </div>

                @if (count($product->orders->unique('user_id')) > 0)
                {{-- Users Order list Modal --}}
                <div class="modal fade orderListModal" id="orderListModal_{{ $product->id }}"
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
                                @foreach ($product->orderDetails as $orderDetail)
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
                                                                                    $qty_unit = ($orderDetail->quantity * floatval($product->product->min_qty)) . ' ' . $product->product->unit;
                                                                                    if($orderDetail->quantity * floatval($product->product->min_qty) < 1){
                                                                                        $qty_unit = (1000 * floatval($product->product->min_qty)) . ' ' . $product->product->secondary_unit;
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
                                                    <img src="{{ uploaded_asset($orderDetail->order->user->avatar_original) }}"
                                                         onerror="this.onerror=null;this.src='{{ static_asset('assets/img/avatar-default.webp') }}';">
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
                @endif
                @endforeach
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

    })
</script>

@endsection
