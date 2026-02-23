@extends('backend.layouts.app')

@section('content')
<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col">
            <h1 class="h3">{{ translate('Edit Auction Product') }}</h1>
        </div>
        <div class="col text-right">
            <a href="{{ route('auction.all_products') }}" class="btn btn-soft-primary">{{ translate('Back to list') }}</a>
        </div>
    </div>
</div>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('auction_product_update.admin', $product->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">{{ translate('Product Information') }}</h5>
        </div>
        <div class="card-body">
            <div class="form-group row">
                <label class="col-md-3 col-from-label">{{ translate('Product Name') }} <span class="text-danger">*</span></label>
                <div class="col-md-8">
                    <input type="text" class="form-control" name="name" value="{{ old('name', $product->getTranslation('name')) }}" required>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-from-label">{{ translate('Category') }} <span class="text-danger">*</span></label>
                <div class="col-md-8">
                    <select class="form-control aiz-selectpicker" name="category_ids[]" data-live-search="true" multiple required>
                        @php $product_category_ids = $product->categories->pluck('id')->toArray(); @endphp
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" {{ in_array($category->id, old('category_ids', $product_category_ids)) ? 'selected' : '' }}>{{ $category->getTranslation('name') }}</option>
                            @foreach ($category->childrenCategories as $child)
                                <option value="{{ $child->id }}" {{ in_array($child->id, old('category_ids', $product_category_ids)) ? 'selected' : '' }}>— {{ $child->getTranslation('name') }}</option>
                            @endforeach
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-from-label">{{ translate('Description') }}</label>
                <div class="col-md-8">
                    <textarea class="aiz-text-editor form-control" name="description">{{ old('description', $product->getTranslation('description')) }}</textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">{{ translate('Auction Details') }}</h5>
        </div>
        <div class="card-body">
            <div class="form-group row">
                <label class="col-md-3 col-from-label">{{ translate('Starting Bid') }} <span class="text-danger">*</span></label>
                <div class="col-md-8">
                    <input type="number" class="form-control" name="starting_bid" value="{{ old('starting_bid', $product->starting_bid) }}" min="0" step="0.01" required>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-from-label">{{ translate('Auction Date Range') }} <span class="text-danger">*</span></label>
                <div class="col-md-8">
                    @php
                        $start = $product->auction_start_date ? date('Y-m-d H:i', $product->auction_start_date) : '';
                        $end = $product->auction_end_date ? date('Y-m-d H:i', $product->auction_end_date) : '';
                        $date_range = $start && $end ? $start . ' to ' . $end : '';
                    @endphp
                    <input type="text" class="form-control aiz-date-range" name="auction_date_range" value="{{ old('auction_date_range', $date_range) }}" data-time-picker="true" data-format="YYYY-MM-DD HH:mm" data-separator=" to " autocomplete="off" required>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">{{ translate('Media') }}</h5>
        </div>
        <div class="card-body">
            <div class="form-group row">
                <label class="col-md-3 col-from-label">{{ translate('Thumbnail') }}</label>
                <div class="col-md-8">
                    <div class="input-group" data-toggle="aizuploader" data-type="image" data-selected="{{ $product->thumbnail_img }}">
                        <div class="input-group-prepend">
                            <div class="input-group-text bg-soft-secondary">{{ translate('Browse') }}</div>
                        </div>
                        <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                        <input type="hidden" name="thumbnail_img" class="selected-files" value="{{ $product->thumbnail_img }}">
                    </div>
                    <div class="file-preview box sm"></div>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-from-label">{{ translate('Gallery Images') }}</label>
                <div class="col-md-8">
                    <div class="input-group" data-toggle="aizuploader" data-type="image" data-multiple="true" data-selected="{{ $product->photos }}">
                        <div class="input-group-prepend">
                            <div class="input-group-text bg-soft-secondary">{{ translate('Browse') }}</div>
                        </div>
                        <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                        <input type="hidden" name="photos" class="selected-files" value="{{ $product->photos }}">
                    </div>
                    <div class="file-preview box sm"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">{{ translate('Shipping') }}</h5>
        </div>
        <div class="card-body">
            <div class="form-group row">
                <label class="col-md-3 col-from-label">{{ translate('Shipping Type') }}</label>
                <div class="col-md-8">
                    <select class="form-control aiz-selectpicker" name="shipping_type">
                        <option value="free" {{ $product->shipping_type == 'free' ? 'selected' : '' }}>{{ translate('Free Shipping') }}</option>
                        <option value="flat_rate" {{ $product->shipping_type == 'flat_rate' ? 'selected' : '' }}>{{ translate('Flat Rate') }}</option>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-from-label">{{ translate('Shipping Cost') }}</label>
                <div class="col-md-8">
                    <input type="number" class="form-control" name="flat_shipping_cost" value="{{ old('flat_shipping_cost', $product->shipping_cost) }}" min="0" step="0.01">
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="form-group row">
                <label class="col-md-3 col-from-label">{{ translate('Status') }}</label>
                <div class="col-md-8">
                    <label class="aiz-switch aiz-switch-success mb-0">
                        <input type="radio" name="button" value="publish" {{ $product->published ? 'checked' : '' }}> {{ translate('Publish') }}
                    </label>
                    <label class="aiz-switch aiz-switch-success mb-0 ml-3">
                        <input type="radio" name="button" value="unpublish" {{ !$product->published ? 'checked' : '' }}> {{ translate('Unpublish') }}
                    </label>
                </div>
            </div>
            <div class="form-group mb-0 text-right">
                <button type="submit" class="btn btn-primary">{{ translate('Update Auction Product') }}</button>
            </div>
        </div>
    </div>
</form>
@endsection
