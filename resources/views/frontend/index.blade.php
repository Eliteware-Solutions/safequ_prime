@extends('frontend.layouts.app', ['header_show' => true, 'header2' => false, 'footer' => true])

@section('content')
    <main>
        <div id="carouselExampleIndicators" class="carousel slide carousel-fade" data-ride="carousel">
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
        </div>

        {{-- <section class="tyke_subscription"> --}}
        {{-- <div class="container"> --}}
        {{-- <div class="row "> --}}
        {{-- <div class="col-lg-4"> --}}
        {{-- <h1 class="fw800">Love what<span class="fw800 primary-color">we do?</span></h1> --}}
        {{-- </div> --}}
        {{-- <div class="col-lg-8"> --}}
        {{-- <p>Join us on our growth journey to empower thousands of Indian farmers and make farm to table a --}}
        {{-- reality for millions of urban Indian households.</p> --}}
        {{-- <div class="link"> --}}
        {{-- <a href="https://www.tykeinvest.com/campaign/safequ-dMZtGjbz" target="_blank">Subscribe to our Tyke Campaign<span><i class="la la-arrow-right la-2x ml-3"></i></span></a> --}}
        {{-- <div class="logos"> --}}
        {{-- <img src="{{ static_asset('assets/img/safequ-logo.png') }}"> --}}
        {{-- <img src="{{ static_asset('assets/img/tyke/logo.png') }}"> --}}
        {{-- </div> --}}
        {{-- </div> --}}
        {{-- </div> --}}

        {{-- </div> --}}
        {{-- </div> --}}
        {{-- </section> --}}


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
                                    <a id="eventBtn" class="active" href="#all_prd" aria-controls="all_prd" role="tab"
                                        data-toggle="tab">all</a>
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
                                            <div class="tab_slider_card">
                                                <div>
                                                    <div class="card-img mb-1">
                                                        <img src="{{ static_asset('assets/img/placeholder.jpg') }}"
                                                            data-src="{{ uploaded_asset($prd_val->product->thumbnail_img) }}"
                                                            onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';"
                                                            alt="{{ $prd_val->product->getTranslation('name') }}"
                                                            class="img-rounded">
                                                    </div>
                                                    <div class="tabcard-detail">
                                                        <span>{{ $prd_val->product->manufacturer_location ?? '-' }}</span>
                                                        <p class="titlecard">{{ $prd_val->product->name ?? '-' }}</p>
                                                        <p class="price">₹ 349.00 / 500 Gms</p>
                                                        <div class="cartbtn">
                                                            <img src="public/assets/img/carts.svg" class=" cart"
                                                                alt="cart">
                                                            <a href="#" class="cartbtn">
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
                                                    <div class="tab_slider_card">
                                                        <div>
                                                            <div class="card-img mb-1">
                                                                <img src="{{ static_asset('assets/img/placeholder.jpg') }}"
                                                                    data-src="{{ uploaded_asset($prd_val->product->thumbnail_img) }}"
                                                                    onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';"
                                                                    alt="{{ $prd_val->product->getTranslation('name') }}"
                                                                    class="img-rounded">
                                                            </div>
                                                            <div class="tabcard-detail">
                                                                <span>{{ $prd_val->product->manufacturer_location ?? '-' }}</span>
                                                                <p class="titlecard">{{ $prd_val->product->name ?? '-' }}
                                                                </p>
                                                                <p class="price">₹ 349.00 / 500 Gms</p>
                                                                <div class="cartbtn">
                                                                    <img src="public/assets/img/carts.svg" class=" cart"
                                                                        alt="cart">
                                                                    <a href="#" class="cartbtn">
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
                        <div class=" text-center"><a href="#" class="viewbtn mt-0">View All</a></div>
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

        <div class="container py-5">
            <div class="mt-2">
                <div class="community-serve text-center">
                    <div class="py-md-5 py-4">
                        <h4 class="fw700 title-txt">Our most
                            <ins class="primary-color fw700">popular communities</ins>
                        </h4>
                        <p class="w-75 mx-auto mb-0 body-txt">More than
                            <ins class="primary-color fw600">500+ customers
                            </ins>
                            across South Mumbai's finest gated communities have already signed up to the SafeQU
                            experience. Choose your community and get started now
                        </p>

                        <div class="py-4">
                            <div class="community-slider owl-carousel owl-theme">

                                @foreach ($communities as $community)
                                    <div class="item py-3 px-2">
                                        <div class="community-card mx-auto p-3 pt-4">
                                            <div class="card-img mb-1">
                                                @if (isset($community->user->avatar_original))
                                                    <img src="{{ uploaded_asset($community->user->avatar_original) }}"
                                                        onerror="this.onerror=null;this.src='{{ static_asset('assets/img/avatar-place.png') }}';"
                                                        class="img-rounded" alt="{{ $community->name }}">
                                                @else
                                                    <img src=""
                                                        onerror="this.onerror=null;this.src='{{ static_asset('assets/img/avatar-place.png') }}';"
                                                        class="img-rounded" alt="{{ $community->name }}">
                                                @endif
                                            </div>
                                            <div class="card-data pt-3 pb-4">
                                                <h6 class="fw700 mb-1">{{ $community->name }}</h6>
                                                <p class="mb-0 body-txt">{{ $community->address }}</p>
                                            </div>

                                            {{-- <div --}}
                                            {{-- class="card-members  @if (count($community->orders->unique('user_id')) > 0) pb-3 @else pb-5 @endif"> --}}
                                            {{-- <div class="mbr-img"> --}}
                                            {{-- @foreach ($community->orders->unique('user_id') as $i => $order) --}}
                                            {{-- @if ($i < 5) --}}
                                            {{-- <img src="{{ uploaded_asset($order->user->avatar_original) }}" --}}
                                            {{-- onerror="this.onerror=null;this.src='{{ static_asset('assets/img/avatar-default.webp') }}';"> --}}
                                            {{-- @endif --}}
                                            {{-- @endforeach --}}
                                            {{-- </div> --}}
                                            {{-- @if (count($community->orders->unique('user_id')) > 0) --}}
                                            {{-- <div class="mbr-cnt"> --}}
                                            {{-- <p class="mb-0 body-txt"> --}}
                                            {{-- {{ count($community->orders->unique('user_id')) > 1? count($community->orders->unique('user_id')) . ' Members': count($community->orders->unique('user_id')) . ' Member' }} --}}
                                            {{-- </p> --}}
                                            {{-- </div> --}}
                                            {{-- @endif --}}
                                            {{-- </div> --}}

                                            @if (auth()->user() &&
                                                intval(auth()->user()->joined_community_id) > 0 &&
                                                auth()->user()->joined_community_id != $community->user_id)
                                                <a href="javascript:void(0);"
                                                    class="btn primary-btn btn-block fw600 text-white"
                                                    onclick="confrimCommunityChange('{{ route('shop.visit', $community->slug) }}');">JOIN</a>
                                            @else
                                                <a href="{{ route('shop.visit', $community->slug) }}"
                                                    class="btn primary-btn btn-block fw600 text-white">JOIN</a>
                                            @endif

                                        </div>
                                    </div>
                                @endforeach

                            </div>
                        </div>

                        {{-- @if (count($communities) > 5)
                            <button class="btn fw700 view-more-btn px-4 mb-3 mb-md-0">View more</button>
                        @endif --}}

                    </div>
                </div>
            </div>
        </div>

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
    </main>
@endsection

@section('script')
    <script>
        $(document).ready(function() {

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

            // $("#tykeModal").modal('show');
        })

        function confrimCommunityChange(url) {
            $('#changeCommunityModal').modal('show');
            $('#changeCommunityModal #change-community-form').attr('action', url);
        }
    </script>

    <script>
        $(document).ready(function() {

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
        });
    </script>
@endsection
