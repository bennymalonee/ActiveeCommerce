@extends('frontend.layouts.app')

@section('content')
@php
    $highest_bid = $product->bids->max('amount');
    $min_bid_amount = $highest_bid != null ? $highest_bid + 1 : $product->starting_bid;
    $gst_rate = function_exists('gst_applicable_product_rate') ? gst_applicable_product_rate($product->id) : null;
@endphp
<div class="py-4">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="bg-white rounded overflow-hidden">
                    <img src="{{ uploaded_asset($product->thumbnail_img) }}" alt="{{ $product->getTranslation('name') }}" class="img-fit w-100" onerror="this.src='{{ static_asset('assets/img/placeholder.jpg') }}';">
                </div>
            </div>
            <div class="col-lg-6">
                <h1 class="fs-20 fw-700 mb-3">{{ $product->getTranslation('name') }}</h1>
                <div class="border rounded p-3 mb-3">
                    <p class="mb-1 fs-14 fw-600">{{ translate('Auction Status') }}</p>
                    @if ($product->auction_end_date > time())
                        <div class="aiz-count-down" data-date="{{ date('Y/m/d H:i:s', $product->auction_end_date) }}"></div>
                    @else
                        <p class="mb-0 text-danger">{{ translate('Ended') }}</p>
                    @endif
                </div>
                <div class="border rounded p-3 mb-3">
                    <p class="mb-1 fs-14 fw-600">{{ translate('Starting Bid') }}</p>
                    <p class="mb-0 fs-18">{{ single_price($product->starting_bid) }}</p>
                </div>
                <div class="border rounded p-3 mb-3">
                    <p class="mb-1 fs-14 fw-600">{{ translate('Highest Bid') }}</p>
                    <p class="mb-0 fs-18">{{ $highest_bid ? single_price($highest_bid) : translate('No bids yet') }}</p>
                </div>
                @if ($product->auction_end_date >= time())
                    @if (Auth::check() && $product->user_id == Auth::id())
                        <p class="text-danger">{{ translate('Seller cannot place bid on own product') }}</p>
                    @else
                        <button type="button" onclick="bid_single_modal({{ $product->id }}, {{ $min_bid_amount }}, {{ $gst_rate ?? 'null' }})" class="btn btn-primary btn-lg">
                            <i class="las la-gavel"></i> {{ translate('Place Bid') }}
                        </button>
                    @endif
                @endif
                @if ($product->getTranslation('description'))
                    <div class="mt-4 pt-3 border-top">
                        <h5 class="fw-600 mb-2">{{ translate('Description') }}</h5>
                        <div class="fs-14">{!! $product->getTranslation('description') !!}</div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
