@extends('frontend.layouts.app', ['new_header' => false, 'header_show' => true, 'header2' => true, 'footer' => true, 'new_footer' => false])

@section('content')
    <main class="main-tag mt-0">

        <div class="breadcrumbs">
            <div class="container">
                <h5 class="mb-0 fw700 text-white text-uppercase">Order Confirmed</h5>
            </div>
        </div>

        <div class="content pb-5">
            <div class="container pt-4 mt-3">
                <div class="status-img">
                    <img src="{{ static_asset('assets/img/right-tick.png') }}" alt="Status Img">
                </div>
                <div class="row justify-content-center pt-3">
                    <div class="col-md-8 col-lg-6">
                        <div class="text-center py-3">
                            <h5 class="body-txt">Thank You!</h5>
                            <h5 class="title-txt">Your order has been placed</h5>
                        </div>

                        <div class="thankyou-card">

                            @foreach ($combined_order->orders as $order)
                                @foreach ($order->orderDetails as $order_detail)
                                    @php
                                        $product = \App\Models\Product::find($order_detail->product_id);
                                    @endphp
                                    <div class="d-flex justify-content-between align-items-center pb-3">
                                        <div class="img-name d-flex align-items-center">
                                            <div class="item-img text-center">
                                                <img src="{{ uploaded_asset($product->photos) }}"
                                                    onerror="this.onerror=null;this.src='{{ static_asset('assets/img/no-image-found.jpg') }}';"
                                                    alt="{{ $product->name }}" />
                                            </div>
                                            <div class="pl-3">
                                                <h6 class="fw700 mb-1">{{ $product->name }}</h6>
                                                <p class="fw500 fsize13 body-txt mb-1">Variety: &nbsp; {{ explode(",",$product->tags)[0] }}
                                                </p>
                                                <p class="fw500 fsize13 body-txt mb-0">Farm Location: &nbsp;
                                                    {{ $product->manufacturer_location }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                                <div class="text-center pt-2">
                                    <a href="{{ route('purchase_details', encrypt($order->id)) }}">
                                        <button class="btn primary-btn text-uppercase btn-round">
                                            Order Details
                                        </button>
                                    </a>
                                </div>
                            @endforeach

                        </div>
                    </div>
                </div>

                <div class="px-3 pt-4 mt-3 pb-2 pb-0 text-center w-md-50 mx-auto">
                    @if (session()->has('shop_slug'))
                        <a href="{{ route('shop.visit', session()->get('shop_slug')) }}">Continue Shopping &nbsp;&nbsp; <i
                                class="fal fa-long-arrow-right"></i></a>
                    @else
                        <a href="{{ route('home') }}">Continue Shopping &nbsp;&nbsp; <i
                                class="fal fa-long-arrow-right"></i></a>
                    @endif
                </div>
            </div>
        </div>
    </main>
@endsection
