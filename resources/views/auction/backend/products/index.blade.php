@extends('backend.layouts.app')

@section('content')
<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col-auto">
            <h1 class="h3">{{ translate('Auction Products') }}</h1>
        </div>
        @can('add_auction_product')
        <div class="col text-right">
            <a href="{{ route('auction_product_create.admin') }}" class="btn btn-circle btn-info">
                <span>{{ translate('Add New Auction Product') }}</span>
            </a>
        </div>
        @endcan
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="row">
            <div class="col-md-6">
                <h5 class="mb-0 h6">{{ translate('All Auction Products') }}</h5>
            </div>
            <div class="col-md-6 text-right">
                <a href="{{ route('auction.all_products') }}" class="badge badge-inline badge-primary p-2 {{ $type == 'all' ? 'bg-primary text-white' : '' }}">{{ translate('All') }}</a>
                <a href="{{ route('auction.inhouse_products') }}" class="badge badge-inline p-2 {{ $type == 'inhouse' ? 'bg-primary text-white' : 'badge-soft-primary' }}">{{ translate('In-house') }}</a>
                <a href="{{ route('auction.seller_products') }}" class="badge badge-inline p-2 {{ $type == 'seller' ? 'bg-primary text-white' : 'badge-soft-primary' }}">{{ translate('Seller') }}</a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <table class="table aiz-table mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ translate('Image') }}</th>
                    <th>{{ translate('Name') }}</th>
                    <th>{{ translate('Starting Bid') }}</th>
                    <th>{{ translate('Auction End') }}</th>
                    <th>{{ translate('Status') }}</th>
                    <th class="text-right">{{ translate('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $key => $product)
                <tr>
                    <td>{{ $key + 1 + ($products->currentPage() - 1) * $products->perPage() }}</td>
                    <td>
                        <img src="{{ uploaded_asset($product->thumbnail_img) }}" alt="{{ $product->getTranslation('name') }}" class="size-60px img-fit rounded">
                    </td>
                    <td>{{ $product->getTranslation('name') }}</td>
                    <td>{{ single_price($product->starting_bid) }}</td>
                    <td>{{ $product->auction_end_date ? date('Y-m-d H:i', $product->auction_end_date) : '-' }}</td>
                    <td>
                        @if($product->published)
                            <span class="badge badge-inline badge-success">{{ translate('Published') }}</span>
                        @else
                            <span class="badge badge-inline badge-secondary">{{ translate('Unpublished') }}</span>
                        @endif
                    </td>
                    <td class="text-right">
                        @can('view_auction_product_bids')
                        <a href="{{ route('product_bids.admin', $product->id) }}" class="btn btn-soft-info btn-icon btn-circle btn-sm" title="{{ translate('Bids') }}"><i class="las la-gavel"></i></a>
                        @endcan
                        @can('edit_auction_product')
                        <a href="{{ route('auction_product_edit.admin', $product->id) }}" class="btn btn-soft-primary btn-icon btn-circle btn-sm" title="{{ translate('Edit') }}"><i class="las la-edit"></i></a>
                        @endcan
                        @can('delete_auction_product')
                        <a href="javascript:void(0)" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-alert" data-href="{{ route('auction_product_destroy.admin', $product->id) }}" title="{{ translate('Delete') }}"><i class="las la-trash"></i></a>
                        @endcan
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
