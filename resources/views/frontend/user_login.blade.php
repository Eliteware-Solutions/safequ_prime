@extends('frontend.layouts.app', ['header_show' => false, 'header2' => false, 'footer' => true])

@section('content')
    <div class="login-screen container py-4">
        <div class="row justify-content-center pt-3">
            <div class="col-lg-5 col-md-7 col-sm-8 px-0">

                <div class="logo-div pb-5">
                    <a href="{{ route('home') }}"> <img src="{{ static_asset('assets/img/safequ-logo.png') }}"
                                                        alt="SafeQu Logo"> </a>
                </div>

                <h5 class="fw700">Login Account</h5>
                <p class="pb-3">Hello, welcome back to your account.</p>

                <form method="POST" class="login-form" action="{{ route('register') }}">
                    @csrf
                    <div class="form-group phone-form-group mb-1">
                        <input type="tel" id="phone-code"
                               class="mb-4 form-control{{ $errors->has('phone') ? ' is-invalid' : '' }}"
                               value="{{ old('phone') }}" placeholder="" name="phone" autocomplete="off">
                    </div>

                    <input type="hidden" name="country_code" value="">

                    <button type="submit" class="btn primary-btn btn-block">Request Otp</button>

                    <a href="{{ route('home') }}">
                        <p class="text-center pt-3 act-price">Skip for now</p>
                    </a>
                </form>

            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ static_asset('assets/js/vendors.js') }}"></script>
    <script type="text/javascript">
        var countryData = window.intlTelInputGlobals.getCountryData(),
            input = document.querySelector("#phone-code");

        for (var i = 0; i < countryData.length; i++) {
            var country = countryData[i];
            if (country.iso2 == 'bd') {
                country.dialCode = '88';
            }
        }

        var iti = intlTelInput(input, {
            separateDialCode: true,
            utilsScript: "{{ static_asset('assets/js/intlTelutils.js') }}?1590403638580",
            onlyCountries: ['in'],
            customPlaceholder: function (selectedCountryPlaceholder, selectedCountryData) {
                if (selectedCountryData.iso2 == 'bd') {
                    return "01xxxxxxxxx";
                }
                return selectedCountryPlaceholder;
            }
        });

        var country = iti.getSelectedCountryData();
        $('input[name=country_code]').val(country.dialCode);

        input.addEventListener("countrychange", function (e) {
            // var currentMask = e.currentTarget.placeholder;

            var country = iti.getSelectedCountryData();
            $('input[name=country_code]').val(country.dialCode);

        });
    </script>
@endsection
