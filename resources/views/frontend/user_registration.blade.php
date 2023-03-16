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

                <h5 class="fw700 pt-3 px-2">Get Started</h5>
                <p class="pb-3 px-2"></p>

                <form method="POST" class="login-form pt-2 px-2" action="{{ route('register') }}">
                    @csrf
                    <input type="hidden" name="referer_user_id" value="{{ $referer_user_id }}">
                    <div class="form-group phone-form-group mb-4">
                        <input type="tel" id="phone-code" required maxlength="10" minlength="10"
                            class="mb-4 form-control{{ $errors->has('phone') ? ' is-invalid' : '' }}"
                            value="{{ old('phone') }}" placeholder="" name="phone" autocomplete="off">
                    </div>

                    <input type="hidden" name="country_code" value="">

                    <button type="submit" class="btn primary-btn btn-block">Request OTP</button>

                    <div class="mb-3">
                        <label class="aiz-checkbox">
                            <span
                                class="opacity-60 fsize11">{{ translate('By signing up you agree to our terms and conditions.') }}</span>
                            <span class="aiz-square-check"></span>
                        </label>
                    </div>

                    <hr>
                    <div class="text-center">
                        <p class="text-muted mb-0">{{ translate('Already have an account?') }}</p>
                        <a href="{{ route('user.login') }}">{{ translate('Log In') }}</a>
                    </div>

                </form>

            </div>
        </div>
    </div>
@endsection
