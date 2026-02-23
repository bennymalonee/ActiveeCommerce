@php
    $auction_products = get_auction_products(12);
@endphp
@if (count($auction_products) > 0)
<section class="pt-32px pb-26px my-4" style="background: {{ get_setting('auction_product_bg_color', '#F0ECE3') }}">
    <div class="container">
        <div class="d-flex align-items-baseline justify-content-between mb-3">
            <h4 class="fs-16 fw-700 mb-0">{{ translate('Auction Products') }}</h4>
            <a href="{{ route('auction_products.all') }}" class="btn btn-sm rounded-1" style="background: {{ get_setting('auction_product_btn_color', '#C7B198') }}; color: #fff;">{{ translate('View All') }}</a>
        </div>
        <div class="row">
            @foreach ($auction_products as $product)
                <div class="col-md-3 col-lg-2 col-6 mb-3">
                    @include('frontend.thecore.partials.product_box_1', ['product' => $product])
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif
