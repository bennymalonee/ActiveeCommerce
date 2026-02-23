@extends('frontend.layouts.app')

@section('content')
<div class="py-4">
    <div class="container">
        <h1 class="h4 fw-700 mb-4">{{ translate('Auction Products') }}</h1>
        <div class="row">
            @foreach ($products as $product)
                <div class="col-6 col-md-4 col-lg-3 mb-4">
                    <div class="card h-100 rounded overflow-hidden hov-shadow-lg has-transition">
                        <a href="{{ route('auction-product', $product->slug) }}" class="d-block text-reset">
                            <img src="{{ uploaded_asset($product->thumbnail_img) }}" alt="{{ $product->getTranslation('name') }}" class="img-fit card-img-top" style="height: 200px; object-fit: cover;" onerror="this.src='{{ static_asset('assets/img/placeholder.jpg') }}';">
                            <div class="card-body p-3">
                                <h6 class="fs-14 fw-500 text-truncate-2">{{ $product->getTranslation('name') }}</h6>
                                <p class="mb-0 fs-14 text-primary fw-600">{{ translate('Starting Bid') }}: {{ single_price($product->starting_bid) }}</p>
                                @if ($product->auction_end_date > time())
                                    <small class="text-muted">{{ translate('Ends') }}: {{ date('M d, Y H:i', $product->auction_end_date) }}</small>
                                @else
                                    <small class="text-danger">{{ translate('Ended') }}</small>
                                @endif
                            </div>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="aiz-pagination mt-4">
            {{ $products->links() }}
        </div>
    </div>
</div>
@endsection
