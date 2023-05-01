@extends('frontend.layouts.app', ['new_header' => false, 'header_show' => true, 'header2' => true, 'footer' => true, 'new_footer' => false])

@section('content')
    <main class="main-tag mt-0">

        <div class="breadcrumbs">
            <div class="container">
                <h5 class="mb-0 fw700 text-white text-uppercase">Order Details</h5>
            </div>
        </div>

        <div class="content pb-5">
            <div class="container">
                <div class="row justify-content-center pb-4">
                    <div class="col-lg-5 col-md-7 col-sm-8 px-0">

                        <div class="user-data px-3 py-4">
                            <div class="img-name-sm px-3 mb-2">
                                <div class="user-img-sm m-0">
                                    <img src="" alt="User Img"
                                        onerror="this.onerror=null;this.src='{{ static_asset('assets/img/avatar-default.webp') }}'"
                                        id="userProfileImage">
                                    <input type="hidden" class="selected-files" name="photo" id="userAvatar"
                                        value="{{ Auth::user()->avatar_original }}">
                                </div>
                                <div class="pl-3">
                                    <p class="fw600 fsize14 mb-1">{{ Auth::user()->name }}</p>
                                    <p class="body-txt fsize12 mb-0">{{ Auth::user()->phone }}</p>
                                </div>
                            </div>
                            <div class="btns text-center pt-3">
                                <a href="{{ route('invoice.download', $order->id) }}" class="fsize12 fw500">
                                    <i class="fad fa-file-download text-primary"></i> &nbsp; Download invoice
                                </a>
                                {{-- <a href="#" class="fsize12 fw500">Repeat</a> --}}
                            </div>
                        </div>

                        <div class="ord-details py-4 px-3 primary-color-bg mt-4">
                            <p class="text-white fw600 pb-1 mb-2">Order Id: &nbsp; <span class="text-white orderId">
                                    #{{ $order->code }}</span></p>

                            <p class="text-white fw600 pb-1 mb-2">Order Status: &nbsp; <span class="text-white orderId">
                                    {{ ucfirst(str_replace('_', ' ', $order->delivery_status)) }}</span></p>

                            <p class="text-white pb-1 mb-2">Time: <span class="text-white dateTime"> &nbsp;
                                    {{ date('d M Y H:i A', strtotime($order->created_at)) }}</span>
                            </p>

                            @if (json_decode($order->shipping_address))
                                @php
                                    $shipping_address = json_decode($order->shipping_address);
                                    $city = trim($shipping_address->city) != '' ? ',<br />' . trim($shipping_address->city) : '';
                                    $state = trim($shipping_address->state) != '' ? ', ' . trim($shipping_address->state) : '';
                                    $country = trim($shipping_address->country) != '' ?  ',<br />' . trim($shipping_address->country) : '';
                                    $postal_code = trim($shipping_address->postal_code) != '' ? ' - ' . trim($shipping_address->postal_code) : '';
                                @endphp
                                <p class="text-white fw600 mb-2">Delivery Address:</p>
                                <p class="text-white address mb-0">
                                    {!! $shipping_address->address . $city . $state . $country . $postal_code !!}
                                </p>
                            @endif
                        </div>

                        <div class="order-data pt-4 mt-3">
                            <table class="table w-100">
                                <thead>
                                    <tr>
                                        <th class="fw700 fsize13">Item Name</th>
                                        <th class="fw700 fsize13 text-center">Qty</th>
                                        <th class="fw700 fsize13 text-right">Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($order->orderDetails as $key => $orderDetail)
                                        @php
                                            if ($orderDetail->quantity * floatval($orderDetail->product->min_qty) < 1) {
                                                $qty_unit = 1000 * floatval($orderDetail->product->min_qty) . ' ' . $orderDetail->product->secondary_unit;
                                            } else {
                                                $qty_unit = $orderDetail->quantity * floatval($orderDetail->product->min_qty) . ' ' . $orderDetail->product->unit;
                                            }
                                        @endphp
                                        <tr>
                                            <td>{{ $orderDetail->product->getTranslation('name') }}</td>
                                            <td class="text-center">{{ $qty_unit }}</td>
                                            <td class="text-right">{!! single_price_web($orderDetail->price) !!} </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    {{--                                 <tr> --}}
                                    {{--                                    <th colspan="2">Sub Total</th> --}}
                                    {{--                                    <th class="text-right">{{$order->grand_total}}</th> --}}
                                    {{--                                </tr> --}}
                                    {{--                                <tr> --}}
                                    {{--                                    <th colspan="2" class="bt-0">Discount</th> --}}
                                    {{--                                    <th class="text-right bt-0">- 200</th> --}}
                                    {{--                                </tr> --}}
                                    @if ($order->orderDetails->sum('shipping_cost') > 0)
                                        <tr>
                                            <th colspan="2" class="bt-0">Shipping cost</th>
                                            <th class="text-right bt-0"> {!! single_price_web($order->orderDetails->sum('shipping_cost')) !!}</th>
                                        </tr>
                                    @endif
                                    @if ($order->coupon_discount > 0)
                                        <tr>
                                            <th colspan="2" class="bt-0">{{ translate('Coupon Discount') }}</th>
                                            <th class="text-right bt-0">
                                                - {!! single_price_web($order->coupon_discount) !!}</th>
                                        </tr>
                                    @endif
                                    {{--                                <tr> --}}
                                    {{--                                    <th colspan="2" class="bt-0">Service Tax</th> --}}
                                    {{--                                    <th class="text-right bt-0">80</th> --}}
                                    {{--                                </tr> --}}
                                    <tr class="bb-1">
                                        <th colspan="2" class="fw600 fsize15 py-2">Total</th>
                                        <th class="fw600 fsize15 text-right py-2">
                                            {!! single_price_web($order->grand_total) !!}
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="pt-4 text-center">
                            @if (session()->has('shop_slug'))
                            <a href="{{ route('shop.visit') }}">
                                <button class="btn primary-btn btn-round px-5">
                                    Continue Shopping &nbsp;&nbsp;
                                    <i class="fal fa-long-arrow-right text-white"></i>
                                </button>
                            </a>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </main>
@endsection
