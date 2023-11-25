@php
use Illuminate\Support\Carbon;
$routeName = \Request::route()->getName();
if (strpos($routeName, 'admin.') === 0) {
$route = 'admin';
} else {
$route = 'seller';
}
@endphp

@extends('layouts.dashboard_layout')
@section('pagecss')
<style>
    .inactiveLink {
        pointer-events: none;
        cursor: default;
    }

    .modal-lg {
        max-width: 60% !important;
    }

</style>
@endsection
@section('content')
<div class="content-wrapper">
    <input type="hidden" id="current_route_name" value="{{ $route }}">
    <div class="page-header">
        <h3 class="page-title">
            <a @if (strpos($routeName, 'admin.' )===0) href="{{ Route('admin.home') }}" @else
                href="{{ Route('seller.home') }}" @endif><span
                    class="page-title-icon bg-gradient-primary text-white mr-2">
                    <i class="mdi mdi-home"></i>
                </span></a>Order Managment
        </h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a @if (strpos($routeName, 'admin.' )===0) href="{{ Route('admin.home') }}"
                        @else href="{{ Route('seller.home') }}" @endif>Dashboard</a></li>
                <li class="breadcrumb-item"><a @if (strpos($routeName, 'admin.' )===0)
                        href="{{ Route('admin.OrderList') }}" @else href="{{ Route('seller.OrderList') }}" @endif>Order
                        List</a></li>
                <li class="breadcrumb-item active" aria-current="page">Order Detail</li>
            </ol>
        </nav>
    </div>
    @if ($message = Session::get('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>{{ $message }}</strong>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif
    @if ($message = Session::get('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>{{ $message }}</strong>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    <div class="row">
        <div class="col-md-7 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Order Details</h4>
                    <div class="table-responsive">
                        <table class="table table-dark table-borderd ">
                            <tbody>
                                <tr>
                                    <td colspan='2' class="text-center">
                                        @if($order_det['order']->payment_status == '0')
                                        <label class="btn btn-gradient-danger btn-block inactiveLink">Payment
                                            Pending</label>
                                        @elseif($order_det['order']->payment_status == '1')
                                        <label class="btn btn-gradient-success btn-block inactiveLink">Payment Successful</label>
                                        @else
                                        <label class="btn btn-secondary btn-block inactiveLink">Payment Refunded</label>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>Order Id</td>
                                    <td class="text-right">{{ $order_det['order']->order_id }}</td>
                                </tr>
                                <tr>
                                    <td>Status</td>
                                    <td class="text-right">
                                        <label for="orderstatus" class="badge badge-info">
                                            @if ($order_det['order']->order_status_id == 7)
                                                @if ($order_det['order']->order_type == '2')
                                                    Delivered
                                                @else
                                                    {{ $order_det['order']->statusname }}
                                                @endif
                                            @else
                                                {{ $order_det['order']->statusname }}
                                            @endif

                                        </label></td>
                                </tr>
                                <tr>
                                    <td>Order Type</td>
                                    <td class="text-right">
                                        @if($order_det['order']->order_type == '1')
                                        <span class="badge badge-pill badge-primary">Pickup</span>
                                        @else
                                        <span class="badge badge-pill badge-primary">Deliver</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>Payment Type</td>
                                    <td class="text-right">
                                        @if($order_det['order']->payment_type == '1')
                                        <span class="badge badge-pill badge-success">Card</span>
                                        @elseif($order_det['order']->payment_type == '2')
                                        <span class="badge badge-pill badge-success">Cash</span>
                                        @else
                                        <span class="badge badge-pill badge-success">Bank</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>Shipping Charges</td>
                                    <td class="text-right">{{ $order_det['order']->shipping_charge }} AED</td>
                                </tr>
                                <tr>
                                    <td>Tax Percentage</td>
                                    <td class="text-right">
                                        {{ ($order_det['order']->tax_percentage)?$order_det['order']->tax_percentage:0 }}
                                        %</td>
                                </tr>
                                <tr>
                                    <td>Processing Fee</td>
                                    <td class="text-right">
                                        {{ ($order_det['order']->processing_fee)?$order_det['order']->processing_fee:0 }}
                                        AED</td>
                                </tr>

                                <tr>
                                    <td>Sub Total</td>
                                    <td class="text-right">
                                        {{ ($order_det['order']->sub_total)?$order_det['order']->sub_total:0 }} AED</td>
                                </tr>
                                <tr>
                                    <td>Transaction Fee</td>
                                    <td class="text-right">
                                        {{ ($order_det['order']->transaction_fee)?$order_det['order']->transaction_fee:0 }}
                                        AED</td>
                                </tr>
                                <tr>
                                    <td>Grand Total</td>
                                    <td class="text-right">{{ $order_det['order']->grand_total }} AED</td>
                                </tr>

                                <tr>
                                    <td>Order Date</td>
                                    <td class="text-right">{{ $order_det['order']->created_at }}</td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="text-center">
                                        <a @if (strpos($routeName, 'admin.' )===0)
                                            href="{{ Route('admin.OrderInvoice',$order_det['myorderid']) }}" @else
                                            href="{{ Route('seller.OrderInvoice',$order_det['myorderid']) }}" @endif
                                            name="View invoice" id="view_invoice" target="_blank"
                                            class="btn btn-gradient-primary btn-block">VIEW INVOICE</a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-5 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 border border-secondary rounded p-2 m-2">
                            <h4>Customer Details</h4>
                            <div class="table-responsive">
                                <table class="table table-light  table-sm">
                                    <tbody>
                                        <tr>
                                            <td>Name</td>
                                            <td>{{ $order_det['user_det']->name }}</td>
                                        </tr>
                                        <tr>
                                            <td>Email</td>
                                            <td>
                                                <a class="badge badge-info"
                                                    href="mailto:{{ $order_det['user_det']->email }} }}">{{ $order_det['user_det']->email }}</a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Mobile</td>
                                            <td>
                                                <a class="badge badge-info"
                                                    href="tel:+{{ $order_det['user_det']->phone }}">{{ $order_det['user_det']->phone }}</a>
                                            </td>
                                        </tr>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-12 border border-secondary rounded p-2 m-2">
                            <h4>Shipping Address</h4>
                            <div class="alert alert-primary">
                                <address>
                                    <p class="font-weight-bold">{{ $order_det['address']->first_name }},</p>
                                    <p> {{ $order_det['address']->phone_num }},</p>
                                    <p> {{ $order_det['address']->address }},</p>
                                    <p> {{ $order_det['address']->city }} </p>
                                </address>
                            </div>
                        </div>
                        @if ($order_det['order']->order_status_id != 2)
                            @if($order_det['order']->payment_type != '1' && $order_det['order']->payment_status == '0')
                                <div class="col-12 border border-secondary rounded p-2 m-2">
                                    <h4>Update Payment Status</h4>
                                    <hr>
                                    <form action="
                                                @if (strpos($routeName, 'admin.' )===0)
                                                    {{ route('admin.updatepaymentstatus') }}
                                                @else
                                                    {{ route('seller.updatepaymentstatus') }}
                                                @endif
                                            "
                                            method="POST">
                                        @csrf
                                        <input type="hidden" name="order_id" value="{{ $order_det['order']->order_id }}">
                                        <div class="form-group">
                                            <label for="update-payment-status">Payment Status</label>
                                            <select id="update-payment-status" required class="form-control" name="payment_status">
                                                <option value="">Unpaid</option>
                                                <option value="1">Paid</option>
                                            </select>
                                        </div>
                                        @if($order_det['order']->payment_type == '3')
                                        <div class="form-group">
                                            <label for="update-bank-transfer-copy">Upload Transfer Copy</label>
                                            <input id="update-bank-transfer-copy" class="form-control-file" type="file" name="transfer_copy">
                                        </div>
                                        @endif
                                        <div>
                                            <button type="submit" class="btn btn-success float-right">Update</button>
                                        </div>
                                    </form>
                                </div>
                            @endif
                        @endif
                    </div>
                    <div>
                        <hr size="2">
                        <h6>Order Status Update & History</h6>
                        @php
                            $order = $order_det['order'];
                        @endphp
                        <div class="d-flex justify-content-center">
                            @if($order->order_status_id == 8)
                            @elseif($order->order_status_id == 10)
                            @elseif( $order->order_status_id == 2)
                                @if($order_det['order']->payment_status != '0')
                                    <button type="button" class="btn btn-outline-danger update_order_status m-2"
                                        data-suborder_id="{{ $order_det['myorderid'] }}"
                                        data-current_status="{{ $order->order_status_id }}"
                                        data-curr_statusname="{{ $order->statusname }}" data-order_id="{{ $order->order_id }}"
                                        @if(strpos($routeName, 'admin.') === 0)
                                        data-route_name = "admin"
                                        @else
                                        data-route_name = "seller"
                                        @endif data-order_type = "{{ $order->order_type }}"
                                        data- data-toggle="tooltip" data-placement="top" title="Update Order Status">
                                        <i class="mdi mdi-camera-timer"></i> Initiate Refund
                                    </button>
                                @endif
                                @else
                                <button type="button" class="btn btn-outline-warning update_order_status m-2"
                                    data-suborder_id="{{ $order_det['myorderid'] }}"
                                    data-current_status="{{ $order->order_status_id }}"
                                    data-curr_statusname="{{ $order->statusname }}" data-payment_status="{{ $order_det['order']->payment_status }}" data-order_id="{{ $order->order_id }}"
                                    @if(strpos($routeName, 'admin.' )===0) data-route_name="admin" @else data-route_name="seller"
                                    @endif data-order_type="{{ $order->order_type }}" data- data-toggle="tooltip"
                                    data-placement="top" title="Update Order Status">
                                    <i class="mdi mdi-camera-timer"></i> Update Order Status
                                </button>
                            @endif
                            <button type="button" class="btn btn-outline-success m-2 view_status_track_details"
                                data-order_type="{{ $order->order_type }}" data-suborder_id="{{ $order->order_id }}"
                                data-order_id="{{ $order->order_id }}" data-toggle="tooltip" data-placement="top"
                                title="View Order Details">
                                <i class="mdi mdi-eye" aria-hidden="true"></i> View Order Track
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Products</h4>
                    <div class="table-responsive">
                        <table class="table 
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Seller</th>
                                    <th>Product</th>
                                    <th>Option</th>
                                    <th>Qty</th>
                                    <th>Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $ordqty = $order_det['qty_det']->toArray();
                                ?>
                                @if(count($ordqty) > 0)
                                    @foreach ($ordqty as $key=>$qty)
                                    <tr>
                                        <td>{{ $key+1 }}</td>
                                        <td>{{ $qty->sellername }}</td>
                                        <td>{{ $qty->prod_name }}</td>
                                        <td>
                                            @foreach ($qty->options as $opt)
                                            <p>
                                                <span class="badge badge-pill badge-secondary">{{ $opt->attr_name }}</span>
                                                <span class="badge badge-pill badge-dark">{{ $opt->option_name }}</span>
                                            </p>
                                            @endforeach
                                        </td>
                                        <td>{{ $qty->prod_qty }}</td>
                                        <td>{{ $qty->total_amount }}</td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="4">
                                            <center>No Record Found</center>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="UpdateOrderStatus" tabindex="-1" aria-labelledby="ViewLicenceModelLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Orders</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-5 grid-margin stretch-card">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Current Order Status</h5>
                                <ul class="list-ticked mt-4" id="ListOrderStatus">

                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-7 grid-margin stretch-card">
                        <div class="card">
                            <div class="card-body stretch-card">
                                <div class="mt-2">
                                    <h5 class="card-title" id="order_action_id"></h5>
                                    <form class="forms-sample" id="update_status_form" method="POST"
                                        enctype="multipart/form-data">
                                        <input type="hidden" name="curr_order_id" id="curr_order_id" value="">
                                        <input type="hidden" name="sub_order_id" id="sub_attr_id" value="">
                                        <input type="hidden" name="curr_status_id" id="curr_status_id" value="">
                                        <input type="hidden" name="order_status" id="new_hidden_status_id" value="">
                                        <input type="hidden" name="order_status_name" id="new_order_status_name"
                                            value="">
                                        <div class="form-group row" id="intial_order_acceptance">
                                            <div class="col-sm-6">
                                                <div class="form-check">
                                                    <label class="form-check-label">
                                                        <input type="radio" class="form-check-input"
                                                            name="verifyorder_status" id="membershipRadios1" value="3"
                                                            checked> Accept <i class="input-helper"></i><i
                                                            class="input-helper"></i><i
                                                            class="input-helper"></i></label>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-check">
                                                    <label class="form-check-label">
                                                        <input type="radio" class="form-check-input"
                                                            name="verifyorder_status" id="membershipRadios2" value="2">
                                                        Decline <i class="input-helper"></i><i
                                                            class="input-helper"></i><i
                                                            class="input-helper"></i></label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group" id="status_needs_images">
                                            <label for="exampleInputUsername1">Image</label>
                                            <input type="file" class="form-control mb-2 mr-sm-2" name="images[]"
                                                required="" multiple>
                                        </div>

                                        <div class="form-group">
                                            <label for="exampleInputUsername1">Remarks</label>
                                            <textarea type="text" id="remarks" name="remarks"
                                                class="form-control mb-2 mr-sm-2" rows="10" cols="50"></textarea>
                                        </div>
                                        <div class="float-right">
                                            <button type="button" class="btn btn-sm btn-gradient-secondary"
                                                data-dismiss="modal">Close</button>
                                            <button type="submit"
                                                class="btn btn-sm btn-gradient-primary mr-2">Submit</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="ViewTrackDetails" tabindex="-1" aria-labelledby="ViewLicenceModelLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Track Order History</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row" id="track_table_id">
                    <div class="col-lg-12 grid-margin stretch-card">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Current Order Status</h5>
                                <div class="table-responsive">
                                    <table class="table 
                                        <thead class="thead-inverse">
                                            <tr>
                                                <th>#</th>
                                                <th>Status</th>
                                                <th>Remarks</th>
                                                <th>Date</th>
                                            </tr>
                                        </thead>
                                        <tbody id="track_status_data">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-center" id="show_images_id">
                    <div class="col-lg-12 grid-margin stretch-card">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-3">
                                    <h6 id="status_img_heading"></h6>
                                    <button type="button"
                                        class="btn btn-sm btn-gradient-warning back_to_track">Back</button>
                                </div>
                                <div class="row display_images">
                                    {{-- owl-carousel owl-theme  --}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-sm float-right btn-gradient-danger"
                    data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('pagescript')
<script src="{{ asset('assets/js/orders.js') }}"></script>
@endsection
