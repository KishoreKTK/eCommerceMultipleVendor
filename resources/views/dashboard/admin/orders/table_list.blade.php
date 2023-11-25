<?php
use Illuminate\Support\Carbon;
?>
<table class="table ">
    <thead>
        <tr>
            <th>#</th>
            <th>OrderId</th>
            <th>Seller</th>
            <th>Price</th>
            <th>Order Type</th>
            <th>Payment Status</th>
            <th>Status</th>
            <th>Order Date</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @if(count($GetOrderDetails) != 0)
            @foreach($GetOrderDetails as $key => $order)
            <tr>
                <th>{{ $key + $GetOrderDetails->firstItem() }}</th>
                <td>{{ $order->order_id }}</td>
                <td>{{ $order->sellername }}</td>
                <td>{{ $order->grand_total }} AED</td>
                <td>
                    @if($order->order_type == '1')
                        <span class="badge badge-pill badge-primary">Pickup</span>
                    @else
                        <span class="badge badge-pill badge-success">Deliver</span>
                    @endif
                </td>
                <td>
                    @if($order->payment_status == '0')
                        <label class="badge badge-danger">Pending</label>
                    @else
                        <label class="badge badge-success">Paid</label>
                    @endif
                </td>
                <td>
                    <label class="badge badge-info">{{ $order->statusname }}</label>
                </td>
                <td>{{ Carbon::parse($order->created_at)->toFormattedDateString() }}</td>
                <td><a href="{{ route('admin.OrderDetail',$order->id) }}" class="btn btn-sm btn-outline-primary">View</a>
                    {{-- <a href="#" class="btn btn-sm btn-outline-danger">Cancel</a></td> --}}
            </tr>
            @endforeach
        @else
        <tr><td colspan="7"><center>No Orders Placed Yet.</center></td></tr>
        @endif
    </tbody>
</table>
<div class="mt-2 float-right">
    @if(count($GetOrderDetails) != 0)
    {!! $GetOrderDetails->links() !!}
    @endif
</div>
