@extends('frontend.layouts.app', ['new_header' => false, 'header_show' => true, 'header2' => false, 'footer' => true, 'new_footer' => false])

@section('content')
    <main class="main-tag-mt-sm">

        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-5 col-md-6 col-sm-8 px-2 pt-3 pb-4">

                    <h5 class="fw600 text-center mb-4">
                        Hello, {{ Auth::user()->name != 'Guest User' ? Auth::user()->name : 'User' }}
                    </h5>

                    <form id="userProfileForm" action="{{ route('user.profile.update') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="profile-img mb-4">
                            <img src="{{ Auth::user()->avatar_original }}" alt="User Img"
                                onerror="this.onerror=null;this.src='{{ static_asset('assets/img/avatar-default.webp') }}'"
                                id="userProfileImage">

                            <div class="cmr-btn flex-acenter-jcenter" data-toggle="aizuploader" data-type="image">
                                <i class="fad fa-camera-alt text-white"></i>
                                <input type="hidden" class="selected-files" name="photo" id="userAvatar"
                                    value="{{ Auth::user()->avatar_original }}">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" name="name" id="name" onkeyup="validate_name();"
                                value="{{ Auth::user()->name == 'Guest User' ? '' : Auth::user()->name }}" required />
                        </div>
                        <div class="form-group">
                            <label for="phone-code">Mobile number</label>
                            <input type="text" name="phone" id="phone-code" value="{{ Auth::user()->phone }}" required
                                disabled />
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" name="email" id="email" value="{{ Auth::user()->email }}"
                                required />
                        </div>
                        <div class="form-group">
                            <label for="address">Flat / House No.</label>
                            <input type="text" name="address" id="address" value="{{ Auth::user()->address }}"
                                required />
                        </div>
                        <button type="submit" class="btn primary-btn mt-4 btn-block">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </main>
@endsection

@section('modal')
    @include('frontend.partials.address_modal')
@endsection

<script>
    function validate_name() {
        let element = document.getElementById('name');
        element.value = element.value.replace(/[^a-zA-Z\s@]+/, '');
    }
</script>
@section('script')
@endsection
