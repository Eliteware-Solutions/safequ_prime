@extends('frontend.layouts.app', ['new_header' => true, 'header_show' => false, 'header2' => false, 'footer' => false, 'new_footer' => true])

@php

    // For Slider content
    $bannerJsn = File::get(storage_path('frontend_json/home-banner.json'));
    $sliders = json_decode($bannerJsn, true);

    // For Customer Reviews
    $customerReviewJson = File::get(storage_path('frontend_json/customer-reviews.json'));
    $customer_reviews = json_decode($customerReviewJson, true);

    // For Farmer Reviews
    $farmerReviewJson = File::get(storage_path('frontend_json/farmer-reviews.json'));
    $farmer_reviews = json_decode($farmerReviewJson, true);

    // For Recycling points
    $recycleJson = File::get(storage_path('frontend_json/recycling.json'));
    $recyclePoints = json_decode($recycleJson, true);

    $whatsAppNo = '917498107182';
    $whatsAppMessage = 'Hey there, I want to create new community for my area.';
@endphp

@section('content')
    <!-- Navigation -->
    <header class="header new-ui">
        <div class="offer-top-bar">
            <div class="container">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center justify-content-start">
                        <p class="offer-text fw500 tiny-text text-white mb-0">Get flat 50%* off up to Rs.100 on your first
                            order on the app. &nbsp;
                            <span>Use Code: Safe2023</span>
                        </p>
                        <button class="btn btn-primary-fill installPWABtn" id="installPWA"
                            style="display: none">Download</button>
                    </div>
                    <div class="top-bar-location">
                        <a href="javascript:void(0);" id="detect-location"
                            class="tiny-text secondary-text detct-loc d-flex align-items-center justify-content-between mb-0">
                            Detect Location
                            <img src="{{ static_asset('assets/img/new-design/dwn-arw.svg') }}" class="injectable"
                                alt="Down Arrow">
                        </a>
                        <a href="javascript:void(0);" id="header-location-name"
                            class="tiny-text secondary-text detct-loc display-none align-items-center justify-content-end mb-0">
                            <img src="{{ static_asset('assets/img/new-design/map-pin.svg') }}" class="injectable mr-2"
                                alt="Down Arrow">
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <nav class="d-flex align-items-center justify-content-between container">
            <div class="d-flex align-items-center fix-part">
                <a href="{{ route('home') }}" class="nav-logo mr-3" aria-label="SafeQu" title="SafeQu">
                    <img src="{{ static_asset('assets/img/new-design/safequ-logo.png') }}" width="260" height="123"
                        alt="SafeQU">
                </a>
                <p class="tiny-text secondary-text mb-0">
                    Honest, Fresh Produce. Delivered to your doorstep.
                </p>
            </div>
            <div class="collapsible d-flex align-items-center justify-content-between trnsn-300ms">


                <!-- For Mobile design -->
                <div class="hidden-top-nav d-flex align-items-center">
                    <div class="d-flex align-items-center">
                        <a href="{{ route('home') }}" class="nav-logo mr-3" aria-label="SafeQu" title="SafeQu">
                            <img src="{{ static_asset('assets/img/new-design/safequ-logo.png') }}" width="260" height="123"
                                alt="SafeQU">
                        </a>
                        <p class="tiny-text secondary-text mb-0">
                            Honest, Fresh Produce. Delivered to your doorstep.
                        </p>
                    </div>

                    <div class="menu-toggle">
                        <img src="{{ static_asset('assets/img/new-design/menu.svg') }}" width="150" height="150"
                            class="injectable" alt="Menu icon">
                    </div>
                </div>

                <ul class="nav-menu ml-lg-auto mb-0 d-flex align-items-center">
                    <li><a href="{{ route('home') }}" class="nav-link fw500 active py-1 px-2 mr-2">Home</a></li>
                    <li><a href="{{ route('shop.visit') }}" class="nav-link fw500 py-1 px-2 mr-2">Products</a></li>
                </ul>

                <div class="nav-icons d-flex align-items-center">
                    <a href="{{ route('user.login') }}" aria-label="Login" title="Login">
                        <img src="{{ static_asset('assets/img/new-design/user-icon-primary.svg') }}"
                            class="injectable ml-2 mr-2 rounded-circle trnsn-300ms" alt="User Icon">
                    </a>
                    <a href="{{ route('cart') }}" aria-label="Cart" title="Cart" class="cart-icon-org position-relative">
                        <img src="{{ static_asset('assets/img/new-design/btn-cart-primary.svg') }}"
                            class="injectable rounded-circle trnsn-300ms" alt="Cart Icon">
                        <span class="rounded-circle trnsn-300ms cart-item-count" style="display: none;"></span>
                    </a>
                </div>
            </div>

            <div class="menu-toggle display-none">
                <img src="{{ static_asset('assets/img/new-design/menu.svg') }}" width="150" height="150"
                    class="injectable" alt="Menu icon">
            </div>
        </nav>
    </header>

    <main class="main-tg-index">
        <input type="hidden" id="local_shop_id" name="local_shop_id" value="{{ intval($local_shop_id) }}">

        <!-- Hero Slider -->
        <section class="hero-sec">
            <div class="banner-location py-3 mb-4 text-left">
                <div class="container">
                    <a href="javascript:void(0);" id="detect-location2"
                        class="tiny-text secondary-text detct-loc d-block align-items-center mb-0 fw500 pl-2">
                        Detect Location<img src="{{ static_asset('assets/img/new-design/dwn-arw.svg') }}"
                            class="injectable" alt="Down Arrow">
                    </a>
                    <a href="javascript:void(0);" id="header-location-name2"
                        class="tiny-text secondary-text detct-loc display-none align-items-center mb-0 fw500">
                        <img src="{{ static_asset('assets/img/new-design/map-pin.svg') }}" class="injectable mr-2"
                            alt="Down Arrow"></a>
                </div>
            </div>

            <div class="owl-carousel owl-theme hero-slider">
                <div class="item p-0">

                    <div class="d-flex carousel-item active position-relative py-5 fixed-hero-banner">
                        <img src="{{ static_asset('assets/img/new-design/hero-bg-3.webp') }}" class="banner b-rd-20"
                            alt="Hero Image">

                        <div class="container bg-sm-white position-relative b-rd-20 py-lg-5 py-3">
                            <div class="row position-relative align-items-center h-100 py-lg-5 p-2">
                                <div class="col">
                                    <h1 class="hero-title fw700 mb-2">Farm Fresh Exotic Fruits and Vegetables
                                    </h1>

                                    <h2 class="hero-subtitle fw600 primary-text mb-2 pb-1">30% Cheaper</h2>

                                    <p class="fw500 mb-4">Fresh exotic fruits and vegetables like strawberries,
                                        avocados & spinach delivered to your doorstep, DIRECTLY from your choice of local
                                        farms. ~30% cheaper than those expensive halls of food or baskets of nature.
                                        <i class="fas smiley align-middle fa-smile primary-text"
                                            style="font-size: 1.5rem"></i>
                                    </p>

                                    <a href="#filters" class="btn scrollTo primary-btn py-2 px-4">
                                        Buy Now
                                    </a>
                                </div>
                            </div>

                        </div>

                    </div>
                </div>
                {{-- @foreach ($sliders as $sl)
                    <div class="item p-0">
                        <div class="d-flex carousel-item position-relative active py-5">
                            <img src="{{ static_asset('assets/img/new-design/' . $sl['bg_image']) }}"
                                class="banner b-rd-20" alt="Hero Image">

                            <div class="container">
                                <div class="row position-relative align-items-center h-100">
                                    <div class="col-lg-6">
                                        <h1 class="text-white mb-4 pb-1">{{ $sl['prd_name'] }}</h1>

                                        <div class="prd-desc d-flex mb-4">
                                            @foreach ($sl['metas'] as $meta)
                                                <div class="d-flex align-items-center mr-md-4">
                                                    <img src="{{ static_asset('assets/img/new-design/' . $meta['icon']) }}"
                                                        class="injectable" width="72" height="72"
                                                        alt="Hero Icon">
                                                    <span class="text-white ml-3">{{ $meta['text'] }}</span>
                                                </div>
                                            @endforeach
                                        </div>

                                        <button class="btn btn-fill-white mt-2"
                                            onclick="addToCart({{ $sl['prd_id'] . ',' . $sl['prd_stock_id'] . ', 1' }});">
                                            <img src="{{ static_asset('assets/img/new-design/btn-cart.svg') }}"
                                                onload="SVGInject(this)" alt="Btn Cart">
                                            Add to cart
                                        </button>
                                    </div>

                                    <div class="col-lg-5 offset-lg-1">
                                        <img src="{{ static_asset('assets/img/new-design/' . $sl['prd_image']) }}"
                                            class="crousel-img" width="569" height="406" alt="Hero Image">
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                @endforeach --}}

            </div>
        </section>

        <!-- Category Grid -->
        <section class="cat-grid py-4">
            <div class="container">
                <div class="row py-md-3">
                    <div class="col-lg-4 col-md-6 py-2 px-lg-4">
                        <a href="#our-range" class="cat-grid-link scrollTo"
                            data-target="category_{{ $parentCategories[0]->id }}"
                            aria-controls="category_{{ $parentCategories[0]->id }}" role="tab" data-toggle="tab">
                            <div class="cat-card cat-1 px-xl-4 p-3 b-rd-10">
                                <p class="cat-name default-text">Fresh <br> Exotic fruits <br> <span
                                        class="primary-text">(Indian)</span>
                                </p>
                                <div class="cat-img text-right">
                                    <img src="{{ static_asset('assets/img/new-design/mango.png') }}" width="241"
                                        height="120" alt="Indian Fruits">
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-lg-4 col-md-6 py-2 px-lg-4">
                        <a href="#our-range" class="cat-grid-link scrollTo"
                            data-target="category_{{ $parentCategories[0]->id }}"
                            aria-controls="category_{{ $parentCategories[0]->id }}" role="tab" data-toggle="tab">
                            <div class="cat-card cat-2 px-xl-4 p-3 b-rd-10">
                                <p class="cat-name default-text">Fresh <br> Exotic fruits <br> <span class="primary-text">
                                        (Imported)</span></p>
                                <div class="cat-img text-right">
                                    <img src="{{ static_asset('assets/img/new-design/grapse.png') }}" width="212"
                                        height="144" alt="Imported Fruits">
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-lg-4 col-md-6 mx-md-auto mx-lg-0 py-2 px-lg-4">
                        <a href="#our-range" class="cat-grid-link scrollTo"
                            data-target="category_{{ $parentCategories[1]->id }}"
                            aria-controls="category_{{ $parentCategories[1]->id }}" role="tab" data-toggle="tab">
                            <div class="cat-card cat-3 px-xl-4 p-3 b-rd-10">
                                <p class="cat-name default-text">Fresh Exotic <br> Vegetables</p>
                                <div class="cat-img text-right">
                                    <img src="{{ static_asset('assets/img/new-design/veg.png') }}" width="171"
                                        height="159" alt="Vegetables">
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Customer Favourits -->
        <section class="cust-fav pt-lg-5 py-4">
            <div class="container">
                <h2 class="title text-center">Customer Favorites</h2>

                <div class="owl-carousel owl-theme">
                    @foreach ($customer_favourites as $prd_val)
                        @php
                            $cart_qty = 0;
                            if (count($cart) > 0 && isset($cart[$prd_val->id])) {
                                $cart_qty = $cart[$prd_val->id]['qty'];
                            }
                            $addCartQty = $cart_qty + 1;

                            $product_total = 0;
                            if (count($cart) > 0 && isset($cart[$prd_val->id])) {
                                $product_total = $cart[$prd_val->id]['total'];
                            }

                            $product_price = $prd_val->price;
                            if (count($cart) > 0 && isset($cart[$prd_val->id])) {
                                $product_price = $cart[$prd_val->id]['price'];
                            }

                            $qty_unit_main = $prd_val->product->unit;
                            if (floatval($prd_val->product->min_qty) < 1) {
                                $qty_unit_main = 1000 * floatval($prd_val->product->min_qty) . ' ' . $prd_val->product->secondary_unit;
                            }
                        @endphp
                        <div class="item">
                            <div class="prd-card b-rd-10 overflow-hide trnsn-300ms w-100">
                                <div class="prd-img">
                                    <img src="{{ uploaded_asset($prd_val->product->photos) }}"
                                        data-src="{{ uploaded_asset($prd_val->product->thumbnail_img) }}"
                                        class="object-cover-center" width="250" height="250"
                                        onerror="this.onerror=null;this.src='{{ static_asset('assets/img/no-image-found.jpg') }}';"
                                        alt="{{ $prd_val->product->getTranslation('name') }}">
                                </div>
                                <div class="prd-content p-3 position-relative">
                                    @if (explode(',', $prd_val->product->tags)[0] != '')
                                        <span
                                            class="prd-tag text-white b-rd-5">{{ explode(',', $prd_val->product->tags)[0] }}</span>
                                    @endif
                                    @if ($prd_val->product->manufacturer_location)
                                        <p class="prd-loc mb-1 secondary-text">
                                            {{ $prd_val->product->manufacturer_location }}</p>
                                    @endif
                                    <p class="prd-name mb-1 fw600 text-black">{{ $prd_val->product->name ?? '-' }}</p>
                                    @if (trim($prd_val->product->variation) != '')
                                        <p class="prd-desc mb-1 light-text">{{ $prd_val->product->variation }}
                                        </p>
                                    @endif
                                    <p class="prd-pricing mb-2 pt-1 fw700">{!! single_price_web($product_price) !!} /
                                        {{ $qty_unit_main }}</p>
                                    <button class="btn primary-btn"
                                        onclick="addToCart({{ $prd_val->product->id }}, {{ $prd_val->id }}, {{ $addCartQty }});">
                                        <img src="{{ static_asset('assets/img/new-design/btn-cart.svg') }}"
                                            onload="SVGInject(this)" alt="Btn Cart">
                                        Add to cart
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- SafeQu Promise -->
        <section class="promise py-lg-5 py-4 v-light-bg">
            <div class="container">
                <h2 class="title text-center mb-2"><span class="primary-text">Our</span> Promise</h2>
                <p class="text-center mb-4"><span class="primary-text fw500">Finest quality, Fresh Exotic Produce </span>
                    from trusted farms of your choice.</p>

                <div class="row py-lg-4 py-3">
                    <div class="col-6 col-lg-3 pb-3 text-center">
                        <div class="icon">
                            <img src="{{ static_asset('assets/img/new-design/fresh.png') }}" width="47"
                                height="37" alt="Truck Icon">
                        </div>
                        <p class="title mb-0">100% Fresh</p>
                    </div>

                    <div class="col-6 col-lg-3 pb-3 text-center">
                        <div class="icon">
                            <img src="{{ static_asset('assets/img/new-design/bucket.png') }}" width="47"
                                height="37" alt="Bucket Icon">
                        </div>
                        <p class="title mb-0">Direct from farm</p>
                    </div>

                    <div class="col-6 col-lg-3 pb-3 text-center">
                        <div class="icon">
                            <img src="{{ static_asset('assets/img/new-design/hand-picked.png') }}" width="47"
                                height="37" alt="Flag Icon">
                        </div>
                        <p class="title mb-0">Hand Picked</p>
                    </div>

                    <div class="col-6 col-lg-3 pb-3 text-center">
                        <div class="icon">
                            <img src="{{ static_asset('assets/img/new-design/truck.png') }}" width="47"
                                height="37" alt="Money Icon">
                        </div>
                        <p class="title mb-0">Delivered in 24hrs*</p>
                    </div>
                </div>

                <p class="text-center mt-lg-0 mt-md-3 mb-1 small">*Excludes imported fresh exotic produce</p>
            </div>
        </section>

        <!-- Deals Of The Day -->
        @if ($deals_of_the_day)
            <section class="deals py-lg-5 py-4">
                <div class="container">
                    {{-- <h2 class="title text-center pt-2">Deal Of The Day</h2>

                    <div class="content py-md-5 py-4 b-rd-20">
                        <div class="row m-0 py-md-3">
                            <div class="col-lg-5 offset-md-1 prd-details py-3">
                                <div class="d-flex align-items-start pb-2 justify-content-center flex-column h-100">
                                    <p class="loc-tag mb-2 p-1 px-2 bg-white d-inline-block b-rd-10">Imported from
                                        Mahabaleshwar
                                    </p>

                                    <h2 class="deal-title text-white mb-3">Strawberry</h2>

                                    <h5 class="qty-val fw700 text-white mb-4">- 500 gram pack</h5>

                                    <button class="btn btn-fill-white org-clr hover-primary"
                                        onclick="addToCart(14, 48, 1);">
                                        <img src="{{ static_asset('assets/img/new-design/btn-cart.svg') }}"
                                            onload="SVGInject(this)" alt="Btn Cart">
                                        Buy Now
                                    </button>
                                </div>
                            </div>

                            <div class="col-lg-5 py-md-3 text-right position-relative deal-img">
                                <img src="{{ static_asset('assets/img/new-design/hero-img-2.webp') }}"
                                    class="deal-prd-img" width="731" height="557" alt="Strawberry">
                                <div class="prd-benefit">
                                    <div class="d-flex align-items-center pb-3 mb-md-3 justify-content-end">
                                        <p class="text-white fw600 mr-3 mb-0">Great <br> for Skin</p>
                                        <img src="{{ static_asset('assets/img/new-design/face.svg') }}"
                                            class="injectable" width="72" height="72" alt="Icon">
                                    </div>
                                    <div class="d-flex align-items-center pb-2 justify-content-end">
                                        <p class="text-white fw600 mr-3 mb-0">Improves <br> Heart Health</p>
                                        <img src="{{ static_asset('assets/img/new-design/heart.svg') }}"
                                            class="injectable" width="72" height="72" alt="Icon">
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div> --}}

                    <div class="content pt-lg-5 pt-4 overflow-hide">
                        <h2 class="title text-center text-white">Deal{{ count($deals_of_the_day) > 1 ? 's' : '' }} Of The
                            Day</h2>

                        <div
                            class="owl-carousel owl-theme product-slider2 mx-auto {{ count($deals_of_the_day) > 1 ? '' : 'scaleCard' }}">
                            @foreach ($deals_of_the_day as $prd_val)
                                @php
                                    $cart_qty = 0;
                                    $product_total = 0;
                                    $product_price = isset($prd_val->productStock) ? $prd_val->productStock->price : 0;
                                    if (count($cart) > 0 && isset($prd_val->productStock) && isset($cart[$prd_val->productStock->id])) {
                                        $cart_qty = $cart[$prd_val->productStock->id]['qty'];
                                        $product_total = $cart[$prd_val->productStock->id]['total'];
                                        $product_price = $cart[$prd_val->productStock->id]['price'];
                                    }
                                    $addCartQty = $cart_qty + 1;

                                    $qty_unit_main = $prd_val->unit;
                                    if (floatval($prd_val->min_qty) < 1) {
                                        $qty_unit_main = 1000 * floatval($prd_val->min_qty) . ' ' . $prd_val->secondary_unit;
                                    }
                                @endphp
                                <div class="item px-2">
                                    <div class="prd-card b-rd-10 overflow-hide trnsn-300ms position-relative">
                                        <div class="deal-type">Flat {{ $prd_val->discount }}% Off</div>
                                        <div class="d-flex align-items-center justify-content-between flex-design">
                                            <div class="prd-img">
                                                <img src="{{ uploaded_asset($prd_val->photos) }}"
                                                    data-src="{{ uploaded_asset($prd_val->thumbnail_img) }}"
                                                    class="object-cover-center" width="250" height="250"
                                                    onerror="this.onerror=null;this.src='{{ static_asset('assets/img/no-image-found.jpg') }}';"
                                                    alt="{{ $prd_val->getTranslation('name') }}">
                                            </div>
                                            <div class="prd-content p-3 position-relative">
                                                @if (explode(',', $prd_val->tags)[0] != '')
                                                    <span
                                                        class="prd-tag text-white b-rd-5">{{ explode(',', $prd_val->tags)[0] }}</span>
                                                @endif
                                                @if ($prd_val->manufacturer_location)
                                                    <p class="prd-loc mb-1 secondary-text">
                                                        {{ $prd_val->manufacturer_location }}</p>
                                                @endif
                                                <p class="prd-name mb-1 fw600 text-black">{{ $prd_val->name ?? '-' }}
                                                </p>
                                                @if (trim($prd_val->variation) != '')
                                                    <p class="prd-desc mb-1 light-text">{{ $prd_val->variation }}</p>
                                                @endif
                                                <p class="prd-disc-pricing mb-0 fw700"><s>{!! single_price_web($product_price) !!} /
                                                        {{ $qty_unit_main }}</s></p>
                                                <p class="prd-pricing mb-2 fw700">
                                                    {!! single_price_web($product_price - round(($product_price * $prd_val->discount) / 100, 2)) !!} /
                                                    {{ $qty_unit_main }}</p>
                                                <button class="btn secondary-btn"
                                                    onclick="addToCart({{ $prd_val->id }}, {{ $prd_val->productStock->id }}, {{ $addCartQty }});">
                                                    <img src="{{ static_asset('assets/img/new-design/btn-cart.svg') }}"
                                                        onload="SVGInject(this)" alt="Btn Cart">
                                                    Add to cart
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </section>
        @endif

        <!-- Hear from Customers -->
        <section class="hear-customers pt-lg-5 py-4 position-relative">
            <div class="container">
                <h2 class="title text-center">Hear it from Our Customers</h2>

                <div class="owl-carousel owl-theme testimonials">

                    @foreach ($customer_reviews as $rev)
                        <div class="item">
                            <div class="p-md-3 trnsn-300ms position-relative b-rd-10 px-2">
                                <div class="user-quote d-flex align-items-start position-relative px-3 px-md-2 h-100">
                                    <div class="usr-img mt-md-4 mr-md-4 mb-4 rounded-circle">
                                        <img src="{{ static_asset('assets/img/json_file_images/' . $rev['image']) }}"
                                            onerror="this.onerror=null;this.src='{{ static_asset('assets/img/user.png') }}';"
                                            alt="Star">
                                    </div>
                                    <div class="quote-card p-3 b-rd-10 position-relative h-100">
                                        <p class="position-relative">{!! $rev['review'] !!}</p>
                                        <div class="d-flex align-items-center position-relative">
                                            <div class="usr-data">
                                                <h5 class="secondary-text mb-1">{{ $rev['name'] }}</h5>
                                                <p class="mb-0">{{ $rev['community'] }}</p>
                                            </div>
                                            <div class="usr-rate">
                                                @for ($i = 1; $i <= $rev['rating']; $i++)
                                                    <img src="{{ static_asset('assets/img/new-design/star.svg') }}"
                                                        onload="SVGInject(this)" alt="Star">
                                                @endfor
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach

                </div>
            </div>
        </section>

        <!-- Our Full Range -->
        @if (count($our_full_range_of_products) > 0)
            <section class="our-range py-lg-5 py-4" id="our-range">
                <div class="container">
                    <h2 class="title text-center">Our Full Range</h2>

                    <div class="filters px-3 pb-3 d-flex flex-wrap align-items-center justify-content-center">
                        <a href="#all_prd" aria-controls="all" role="tab" data-toggle="tab"
                            class="selected m-2 rounded-lg">All</a>
                        @foreach ($parentCategories as $p_category)
                            @if ($p_category->name != 'Flowers')
                                <a href="#category_{{ $p_category->id }}" aria-controls="category_{{ $p_category->id }}"
                                    role="tab" data-toggle="tab"
                                    class="m-md-2 my-2 mx-1 rounded-lg">{{ $p_category->name }}</a>
                            @endif
                        @endforeach
                    </div>
                    <div class="tab-content py-0 mb-1" id="filters">
                        <div role="tabpanel" class="filter-carousel tab-pane active" id="all_prd">
                            <div class="owl-carousel owl-theme product-slider">
                                @foreach ($our_full_range_of_products as $prd_val)
                                    @php
                                        $cart_qty = 0;
                                        if (count($cart) > 0 && isset($cart[$prd_val->id])) {
                                            $cart_qty = $cart[$prd_val->id]['qty'];
                                        }
                                        $addCartQty = $cart_qty + 1;

                                        $product_total = 0;
                                        if (count($cart) > 0 && isset($cart[$prd_val->id])) {
                                            $product_total = $cart[$prd_val->id]['total'];
                                        }

                                        $product_price = $prd_val->price;
                                        if (count($cart) > 0 && isset($cart[$prd_val->id])) {
                                            $product_price = $cart[$prd_val->id]['price'];
                                        }

                                        $qty_unit_main = $prd_val->product->unit;
                                        if (floatval($prd_val->product->min_qty) < 1) {
                                            $qty_unit_main = 1000 * floatval($prd_val->product->min_qty) . ' ' . $prd_val->product->secondary_unit;
                                        }
                                    @endphp
                                    <div class="item">
                                        <div class="prd-card b-rd-10 overflow-hide trnsn-300ms w-100">
                                            <div class="prd-img">
                                                <img src="{{ uploaded_asset($prd_val->product->photos) }}"
                                                    data-src="{{ uploaded_asset($prd_val->product->thumbnail_img) }}"
                                                    class="object-cover-center" width="250" height="250"
                                                    onerror="this.onerror=null;this.src='{{ static_asset('assets/img/no-image-found.jpg') }}';"
                                                    alt="{{ $prd_val->product->getTranslation('name') }}">
                                            </div>
                                            <div class="prd-content p-3 position-relative">
                                                @if (explode(',', $prd_val->product->tags)[0] != '')
                                                    <span
                                                        class="prd-tag text-white b-rd-5">{{ explode(',', $prd_val->product->tags)[0] }}</span>
                                                @endif
                                                @if ($prd_val->product->manufacturer_location)
                                                    <p class="prd-loc mb-1 secondary-text">
                                                        {{ $prd_val->product->manufacturer_location }}</p>
                                                @endif
                                                <p class="prd-name mb-1 fw600 text-black">
                                                    {{ $prd_val->product->name ?? '-' }}</p>
                                                @if (trim($prd_val->product->variation) != '')
                                                    <p class="prd-desc mb-1 light-text">{{ $prd_val->product->variation }}
                                                    </p>
                                                @endif
                                                <p class="prd-pricing mb-2 pt-1 fw700">{!! single_price_web($product_price) !!} /
                                                    {{ $qty_unit_main }}</p>
                                                <button class="btn primary-btn"
                                                    onclick="addToCart({{ $prd_val->product->id }}, {{ $prd_val->id }}, {{ $addCartQty }});">
                                                    <img src="{{ static_asset('assets/img/new-design/btn-cart.svg') }}"
                                                        onload="SVGInject(this)" alt="Btn Cart">
                                                    Add to cart
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        @foreach ($parentCategories as $p_category)
                            @if ($p_category->name != 'Flowers')
                                <div role="tabpanel" class="filter-carousel tab-pane"
                                    id="category_{{ $p_category->id }}">
                                    @if (count($all_products[$p_category->id]) > 0)
                                        <div class="owl-carousel owl-theme product-slider">
                                            @foreach ($all_products[$p_category->id] as $prd_val)
                                                @php
                                                    $cart_qty = 0;
                                                    if (count($cart) > 0 && isset($cart[$prd_val->id])) {
                                                        $cart_qty = $cart[$prd_val->id]['qty'];
                                                    }
                                                    $addCartQty = $cart_qty + 1;

                                                    $product_total = 0;
                                                    if (count($cart) > 0 && isset($cart[$prd_val->id])) {
                                                        $product_total = $cart[$prd_val->id]['total'];
                                                    }

                                                    $product_price = $prd_val->price;
                                                    if (count($cart) > 0 && isset($cart[$prd_val->id])) {
                                                        $product_price = $cart[$prd_val->id]['price'];
                                                    }

                                                    $qty_unit_main = $prd_val->product->unit;
                                                    if (floatval($prd_val->product->min_qty) < 1) {
                                                        $qty_unit_main = 1000 * floatval($prd_val->product->min_qty) . ' ' . $prd_val->product->secondary_unit;
                                                    }
                                                @endphp
                                                <div class="item">
                                                    <div class="prd-card b-rd-10 overflow-hide trnsn-300ms w-100">
                                                        <div class="prd-img">
                                                            <img src="{{ uploaded_asset($prd_val->product->photos) }}"
                                                                data-src="{{ uploaded_asset($prd_val->product->thumbnail_img) }}"
                                                                class="object-cover-center" width="250" height="250"
                                                                onerror="this.onerror=null;this.src='{{ static_asset('assets/img/no-image-found.jpg') }}';"
                                                                alt="{{ $prd_val->product->getTranslation('name') }}">
                                                        </div>
                                                        <div class="prd-content p-3 position-relative">
                                                            @if (explode(',', $prd_val->product->tags)[0] != '')
                                                                <span
                                                                    class="prd-tag text-white b-rd-5">{{ explode(',', $prd_val->product->tags)[0] }}</span>
                                                            @endif
                                                            @if ($prd_val->product->manufacturer_location)
                                                                <p class="prd-loc mb-1 secondary-text">
                                                                    {{ $prd_val->product->manufacturer_location }}</p>
                                                            @endif
                                                            <p class="prd-name mb-1 fw600 text-black">
                                                                {{ $prd_val->product->name ?? '-' }}</p>
                                                            @if (trim($prd_val->product->variation) != '')
                                                                <p class="prd-desc mb-1 light-text">
                                                                    {{ $prd_val->product->variation }}</p>
                                                            @endif
                                                            <p class="prd-pricing mb-2 pt-1 fw700">{!! single_price_web($product_price) !!}
                                                                /
                                                                {{ $qty_unit_main }}</p>
                                                            <button class="btn primary-btn"
                                                                onclick="addToCart({{ $prd_val->product->id }}, {{ $prd_val->id }}, {{ $addCartQty }});">
                                                                <img src="{{ static_asset('assets/img/new-design/btn-cart.svg') }}"
                                                                    onload="SVGInject(this)" alt="Btn Cart">
                                                                Add to cart
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <h4 class="pt-3 pb-5 mb-3 text-center">No Products Available</h4>
                                    @endif
                                </div>
                            @endif
                        @endforeach
                    </div>

                    <div class="text-center mb-2" id="viewAllPrd">
                        <a href="{{ route('shop.visit') }}" class="btn btn-fill-black d-inline-block mx-auto">
                            View complete harvest range
                        </a>
                    </div>
                </div>
            </section>
        @endif

        <!-- Rewards -->
        <section class="rewards py-lg-5 py-4 v-light-bg">
            <div class="container py-lg-5 py-2">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="rwd-ttl">
                            <h3 class="title">Get <span class="secondary-text">flat 50%*</span> off up to
                                Rs.100 on your first order on the app.</h3>
                            <h3 class="title">Use Code: <span class="primary-bg text-white b-rd-10">safe2023</span></h3>
                        </div>
                    </div>
                    <div class="col-lg-6 text-center pt-2">
                        <img src="{{ static_asset('assets/img/new-design/rewards.svg') }}" width="598" height="357"
                            class="injectable" alt="Rewards Image">
                    </div>
                </div>
            </div>
        </section>

        <!-- Insta Feed -->
        <section class="insta-feed pt-lg-5 py-4">
            <div class="container">
                <h2 class="title text-center">Instagram Feeds</h2>

                <div class="owl-carousel owl-theme insta-feed-slider" id="instafeed"></div>

            </div>
        </section>

        <!-- Change Community Modal Starts -->
        <div class="modal fade changeCommunityModal" id="changeCommunityModal" tabindex="-1"
            aria-labelledby="changeCommunityModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Change Community</h5>
                        <div class="close-btn text-right">
                            <a href="javascript:void(0)" class="fw900" data-dismiss="modal">X</a>
                        </div>
                    </div>
                    <form action="" class="form-default" role="form" method="GET" id="change-community-form">
                        @csrf
                        <div class="modal-body">
                            <div class="item-details px-sm-3">
                                <div class="order-list text-center py-3">
                                    <h6> Are you sure you want to leave {{ session()->get('shop_name') }}
                                        community ? </h6>
                                    <p class="mb-0">
                                        <i class="fad primary-color fa-exclamation-circle fsize14" aria-hidden="true"></i>
                                        <span class="fsize12 body-txt ordered-qty"> Cart items will get removed.
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn primary-btn fw600 text-white">Yes</button>
                            <button type="button" class="btn btn-secondary btn-no fw600 text" data-dismiss="modal">No
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </main>

    <footer class="position-relative">
        <div class="container pb-lg-5 pb-4">
            <div class="row">
                <div class="col-lg-3 col-md-4 pt-3">
                    <div class="footer-logo pb-3">
                        <img src="{{ static_asset('assets/img/new-design/safequ-logo.png') }}" width="260"
                            height="123" alt="SafeQu Logo">
                    </div>
                    <p class="text-white">
                        Honest, Fresh Produce. <br /> Delivered to your doorstep.
                    </p>
                </div>
                <div class="col-lg-9 col-md-8">
                    <div class="row">
                        <div class="col-lg-4 col-sm-6 py-3">
                            <p class="links-tag text-white fw600">Connect with Us</p>
                            <ul class="p-0 m-0">
                                <li><a href="#">Eluciidaate Tech Pvt Ltd</a></li>
                                <li><a href="mailto:customerservice@safequ.co">customerservice@safequ.co</a></li>
                                <li><a href="https://wa.me/{{ $whatsAppNo }}" class="scrollTo">{{ $whatsAppNo }}</a>
                                </li>
                            </ul>
                        </div>
                        <div class="col-lg-2 col-md-5 offset-md-1 offset-lg-0 col-6 py-3">
                            <p class="links-tag text-white fw600">Quick Links</p>
                            <ul class="p-0 m-0">
                                <li><a href="{{ route('home') }}">Home</a></li>
                                <li><a href="{{ route('shop.visit') }}">Products</a></li>
                                {{-- <li><a href="#communitiesSec" class="scrollTo">Community</a></li> --}}
                            </ul>
                        </div>
                        <div class="col-lg-3 col-6 py-3">
                            <p class="links-tag text-white fw600">Other Links</p>
                            <ul class="p-0 m-0">
                                <li><a href="{{ route('user.login') }}">My Account</a></li>
                                <li><a href="{{ route('user.login') }}">Order History</a></li>
                                <li><a href="{{ route('cart') }}">Cart</a></li>
                            </ul>
                        </div>
                        <div class="col-lg-3 col-md-5 offset-md-1 offset-lg-0  col-sm-6 py-3">
                            <p class="links-tag text-white fw600">Legal Links</p>
                            <ul class="p-0 m-0">
                                <li><a href="{{ static_asset('assets/docs/privacy-policy.pdf') }}"
                                        target="_blank">Privacy Policy</a></li>
                                <li><a href="{{ static_asset('assets/docs/terms-and-conditions-buyer.pdf') }}"
                                        target="_blank">Terms & Conditions</a></li>
                                <li><a href="{{ static_asset('assets/docs/return-and-refund.pdf') }}"
                                        target="_blank">Return Policy</a></li>
                                <li><a href="{{ static_asset('assets/docs/return-and-refund.pdf') }}"
                                        target="_blank">Refund Policy</a></li>
                            </ul>
                        </div>
                        {{-- <div class="col-md-3 col-6 py-3">
                            <p class="links-tag text-white fw700"></p>
                            <ul class="p-0 m-0">
                                <li><a href="https://m.facebook.com/safequ.in/" target="_blank">Facebook</a></li>
                                <li><a href="https://www.instagram.com/safequ.india/" target="_blank">Instagram</a></li>
                            </ul>
                        </div> --}}
                    </div>
                </div>
            </div>
        </div>
        <div class="copyright text-center position-relative">
            <p class="text-white mb-0 fw500">Copyright &copy; 2023 SafeQu. All Rights Reserved.</p>
        </div>
    </footer>

    <div class="whats-app-icon">
        <a href="https://wa.me/{{ $whatsAppNo }}" target="_blank" class="d-block" area-lable="Contact Us"
            title="Contact Us">
            <img src="{{ static_asset('assets/img/new-design/whatsapp.svg') }}" class="injectable" width="40"
                height="40" alt="WhatsApp">
        </a>
    </div>

    <div class="fixed-cart b-rd-50p" style="display: none;">
        <a href="{{ route('cart') }}" aria-label="Cart" title="Cart"
            class="cart-icon-org position-relative d-block">
            <img src="{{ static_asset('assets/img/new-design/btn-cart-primary.svg') }}"
                class="injectable rounded-circle trnsn-300ms" alt="Cart Icon">
            <span class="rounded-circle trnsn-300ms cart-item-count"></span>
        </a>
    </div>

    <!-- Hidden Field for PWA for temp solution to undefined error of id -->
    <input type="hidden" id="installPWAMenu">
@endsection

@section('script')
    <script>
        // ---- SVG Injector -  To convert IMG tag in SVG code. (Only for SVG images)
        SVGInject(document.querySelectorAll("img.injectable"));

        $(document).scroll(function() {
            let scroll = $(document).scrollTop();
            let header = $("header");
            let topBar = $(".offer-top-bar");

            let scrollHeight = $(document).width() > 991 ? 0.1 : topBar.outerHeight();

            if (scroll >= scrollHeight) {
                header.addClass("fixedHeader");
                if ($(document).width() < 992) {
                    header.css('top', 0 - topBar.outerHeight())
                }

                if ($(document).width() > 991) {
                    $('.hero-sec').css('padding-top', header.outerHeight() + 30)
                } else {
                    $('.hero-sec').css('padding-top', header.outerHeight())
                }
                //
            } else {
                header.removeClass("fixedHeader");

                if ($(document).width() > 991) {
                    $('.hero-sec').css('padding-top', '30px')
                } else {
                    $('.hero-sec').css('padding-top', 0)
                }
            }
        });

        $(document).ready(function() {
            $('a.scrollTo').click(function() {
                let target = $(this).attr('href');
                let extraScroll = $(document).width() > 991 ? 75 : 8;

                $('html, body').animate({
                    scrollTop: ($(target).offset().top - ($("header").outerHeight() + extraScroll))
                }, 20)
            })

            setInterval(() => {
                $(".smiley").hasClass("fa-smile-wink") ? $(".smiley").removeClass("fa-smile-wink").addClass(
                    "fa-smile") : $(".smiley").removeClass("fa-smile").addClass("fa-smile-wink");
            }, 1000);

            $('a[data-toggle="tab"]').click(function() {
                $('a[data-toggle="tab"]').removeClass('selected');
                $(this).addClass('selected');

                var activeDiv = $(this).attr('href');

                if ($(this).data('target') !== "undefined") {
                    $('a[data-toggle="tab"][aria-controls="' + $(this).data('target') + '"]').addClass(
                        'selected')

                    activeDiv = '#' + $(this).data('target');
                }

                $('.filter-carousel').removeClass('active');
                console.log(activeDiv);
                $(activeDiv).addClass('active');

                $(".filter-carousel .owl-carousel").trigger('refresh.owl.carousel');

                $('.prd-content').each(function() {
                    $(this).css('min-height', ($(this).parent().innerHeight() - ($(this).parent()
                        .find('.prd-img').innerHeight())));
                })
            })

            let carouselObj = {
                loop: true,
                responsiveClass: true,
                dots: false,
                // autoplay: true,
                // autoplayTimeout: 4500,
                // smartSpeed: 1500,
                navText: [
                    "<img src='{{ static_asset('assets/img/new-design/left-arw-o.svg') }}' class='injectable nav-arrow' onload='SVGInject(this)' alt='Nav Btn'>",
                    "<img src='{{ static_asset('assets/img/new-design/right-arw-o.svg') }}' class='injectable nav-arrow' onload='SVGInject(this)' alt='Nav Btn'>"
                ]
            }

            $('.carousel').carousel({
                interval: 7000,
            })

            $('.product-slider').owlCarousel({
                ...carouselObj,
                ...{
                    margin: 30,
                    nav: false,
                    dots: true,
                    responsive: {
                        0: {
                            center: true,
                            items: 1.25,
                            margin: 50,
                        },
                        575: {
                            items: 2,
                        },
                        768: {
                            items: 3,
                        },
                        992: {
                            items: 4,
                        },
                        1200: {
                            items: 4,
                            nav: true,
                            dots: false,
                        },
                        1440: {
                            items: 5,
                            nav: true,
                            dots: false,
                        },
                    },
                }
            })


            <?php if (count($deals_of_the_day) > 1){ ?>
            $('.product-slider2').owlCarousel({
                ...carouselObj,
                ...{
                    loop: false,
                    margin: 10,
                    nav: false,
                    dots: true,
                    autoplay: false,
                    responsive: {
                        0: {
                            items: 1,
                            center: true
                        },
                        767: {
                            items: 2,
                            center: false,
                            dots: false
                        },
                    }
                }
            })
            <?php  } else { ?>
            $('.product-slider2').owlCarousel({
                ...carouselObj,
                ...{
                    loop: false,
                    margin: 10,
                    nav: false,
                    dots: false,
                    autoplay: false,
                    items: 1,
                    center: true
                }
            })
            <?php  } ?>

            $('.cust-fav .owl-carousel').owlCarousel({
                ...carouselObj,
                ...{
                    margin: 30,
                    nav: false,
                    dots: true,
                    responsive: {
                        0: {
                            center: true,
                            items: 1.25,
                            margin: 50,
                        },
                        575: {
                            items: 2,
                        },
                        768: {
                            items: 3,
                        },
                        992: {
                            items: 4,
                        },
                        1360: {
                            items: 5,
                            nav: true,
                            dots: false,
                            autoplay: false
                        }
                    },
                }
            })

            $('.hear-customers .testimonials').owlCarousel({
                ...carouselObj,
                ...{
                    margin: 15,
                    dots: true,
                    responsive: {
                        0: {
                            items: 1,
                        },
                        992: {
                            items: 2,
                            nav: true,
                            dots: false,
                        },
                    }
                }
            })

            $('.hear-farmers .testimonials').owlCarousel({
                ...carouselObj,
                ...{
                    margin: 10,
                    items: 1,
                    nav: false,
                    dots: true,
                    center: true,
                    responsive: {
                        767: {
                            nav: true,
                            dots: false,
                        },
                    }
                }
            })

            $('.hero-slider').owlCarousel({
                loop: false,
                margin: 30,
                items: 1.05,
                center: true,
                dots: false,
                // autoplay: true,
                // autoplayTimeout: 7000,
                smartSpeed: 2000,
                // responsive: {
                //     0: {
                //         items: 1.05,
                //     },
                //     992: {
                //         items: 1.15,
                //     },
                // }
            })

            setTimeout(() => {
                // $(".prd-img").css('height', $(this).clientWidth);
                var dealsImgHeight = $(".deals .prd-img:first").innerWidth();
                var custFavImgHeight = $(".cust-fav .prd-img:first").innerWidth();
                var ourRangeImgHeight = $(".our-range .prd-img:first").innerWidth();
                var communityImgHeight = $(".communities .cm-img:first").innerWidth();
                var instaFeedImgHeight = $("#instafeed .feed-image:first").innerWidth();

                $(".deals .prd-img").each(function() {
                    $(this).css('height', dealsImgHeight);
                })
                $(".cust-fav .prd-img").each(function() {
                    $(this).css('height', custFavImgHeight);
                })
                $(".our-range .prd-img").each(function() {
                    $(this).css('height', ourRangeImgHeight);
                })
                $(".communities .cm-img").each(function() {
                    $(this).css('height', communityImgHeight);
                })
                $("#instafeed .feed-image").each(function() {
                    $(this).css('height', instaFeedImgHeight);
                })

                $('.prd-content').each(function() {
                    $(this).css('min-height', ($(this).parent().innerHeight() - ($(this).parent()
                        .find('.prd-img').innerHeight())));
                })
            }, 1000)


            setTimeout(() => {
                $(".our-range").css('height', document.getElementById('our-range').clientHeight);
            }, 1500)

            $('.menu-toggle').click(function() {
                $(this).toggleClass('active');
                $('.collapsible').toggleClass('active');
            })

            if ($(document).width() < 992) {
                $('.nav-menu, .nav-icons').addClass('container');
            }

            // Detect Location Starts
            let locationButton = document.getElementById("detect-location");
            let locationDiv = document.getElementById("header-location-name");
            let locationButton2 = document.getElementById("detect-location2");
            let locationDiv2 = document.getElementById("header-location-name2");

            navigator.permissions.query({
                name: 'geolocation'
            }).then(function(result) {
                // Will return ['granted', 'prompt', 'denied']
                if (result.state == 'granted') {
                    //returns position(latitude and longitude) or error
                    navigator.geolocation.getCurrentPosition(showLocation, checkError);
                }
            });

            locationButton.addEventListener("click", () => {
                //Geolocation APU is used to get geographical position of a user and is available inside the navigator object
                if (navigator.geolocation) {
                    //returns position(latitude and longitude) or error
                    navigator.geolocation.getCurrentPosition(showLocation, checkError);
                } else {
                    //For old browser i.e IE
                    AIZ.plugins.notify('danger',
                        '{{ translate('The browser does not support geolocation') }}');
                }
            });

            //Error Checks
            const checkError = (error) => {
                switch (error.code) {
                    case error.PERMISSION_DENIED:
                        AIZ.plugins.notify('danger', '{{ translate('Please allow access to location') }}');
                        break;
                    case error.POSITION_UNAVAILABLE:
                        //usually fired for firefox
                        AIZ.plugins.notify('danger', '{{ translate('Location Information unavailable') }}');
                        break;
                    case error.TIMEOUT:
                        AIZ.plugins.notify('danger',
                            '{{ translate('The request to get user location timed out') }}');
                }
            };

            const showLocation = async (position) => {
                //We user the NOminatim API for getting actual addres from latitude and longitude
                let response = await fetch(
                    `https://nominatim.openstreetmap.org/reverse?lat=${position.coords.latitude}&lon=${position.coords.longitude}&format=json`
                );
                //store response object
                let data = await response.json();
                // locationDiv.innerText = `${data.address.city}`;
                locationDiv.append(`${data.address.city}`);
                locationDiv.classList.remove('display-none');
                locationDiv.classList.add('d-flex');
                locationButton.classList.remove('d-flex');
                locationButton.classList.add('display-none');

                locationDiv2.append(`${data.address.city}, ${data.address.state}, ${data.address.country}`);
                locationDiv2.classList.remove('display-none');
                locationDiv2.classList.add('d-block');
                locationButton2.classList.remove('d-block');
                locationButton2.classList.add('display-none');
                // locationDiv.innerText = `${data.address.city}, ${data.address.country}`;
            };
            // Detect Location Ends

            // InstaFeed
            $.ajax({
                type: "GET",
                url: 'https://feeds.behold.so/Uxbc9QWrdl5z39UXeMbD',
                success: function(data) {

                    $.each(data, function(key, val) {
                        if (val['mediaType'] == "IMAGE") {
                            var media = '<img src="' + val['mediaUrl'] +
                                '" class="feed-image" alt="Insta Feed">';
                        } else {
                            var media =
                                `<video width="100%" controls preload="auto" class="feed-image"> <source src= "` +
                                val['mediaUrl'] + `" type="video/mp4"> </video>`;
                        }

                        let html = `<div class="item">
                            <div class="feed-card trnsn-300ms w-100">
                                <div class="feed-img">` + media + `</div>
                                <p class="pt-2 my-2 px-1 feed-caption">` + val['caption'] + `</p>
                            </div>
                        </div>`;
                        $('#instafeed').append(html)
                    });

                    $(".insta-feed-slider").owlCarousel({
                        ...carouselObj,
                        margin: 30,
                        nav: false,
                        dots: true,
                        responsive: {
                            0: {
                                items: 1,
                            },
                            575: {
                                items: 2,
                            },
                            768: {
                                items: 3,
                            },
                            992: {
                                items: 4
                            },
                            1200: {
                                items: 4,
                                nav: true,
                                dots: false,
                            },
                            1440: {
                                items: 5,
                                nav: true,
                                dots: false,
                            }
                        }
                    })
                },
            });
        })

        function confrimCommunityChange(url) {
            $('#changeCommunityModal').modal('show');
            $('#changeCommunityModal #change-community-form').attr('action', url);
        }

        function setLocalCommunity(shop_id) {
            if (shop_id > 0) {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '{{ route('home.set_local_community') }}',
                    data: {
                        id: shop_id
                    },
                    success: function(data) {
                        if (data.status == 1) {
                            window.location.reload();
                            {{-- window.location.replace("{{ route('shop.visit') }}"+"/"+data.shop_slug); --}}
                        } else {
                            AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                        }
                    }
                });
            } else {
                AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
            }
        }
    </script>
@endsection
