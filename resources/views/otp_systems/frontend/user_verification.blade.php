@extends('frontend.layouts.app', ['new_header' => false, 'header_show' => false, 'header2' => false, 'footer' => false])

@section('meta_title', get_setting('website_name') . ' | OTP')

@section('content')
    <div class="login-screen container py-4">
        <div class="row justify-content-center pt-3">
            <div class="col-lg-5 col-md-7 col-sm-8 px-0">

                <div class="logo-div pb-5">
                    <a href="{{ route('home') }}">
                        <img src="{{ static_asset('assets/img/new-design/safequ-logo.png') }}" alt="SafeQu Logo">
                    </a>
                </div>

                <h5 class="fw700 pt-3 px-2">Verification Code</h5>
                <p class="pb-3 px-2">Code sent to <span class="otp-to-phone fw600">{{ $user->phone }}</span></p>

                <form method="post" class="otp-form px-2" action="{{ route('verification.submit') }}" autocomplete="off">
                    @csrf

                    <div class="form-group mb-4">
                        <input type="text" id="digit-1" name="digit-1" data-next="digit-2" required
                            onkeyup="this.value = this.value.replace(/[^0-9]/g, '')" />
                        <input type="text" id="digit-2" name="digit-2" data-next="digit-3" data-previous="digit-1"
                            required onkeyup="this.value = this.value.replace(/[^0-9]/g, '')" />
                        <input type="text" id="digit-3" name="digit-3" data-next="digit-4" data-previous="digit-2"
                            required onkeyup="this.value = this.value.replace(/[^0-9]/g, '')" />
                        <input type="text" id="digit-4" name="digit-4" data-previous="digit-3" required
                            onkeyup="this.value = this.value.replace(/[^0-9]/g, '')" />
                    </div>

                    <input type="hidden" name="verification_code" class="code_verify" value="">
                    <input type="hidden" name="user_id" value="{{ $user->id }}">

                    {{-- <p class="mb-4 text-center">Resend code in
                        <span class="otp-timer act-price fw600">0:46</span>
                    </p> --}}

                    <a href="{{ route('verification.phone.resend', encrypt($user->id)) }}">
                        <p class="mb-4 text-center act-price fw600">Resend code</p>
                    </a>

                    <button type="button" onclick="formSubmit()" class="btn primary-btn btn-block text-white">
                        {{ translate('Verify OTP') }}
                    </button>
                    <button type="submit" class="submit-btn p-0 m-0" style="display: none">submit</button>
                </form>

                <a href="{{ route('user.login') }}">
                    <p class="text-center mt-2 py-2 mb-2 act-price"><i class="fal act-price fa-long-arrow-left"></i>
                        &nbsp;&nbsp; Back to login</p>
                </a>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {

            $('.otp-form').find('input[type=text]').each(function() {
                $(this).attr('maxlength', 1);
                $(this).on('keyup touchend', function(e) {
                    var parent = $($(this).parent());

                    if (e.keyCode === 8 || e.keyCode === 37) {
                        var prev = parent.find('input#' + $(this).data('previous'));

                        if (prev.length) {
                            $(prev).select();
                        }

                    } else if ($.isNumeric($(this).val())) {

                        var next = parent.find('input#' + $(this).data('next'));

                        if (next.length) {
                            $(next).select();
                        } else {
                            if (parent.data('autosubmit')) {
                                parent.submit();
                            }
                        }
                    }
                });
            });
        })

        function formSubmit() {
            $(".code_verify").val('');

            $('.otp-form').find('input[type=text]').each(function() {
                let num = $(this).val();

                $(".code_verify").val(function() {
                    return this.value + num;
                })
            })

            $('.submit-btn').trigger('click');
        }
    </script>
@endsection
