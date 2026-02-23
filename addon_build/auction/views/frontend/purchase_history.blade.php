@extends('frontend.layouts.app')

@section('content')
<div class="py-4">
    <div class="container">
        <h1 class="h4 fw-700 mb-4">{{ translate('Auction Purchase History') }}</h1>
        <div class="card">
            <div class="card-body">
                <table class="table aiz-table mb-0">
                    <thead>
                        <tr>
                            <th>{{ translate('Order Code') }}</th>
                            <th>{{ translate('Amount') }}</th>
                            <th>{{ translate('Payment Status') }}</th>
                            <th>{{ translate('Delivery Status') }}</th>
                            <th>{{ translate('Date') }}</th>
                            <th class="text-right">{{ translate('Options') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orders as $order)
                        <tr>
                            <td>{{ $order->code }}</td>
                            <td>{{ single_price($order->grand_total) }}</td>
                            <td>{{ translate(ucfirst(str_replace('_', ' ', $order->payment_status))) }}</td>
                            <td>{{ translate(ucfirst(str_replace('_', ' ', $order->delivery_status))) }}</td>
                            <td>{{ $order->created_at ? $order->created_at->format('Y-m-d H:i') : '-' }}</td>
                            <td class="text-right">
                                <a href="{{ route('purchase_history.details', $order->id) }}" class="btn btn-soft-info btn-sm">{{ translate('View') }}</a>
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
    </div>
</div>
@endsection
