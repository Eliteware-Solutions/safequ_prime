<input type="hidden" id="product_id" value="{{ $product->product->id }}">
<input type="hidden" id="product_stock_id" value="{{ $product->id }}">
<div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
        <div class="modal-body">
            <div class="close-btn text-right">
                <a href="javascript:void(0)" class="fw900" data-dismiss="modal">X</a>
            </div>
            <div class="item-details px-sm-4">
                <div class="d-flex justify-content-between align-items-center pb-3">
                    <div class="img-name">
                        <div class="item-img text-center">
                            <img src="{{ uploaded_asset($product->product->photos) }}"
                                 onerror="this.onerror=null;this.src='{{ static_asset('assets/img/no-image-found.jpg') }}';"
                                 alt="{{ $product->product->name }}"/>
                        </div>
                        <div class="pl-3">
                            <h6 class="fw700">{{ $product->product->name }}</h6>
                            <p class="fw600 body-txt mb-0">Variety: {{ $product->product->variation }}</p>
                        </div>
                    </div>
                </div>

                <div class="distributor pt-2">
                    <div class="display-table">
                        <div class="table-cell">
                            <i class="fad fa-tractor fsize18"></i>
                        </div>
                        <div class="table-cell">
                            <h6 class="mb-1">Farm location:</h6>
                            <p class="mb-0">{{ $product->product->manufacturer_location }}</p>
                        </div>
                    </div>
                    <hr>
                    @php
                        if(floatval($product->product->min_qty) < 1){
                            $product->product->unit = floatval($product->product->min_qty) * 1000 .' '.$product->product->secondary_unit;
                        }
                    @endphp
                    <div class="qty-select text-center pb-3">
                        <h6 class="fw600 mb-3">Select Quantity</h6>
                        <div class="d-flex justify-content-center">
                            <div class="item-count">
                                <button class="btn mr-2"
                                        onclick="this.parentNode.querySelector('input[type=number]').stepDown();"
                                        type="button"><i class="fa fa-minus"></i></button>

                                <input class="quantity form-control" min="1" name="quantity" value="1"
                                       type="number" id="quantity" readonly
                                       onchange="this.value = this.value.replace(/[^0-9]/g, '')"/>

                                <span class="itm-unit fw500" id="itm-cnt">1</span>&nbsp;
                                <span class="itm-unit fw500" id="qty_unit">({{ $product->product->unit }})</span>

                                <button class="btn ml-2"
                                        onclick="this.parentNode.querySelector('input[type=number]').stepUp();"
                                        type="button"><i class="fa fa-plus"></i></button>
                            </div>
                        </div>

                        <div class="py-4">
                            <h4 class="fw700 act-price mb-1 lh-1" id="total_price">
                                {!! single_price_web($product->price) !!}
                            </h4>
                            <i class="body-txt fsize15">
                                (<span id="unit_price">{!! single_price_web($product->price) !!}</span>
                                / {{ $product->product->unit }})</i>
                        </div>

                        <!--                        <a href="cart.html">-->
                        <button class="btn primary-btn text-uppercase"
                                onclick="addProductToCart({{ $product->product->id }}, {{$product->id}})">
                            <i class="fas fa-shopping-cart text-white fsize18"></i>
                            &nbsp; Add to Cart
                        </button>
                        <!--                        </a>-->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    $(document).ready(function () {

        $('.item-count button').on('click', function () {
            let qty = $("#quantity").val();
            $("#itm-cnt").text(qty);

            if (qty >= 10) {
                $("#itm-cnt").removeClass("d2 d3");
                $("#itm-cnt").addClass("d2");
            }

            if (qty >= 100) {
                $("#itm-cnt").removeClass("d2 d3");
                $("#itm-cnt").addClass("d3");
            }

            (qty < 10) ? $("#itm-cnt").removeClass("d2 d3") : "";

            getVariantPrice(qty);

        })

    })

    function getVariantPrice(qty) {
        if (qty > 0 && $('#product_id').val() > 0) {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: '{{ route('products.variant_price') }}',
                data: {id: $('#product_id').val(),stock_id: $('#product_stock_id').val(), quantity: qty},
                success: function (data) {
                    if (data.total_price != '') {
                        $('#total_price').html('');
                        $('#total_price').html(data.total_price);
                    }
                    $('#unit_price').html(data.unit_price);
                    $('#qty_unit').html(data.qty_unit);
                }
            });
        }
    }

    function addProductToCart(productId, productStockId) {
        let qty = $("#quantity").val();
        if (productId > 0 && qty > 0) {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: '{{ route('cart.addToCart') }}',
                data: {id: productId, productStockId: productStockId, quantity: qty},
                success: function (data) {
                    if (data.status == 1) {
                        {{-- AIZ.plugins.notify('success', "{{ translate('Item has been removed from cart') }}");--}}
                        window.location.replace("{{ route('cart') }}");
                    } else {
                        AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                    }
                }
            });
        } else {
            AIZ.plugins.notify('warning', "{{ translate('Something went wrong. Please try again') }}");
        }
    }
</script>

