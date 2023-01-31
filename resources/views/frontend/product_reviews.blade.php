@extends('frontend.layouts.app', ['new_header' => false, 'header_show' => false, 'header2' => false, 'footer' => false])

@section('content')

    <main class="main-tag-mt-sm">
        <div class="container">
            <div class="row justify-content-center py-4">
                <div class="col-lg-5 col-md-7 col-sm-8 px-1 pb-2">
                    @if (!empty($product))
                        <div class="review-itm-card p-3">
                            <div class="img-name">
                                <div class="item-img text-center">
                                    <img src="{{ uploaded_asset($product->photos) }}"
                                        onerror="this.onerror=null;this.src='{{ static_asset('assets/img/no-image-found.jpg') }}';"
                                        alt="{{ $product->name }}" />
                                </div>
                                <div class="pl-3">
                                    <h6 class="fw700">{{ $product->name }}</h6>
                                    <p class="fw600 body-txt mb-2">Variety: {{ $product->tags }}</p>
                                    <p class="rating-stars mb-0 fsize16">
                                        {!! renderStarRating($product->rating) !!}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                    <hr>

                    <div class="review-card py-2 text-center">
                        @if (Auth::check())
                            @php
                                $commentable = false;
                            @endphp
                            @if (\App\Models\Review::where('user_id', Auth::user()->id)->where('product_id', $product->id)->first() == null)
                                @php
                                    $commentable = true;
                                @endphp
                            @endif
                        @endif

                        @if ($commentable)
                            <div class="user-img mb-3">
                                <img src="{{ uploaded_asset(Auth::user()->avatar_original) }}"
                                    alt="{{ Auth::user()->name }}"
                                    onerror="this.onerror=null;this.src='https://cdn.iconscout.com/icon/free/png-256/avatar-370-456322.png'">
                            </div>
                            <p class="fw600 fsize15 title-txt mb-1">{{ Auth::user()->name }}</p>
                            <p class="fw500 fsize13 light-text mb-1">Phone No.: {{ Auth::user()->phone }}</p>

                            <form action="{{ route('reviews.store') }}" class="review-form py-3" method="POST"
                                role="form">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">

                                <div class="form-group mb-2">
                                    <div class="rating-group">
                                        <label aria-label="1 star" class="rating__label" for="rating-1"><i
                                                class="rating__icon rating__icon--star fa fa-star"></i></label>
                                        <input class="rating__input" name="rating" id="rating-1" value="1"
                                            type="radio" />
                                        <label aria-label="2 stars" class="rating__label" for="rating-2"><i
                                                class="rating__icon rating__icon--star fa fa-star"></i></label>
                                        <input class="rating__input" name="rating" id="rating-2" value="2"
                                            type="radio" />
                                        <label aria-label="3 stars" class="rating__label" for="rating-3"><i
                                                class="rating__icon rating__icon--star fa fa-star"></i></label>
                                        <input class="rating__input" name="rating" id="rating-3" value="3"
                                            type="radio" />
                                        <label aria-label="4 stars" class="rating__label" for="rating-4"><i
                                                class="rating__icon rating__icon--star fa fa-star"></i></label>
                                        <input class="rating__input" name="rating" id="rating-4" value="4"
                                            type="radio" checked />
                                        <label aria-label="5 stars" class="rating__label" for="rating-5"><i
                                                class="rating__icon rating__icon--star fa fa-star"></i></label>
                                        <input class="rating__input" name="rating" id="rating-5" value="5"
                                            type="radio" />
                                    </div>
                                </div>

                                <div class="form-group">
                                    <textarea name="comment" id="review" class="form-control" rows="3" placeholder="Additional comments..."></textarea>
                                </div>

                                <button type="submit" class="btn primary-btn">Submit Review</button>
                            </form>
                            <hr>
                        @endif


                        @foreach ($reviews as $review)
                            <div class="user-review text-left bb-1 mb-2">
                                <div class="data py-3">
                                    <div class="user">
                                        <img src="{{ uploaded_asset($review->user->avatar_original) }}"
                                            onerror="this.onerror=null;this.src='https://cdn.iconscout.com/icon/free/png-256/avatar-370-456322.png'"
                                            alt="{{ $review->user->name }}">
                                        <div class="pl-3">
                                            <p class="fw600 fsize15 title-txt mb-1">{{ $review->user->name }}</p>
                                            <p class="rating-stars mb-0 fsize15">
                                                {!! renderStarRating($review->rating) !!}
                                            </p>
                                        </div>
                                    </div>
                                    <p class="fw600 fsize13 body-txt mb-0">
                                        {{ date('d/m/Y H:i', strtotime($review->created_at)) }}</p>
                                </div>
                                <p class="body-txt fsize13">{{ $review->comment }}</p>
                            </div>
                        @endforeach

                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection

@section('script')
    <script></script>
@endsection
