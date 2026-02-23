<div class="modal-body px-4 py-5">
    <div class="text-center py-3">
        <p class="fs-16 fw-500 mb-2">{{ translate('Auction Product') }}</p>
        <p class="fs-14 text-gray mb-4">{{ translate('You can add this auction product to cart and proceed to checkout when the auction ends.') }}</p>
        <div class="media justify-content-center">
            <img src="{{ static_asset('assets/img/placeholder.jpg') }}" data-src="{{ uploaded_asset($product->thumbnail_img) }}"
                class="mr-3 lazyload size-80px img-fit rounded-1" alt="{{ $product->getTranslation('name') }}" onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';">
            <div class="media-body text-left">
                <h6 class="fs-14 fw-500">{{ $product->getTranslation('name') }}</h6>
                <p class="fs-14 text-gray mb-0">{{ translate('Starting Bid') }}: {{ single_price($product->starting_bid) }}</p>
            </div>
        </div>
        <div class="mt-4">
            <a href="{{ route('auction-product', $product->slug) }}" class="btn btn-primary btn-sm rounded-1">{{ translate('View Product') }}</a>
            <button type="button" class="btn btn-secondary btn-sm rounded-1 ml-2" data-dismiss="modal">{{ translate('Close') }}</button>
        </div>
    </div>
</div>
