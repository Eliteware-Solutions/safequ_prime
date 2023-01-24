@extends('frontend.layouts.app', ['new_header' => false, 'header_show' => true, 'header2' => false, 'footer' => true, 'new_footer' => false])

@section('content')
    <main class="main-tag main-tag-mt">
        <div class="container">
            <div class="row justify-content-center py-4 py-md-5">
                <div class="col-lg-5 col-md-7 col-sm-9 px-0">

                    <div class="flex-acenter-jbtw pb-4">
                        <h5 class="fw700 title-txt mb-1">My Farm Orders</h5>

                        <form action="{{ route('purchase_history.index') }}" method="GET" id="order-filter-form">
                            <select name="dropdownFilter" id="dropdownFilter" class="form-control p-0 m-0"
                                onchange="this.form.submit();">
                                <option value="pending" @if ($dropdownFilter == 'pending') selected @endif>Pending</option>
                                <option value="delivered" @if ($dropdownFilter == 'delivered') selected @endif>Delivered
                                </option>
                            </select>
                        </form>
                    </div>

                    @foreach ($orders as $order)
                        <div class="ord-item-card p-3 mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="{{ route('purchase_details', encrypt($order->id)) }}">
                                    <div class="pr-2">
                                        <p class="fw600 fsize14 title-txt mb-1">
                                            Order # {{ $order->code }}
                                            @if ($order->replaced_order_id > 0)
                                                <span class="badge badge-success fsize10">Replaced</span>
                                            @endif
                                        </p>
                                        <p class="mb-0 lh-17">
                                            <span class="fsize13 body-txt ordered-qty">
                                                {{ date('d M Y H:i', $order->date) }}
                                            </span>
                                        </p>
                                    </div>
                                </a>
                                <a href="{{ route('invoice.download', $order->id) }}" class="fsize13">
                                    <i class="fad fa-file-download text-primary"></i> &nbsp; Invoice
                                </a>
                            </div>

                            <!--                            <div class="py-1">
                                        <a href="{{ route('invoice.download', $order->id) }}" class="fsize13">
                                            <i class="fad fa-file-download text-primary"></i> &nbsp; Download invoice
                                        </a>
                                    </div>-->

                            <div class="delivery-status justify-content-between align-items-start pt-2">

                                <table class="fsize11" width="100%">
                                    <th><b>#</b></th>
                                    <th><b>Product</b></th>
                                    <th><b>Estimated Delivery Date</b></th>
                                    <tbody>
                                        @foreach ($order->orderDetails as $key => $detail)
                                            @php
                                                if ($detail->is_archived == 1):
                                                    $deliveryDate = date('d M Y', strtotime($detail->archive_product_stock->purchase_end_date . '+' . intval($detail->archive_product_stock->est_shipping_days) . ' days'));
                                                else:
                                                    $deliveryDate = date('d M Y', strtotime($detail->product_stock->purchase_end_date . '+' . intval($detail->product_stock->est_shipping_days) . ' days'));
                                                endif;
                                            @endphp
                                            <tr>
                                                <td style="width:5%"> {{ ++$key }} </td>
                                                <td style="width:55%"> {{ $detail->product->name }} </td>
                                                <td style="width:40%">
                                                    @if ($detail->delivery_status == 'delivered')
                                                        {{ date('d M Y', strtotime($detail->updated_at)) . ' [Delivered]' }}
                                                    @else
                                                        {{ $deliveryDate }}
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                                {{-- @if (Auth::check())
                                    @php
                                        $commentable = false;
                                    @endphp
                                    @if ($commentable)
                                        <a href="{{ route('product_reviews', 'product_id') }}">
                                            <p class="mb-0 fsize15 rating-stars">
                                                {!! renderStarRating($detail->product->rating) !!}
                                            </p>
                                        </a>
                                    @endif
                                @endif --}}
                            </div>
                        </div>
                    @endforeach

                </div>
            </div>
        </div>
    </main>
@endsection

@section('script')
@endsection
