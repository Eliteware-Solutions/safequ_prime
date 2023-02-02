@extends('frontend.layouts.app', ['new_header' => true, 'header_show' => false, 'header2' => false, 'footer' => false, 'new_footer' => true])

@php
    // For Customer and Farmer Reviews
    $json = File::get(storage_path('testimonials/reviews.json'));
    $json_data = json_decode($json, true);
@endphp

@section('content')

    <!-- Navigation -->
    <header class="header">
        <nav class="d-flex align-items-center justify-content-between container">
            <div class="d-flex align-items-center fix-part">
                <a href="/" class="nav-logo mr-3" aria-label="SafeQu" title="SafeQu">
                    <img src="{{ static_asset('assets/img/new-design/safequ-logo.png') }}" width="260" height="123"
                        alt="SafeQU">
                </a>
                <a href="javascript:void(0);"
                    class="detct-loc text-black fw600 mr-2 d-flex align-items-center justify-content-between mb-0">
                    Detect Location
                    <img src="{{ static_asset('assets/img/new-design/dwn-arw.svg') }}" class="injectable" alt="Down Arrow">
                </a>
            </div>
            <div class="collapsible d-flex align-items-center justify-content-between trnsn-300ms">
                <div class="search-bar mx-auto d-flex align-items-center rounded-pill">
                    <input type="text" id="nav-search-text" class="rounded-pill trnsn-300ms" value=""
                        placeholder="Search Here">
                    <img src="{{ static_asset('assets/img/new-design/search-icn.svg') }}" width="40" height="40"
                        class="injectable ml-1 rounded-circle trnsn-300ms" id="nav-search" alt="Search Icon">
                </div>

                <ul class="nav-menu p-0 m-0 d-flex align-items-center">
                    <li><a href="/" class="nav-link fw700 active py-1 px-2 mr-2">Home</a></li>
                    <li><a href="#" class="nav-link fw700 py-1 px-2 mr-2">Products</a></li>
                    <li><a href="#" class="nav-link fw700 py-1 px-2 mr-2">Blogs</a></li>
                    <li><a href="#" class="nav-link fw700 py-1 px-2 mr-2">Community</a></li>
                </ul>

                <div class="nav-icons d-flex align-items-center">
                    <a href="#" aria-label="SafeQu" title="SafeQu">
                        <img src="{{ static_asset('assets/img/new-design/user-icon-primary.svg') }}"
                            class="injectable ml-2 mr-2 rounded-circle trnsn-300ms" alt="User Icon">
                    </a>
                    <a href="#" aria-label="SafeQu" title="SafeQu">
                        <img src="{{ static_asset('assets/img/new-design/btn-cart-primary.svg') }}"
                            class="injectable rounded-circle trnsn-300ms" alt="Cart Icon">
                    </a>
                </div>
            </div>

            <div class="menu-toggle display-none">
                <img src="{{ static_asset('assets/img/new-design/menu.svg') }}" width="150" height="150"
                    class="injectable" alt="Menu icon">
            </div>
        </nav>
    </header>

    <main>
        <input type="hidden" id="local_shop_id" name="local_shop_id" value="{{ intval($local_shop_id) }}">

        <!-- Hero Slider -->
        <section class="hero-sec">

            <div class="owl-carousel owl-theme hero-slider">
                <div class="item p-0">

                    <div class="d-flex carousel-item active position-relative py-5">
                        <img src="{{ static_asset('assets/img/new-design/hero-bg-1.webp') }}" class="banner b-rd-20"
                            alt="Hero Image">

                        <div class="container">
                            <div class="row position-relative align-items-center h-100">
                                <div class="col-lg-6">
                                    <p class="text-white hero-subtitle mb-1">Exotic Fruits</p>

                                    <h1 class="text-white mb-4 pb-1">30%* Cheaper</h1>

                                    <p class="text-white fw500 mb-4">Farm fresh produce like strawberries & avocados
                                        delivered to
                                        your doorstep, DIRECTLY from your choice of local farms serving your community. ~30%
                                        cheaper than those expensive halls of food or baskets of nature.</p>

                                    <button class="btn btn-fill-white hover-primary">Join Your Community Now</button>
                                </div>

                                <div class="col-lg-6">
                                    <img src="{{ static_asset('assets/img/new-design/hero-img-1.webp') }}"
                                        class="crousel-img" width="731" height="557" alt="Hero Image">
                                </div>
                            </div>

                        </div>

                    </div>
                </div>

                <div class="item p-0">
                    <div class="d-flex carousel-item position-relative active py-5">
                        <img src="{{ static_asset('assets/img/new-design/hero-bg-2.webp') }}" class="banner b-rd-20"
                            alt="Hero Image">

                        <div class="container">
                            <div class="row position-relative align-items-center h-100">
                                <div class="col-lg-6">
                                    <h1 class="text-white mb-4 pb-1">Strawberry</h1>

                                    <div class="prd-desc d-flex mb-4">
                                        <div class="d-flex align-items-center mr-md-4">
                                            <img src="{{ static_asset('assets/img/new-design/hero-icon-1.svg') }}"
                                                class="injectable" width="72" height="72" alt="Hero Icon">
                                            <span class="text-white ml-3">Great for Skin</span>
                                        </div>

                                        <div class="d-flex align-items-center">
                                            <img src="{{ static_asset('assets/img/new-design/hero-icon-2.svg') }}"
                                                class="injectable" width="72" height="72" alt="Hero Icon">
                                            <span class="text-white ml-3">Improves Heart Health</span>
                                        </div>
                                    </div>

                                    <button class="btn btn-fill-white mt-2">
                                        <img src="{{ static_asset('assets/img/new-design/btn-cart.svg') }}"
                                            onload="SVGInject(this)" alt="Btn Cart">
                                        Add to cart
                                    </button>
                                </div>

                                <div class="col-lg-5 offset-lg-1">
                                    <img src="{{ static_asset('assets/img/new-design/hero-img-2.webp') }}"
                                        class="crousel-img" width="569" height="406" alt="Hero Image">
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </section>

        <!-- Category Grid -->
        <section class="cat-grid py-4">
            <div class="container">
                <div class="row py-md-3">
                    <div class="col-md-4 py-2 px-lg-4">
                        <div class="cat-card cat-1 px-xl-4 p-3 b-rd-10">
                            <p class="fw600 cat-name">Fresh <br> Vegetables</p>
                            <div class="cat-img text-right">
                                <img src="{{ static_asset('assets/img/new-design/vegs.png') }}" width="241"
                                    height="120" alt="Vegetables">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 py-2 px-lg-4">
                        <div class="cat-card cat-2 px-xl-4 p-3 b-rd-10">
                            <p class="fw600 cat-name">Fresh <br> Fruits</p>
                            <div class="cat-img text-right">
                                <img src="{{ static_asset('assets/img/new-design/fruits.png') }}" width="212"
                                    height="144" alt="Fruits">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 py-2 px-lg-4">
                        <div class="cat-card cat-3 px-xl-4 p-3 b-rd-10">
                            <p class="fw600 cat-name">Fresh <br> Flowers</p>
                            <div class="cat-img text-right">
                                <img src="{{ static_asset('assets/img/new-design/flowers.png') }}" width="171"
                                    height="159" alt="Flowers">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Customer Favourits -->
        <section class="cust-fav pt-lg-5 py-4">
            <div class="container">
                <h2 class="title text-center">Customer Favorites</h2>
                <div class="owl-carousel owl-theme product-slider">
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
                            <div class="prd-card b-rd-10 overflow-hide trnsn-300ms">
                                <div class="prd-img">
                                    <img src="{{ uploaded_asset($prd_val->product->photos) }}"
                                        data-src="{{ uploaded_asset($prd_val->product->thumbnail_img) }}"
                                        class="object-cover-center" width="250" height="250"
                                        onerror="this.onerror=null;this.src='{{ static_asset('assets/img/no-image-found.jpg') }}';"
                                        alt="{{ $prd_val->product->getTranslation('name') }}">
                                </div>
                                <div class="prd-content p-3 position-relative">
                                    <span class="prd-tag text-white b-rd-5">Organic</span>
                                    <p class="prd-loc mb-1 secondary-text">
                                        {{ $prd_val->product->manufacturer_location ?? '-' }}</p>
                                    <p class="prd-name mb-1 fw700 text-black">{{ $prd_val->product->name ?? '-' }}</p>
                                    <p class="prd-desc mb-1 light-text">Flavor : Sour</p>
                                    <p class="prd-pricing mb-2 fw700">&#8377; {!! single_price_web($product_price) !!} /
                                        {{ $qty_unit_main }}</p>
                                    <button class="btn secondary-btn-o"
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

        <!-- About -->
        <section class="about pt-lg-5 py-4 v-light-bg">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6 py-2">
                        <div class="d-flex justify-content-center flex-column h-100">
                            <h2 class="title mb-2">Pay Lowest Price</h2>
                            <h3 class="title title-large">
                                <span class="secondary-text">Get Healthiest</span> <br>
                                <span class="primary-text">Vegetables And Fruits.</span>
                            </h3>
                            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Fugiat neque velit nostrum error
                                necessitatibus voluptates sit distinctio saepe quas!</p>

                            <button class="btn btn-fill-black mb-3">Shop Now</button>
                        </div>
                    </div>
                    <div class="col-lg-5 offset-lg-1 py-2">
                        <div class="about-img position-relative">
                            <img src="{{ static_asset('assets/img/new-design/ellipse-bg.svg') }}" class="injectable"
                                width="490" height="531" alt="Ellipse">
                            <img src="{{ static_asset('assets/img/new-design/fruits-heart.webp') }}"
                                class="position-relative" width="554" height="592" alt="Fruite">
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Deals Of The Day -->
        <section class="deals pt-lg-5 py-4">
            <div class="container">
                <div class="content pt-lg-5 pt-4 overflow-hide">
                    <h2 class="title text-center pt-2 text-white">Deals Of The Day</h2>
                    <div class="owl-carousel owl-theme product-slider2 mx-auto">
                        @foreach ($deals_of_the_day as $prd_val)
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
                                <div class="prd-card b-rd-10 overflow-hide trnsn-300ms">
                                    <div class="prd-img">
                                        <img src="{{ uploaded_asset($prd_val->product->photos) }}"
                                            data-src="{{ uploaded_asset($prd_val->product->thumbnail_img) }}"
                                            class="object-cover-center" width="250" height="250"
                                            onerror="this.onerror=null;this.src='{{ static_asset('assets/img/no-image-found.jpg') }}';"
                                            alt="{{ $prd_val->product->getTranslation('name') }}">
                                    </div>
                                    <div class="prd-content p-3 position-relative">
                                        <span class="prd-tag text-white b-rd-5">Organic</span>
                                        <p class="prd-loc mb-1 secondary-text">
                                            {{ $prd_val->product->manufacturer_location ?? '-' }}</p>
                                        <p class="prd-name mb-1 fw700 text-black">{{ $prd_val->product->name ?? '-' }}</p>
                                        <p class="prd-desc mb-1 light-text">Flavor : Sour</p>
                                        <p class="prd-pricing mb-2 fw700">&#8377; {!! single_price_web($product_price) !!} /
                                            {{ $qty_unit_main }}</p>
                                        <button class="btn secondary-btn-o"
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
            </div>
        </section>

        <!-- Hear from Customers -->
        <section class="hear-customers pt-lg-5 py-4 v-light-bg">
            <div class="container">
                <h2 class="title text-center">Hear it from Our Customers</h2>

                <div class="owl-carousel owl-theme testimonials">

                    @foreach ($json_data['customer_reviews'] as $rev)
                        <div class="item">
                            <div class="quote-card p-3 trnsn-300ms position-relative b-rd-10">
                                <div class="d-flex align-items-center position-relative">
                                    <div class="usr-img mr-2">
                                        <img src="{{ static_asset('assets/img/new-design/user-icon.svg') }}"
                                            onload="SVGInject(this)" alt="Star">
                                    </div>
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
                                <p class="mb-1 mt-3 position-relative">{!! $rev['review'] !!}</p>
                            </div>
                        </div>
                    @endforeach

                </div>
            </div>
        </section>

        <!-- Our Full Range -->
        @if (count($our_full_range_of_products) > 0)
            <section class="our-range pt-lg-5 py-4" id="our-range">
                <div class="container">
                    <h2 class="title text-center">Our Full Range</h2>

                    <div class="filters px-3 pb-4 d-flex flex-wrap align-items-center justify-content-center">
                        <a href="#all_prd" aria-controls="all" role="tab" data-toggle="tab"
                            class="selected m-2 rounded-lg">All</a>
                        @foreach ($parentCategories as $p_category)
                            <a href="#category_{{ $p_category->id }}" aria-controls="category_{{ $p_category->id }}"
                                role="tab" data-toggle="tab" class="m-2 rounded-lg">{{ $p_category->name }}</a>
                        @endforeach
                    </div>
                    <div class="tab-content py-0">
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
                                        <div class="prd-card b-rd-10 overflow-hide trnsn-300ms">
                                            <div class="prd-img">
                                                <img src="{{ uploaded_asset($prd_val->product->photos) }}"
                                                    data-src="{{ uploaded_asset($prd_val->product->thumbnail_img) }}"
                                                    class="object-cover-center" width="250" height="250"
                                                    onerror="this.onerror=null;this.src='{{ static_asset('assets/img/no-image-found.jpg') }}';"
                                                    alt="{{ $prd_val->product->getTranslation('name') }}">
                                            </div>
                                            <div class="prd-content p-3 position-relative">
                                                <span class="prd-tag text-white b-rd-5">Organic</span>
                                                <p class="prd-loc mb-1 secondary-text">
                                                    {{ $prd_val->product->manufacturer_location ?? '-' }}</p>
                                                <p class="prd-name mb-1 fw700 text-black">
                                                    {{ $prd_val->product->name ?? '-' }}</p>
                                                <p class="prd-desc mb-1 light-text">Flavor : Sour</p>
                                                <p class="prd-pricing mb-2 fw700">&#8377; {!! single_price_web($product_price) !!} /
                                                    {{ $qty_unit_main }}</p>
                                                <button class="btn secondary-btn-o"
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
                            <div role="tabpanel" class="filter-carousel tab-pane" id="category_{{ $p_category->id }}">
                                @if (count($best_selling_products[$p_category->id]) > 0)
                                    <div class="owl-carousel owl-theme product-slider">
                                        @foreach ($best_selling_products[$p_category->id] as $prd_val)
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
                                                <div class="prd-card b-rd-10 overflow-hide trnsn-300ms">
                                                    <div class="prd-img">
                                                        <img src="{{ uploaded_asset($prd_val->product->photos) }}"
                                                            data-src="{{ uploaded_asset($prd_val->product->thumbnail_img) }}"
                                                            class="object-cover-center" width="250" height="250"
                                                            onerror="this.onerror=null;this.src='{{ static_asset('assets/img/no-image-found.jpg') }}';"
                                                            alt="{{ $prd_val->product->getTranslation('name') }}">
                                                    </div>
                                                    <div class="prd-content p-3 position-relative">
                                                        <span class="prd-tag text-white b-rd-5">Organic</span>
                                                        <p class="prd-loc mb-1 secondary-text">
                                                            {{ $prd_val->product->manufacturer_location ?? '-' }}</p>
                                                        <p class="prd-name mb-1 fw700 text-black">
                                                            {{ $prd_val->product->name ?? '-' }}</p>
                                                        <p class="prd-desc mb-1 light-text">Flavor : Sour</p>
                                                        <p class="prd-pricing mb-2 fw700">&#8377; {!! single_price_web($product_price) !!} /
                                                            {{ $qty_unit_main }}</p>
                                                        <button class="btn secondary-btn-o"
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
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        <!-- Recycling -->
        <section class="recycling py-4">
            <div class="container position-relative">
                <div class="content b-rd-10">
                    <div class="bg-img b-rd-20 overflow-hide">
                        <img src="{{ static_asset('assets/img/new-design/recycle-bg.webp') }}" width="1440"
                            height="397" alt="Recycling Bg">
                    </div>
                    <div class="row position-relative py-3 py-lg-0">
                        <div class="col-lg-4 offset-lg-1 py-3">
                            <div class="recycle-img">
                                <img src="{{ static_asset('assets/img/new-design/recycle-img.webp') }}" width="374"
                                    height="366" alt="Recycling">
                            </div>
                        </div>
                        <div class="col-lg-5 offset-lg-1 py-3">
                            <div class="d-flex justify-content-center flex-column h-100">
                                <h2 class="title secondary-text">We F & V Certified Plastic Neutral</h2>
                                <p class="large text-white">We F & V Certified Plastic Neutral Brand</p>
                                <p class="large text-white">We will Recycle an equal amount of plastic Focused by us</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Hear from Farmers -->
        <section class="hear-farmers pt-lg-5 py-4 v-light-bg">
            <div class="container">
                <h2 class="title text-center">Hear it from Our Farmers</h2>

                <div class="owl-carousel owl-theme testimonials">
                    @foreach ($json_data['farmer_reviews'] as $rev)
                        <div class="item">
                            <div class="quote-card p-3 trnsn-300ms position-relative b-rd-10">
                                <div class="d-flex align-items-center position-relative">
                                    <div class="usr-img mr-2">
                                        <img src="{{ static_asset('assets/img/new-design/user-icon.svg') }}"
                                            onload="SVGInject(this)" alt="Star">
                                    </div>
                                    <div class="usr-data">
                                        <h5 class="secondary-text mb-1">{{ $rev['name'] }}</h5>
                                        <p class="mb-0">{{ $rev['farm'] }}</p>
                                    </div>
                                    <div class="usr-rate">
                                        @for ($i = 1; $i <= $rev['rating']; $i++)
                                            <img src="{{ static_asset('assets/img/new-design/star.svg') }}"
                                                onload="SVGInject(this)" alt="Star">
                                        @endfor
                                    </div>
                                </div>
                                <p class="mb-1 mt-3 position-relative">{!! $rev['review'] !!}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- Communities -->
        <section class="communities pt-lg-5 py-4">
            <div class="container">
                <h2 class="title text-center mb-2">Our Most Popular Communities</h2>
                <p class="sub-text mb-4 text-center mx-auto fw500">
                    More than <span class="primary-text fw700">500+ customers</span> across South Mumbai's finest gated
                    communities have already signed up to the SafeQU experience. Choose your community.
                </p>

                <div class="owl-carousel owl-theme community-slider mb-3">
                    @foreach ($communities as $community)
                        <div class="item">
                            <div class="community-card p-3 trnsn-300ms position-relative b-rd-10">
                                <div class="cm-img b-rd-10 overflow-hide">
                                    @if (isset($community->user->avatar_original))
                                        <img src="{{ uploaded_asset($community->user->avatar_original) }}"
                                            onerror="this.onerror=null;this.src='{{ static_asset('assets/img/community-building.png') }}';"
                                            class="img-rounded" alt="{{ $community->name }}" width="200"
                                            height="200">
                                    @else
                                        <img src=""
                                            onerror="this.onerror=null;this.src='{{ static_asset('assets/img/community-building.png') }}';"
                                            class="img-rounded" alt="{{ $community->name }}">
                                    @endif
                                </div>
                                <div class="content text-center pt-3">
                                    <p class="cm-loc mb-1 fw500 secondary-text">
                                        <img src="{{ static_asset('assets/img/new-design/map-pin.svg') }}"
                                            onload="SVGInject(this)" width="10" height="12" alt="Map Pin">
                                        {{ $community->address }}
                                    </p>
                                    <p class="cm-title fw700">{{ $community->name }}</p>
                                    @if (auth()->user() &&
                                            intval(auth()->user()->joined_community_id) > 0 &&
                                            auth()->user()->joined_community_id != $community->user_id)
                                        <a href="javascript:void(0);" class="btn secondary-btn-o"
                                            onclick="confrimCommunityChange('{{ route('shop.visit', $community->slug) }}');">
                                            Join Now</a>
                                    @else
                                        <a href="{{ route('shop.visit', $community->slug) }}"
                                            class="btn secondary-btn-o">Join Now</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <p class="sub-text text-center mx-auto fw600 pb-lg-3 pb-2">
                    Not able to find your community? Request to get started now. Ping us here and we will get your community
                    setup in minutes.
                </p>

                <button class="btn btn-fill-black mx-auto d-block mb-4">Create Community</button>
            </div>
        </section>

        <!-- Rewards -->
        <section class="rewards py-lg-5 py-4">
            <div class="container py-lg-5 py-2">
                <div class="row">
                    <div class="col-lg-6">
                        <h2 class="title">Rewards for Using App on <br> Continuous Basis</h2>
                    </div>
                    <div class="col-lg-6 text-center pt-2">
                        <img src="{{ static_asset('assets/img/new-design/rewards.svg') }}" width="598" height="357"
                            class="injectable" alt="Rewards Image">
                    </div>
                </div>
            </div>
        </section>

        <!-- Insta Feed -->
        <section class="insta-feed pt-lg-5 py-4 v-light-bg">
            <div class="container">
                <h2 class="title text-center">Instagram Feeds</h2>

                <div class="owl-carousel owl-theme insta-feed-slider" id="instafeed"></div>

            </div>
        </section>

        <!-- Get In Touch -->
        <section class="get-in-touch pt-lg-5 py-4 v-light-bg">
            <div class="container">
                <div class="content b-rd-10 overflow-hide text-center">
                    <div class="row position-relative">
                        <div class="col-lg-7 col-md-8 mx-auto">
                            <h2 class="title text-white">Need Any Help?</h2>
                            <p class="text-white fw700">Lorem ipsum dolor sit amet, consectetur
                                adipisicing elit. Architecto blanditiis perspiciatis aspernatur dolores enim earum libero,
                                vero laudantium error nisi omnis, nesciunt, neque tempore!</p>
                            <button class="btn btn-fill-white">Get in touch</button>
                        </div>
                    </div>
                </div>
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

        <!-- Join Community Modal -->
        <div class="modal fade" id="joinCommunity" tabindex="-1" aria-labelledby="joinCommunityLabel"
            aria-hidden="true" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-dialog-scrollable">
                <div class="modal-content">

                    <div class="modal-header text-center">
                        <h5 class="modal-title" id="joinCommunityLabel">Select Your Community</h5>
                    </div>

                    <div class="modal-body">
                        <div class="row">
                            @foreach ($communities as $community)
                                <div class="col-lg-4 col-md-6 px-2 py-3">
                                    <a href="javascript:void(0);" class="position-relative"
                                        onclick="setLocalCommunity({{ $community->id }});">
                                        <div class="community-card mx-auto p-3">
                                            <div class="card-img">
                                                @if (isset($community->user->avatar_original))
                                                    <img src="{{ uploaded_asset($community->user->avatar_original) }}"
                                                        onerror="this.onerror=null;this.src='{{ static_asset('assets/img/community-building.png') }}';"
                                                        class="img-rounded" alt="{{ $community->name }}">
                                                @else
                                                    <img src=""
                                                        onerror="this.onerror=null;this.src='{{ static_asset('assets/img/community-building.png') }}';"
                                                        class="img-rounded" alt="{{ $community->name }}">
                                                @endif
                                            </div>
                                            <div class="card-data ml-2 mr-auto">
                                                <h6 class="mb-1">{{ $community->name }}</h6>
                                                <p class="mb-0 body-txt">{{ $community->address }}</p>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </main>

    <footer>
        <div class="container pb-lg-5 pb-4">
            <div class="row">
                <div class="col-md-4 pt-3">
                    <div class="footer-logo pb-3">
                        <img src="{{ static_asset('assets/img/new-design/safequ-logo.png') }}" width="260"
                            height="123" alt="SafeQu Logo">
                    </div>
                    <p class="fw600 text-white">
                        Lorem ipsum dolor sit, amet consectetur elit. Quasi, non quam fugiat, aliquam obcaecati eveniet
                        adipisicing.
                    </p>
                </div>
                <div class="col-md-8">
                    <div class="row">
                        <div class="col-md-3 col-6 py-3">
                            <p class="links-tag primary-text fw700"><span class="secondary-text">Quick </span> Links</p>
                            <ul class="p-0 m-0">
                                <li><a href="/">Home</a></li>
                                <li><a href="/">Products</a></li>
                                <li><a href="/">Blogs</a></li>
                                <li><a href="/">Community</a></li>
                            </ul>
                        </div>
                        <div class="col-md-3 col-6 py-3">
                            <p class="links-tag primary-text fw700"><span class="secondary-text">Other </span> Links</p>
                            <ul class="p-0 m-0">
                                <li><a href="/">My Account</a></li>
                                <li><a href="/">Order History</a></li>
                                <li><a href="/">Cart</a></li>
                            </ul>
                        </div>
                        <div class="col-md-3 col-6 py-3">
                            <p class="links-tag primary-text fw700"><span class="secondary-text">Legal </span> Links</p>
                            <ul class="p-0 m-0">
                                <li><a href="/">Privacy Policy</a></li>
                                <li><a href="/">Terms & Conditions</a></li>
                                <li><a href="/">Return Policy</a></li>
                                <li><a href="/">Refund Policy</a></li>
                            </ul>
                        </div>
                        <div class="col-md-3 col-6 py-3">
                            <p class="links-tag primary-text fw700"><span class="secondary-text">Social </span> Links
                            </p>
                            <ul class="p-0 m-0">
                                <li><a href="/">Facebook</a></li>
                                <li><a href="/">Instagram</a></li>
                                <li><a href="/">WhatsApp</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="copyright text-center">
            <p class="text-white mb-0 fw700">Copyright &copy; 2022 SafeQu. All Rights Reserved.</p>
        </div>
    </footer>

    <div class="whats-app-icon">
        <a href="#" class="d-block" area-lable="Contact Us" title="Contact Us">
            <img src="{{ static_asset('assets/img/new-design/whatsapp.svg') }}" class="injectable" width="40"
                height="40" alt="WhatsApp">
        </a>
    </div>
@endsection

@section('script')
    <script>
        // ---- SVG Injector -  To convert IMG tag in SVG code. (Only for SVG images)
        SVGInject(document.querySelectorAll("img.injectable"));

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

        $('.product-slider, .community-slider').owlCarousel({
            ...carouselObj,
            ...{
                margin: 30,
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
                        items: 4,
                    },
                    1200: {
                        items: 4,
                        nav: true,
                    },
                    1440: {
                        items: 5,
                    },
                },
            }
        })

        $('.product-slider2').owlCarousel({
            ...carouselObj,
            ...{
                margin: 15,
                responsive: {
                    0: {
                        items: 1,
                    },
                    767: {
                        items: 2,
                    },
                    992: {
                        items: 3,
                    },
                    1200: {
                        items: 4,
                        nav: true,
                    },
                }
            }
        })

        $('.testimonials').owlCarousel({
            ...carouselObj,
            ...{
                margin: 15,
                responsive: {
                    0: {
                        items: 1,
                    },
                    767: {
                        items: 2,
                    },
                    1200: {
                        items: 3,
                        nav: true,
                    },
                }
            }
        })

        $('.hero-slider').owlCarousel({
            loop: true,
            margin: 30,
            items: 1.15,
            center: true,
            dots: false,
            autoplay: true,
            autoplayTimeout: 7000,
            smartSpeed: 2000,
            responsive: {
                0: {
                    items: 1.05,
                },
                992: {
                    items: 1.15,
                },
            }
        })

        setTimeout(() => {
            $(".our-range").css('height', document.getElementById('our-range').clientHeight);
        }, 1000)

        $('.menu-toggle').click(function() {
            $(this).toggleClass('active');
            $('.collapsible').toggleClass('active');
        })

        $(document).ready(function() {

            // Join Community modal trigger after Page Load
            if ($('#local_shop_id').val() == 0 || $('#local_shop_id').val() == null) {
                setTimeout(function() {
                    $('#joinCommunity').modal('show');
                }, 1000)
            }

            // $('.carousel').carousel({
            //     interval: 7000,
            // })

            // setInterval(() => {
            //     $(".smiley").hasClass("fa-smile-wink") ? $(".smiley").removeClass("fa-smile-wink").addClass(
            //         "fa-smile") : $(".smiley").removeClass("fa-smile").addClass("fa-smile-wink");
            // }, 1000);

            $('a[data-toggle="tab"]').click(function() {
                $('a[data-toggle="tab"]').removeClass('selected');
                $(this).addClass('selected');

                let activeDiv = $(this).attr('href');

                $('.filter-carousel').removeClass('active');
                $(activeDiv).addClass('active');

                $(".filter-carousel .owl-carousel").trigger('refresh.owl.carousel');
            })

            // $("#tykeModal").modal('show');
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

    <script src="{{ static_asset('assets/js/instafeed.min.js')}}"></script>
    <script type="text/javascript">
        let templateHTML = `<div class="item">
            <div class="feed-card trnsn-300ms">
                <div class="feed-img">
                    <img src="{{ image }}" width="281" height="281" alt="Insta Feed">
                </div>
                <div class="likes d-flex align-items-center py-2 fw600 light-text">
                    <img src="./assets/images/like.svg" onload="SVGInject(this)" alt="Like">
                    16 likes
                </div>
                <p class="mb-0">{{ caption }}</p>
            </div>
        </div>`;

        var feed = new Instafeed({
            accessToken: 'IGQVJVMEpHcFBwZAEw0NHFVa1NTWHluejlhSWJNV0VWbFB3LWswSHNtRjNwbjdXTFhhb0RlM2ZAvb2dkMDRFNmRIaURDVFRCU0kyOVlyR1lqTUV6aTFwT0tKTENScW8yWVBzNXhGOWJfNHVyd0lYTnRGWQZDZD',
            template: templateHTML
        });
        feed.run();

        setTimeout(() => {
            $(".insta-feed-slider").owlCarousel({
                ...carouselObj,
                margin: 30,
                responsive: {
                    0: {
                        items: 1
                    },
                    575: {
                        items: 2
                    },
                    768: {
                        items: 3
                    },
                    992: {
                        items: 4
                    },
                    1200: {
                        items: 4,
                        nav: !0
                    },
                    1440: {
                        items: 5
                    }
                }
            })
        }, 2000);
    </script>
@endsection
