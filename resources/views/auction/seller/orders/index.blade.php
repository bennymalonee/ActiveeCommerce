@extends('seller.layouts.app')

@section('panel_content')
<div class="aiz-titlebar mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col">
            <h1 class="h3">{{ translate('Auction Product Orders') }}</h1>
        </div>
        <div class="col text-right">
            <a href="{{ route('auction_products.seller.index') }}" class="btn btn-soft-primary">{{ translate('Back to Auction Products') }}</a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <table class="table aiz-table mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ translate('Order Code') }}</th>
                    <th>{{ translate('Amount') }}</th>
                    <th>{{ translate('Payment') }}</th>
                    <th>{{ translate('Delivery') }}</th>
                    <th>{{ translate('Date') }}</th>
                    <th class="text-right">{{ translate('Options') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orders as $key => $order)
                <tr>
                    <td>{{ $key + 1 + ($orders->currentPage() - 1) * $orders->perPage() }}</td>
                    <td>{{ $order->code }}</td>
                    <td>{{ single_price($order->grand_total) }}</td>
                    <td>{{ translate(ucfirst(str_replace('_', ' ', $order->payment_status))) }}</td>
                    <td>{{ translate(ucfirst(str_replace('_', ' ', $order->delivery_status))) }}</td>
                    <td>{{ $order->created_at ? $order->created_at->format('Y-m-d H:i') : '-' }}</td>
                    <td class="text-right">
                        <a href="{{ route('seller.orders.show', $order->id) }}" class="btn btn-soft-info btn-icon btn-circle btn-sm" title="{{ translate('View') }}"><i class="las la-eye"></i></a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="aiz-pagination mt-3">
            {{ $orders->links() }}
        </div>
    </div>
</div>
@endsection
