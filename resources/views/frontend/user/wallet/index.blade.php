@extends('frontend.layouts.app', ['new_header' => false, 'header_show' => true, 'header2' => false, 'footer' => true, 'new_footer' => false])

@section('content')
    <main class="main-tag main-tag-mt">
        <div class="container walletDetails">
            <div class="row justify-content-center py-4">
                <div class="col-lg-5 col-md-6 col-sm-8 py-2 px-1">

                    <div class="wallet-amt-card">
                        <i class="fad fa-wallet text-white fa-2x"></i>
                        <p class="mb-1 text-white fsize13">SafeQu Balance</p>
                        <h4 class="fw700 mb-3 text-white">
                            {!! single_price(Auth::user()->balance) !!}
                        </h4>
                        @if (get_setting('razorpay') == 1)
                            <button class="btn btn-outline-light fsize14 fw500 px-4 btn-round btn-before-none"
                                data-toggle="modal" data-target="#addMoneyModal">
                                Add Money
                            </button>
                        @endif
                    </div>

                    <div class="trans-history pt-4 mt-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw700 title-txt">Transaction History</h6>
                            <i class="fad fa-filter fsize16 py-1 px-2" data-toggle="modal"
                                data-target="#filterTransModal"></i>
                        </div>

                        <div class="pt-4">
                            @forelse ($wallets as $key => $wallet)
                                <div class="transactions">
                                    <div
                                        class="flex-acenter-jbtw viewDetails cursor-pointer flex-wrap @if ($wallet->amount > 0) credit @else debit @endif">
                                        <div class="flex-acenter-jstart">
                                            <div class="indicater text-center">
                                                <i class="fad fa-long-arrow-left text-danger"></i>
                                                <i class="fad fa-long-arrow-right text-success"></i>
                                            </div>
                                            <div class="pl-2">
                                                <h6 class="mb-0 title-txt">
                                                    @if ($wallet->amount > 0)
                                                        Credit
                                                    @else
                                                        Debit
                                                    @endif
                                                </h6>
                                                <p class="mb-0 body-txt">{{ dateFormat($wallet->created_at) }}</p>
                                            </div>
                                        </div>

                                        <div class="amt text-right">
                                            <h6 class="amount mb-1">
                                                {!! single_price(abs($wallet->amount)) !!}
                                            </h6>

                                            <i class="fad fa-caret-circle-right align-middle"></i>
                                        </div>
                                    </div>

                                    <div class="w-100 transDetails">
                                        <div class="pt-2 px-2">
                                            @php
                                                $payment_detail = json_decode($wallet->payment_details);
                                            @endphp
                                            @if ($wallet->payment_method == 'razorpay')
                                                <p class="body-txt mb-1"><span class="fw500 fsize13">Payment method:</span>
                                                    Razorpay</p>
                                                <p class="body-txt mb-1"><span class="fw500 fsize13">Transaction id:</span>
                                                    {{ $payment_detail->id }}
                                                </p>
                                            @elseif ($wallet->payment_method == 'order')
                                                <a href="{{ route('purchase_details', encrypt($payment_detail->id)) }}">
                                                    <p class="body-txt mb-1"><span class="fw500 fsize13">Order No.:</span>
                                                        #{{ $payment_detail->code }}</p>
                                                </a>
                                            @elseif ($wallet->payment_method == 'bonus')
                                                <p class="body-txt mb-1"><span class="fw500 fsize13">Payment method:</span>
                                                    Welcome Bonus</p>
                                            @elseif ($wallet->payment_method == 'admin_recharge')
                                                <p class="body-txt mb-1"><span class="fw500 fsize13">Payment method:</span>
                                                    Offline Recharge</p>
                                                <p class="body-txt mb-1"><span class="fw500 fsize13">Reason:</span>
                                                    {{ $payment_detail->reason }}
                                                </p>
                                            @else
                                                <p class="body-txt mb-1"><span class="fw500 fsize13">Payment method:</span>
                                                    Referral Bonus</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="row">
                                    <div class="col-xl-8 mx-auto">
                                        <div class="shadow-sm bg-white p-4 rounded">
                                            <div class="text-center p-3">
                                                <i class="las la-frown la-3x opacity-60 mb-3"></i>
                                                <h3 class="h4 fw-700">{{ translate('No transactions found.!') }}</h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforelse
                        </div>
                    </div>
                    <div class="aiz-pagination pt-4">
                        {{ $wallets->links() }}
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection


@section('modal')
    <!-- Add Money to wallet Popup -->
    <div class="modal fade" id="addMoneyModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
        aria-labelledby="addMoneyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addMoneyModalLabel">Add money to wallet</h5>
                    <div class="close-btn text-right">
                        <a href="javascript:void(0)" class="fw900" data-dismiss="modal">X</a>
                    </div>
                </div>
                <div class="modal-body">
                    <form class="" action="{{ route('wallet.recharge') }}" method="post">
                        @csrf
                        <div class="py-2 text-center">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="basic-addon1">
                                        <ins class="currency-symbol">&#8377;</ins>
                                    </span>
                                </div>
                                <input type="text" class="form-control" name="amount" id="wallet_amount"
                                    placeholder="0" />
                            </div>
                            <div class="amt-select text-center pb-3">
                                <button type="button" class="btn amt-btn my-1 btn-round" data-amt="100">100</button>
                                <button type="button" class="btn amt-btn my-1 btn-round" data-amt="500">500</button>
                                <button type="button" class="btn amt-btn my-1 btn-round" data-amt="1000">1000</button>
                                <button type="button" class="btn amt-btn my-1 btn-round" data-amt="2000">2000</button>
                            </div>
                            <input type="hidden" name="payment_option" value="razorpay">
                            <button class="btn primary-btn disabled" type="submit"> Add &nbsp;
                                <ins class="currency-symbol text-white fw400">&#8377;</ins>
                                <span id="btnAmt" class="text-white fw500">0</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Popup -->
    <div class="modal fade" id="filterTransModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
        aria-labelledby="filterTransModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="filterTransModalLabel">Filter</h5>
                    <div class="close-btn text-right">
                        <a href="javascript:void(0)" class="fw900" data-dismiss="modal">X</a>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="py-2 px-3">
                        <form method="post" action="{{ route('wallet.filter') }}">
                            @csrf
                            <div class="form-group">
                                <label for="from_date" class="fw500">From Date</label>
                                <input type="date" class="form-control" name="from_date" id="from_date"
                                    value="{{ $request->from_date ?? '' }}" />
                            </div>
                            <div class="form-group pb-3">
                                <label for="from_date" class="fw500">To Date</label>
                                <input type="date" class="form-control" name="to_date" id="to_date"
                                    value="{{ $request->to_date ?? '' }}" />
                            </div>
                            <div class="form-group pb-3">
                                <label for="from_date" class="fw500">Transaction Type</label>

                                <div class="switch">
                                    <input type="radio" class="switch-input" name="view" value="all"
                                        id="all" @if (isset($request->view) && $request->view == 'all') checked @endif>
                                    <label for="all" class="switch-label switch-label-all mb-0">All</label>

                                    <input type="radio" class="switch-input" name="view" value="credit"
                                        id="credit" @if (isset($request->view) && $request->view == 'credit') checked @endif />
                                    <label for="credit" class="switch-label switch-label-in">Credit</label>

                                    <input type="radio" class="switch-input" name="view" value="debit"
                                        id="debit" @if (isset($request->view) && $request->view == 'debit') checked @endif />
                                    <label for="debit" class="switch-label switch-label-out">Debit</label>

                                    <span class="switch-selection"></span>
                                </div>

                            </div>

                            <button class="btn primary-btn btn-block"> Apply</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            // Unit Qty select
            $(".amt-btn").on('click', function() {
                let amount = $(this).data('amt');
                $('#wallet_amount').val(amount);
                $('#btnAmt').text(amount);
                $('#btnAmt').parent().removeClass("disabled");
            })

            $('#wallet_amount').on('keyup touchend', function() {
                let value = $(this).val();

                if (value != '' && value != 0) {
                    $('#btnAmt').text(value), $('#btnAmt').parent().removeClass("disabled");
                } else {
                    $('#btnAmt').text(0), $('#btnAmt').parent().addClass("disabled");
                }
            })

            $(".viewDetails").on('click', function() {
                let icn = $(this).find('i.fa-caret-circle-right');
                let el = $(this).parent().find('.transDetails');
                let hght = el.children('.pt-2').outerHeight();

                el.hasClass('active') ? (el.toggleClass('active').css('height', '0'), icn.css('transform',
                        'rotate(0deg)')) :
                    (el.toggleClass('active').css('height', hght), icn.css('transform', 'rotate(90deg)'));
            })
        })
    </script>
@endsection
