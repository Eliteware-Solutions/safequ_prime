@extends('backend.layouts.app')

@section('content')
    <div class="aiz-titlebar text-left mt-2 mb-3">
        <h5 class="mb-0 h6">{{ translate('Edit Product') }}</h5>
    </div>
    <div class="">
        <form class="form form-horizontal mar-top" action="{{route('wholesale_product_update.admin', $product->id)}}"
              method="POST" enctype="multipart/form-data" id="choice_form">
            <div class="row gutters-5">
                <div class="col-lg-8">
                    <input name="_method" type="hidden" value="POST">
                    <input type="hidden" name="id" value="{{ $product->id }}">
                    <input type="hidden" name="lang" value="{{ $lang }}">
                    @csrf
                    <div class="card">
                        <div class="card-body">
                            <div class="form-group row">
                                <label class="col-lg-3 col-from-label">{{translate('Product Name')}} <i
                                            class="las la-language text-danger"
                                            title="{{translate('Translatable')}}"></i></label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control" name="name"
                                           placeholder="{{translate('Product Name')}}"
                                           value="{{ $product->getTranslation('name', $lang) }}" required>
                                </div>
                            </div>
                            <div class="form-group row" id="category">
                                <label class="col-md-3 col-from-label">{{translate('Category')}} <span
                                        class="text-danger">*</span></label>
                                <div class="col-md-8">
                                    <select class="form-control aiz-selectpicker" name="parent_category_id" id="parent_category_id"
                                            data-live-search="true" required>
                                        <option value=""></option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}" @if($category->id == $product->parent_category_id) selected @endif >
                                                {{ $category->getTranslation('name') }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row" id="sub_category">
                                <label class="col-md-3 col-from-label">{{translate('Sub Category')}} <span
                                        class="text-danger">*</span></label>
                                <div class="col-md-8" id="sub_category_select">
                                    <select class="form-control aiz-selectpicker" name="sub_category_id" id="sub_category_id"
                                            data-live-search="true" required>
                                        @foreach ($sub_categories as $sub_category)
                                            <option value="{{ $sub_category->id }}" @if($sub_category->id == $product->sub_category_id) selected @endif>
                                                {{ $sub_category->getTranslation('name') }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 col-from-label">{{translate('Variation')}}</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="variation"
                                           placeholder="{{ translate('Variation') }}" value="{{ $product->variation }}">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-3 col-from-label">{{translate('Unit')}} <i
                                            class="las la-language text-danger"
                                            title="{{translate('Translatable')}}"></i> </label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control" name="unit"
                                           placeholder="{{ translate('Unit (e.g. KG, Pc etc)') }}"
                                           value="{{$product->getTranslation('unit', $lang)}}" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-lg-3 col-from-label">Secondary Unit</label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control" name="secondary_unit"
                                           placeholder="Secondary Unit (e.g. gm, ml etc)"
                                           value="{{$product->secondary_unit}}" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-3 col-from-label">{{translate('Minimum Purchase Qty')}}</label>
                                <div class="col-lg-8">
                                    <input type="number" lang="en" class="form-control" name="min_qty"
                                           value="{{$product->min_qty}}" min="0.001" step=".001" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-3 col-from-label">{{translate('Tags')}}</label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control aiz-tag-input" name="tags[]" id="tags"
                                           value="{{ $product->tags }}"
                                           placeholder="{{ translate('Type to add a tag') }}" data-role="tagsinput">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 col-from-label">{{translate('Manufacturer Name')}}</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="manufacturer_location"
                                           placeholder="{{ translate('Manufacturer Name') }}"
                                           value="{{ $product->manufacturer_location }}">
                                </div>
                            </div>

                            @if (addon_is_activated('pos_system'))
                                <div class="form-group row">
                                    <label class="col-lg-3 col-from-label">{{translate('Barcode')}}</label>
                                    <div class="col-lg-8">
                                        <input type="text" class="form-control" name="barcode"
                                               placeholder="{{ translate('Barcode') }}" value="{{ $product->barcode }}">
                                    </div>
                                </div>
                            @endif

                            @if (addon_is_activated('refund_request'))
                                <div class="form-group row">
                                    <label class="col-lg-3 col-from-label">{{translate('Refundable')}}</label>
                                    <div class="col-lg-8">
                                        <label class="aiz-switch aiz-switch-success mb-0" style="margin-top:5px;">
                                            <input type="checkbox" name="refundable"
                                                   @if ($product->refundable == 1) checked @endif>
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Product Price & Stock Starts -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 h6">{{translate('Product price + stock')}}</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group row">
                                <label class="col-lg-3 col-from-label">{{translate('Unit price')}}</label>
                                <div class="col-lg-6">
                                    <input type="text" placeholder="{{translate('Unit price')}}" name="unit_price"
                                           class="form-control" value="{{ (isset($product_stock->price) ? $product_stock->price : '') }}" required>
                                </div>
                            </div>

                            <div id="show-hide-div">
                                <div class="form-group row" id="quantity">
                                    <label class="col-lg-3 col-from-label">{{translate('Quantity')}}</label>
                                    <div class="col-lg-6">
                                        <input type="number" lang="en" value="{{ (isset($product_stock->qty) && floatval($product_stock->qty) > 0 ? $product_stock->qty : 0) }}" step="1"
                                               placeholder="{{translate('Quantity')}}" name="current_stock"
                                               class="form-control">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 col-from-label">
                                        {{translate('SKU')}}
                                    </label>
                                    <div class="col-md-6">
                                        <input type="text" placeholder="{{ translate('SKU') }}"
                                               value="{{ (isset($product_stock->sku) ? $product_stock->sku : '') }}" name="sku" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 col-from-label">
                                    {{translate('Wholesale Prices')}}
                                </label>
                                <div class="col-md-6">
                                    <div class="qunatity-price">
                                        @if(isset($product_stock->wholesalePrices))
                                            @foreach ($product_stock->wholesalePrices as $wholesalePrice)
                                                <div class="row gutters-5">
                                                    <div class="col-3">
                                                        <div class="form-group">
                                                            <input type="text" class="form-control"
                                                                   placeholder="{{translate('Min QTY')}}"
                                                                   name="wholesale_min_qty[]"
                                                                   value="{{ $wholesalePrice->min_qty }}" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-3">
                                                        <div class="form-group">
                                                            <input type="text" class="form-control"
                                                                   placeholder="{{ translate('Max QTY') }}"
                                                                   name="wholesale_max_qty[]"
                                                                   value="{{ $wholesalePrice->max_qty }}" required>
                                                        </div>
                                                    </div>
                                                    <div class="col">
                                                        <div class="form-group">
                                                            <input type="text" class="form-control"
                                                                   placeholder="{{ translate('Price per piece') }}"
                                                                   name="wholesale_price[]"
                                                                   value="{{ $wholesalePrice->price }}" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-auto">
                                                        <button type="button"
                                                                class="mt-1 btn btn-icon btn-circle btn-sm btn-soft-danger"
                                                                data-toggle="remove-parent" data-parent=".row">
                                                            <i class="las la-times"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @else
                                        @endif
                                    </div>
                                    <button
                                        type="button"
                                        class="btn btn-soft-secondary btn-sm"
                                        data-toggle="add-more"
                                        data-content='<div class="row gutters-5">
                                        <div class="col-3">
                                            <div class="form-group">
                                                <input type="text" class="form-control" placeholder="{{translate('Min Qty')}}" name="wholesale_min_qty[]" required>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-group">
                                                <input type="text" class="form-control" placeholder="{{ translate('Max Qty') }}" name="wholesale_max_qty[]" required>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="form-group">
                                                <input type="text" class="form-control" placeholder="{{ translate('Price per piece') }}" name="wholesale_price[]" required>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <button type="button" class="mt-1 btn btn-icon btn-circle btn-sm btn-soft-danger" data-toggle="remove-parent" data-parent=".row">
                                                <i class="las la-times"></i>
                                            </button>
                                        </div>
                                    </div>'
                                        data-target=".qunatity-price">
                                        {{ translate('Add More') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Product Price & Stock Ends -->

                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 h6">{{translate('Product Images')}}</h5>
                        </div>
                        <div class="card-body">

                            <div class="form-group row">
                                <label class="col-md-3 col-form-label"
                                       for="signinSrEmail">{{translate('Gallery Images')}}</label>
                                <div class="col-md-8">
                                    <div class="input-group" data-toggle="aizuploader" data-type="image"
                                         data-multiple="true">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
                                        </div>
                                        <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                        <input type="hidden" name="photos" value="{{ $product->photos }}"
                                               class="selected-files">
                                    </div>
                                    <div class="file-preview box sm">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label"
                                       for="signinSrEmail">{{translate('Thumbnail Image')}}
                                    <small>(290x300)</small></label>
                                <div class="col-md-8">
                                    <div class="input-group" data-toggle="aizuploader" data-type="image">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
                                        </div>
                                        <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                        <input type="hidden" name="thumbnail_img" value="{{ $product->thumbnail_img }}"
                                               class="selected-files">
                                    </div>
                                    <div class="file-preview box sm">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 h6">{{translate('Product Videos')}}</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group row">
                                <label class="col-lg-3 col-from-label">{{translate('Video Provider')}}</label>
                                <div class="col-lg-8">
                                    <select class="form-control aiz-selectpicker" name="video_provider"
                                            id="video_provider">
                                        <option value="youtube" <?php if ($product->video_provider == 'youtube') echo "selected"; ?> >{{translate('Youtube')}}</option>
                                        <option value="dailymotion" <?php if ($product->video_provider == 'dailymotion') echo "selected"; ?> >{{translate('Dailymotion')}}</option>
                                        <option value="vimeo" <?php if ($product->video_provider == 'vimeo') echo "selected"; ?> >{{translate('Vimeo')}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-3 col-from-label">{{translate('Video Link')}}</label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control" name="video_link"
                                           value="{{ $product->video_link }}"
                                           placeholder="{{ translate('Video Link') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 h6">{{translate('Product Description')}}</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group row">
                                <label class="col-lg-3 col-from-label">{{translate('Description')}} <i
                                            class="las la-language text-danger"
                                            title="{{translate('Translatable')}}"></i></label>
                                <div class="col-lg-9">
                                    <textarea class="aiz-text-editor"
                                              name="description">{{ $product->getTranslation('description', $lang) }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 h6">{{translate('PDF Specification')}}</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label"
                                       for="signinSrEmail">{{translate('PDF Specification')}}</label>
                                <div class="col-md-8">
                                    <div class="input-group" data-toggle="aizuploader">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
                                        </div>
                                        <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                        <input type="hidden" name="pdf" value="{{ $product->pdf }}"
                                               class="selected-files">
                                    </div>
                                    <div class="file-preview box sm">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 h6">{{translate('SEO Meta Tags')}}</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group row">
                                <label class="col-lg-3 col-from-label">{{translate('Meta Title')}}</label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control" name="meta_title"
                                           value="{{ $product->meta_title }}" placeholder="{{translate('Meta Title')}}">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-3 col-from-label">{{translate('Description')}}</label>
                                <div class="col-lg-8">
                                    <textarea name="meta_description" rows="8"
                                              class="form-control">{{ $product->meta_description }}</textarea>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label"
                                       for="signinSrEmail">{{translate('Meta Images')}}</label>
                                <div class="col-md-8">
                                    <div class="input-group" data-toggle="aizuploader" data-type="image"
                                         data-multiple="true">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
                                        </div>
                                        <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                        <input type="hidden" name="meta_img" value="{{ $product->meta_img }}"
                                               class="selected-files">
                                    </div>
                                    <div class="file-preview box sm">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label">{{translate('Slug')}}</label>
                                <div class="col-md-8">
                                    <input type="text" placeholder="{{translate('Slug')}}" id="slug" name="slug"
                                           value="{{ $product->slug }}" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">

                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 h6" class="dropdown-toggle" data-toggle="collapse"
                                data-target="#collapse_2">
                                {{translate('Shipping Configuration')}}
                            </h5>
                        </div>
                        <div class="card-body collapse show" id="collapse_2">
                            @if (get_setting('shipping_type') == 'product_wise_shipping')
                                <div class="form-group row">
                                    <label class="col-lg-6 col-from-label">{{translate('Free Shipping')}}</label>
                                    <div class="col-lg-6">
                                        <label class="aiz-switch aiz-switch-success mb-0">
                                            <input type="radio" name="shipping_type" value="free"
                                                   @if($product->shipping_type == 'free') checked @endif>
                                            <span></span>
                                        </label>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-lg-6 col-from-label">{{translate('Flat Rate')}}</label>
                                    <div class="col-lg-6">
                                        <label class="aiz-switch aiz-switch-success mb-0">
                                            <input type="radio" name="shipping_type" value="flat_rate"
                                                   @if($product->shipping_type == 'flat_rate') checked @endif>
                                            <span></span>
                                        </label>
                                    </div>
                                </div>

                                <div class="flat_rate_shipping_div" style="display: none">
                                    <div class="form-group row">
                                        <label class="col-lg-6 col-from-label">{{translate('Shipping cost')}}</label>
                                        <div class="col-lg-6">
                                            <input type="number" lang="en" min="0" value="{{ $product->shipping_cost }}"
                                                   step="0.01" placeholder="{{ translate('Shipping cost') }}"
                                                   name="flat_shipping_cost" class="form-control">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-md-6 col-from-label">{{translate('Is Product Quantity Mulitiply')}}</label>
                                    <div class="col-md-6">
                                        <label class="aiz-switch aiz-switch-success mb-0">
                                            <input type="checkbox" name="is_quantity_multiplied" value="1"
                                                   @if($product->is_quantity_multiplied == 1) checked @endif>
                                            <span></span>
                                        </label>
                                    </div>
                                </div>
                            @else
                                <p>
                                    {{ translate('Product wise shipping cost is disable. Shipping cost is configured from here') }}
                                    <a href="{{route('shipping_configuration.index')}}"
                                       class="aiz-side-nav-link {{ areActiveRoutes(['shipping_configuration.index','shipping_configuration.edit','shipping_configuration.update'])}}">
                                        <span class="aiz-side-nav-text">{{translate('Shipping Configuration')}}</span>
                                    </a>
                                </p>
                            @endif

                            <div class="form-group row pt-3">
                                <label class="col-md-6 col-from-label">{{translate('Best Selling Product')}}</label>
                                <div class="col-md-6">
                                    <label class="aiz-switch aiz-switch-success mb-0">
                                        @if(isset($product_stock->is_best_selling))
                                            <input type="checkbox" name="is_best_selling" {{ $product_stock->is_best_selling ? 'checked' : '' }} value="1">
                                            <span></span>
                                        @else
                                            <input type="checkbox" name="is_best_selling" value="1">
                                            <span></span>
                                        @endif
                                    </label>
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label for="name">
                                    {{translate('Shipping Days')}}
                                </label>
                                <div class="input-group">
                                    <input type="number" class="form-control" name="est_shipping_days"
                                           value="{{ (isset($product_stock->est_shipping_days) ? $product_stock->est_shipping_days : '') }}" min="1" step="1"
                                           placeholder="{{translate('Shipping Days')}}">
                                    <div class="input-group-prepend">
                                    <span class="input-group-text"
                                          id="inputGroupPrepend">{{translate('Days')}}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 h6">{{translate('Low Stock Quantity Warning')}}</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group mb-3">
                                <label for="name">
                                    {{translate('Quantity')}}
                                </label>
                                <input type="number" name="low_stock_quantity"
                                       value="{{ $product->low_stock_quantity }}" min="0" step="1" class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 h6">
                                {{translate('Stock Visibility State')}}
                            </h5>
                        </div>

                        <div class="card-body">

                            <div class="form-group row">
                                <label class="col-md-6 col-from-label">{{translate('Show Stock Quantity')}}</label>
                                <div class="col-md-6">
                                    <label class="aiz-switch aiz-switch-success mb-0">
                                        <input type="radio" name="stock_visibility_state" value="quantity"
                                               @if($product->stock_visibility_state == 'quantity') checked @endif>
                                        <span></span>
                                    </label>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-6 col-from-label">{{translate('Show Stock With Text Only')}}</label>
                                <div class="col-md-6">
                                    <label class="aiz-switch aiz-switch-success mb-0">
                                        <input type="radio" name="stock_visibility_state" value="text"
                                               @if($product->stock_visibility_state == 'text') checked @endif>
                                        <span></span>
                                    </label>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-6 col-from-label">{{translate('Hide Stock')}}</label>
                                <div class="col-md-6">
                                    <label class="aiz-switch aiz-switch-success mb-0">
                                        <input type="radio" name="stock_visibility_state" value="hide"
                                               @if($product->stock_visibility_state == 'hide') checked @endif>
                                        <span></span>
                                    </label>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 h6">{{translate('Cash On Delivery')}}</h5>
                        </div>
                        <div class="card-body">
                            @if (get_setting('cash_payment') == '1')
                                <div class="form-group row">
                                    <div class="col-md-12">
                                        <div class="form-group row">
                                            <label class="col-md-6 col-from-label">{{translate('Status')}}</label>
                                            <div class="col-md-6">
                                                <label class="aiz-switch aiz-switch-success mb-0">
                                                    <input type="checkbox" name="cash_on_delivery" value="1"
                                                           @if($product->cash_on_delivery == 1) checked @endif>
                                                    <span></span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <p>
                                    {{ translate('Cash On Delivery option is disabled. Activate this feature from here') }}
                                    <a href="{{route('activation.index')}}"
                                       class="aiz-side-nav-link {{ areActiveRoutes(['shipping_configuration.index','shipping_configuration.edit','shipping_configuration.update'])}}">
                                        <span class="aiz-side-nav-text">{{translate('Cash Payment Activation')}}</span>
                                    </a>
                                </p>
                            @endif
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 h6">{{translate('Featured')}}</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group row">
                                <div class="col-md-12">
                                    <div class="form-group row">
                                        <label class="col-md-6 col-from-label">{{translate('Status')}}</label>
                                        <div class="col-md-6">
                                            <label class="aiz-switch aiz-switch-success mb-0">
                                                <input type="checkbox" name="featured" value="1"
                                                       @if($product->featured == 1) checked @endif>
                                                <span></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 h6">{{translate('Flash Deal')}}</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group row">
                                <div class="col-md-12">
                                    <div class="form-group row">
                                        <label class="col-md-6 col-from-label">{{translate('Status')}}</label>
                                        <div class="col-md-6">
                                            <label class="aiz-switch aiz-switch-success mb-0">
                                                <input type="checkbox" name="todays_deal" value="1" class="todays_deal"
                                                       @if($product->todays_deal == 1) checked @endif>
                                                <span></span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="form-group mb-3 deal_discount_div" style="display: none;">
                                        <label for="name">
                                            {{translate('Discount')}} (%)
                                        </label>
                                        <input type="number" name="deal_discount"
                                            value="{{$product->discount ? $product->discount : '1'}}"
                                            min="1" step="1" class="form-control deal_discount">
                                        <input type="hidden" name="discount_type" value="percent">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card" style="display: none;">
                        <div class="card-header">
                            <h5 class="mb-0 h6">{{translate('Flash Deal')}}</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group mb-3">
                                <label for="name">
                                    {{translate('Add To Flash')}}
                                </label>
                                <select class="form-control aiz-selectpicker" name="flash_deal_id" id="video_provider">
                                    <option value="">Choose Flash Title</option>
                                    @foreach(\App\Models\FlashDeal::where("status", 1)->get() as $flash_deal)
                                        <option value="{{ $flash_deal->id}}"
                                                @if($product->flash_deal_product && $product->flash_deal_product->flash_deal_id == $flash_deal->id) selected @endif>
                                            {{ $flash_deal->title }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group mb-3">
                                <label for="name">
                                    {{translate('Discount')}}
                                </label>
                                <input type="number" name="flash_discount"
                                       value="{{$product->flash_deal_product ? $product->flash_deal_product->discount : '0'}}"
                                       min="0" step="1" class="form-control">
                            </div>
                            <div class="form-group mb-3">
                                <label for="name">
                                    {{translate('Discount Type')}}
                                </label>
                                <select class="form-control aiz-selectpicker" name="flash_discount_type" id="">
                                    <option value="">Choose Discount Type</option>
                                    <option value="amount"
                                            @if($product->flash_deal_product && $product->flash_deal_product->discount_type == 'amount') selected @endif>
                                        {{translate('Flat')}}
                                    </option>
                                    <option value="percent"
                                            @if($product->flash_deal_product && $product->flash_deal_product->discount_type == 'percent') selected @endif>
                                        {{translate('Percent')}}
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 h6">{{translate('VAT & Tax')}}</h5>
                        </div>
                        <div class="card-body">
                            @foreach(\App\Models\Tax::where('tax_status', 1)->get() as $tax)
                                <label for="name">
                                    {{$tax->name}}
                                    <input type="hidden" value="{{$tax->id}}" name="tax_id[]">
                                </label>

                                @php
                                    $tax_amount = 0;
                                    $tax_type = '';
                                    foreach($tax->product_taxes as $row) {
                                        if($product->id == $row->product_id) {
                                            $tax_amount = $row->tax;
                                            $tax_type = $row->tax_type;
                                        }
                                    }
                                @endphp

                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <input type="number" lang="en" min="0" value="{{ $tax_amount }}" step="0.01"
                                               placeholder="{{ translate('Tax') }}" name="tax[]" class="form-control"
                                               required>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <select class="form-control aiz-selectpicker" name="tax_type[]">
                                            <option value="amount" @if($tax_type == 'amount') selected @endif>
                                                {{translate('Flat')}}
                                            </option>
                                            <option value="percent" @if($tax_type == 'percent') selected @endif>
                                                {{translate('Percent')}}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                </div>
                <div class="col-12">
                    <div class="mb-3 text-right">
                        <button type="submit" name="button"
                                class="btn btn-info">{{ translate('Update Product') }}</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

@endsection

@section('script')

    <script type="text/javascript">

        "use strict";

        $(document).ready(function () {
            show_hide_shipping_div();
            $('.remove-files').on('click', function () {
                $(this).parents(".col-md-4").remove();
            });

            if($('input.todays_deal').is(':checked')) {
                $('input.deal_discount').attr('required', true)
                $('.deal_discount_div').slideDown();
            }
            else {
                $('input.deal_discount').attr('required', false)
                $('.deal_discount_div').slideUp();
            }

            $('input.todays_deal').on('change',function() {
                if($(this).is(':checked')) {
                    $('input.deal_discount').attr('required', true)
                    $('.deal_discount_div').slideDown();
                }
                else {
                    $('input.deal_discount').attr('required', false)
                    $('.deal_discount_div').slideUp();
                }
            })
        });

        $("[name=shipping_type]").on("change", function () {
            show_hide_shipping_div();
        });

        function show_hide_shipping_div() {
            var shipping_val = $("[name=shipping_type]:checked").val();

            $(".flat_rate_shipping_div").hide();

            if (shipping_val == 'flat_rate') {
                $(".flat_rate_shipping_div").show();
            }
        }

        $("#parent_category_id").on("change", function () {
            if ($(this).val() > 0) {
                $.post('{{ route('wholesale_products.load_subcategory') }}', {
                    _token: AIZ.data.csrf,
                    id: $(this).val()
                }, function (data) {
                    $('#sub_category_select').html('');
                    $('#sub_category_select').html(data.subcategory);

                    $("select[name=sub_category_id]").selectpicker("refresh");
                });
            }
        });

        AIZ.plugins.tagify();

    </script>

@endsection
