@extends('frontend.layouts.app', ['new_header' => false, 'header_show' => false, 'header2' => false, 'footer' => false])

@section('content')
    <div class="login-screen container py-4">
        <div class="row justify-content-center pt-3">
            <div class="col-lg-5 col-md-7 col-sm-8 px-0">

                <div class="logo-div pb-5">
                    <a href="{{ route('home') }}">
                        <img src="{{ static_asset('assets/img/new-design/safequ-logo.png') }}" alt="SafeQu Logo">
                    </a>
                </div>

                <h5 class="fw700 pt-3 px-2">Login account</h5>
                <p class="pb-3 px-2">Hello, welcome back to your account.</p>

                <form method="POST" class="login-form pt-2 px-2" action="{{ route('register') }}">
                    @csrf
                    <input type="hidden" name="community_id" id="community_id" value="{{ $community_id }}">
                    <div class="form-group phone-form-group mb-4">
                        <input type="tel" id="phone-code" required maxlength="10" minlength="10"
                            class="mb-4 form-control{{ $errors->has('phone') ? ' is-invalid' : '' }}"
                            value="{{ old('phone') }}" placeholder="" name="phone" autocomplete="off">
                    </div>

                    <input type="hidden" name="country_code" value="">

                    <button type="submit" class="btn primary-btn btn-block">Request OTP</button>

                    {{--                    <a href="{{ route('home') }}"> --}}
                    {{--                        <p class="text-center pt-3 act-price">Skip for now</p> --}}
                    {{--                    </a> --}}
                </form>

            </div>
        </div>
    </div>
@endsection
