@extends('seller.layouts.app')

@section('panel_content')
<div class="aiz-titlebar mt-2 mb-3">
    <h5 class="mb-0 h6">{{ translate('Add New Auction Product') }}</h5>
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

<form action="{{ route('auction_product_store.seller') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">{{ translate('Product Information') }}</h5>
        </div>
        <div class="card-body">
            <div class="form-group row">
                <label class="col-md-3 col-from-label">{{ translate('Product Name') }} <span class="text-danger">*</span></label>
                <div class="col-md-8">
                    <input type="text" class="form-control" name="name" value="{{ old('name') }}" required>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-from-label">{{ translate('Category') }} <span class="text-danger">*</span></label>
                <div class="col-md-8">
                    <select class="form-control aiz-selectpicker" name="category_ids[]" data-live-search="true" multiple required>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->getTranslation('name') }}</option>
                            @foreach ($category->childrenCategories as $child)
                                <option value="{{ $child->id }}">— {{ $child->getTranslation('name') }}</option>
                            @endforeach
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-from-label">{{ translate('Description') }}</label>
                <div class="col-md-8">
                    <textarea class="aiz-text-editor form-control" name="description">{{ old('description') }}</textarea>
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
                    <input type="number" class="form-control" name="starting_bid" value="{{ old('starting_bid', 0) }}" min="0" step="0.01" required>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-from-label">{{ translate('Auction Date Range') }} <span class="text-danger">*</span></label>
                <div class="col-md-8">
                    <input type="text" class="form-control aiz-date-range" name="auction_date_range" data-time-picker="true" data-format="YYYY-MM-DD HH:mm" data-separator=" to " autocomplete="off" required>
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
                    <div class="input-group" data-toggle="aizuploader" data-type="image">
                        <div class="input-group-prepend">
                            <div class="input-group-text bg-soft-secondary">{{ translate('Browse') }}</div>
                        </div>
                        <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                        <input type="hidden" name="thumbnail_img" class="selected-files">
                    </div>
                    <div class="file-preview box sm"></div>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-from-label">{{ translate('Gallery Images') }}</label>
                <div class="col-md-8">
                    <div class="input-group" data-toggle="aizuploader" data-type="image" data-multiple="true">
                        <div class="input-group-prepend">
                            <div class="input-group-text bg-soft-secondary">{{ translate('Browse') }}</div>
                        </div>
                        <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                        <input type="hidden" name="photos" class="selected-files">
                    </div>
                    <div class="file-preview box sm"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="form-group row">
                <label class="col-md-3 col-from-label">{{ translate('Shipping Type') }}</label>
                <div class="col-md-8">
                    <select class="form-control aiz-selectpicker" name="shipping_type">
                        <option value="free">{{ translate('Free Shipping') }}</option>
                        <option value="flat_rate">{{ translate('Flat Rate') }}</option>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-from-label">{{ translate('Shipping Cost') }}</label>
                <div class="col-md-8">
                    <input type="number" class="form-control" name="flat_shipping_cost" value="0" min="0" step="0.01">
                </div>
            </div>
            <div class="form-group mb-0">
                <input type="hidden" name="button" value="publish">
                <button type="submit" class="btn btn-primary">{{ translate('Save Auction Product') }}</button>
            </div>
        </div>
    </div>
</form>
@endsection
