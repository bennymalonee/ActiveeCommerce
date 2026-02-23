@extends('seller.layouts.app')

@section('panel_content')
<div class="aiz-titlebar mt-2 mb-4">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="h3">{{ translate('Auction Products') }}</h1>
        </div>
        <div class="col-md-6 text-right">
            <a href="{{ route('auction_product_create.seller') }}" class="btn btn-primary"><i class="las la-plus"></i> {{ translate('Add New Auction Product') }}</a>
            <a href="{{ route('auction_products_orders.seller') }}" class="btn btn-soft-info">{{ translate('Auction Orders') }}</a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <table class="table aiz-table mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ translate('Image') }}</th>
                    <th>{{ translate('Name') }}</th>
                    <th>{{ translate('Starting Bid') }}</th>
                    <th>{{ translate('Auction End') }}</th>
                    <th>{{ translate('Bids') }}</th>
                    <th class="text-right">{{ translate('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $key => $product)
                <tr>
                    <td>{{ $key + 1 + ($products->currentPage() - 1) * $products->perPage() }}</td>
                    <td><img src="{{ uploaded_asset($product->thumbnail_img) }}" alt="" class="size-60px img-fit rounded"></td>
                    <td>{{ $product->getTranslation('name') }}</td>
                    <td>{{ single_price($product->starting_bid) }}</td>
                    <td>{{ $product->auction_end_date ? date('Y-m-d H:i', $product->auction_end_date) : '-' }}</td>
                    <td>{{ $product->bids->count() }}</td>
                    <td class="text-right">
                        <a href="{{ route('product_bids.seller', $product->id) }}" class="btn btn-soft-info btn-icon btn-circle btn-sm" title="{{ translate('Bids') }}"><i class="las la-gavel"></i></a>
                        <a href="{{ route('auction_product_edit.seller', $product->id) }}" class="btn btn-soft-primary btn-icon btn-circle btn-sm" title="{{ translate('Edit') }}"><i class="las la-edit"></i></a>
                        <a href="javascript:void(0)" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-alert" data-href="{{ route('auction_product_destroy.seller', $product->id) }}" title="{{ translate('Delete') }}"><i class="las la-trash"></i></a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="aiz-pagination mt-3">
            {{ $products->links() }}
        </div>
    </div>
</div>
@endsection
