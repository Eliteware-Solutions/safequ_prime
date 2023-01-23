@extends('frontend.layouts.app', ['header_show' => true, 'header2' => false, 'footer' => true])

@section('content')
    <main>
        <input type="hidden" id="local_shop_id" name="local_shop_id" value="{{ intval($local_shop_id) }}">

        <!-- Hero Slider -->
        <section class="hero-sec">

            <div class="owl-carousel owl-theme hero-slider">
                <div class="item p-0">

                    <div class="d-flex carousel-item active position-relative py-5">
                        <img src="{{ static_asset('assets/img/new-design/hero-bg-1.webp') }}" class="banner rounded-4"
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
                        <img src="{{ static_asset('assets/img/new-design/hero-bg-2.webp') }}" class="banner rounded-4"
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

        {{-- <div id="carouselExampleIndicators" class="carousel slide carousel-fade" data-ride="carousel">
            <div class="carousel-inner">
                <div class="carousel-item active">

                    <div class="home-slider">
                        <div class="container container2">
                            <div class="row">
                                <div class="col-md-7 px-0">
                                    <div class="slider-content animate__animated animate__fadeInLeft 100vh pr-lg-5 py-4">
                                        <h1 class="fw600 mb-0">Exotic Fruits</h1>
                                        <h1 class="fw800 mb-3 primary-color">30%* cheaper.</h1>
                                        <p class="mb-2 pr-md-4 fw500 sub-txt">Farm fresh produce like strawberries &
                                            avocados delivered to your doorstep, <span
                                                class="fw700 fsize17 primary-color">DIRECTLY</span> from your choice
                                            of
                                            local farms serving your community. ~30% cheaper than those expensive <u
                                                class="fw500 text-underline">halls of food</u> or <u
                                                class="fw500 text-underline">baskets of nature</u> <i
                                                class="fas fa-smile-wink smiley primary-color-dark align-middle"></i>
                                        </p>

                                        <a href="#communities">
                                            <p class="explore-card my-4 fw500">Join your community now &nbsp;
                                                <i class="fal fa-long-arrow-right fsize20 align-middle"></i>
                                            </p>
                                        </a>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="communities"></div>

                </div>
            </div>
        </div> --}}

        <!-- Category Grid -->
        <section class="cat-grid py-4">
            <div class="container">
                <div class="row py-md-3">
                    <div class="col-md-4 py-2 px-lg-4">
                        <div class="cat-card cat-1 px-xl-4 p-3 b-rd-10">
                            <p class="fw600 cat-name">Fresh <br> Vegetables</p>
                            <div class="cat-img text-right">
                                <img src="{{ static_asset('assets/img/new-design/vegs.png') }}" width="241" height="120" alt="Vegetables">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 py-2 px-lg-4">
                        <div class="cat-card cat-2 px-xl-4 p-3 b-rd-10">
                            <p class="fw600 cat-name">Fresh <br> Fruits</p>
                            <div class="cat-img text-right">
                                <img src="{{ static_asset('assets/img/new-design/fruits.png') }}" width="212" height="144" alt="Fruits">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 py-2 px-lg-4">
                        <div class="cat-card cat-3 px-xl-4 p-3 b-rd-10">
                            <p class="fw600 cat-name">Fresh <br> Flowers</p>
                            <div class="cat-img text-right">
                                <img src="{{ static_asset('assets/img/new-design/flowers.png') }}" width="171" height="159" alt="Flowers">
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
                    <div class="item">
                        <div class="prd-card b-rd-10 overflow-hide trnsn-300ms">
                            <div class="prd-img">
                                <img src="{{ static_asset('assets/img/new-design/afghani-apricot.png') }}" class="object-cover-center" width="264"
                                    height="215" alt="Afghanistan Apricot">
                            </div>
                            <div class="prd-content p-3 position-relative">
                                <span class="prd-tag text-white b-rd-5">Hydroponic</span>
                                <p class="prd-loc mb-1 secondary-text">Afghanistan</p>
                                <p class="prd-name mb-1 fw700 text-black">Afghanistan Apricot</p>
                                <p class="prd-desc mb-1 light-text">Flavor : Sour</p>
                                <p class="prd-pricing mb-2 fw700">&#8377; 349.00 / 500 Gms</p>
                                <button class="btn secondary-btn-o">
                                    <img src="{{ static_asset('assets/img/new-design/btn-cart.svg') }}" onload="SVGInject(this)" alt="Btn Cart">
                                    Add to cart
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        @if (count($best_selling_products_combined) > 0)
            <section class="tabslidersec">
                <div class="container">
                    <div class="row">
                        <div class="col-12">
                            <h4 class="fw700 title-txt text-center">Best Selling
                                <ins class="primary-color fw700">Products</ins>
                            </h4>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div>
                                <!-- Nav tabs -->
                                <ul class="nav nav-tabs tabslider" role="tablist">
                                    <li role="presentation">
                                        <a id="eventBtn" class="active" href="#all_prd" aria-controls="all_prd"
                                            role="tab" data-toggle="tab">all</a>
                                    </li>
                                    @foreach ($parentCategories as $p_category)
                                        <li role="presentation">
                                            <a href="#category_{{ $p_category->id }}" class="sec"
                                                aria-controls="category_{{ $p_category->id }}" role="tab"
                                                data-toggle="tab">{{ $p_category->name }}</a>
                                        </li>
                                    @endforeach
                                </ul>
                                <!-- Tab panes -->
                                <div class="tab-content py-0">
                                    <div role="tabpanel" class="tab-pane active" id="all_prd">
                                        <div class="owl-carousel alltabs ">
                                            @foreach ($best_selling_products_combined as $prd_val)
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
                                                <div class="tab_slider_card">
                                                    <div>
                                                        <div class="card-img mb-1">
                                                            <img src="{{ uploaded_asset($prd_val->product->photos) }}"
                                                                data-src="{{ uploaded_asset($prd_val->product->thumbnail_img) }}"
                                                                onerror="this.onerror=null;this.src='{{ static_asset('assets/img/no-image-found.jpg') }}';"
                                                                alt="{{ $prd_val->product->getTranslation('name') }}"
                                                                class="img-rounded">
                                                        </div>
                                                        <div class="tabcard-detail">
                                                            <span>{{ $prd_val->product->manufacturer_location ?? '-' }}</span>
                                                            <p class="titlecard">{{ $prd_val->product->name ?? '-' }}</p>
                                                            <p class="price">{!! single_price_web($product_price) !!} /
                                                                {{ $qty_unit_main }}
                                                            </p>
                                                            <div class="cartbtn">
                                                                <img src="public/assets/img/carts.svg" class=" cart"
                                                                    alt="cart">
                                                                <a href="javacript:;" class="cartbtn"
                                                                    onclick="addToCart({{ $prd_val->product->id }}, {{ $prd_val->id }}, {{ $addCartQty }});">
                                                                    Add to Cart
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    @foreach ($parentCategories as $p_category)
                                        <div role="tabpanel" class="tab-pane " id="category_{{ $p_category->id }}">
                                            @if (count($best_selling_products[$p_category->id]) > 0)
                                                <div class="owl-carousel alltabs ">
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
                                                        <div class="tab_slider_card">
                                                            <div>
                                                                <div class="card-img mb-1">
                                                                    <img src="{{ uploaded_asset($prd_val->product->photos) }}"
                                                                        data-src="{{ uploaded_asset($prd_val->product->thumbnail_img) }}"
                                                                        onerror="this.onerror=null;this.src='{{ static_asset('assets/img/no-image-found.jpg') }}';"
                                                                        alt="{{ $prd_val->product->getTranslation('name') }}"
                                                                        class="img-rounded">
                                                                </div>
                                                                <div class="tabcard-detail">
                                                                    <span>{{ $prd_val->product->manufacturer_location ?? '-' }}</span>
                                                                    <p class="titlecard">
                                                                        {{ $prd_val->product->name ?? '-' }}
                                                                    </p>
                                                                    <p class="price">{!! single_price_web($product_price) !!} /
                                                                        {{ $qty_unit_main }}</p>
                                                                    <div class="cartbtn">
                                                                        <img src="public/assets/img/carts.svg"
                                                                            class=" cart" alt="cart">
                                                                        <a href="javacript:;" class="cartbtn"
                                                                            onclick="addToCart({{ $prd_val->product->id }}, {{ $prd_val->id }}, {{ $addCartQty }});">
                                                                            Add to Cart
                                                                        </a>
                                                                    </div>
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
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class=" text-center"><a href="{{ route('shop.visit', $seller->user->shop->slug) }}"
                                    class="viewbtn mt-0">View All</a></div>
                        </div>
                    </div>
                </div>
            </section>
        @endif


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
                            <img src="{{ static_asset('assets/img/new-design/ellipse-bg.svg') }}" class="injectable" width="490" height="531"
                                alt="Ellipse">
                            <img src="{{ static_asset('assets/img/new-design/fruits-heart.webp') }}" class="position-relative" width="554"
                                height="592" alt="Fruite">
                        </div>
                    </div>
                </div>
            </div>
        </section>


        <div class="light-bg py-5">
            <div class="container pt-3 services">
                <div class="row">
                    <div class="col-lg-3 col-sm-6 px-lg-4 pt-2 pb-3 text-center">
                        <div class="icons3D">
                            <img src="{{ static_asset('assets/img/fast-delivery.png') }}" alt="3D Icon" />
                        </div>
                        <h6 class="fw700 my-2">Direct from farms of your choice</h6>
                        <p class="mb-0">Love strawberries? Order them directly from a farm in Nashik or
                            Mahabaleshwar. Your choice!</p>
                    </div>

                    <div class="col-lg-3 col-sm-6 px-lg-4 pt-2 pb-3 text-center">
                        <div class="icons3D">
                            <img src="{{ static_asset('assets/img/fruits-vector-graphic.png') }}" alt="3D Icon" />
                        </div>

                        <h6 class="fw700 fsize16 my-2">Fresh, like it's from your back garden</h6>
                        <p class="mb-0">Order only what you need and get it fresh from the farm within 12hrs** of
                            harvest! **For most products</p>
                    </div>

                    <div class="col-lg-3 col-sm-6 px-lg-4 pt-2 pb-3 text-center">
                        <div class="icons3D">
                            <img src="{{ static_asset('assets/img/india-flag.png') }}" alt="3D Icon" />
                        </div>

                        <h6 class="fw700 fsize16 my-2">Atmanirbhar India</h6>
                        <p class="mb-0">Support farmers across India by cutting out the middlemen.</p>
                    </div>

                    <div class="col-lg-3 col-sm-6 px-lg-4 pt-2 pb-3 text-center">
                        <div class="icons3D">
                            <img src="{{ static_asset('assets/img/fair-pricing.png') }}" alt="3D Icon" />
                        </div>

                        <h6 class="fw700 fsize16 my-2">Fair Pricing</h6>
                        <p class="mb-0">Without middlemen and a dynamic community model at play, the local farms
                            are able to offer you fresh produce at lower prices compared to high street retailers.</p>
                    </div>
                </div>
            </div>
        </div>

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

        <div class="primary-color-bg community-create my-4">
            <div class="container py-3">
                <div class="row justify-content-center">
                    <div class="col-md-9 px-0">
                        <div class="px-4 py-5 text-center">
                            <h5 class="text-white mb-3 fw600">Not able to find your community? <br />
                                Request to get started now.
                            </h5>

                            <p class="text-white fw500 mb-4">Ping us here and we will get your community setup in
                                minutes.
                            </p>
                            <a href="https://uh19vww4t9p.typeform.com/to/ZuY8xtQq" target="_blank">
                                <button type="button" class="btn mt-3">Create Community</button>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="py-5">
            <div class="container mb-3">
                <h4 class="fw700 text-center">Why communities love <i
                        class="fa fa-heart animated faa-pulse primary-color"></i> SafeQU?</h4>

                <div class="row justify-content-center pt-3">
                    <div class="col-md-7 light-bg">
                        <div class="testimonials owl-carousel owl-theme">
                            <div class="item p-4 px-md-5">
                                <i class="fad fa-quote-left fa-3x mb-3"></i>
                                <p class="body-txt text-center font-italic">SafeQU gives me a business class experience
                                    in
                                    my grocery shopping.</p>

                                <p class="fw700 text-center">NK
                                    <br>
                                    <span class="fsize13">Planet Godrej</span>
                                </p>
                                <p class="text-right mb-0">
                                    <i class="fad fa-quote-right fa-3x"></i>
                                </p>
                            </div>
                            <div class="item p-4 px-md-5">
                                <i class="fad fa-quote-left fa-3x mb-3"></i>
                                <p class="body-txt text-center font-italic">Love all your produce….Avocados, Passion
                                    Fruits
                                    and Strawberries. Most of all your customer attention to detail. Thanks!</p>

                                <p class="fw700 text-center">SK
                                    <br>
                                    <span class="fsize13">Chaitanya Towers</span>
                                </p>
                                <p class="text-right mb-0">
                                    <i class="fad fa-quote-right fa-3x"></i>
                                </p>
                            </div>
                            <div class="item p-4 px-md-5">
                                <i class="fad fa-quote-left fa-3x mb-3"></i>
                                <p class="body-txt text-center font-italic">7 days in and my light pink roses are still
                                    fresh & beautiful. Thanks, SafeQU for sorting me out with my weekly flowers</p>

                                <p class="fw700 text-center">Tarana.S
                                    <br>
                                    <span class="fsize13">Lodha Park</span>
                                </p>
                                <p class="text-right mb-0">
                                    <i class="fad fa-quote-right fa-3x"></i>
                                </p>
                            </div>
                            <div class="item p-4 px-md-5">
                                <i class="fad fa-quote-left fa-3x mb-3"></i>
                                <p class="body-txt text-center font-italic">Thankyou
                                    <a href="https://www.instagram.com/safequ.india/" target="_blank">
                                        <ins class="primary-color">@safequ.india</ins>
                                    </a> for introducing me to the
                                    brilliant farm fresh produce and the weekly salad subscription service. The Romaine
                                    Lettuce particularly felt like it had been picked from my back garden
                                </p>

                                <p class="fw700 text-center">Hema W
                                    <br>
                                    <span class="fsize13">Lodha Park</span>
                                </p>
                                <p class="text-right mb-0">
                                    <i class="fad fa-quote-right fa-3x"></i>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

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
        <!-- Change Community Modal Ends -->

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

    </main>

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

        $(document).ready(function() {

            // Join Community modal trigger after Page Load
            if ($('#local_shop_id').val() == 0 || $('#local_shop_id').val() == null) {
                setTimeout(function() {
                    $('#joinCommunity').modal('show');
                }, 1000)
            }

            $('.carousel').carousel({
                interval: 7000,
            })

            setInterval(() => {
                $(".smiley").hasClass("fa-smile-wink") ? $(".smiley").removeClass("fa-smile-wink").addClass(
                    "fa-smile") : $(".smiley").removeClass("fa-smile").addClass("fa-smile-wink");
            }, 1000);

            $('.community-slider').owlCarousel({
                loop: true,
                autoplay: false,
                autoplayTimeout: 4000,
                autoplayHoverPause: false,
                smartSpeed: 1500,
                responsive: {
                    0: {
                        items: 1
                    },
                    460: {
                        items: 2
                    },
                    768: {
                        items: 3
                    },
                    991: {
                        items: 4
                    },
                    1200: {
                        items: 5
                    }
                }
            })

            $('.testimonials').owlCarousel({
                loop: true,
                nav: true,
                dots: true,
                margin: 10,
                // autoplay: true,
                // autoplayTimeout: 4000,
                autoplayHoverPause: false,
                smartSpeed: 700,
                items: 1,
                navText: ["<i class='fa fa-chevron-left'></i>", "<i class='fa fa-chevron-right'></i>"]
            });

            $(".tabslidersec .owl-carousel").owlCarousel({
                items: 4,
                loop: true,
                margin: 10,
                autoplay: true,
                autoplayTimeout: 4000,
                smartSpeed: 1200,
                autoplayHoverPause: true,
                responsive: {
                    0: {
                        items: 1,
                        nav: true
                    },
                    600: {
                        items: 2,
                        loop: false,
                        loop: true
                    },
                    991: {
                        items: 2,
                        loop: false,
                        loop: true
                    },
                    1000: {
                        items: 3,
                        loop: false,
                        loop: true
                    },
                    1200: {
                        items: 4,
                        nav: true,
                        loop: false,
                        loop: true
                    }
                }
            });

            $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
                e.target // newly activated tab
                e.relatedTarget // previous active tab
                $(".owl-carousel").trigger('refresh.owl.carousel');
            });

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
@endsection
