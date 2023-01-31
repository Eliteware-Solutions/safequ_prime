@extends('frontend.layouts.app', ['new_header' => false, 'header_show' => true, 'header2' => false, 'footer' => true, 'new_footer' => false])

@section('content')
    <main class="main-tag-mt-sm">

        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-5 col-md-6 col-sm-8 px-2">
                    <div class="notifications py-4">
                        <h5 class="mb-0 fw700 title-txt mb-4">Notifications</h5>

                        @forelse($notifications as $notification)
                            @if ($notification->type == 'App\Notifications\OrderNotification')
                                <div class="notify-crd">
                                    <div class="d-flex justify-content-start align-items-center pr-2">
                                        <!-- <div class="img-name pr-2">
                                                        <div class="item-img item-img-sm text-center">
                                                            <img src="{{ static_asset('assets/img/strawberry.png') }}" alt="Item image" />
                                                        </div>
                                                    </div>-->
                                        <div>
                                            <p class="fw500 fsize14 title-txt mb-1">
                                                {{ translate('Your Order: ') }}
                                                <a class="notification_a"
                                                    href="{{ route('purchase_details', encrypt($notification->data['order_id'])) }}">
                                                    {{ $notification->data['order_code'] }}
                                                </a>
                                                {{ translate(' has been ' . ucfirst(str_replace('_', ' ', $notification->data['status']))) }}
                                            </p>
                                            <p class="mb-0 fsize13 body-txt ordered-qty">
                                                {{ date('d M Y H:i', strtotime($notification->created_at)) }}
                                            </p>
                                        </div>
                                    </div>
                                    <a href="{{ route('view-notification', $notification->id) }}"><i
                                            class="fad fa-times p-2"></i></a>
                                </div>
                            @endif

                        @empty
                            <!-- No Notifications -->
                            <div class="row justify-content-center py-5">
                                <div class="col-lg-5 col-md-6 col-sm-8 text-center empty-notification">
                                    <div class="mb-4 empty-notify-bell">
                                        <img src="{{ static_asset('assets/img/notification-bell.png') }}"
                                            alt="notification-bell">
                                    </div>

                                    <h6 class="fw700">Nothing here!!!</h6>
                                    <p class="fsize13">Subscribe to anything. Tell us how you want your day sorted
                                        and we will do the
                                        heavy lifting for you.</p>
                                </div>
                            </div>
                            <!-- No Notifications -->
                        @endforelse

                    </div>
                </div>
            </div>

            <hr>

        </div>
    </main>
@endsection
