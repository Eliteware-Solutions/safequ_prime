@extends('frontend.layouts.app',['header_show' => false, 'header2' => false, 'footer' => false])

@section('content')
    <main class="main-tag mt-0">
        <div class="breadcrumbs">
            <div class="container">
                <h5 class="mb-0 fw700 text-white text-uppercase">Bill Paid</h5>
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
                            <h4 class="title-txt mb-3">Payment Successful!</h4>
                            <h5 class="body-txt">Hooray!! You have completed your payment.</h5>
                        </div>
                        <div class="line"></div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
