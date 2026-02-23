@extends('backend.layouts.app')

@section('content')
<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col">
            <h1 class="h3">{{ translate('Bids for') }}: {{ $product->getTranslation('name') }}</h1>
        </div>
        <div class="col text-right">
            <a href="{{ route('auction.all_products') }}" class="btn btn-soft-primary">{{ translate('Back to Auction Products') }}</a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <table class="table aiz-table mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ translate('User') }}</th>
                    <th>{{ translate('Amount') }}</th>
                    <th>{{ translate('Date') }}</th>
                    @can('delete_auction_product_bids')
                    <th class="text-right">{{ translate('Actions') }}</th>
                    @endcan
                </tr>
            </thead>
            <tbody>
                @foreach ($bids as $key => $bid)
                <tr>
                    <td>{{ $key + 1 + ($bids->currentPage() - 1) * $bids->perPage() }}</td>
                    <td>{{ $bid->user->name ?? $bid->user->email ?? '-' }}</td>
                    <td>{{ single_price($bid->amount) }}</td>
                    <td>{{ $bid->created_at ? $bid->created_at->format('Y-m-d H:i') : '-' }}</td>
                    @can('delete_auction_product_bids')
                    <td class="text-right">
                        <a href="javascript:void(0)" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-alert" data-href="{{ route('product_bids_destroy.admin', $bid->id) }}" title="{{ translate('Delete') }}"><i class="las la-trash"></i></a>
                    </td>
                    @endcan
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="aiz-pagination mt-3">
            {{ $bids->links() }}
        </div>
    </div>
</div>
@endsection
