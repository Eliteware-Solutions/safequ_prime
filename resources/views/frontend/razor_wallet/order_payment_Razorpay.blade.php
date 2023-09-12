@extends('frontend.layouts.app')

@section('content')
    <form action="{!!route('payment.rozer')!!}" method="POST" id='razorpay' style="display: none;">
        <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
        <input type="hidden" name="_token" value="{!!csrf_token()!!}">
        <input type="hidden" name="wallet_amount" value="{{$wallet_amount}}">
        <input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id">
    </form>

@endsection

@section('script')
    <script type="text/javascript">
        $(document).ready(function (e) {

            let options = {
                "key": "{{ env('RAZOR_KEY') }}", // Enter the Key ID generated from the Dashboard
                "amount": "{{round(($combined_order->grand_total - $wallet_amount) * 100)}}", //amount need to be in multiple of 100
                "order_id": "{{ $razorpayOrderId }}",
                "name": "{{ env('APP_NAME') }}",
                "description": "Cart Payment",
                "image": "{{ uploaded_asset(get_setting('header_logo')) }}",
                "prefill": {
                    "name": "{{ Auth::user()->name}}",
                    "email": "{{ Auth::user()->email}}",
                    "contact": "{{ Auth::user()->phone}}",
                },
                "theme": {
                    "color": "#ff7529"
                },
                "notes": {!! json_encode($notes) !!},
                config: {
                    display: {
                        blocks: {
                            banks: {
                                name: "Pay using UPI",
                                instruments: [
                                    {
                                        method: "upi",
                                        flows: ["collect"],
                                        apps: ["google_pay"]
                                    },
                                ]
                            },
                        },
                        sequence: ['block.banks'],
                        preferences: {
                            show_default_blocks: true // Should Checkout show its default blocks?
                        }
                    }
                },
                "handler": function (response) {
                    document.getElementById('razorpay_payment_id').value = response.razorpay_payment_id;
                    document.getElementById('razorpay').submit();
                },
                "modal": {
                    "ondismiss": function () {
                        window.location.href = '{{ route('cart') }}';
                    }
                }
            };

            let rzp = new Razorpay(options);
            rzp.open();

            rzp.on('payment.failed', function(data){
                document.getElementById('razorpay_payment_id').value = data.error.metadata.payment_id;
                document.getElementById('razorpay').submit();
            })

            e.preventDefault()
        });
    </script>
@endsection
