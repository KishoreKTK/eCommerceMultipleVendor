
@extends('layouts.dashboard_layout')
@php
use Illuminate\Support\Carbon;

$routeName = \Request::route()->getName();
@endphp

@section('pagecss')
@endsection
@section('content')
<div class="content-wrapper">
    <div class="page-header">
        <h3 class="page-title">
            <a
            @if (strpos($routeName, 'admin.') === 0)
                href="{{ Route('admin.home') }}"
            @else
                href="{{ Route('seller.home') }}"
            @endif
            ><span class="page-title-icon bg-gradient-primary text-white mr-2">
                <i class="mdi mdi-home"></i>
            </span></a>Order Managment
        </h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a  @if (strpos($routeName, 'admin.') === 0)
                    href="{{ Route('admin.home') }}"
                @else
                    href="{{ Route('seller.home') }}"
                @endif>Dashboard</a></li>
              <li class="breadcrumb-item active" aria-current="page">Orders Lists</li>
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
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="border border-rounded p-2 mb-4">
                        <form id="filter_orders_form"
                            @if (strpos($routeName, 'admin.') === 0)
                                action="{{ Route('admin.OrderList') }}"
                            @else
                                action="{{ Route('seller.OrderList') }}"
                            @endif method="GET">
                            <p class="card-description"> Filter Orders </p>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInputName1">Keyword</label>
                                        <input type="text" class="form-control" name="keyword" value="{{ $keyword }}" id="exampleInputUsername1" placeholder="Search by Order id">
                                    </div>
                                </div>
                                @if (strpos($routeName, 'admin.') === 0)
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="exampleInputName1">Seller</label>
                                            <select name="seller_id" class="form-control mx-1 form-control-sm">
                                                <option value="" {{ $seller_id == '' ? 'selected' : '' }}>Select Seller</option>
                                                @foreach ($seller_list as $seller)
                                                    <option value="{{ $seller->id }}" {{ $seller_id == $seller->id ? 'selected' : '' }}>{{ $seller->seller_full_name_buss }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                @endif

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInputName1">Order Status</label>
                                        <select name="status" class="form-control mx-1 form-control-sm">
                                            <option value="" {{ $status == '' ? 'selected' : '' }}>Order Status</option>
                                            @foreach ($order_status as $ordersts){
                                                <option value="{{ $ordersts->id }}" {{ $status == $ordersts->id ? 'selected' : '' }}>{{ $ordersts->name }}</option>
                                            }
                                            @endforeach
                                         </select>
                                    </div>
                                </div>


                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInputName1">Order Type</label>
                                        <select name="ordertype" class="form-control mx-1 form-control-sm">
                                            <option value="" {{ $ordertype == '' ? 'selected' : '' }}>Order type</option>
                                            <option value="1" {{ $ordertype == '1' ? 'selected' : '' }}>Pickup</option>
                                            <option value="2" {{ $ordertype == '2' ? 'selected' : '' }}>Delivery</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInputName1">Payment Type</label>
                                        <select name="paymenttype" class="form-control mx-1 form-control-sm">
                                            <option value="" {{ $paymenttype == '' ? 'selected' : '' }}>Payment Type</option>
                                            <option value="1" {{ $paymenttype == '1' ? 'selected' : '' }}>Card</option>
                                            <option value="2" {{ $paymenttype == '2' ? 'selected' : '' }}>Cash</option>
                                            {{-- <option value="2" {{ $paymenttype == '2' ? 'selected' : '' }}>Refunded</option> --}}
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInputName1">Payment Status</label>
                                        <select name="paymentstatus" class="form-control mx-1 form-control-sm">
                                            <option value="" {{ $paymentstatus == '' ? 'selected' : '' }}>Payment Status</option>
                                            <option value="0" {{ $paymentstatus == '0' ? 'selected' : '' }}>Pending</option>
                                            <option value="1" {{ $paymentstatus == '1' ? 'selected' : '' }}>Paid</option>
                                            <option value="2" {{ $paymentstatus == '2' ? 'selected' : '' }}>Refunded</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInputName1">Start Date</label>
                                        <input type="date" id="startdateid" class="form-control" value="{{ $start_dt }}"
                                        name="startdate" placeholder="Start Date">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInputName1">End Date</label>
                                        <input type="date" id="enddateid" class="form-control" value="{{ $end_dt }}" name="enddate" placeholder="End Date">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12 d-flex justify-content-end">
                                    <button type="button" id="check_filter_btn" class="btn btn-outline-primary btn-fw mx-2 btn-sm">Filter</button>
                                    <a  @if (strpos($routeName, 'admin.') === 0)
                                            href="{{ Route('admin.OrderList') }}"
                                        @else
                                            href="{{ Route('seller.OrderList') }}"
                                        @endif
                                        class="btn btn-outline-secondary btn-fw mr-2 btn-sm">Reset</a>
                                </div>
                            </div>
                        </form>
                    </div>

                    <hr>
                    <div class="d-flex mb-2">
                        <div class="p-2 flex-grow-1"><h4 class="card-title">Orders List </h4></div>
                        @if(count($GetOrderDetails) != 0)
                        <div class="p-2">
                            <form
                                @if (strpos($routeName, 'admin.') === 0)
                                    action="{{ route('admin.ExportOrders') }}"
                                @else
                                    action="{{ route('seller.ExportOrders') }}"
                                @endif
                                method="POST">
                                @csrf
                                <input type="hidden" name="seller_id" value="{{ $seller_id }}">
                                <input type="hidden" name="ordertype" value="{{ $ordertype }}">
                                <input type="hidden" name="keyword" value="{{ $keyword }}">
                                <input type="hidden" name="status" value="{{ $status }}">
                                <input type="hidden" name="paymentstatus" value="{{ $paymentstatus }}">
                                <input type="hidden" name="startdate" value="{{ $start_dt }}">
                                <input type="hidden" name="enddate" value="{{ $end_dt }}">
                                <button class="btn btn-outline-dark btn-fw btn-sm">
                                    <i class="mdi mdi-download text-dark icon-sm"
                                    aria-hidden="true"></i> Download
                                </button>
                            </form>
                        </div>
                        @endif
                    </div>
                    <div class="table-responsive">
                        <table class="table ">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>OrderId</th>
                                    @if (strpos($routeName, 'admin.') === 0)
                                    <th>Seller</th>
                                    @else
                                    <th>Customer</th>
                                    @endif
                                    <th>Price</th>
                                    <th>Order Type</th>
                                    <th>Payment Type</th>
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
                                        @if (strpos($routeName, 'admin.') === 0)
                                        <td>{{ $order->sellername }}</td>
                                        @else
                                        <td>{{ $order->username }}</td>
                                        @endif

                                        <td>{{ $order->grand_total }} AED</td>
                                        <td class="text-center">
                                            @if($order->order_type == '1')
                                                <span class="badge badge-pill badge-info">Pickup</span>
                                            @else
                                                <span class="badge badge-pill badge-primary">Deliver</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($order->payment_type == '1')
                                                <span class="badge badge-pill badge-success">Card</span>
                                            @elseif($order->payment_type == '2')
                                                <span class="badge badge-pill badge-info">Cash</span>
                                            @else
                                                <span class="badge badge-pill badge-info">Bank Transfer</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($order->payment_status == '0')
                                                @if($order->payment_type == '1')
                                                <label class="badge badge-danger">Failed</label>
                                                @else
                                                <label class="badge badge-danger">Pending</label>
                                                @endif
                                            @elseif($order->payment_status == '1')
                                                <label class="badge badge-success">Paid</label>
                                            @else
                                                <label class="badge badge-secondary">Refunded</label>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <label class="badge badge-{{ $order->status_color }}">
                                                {{ $order->statusname }}
                                            </label>
                                        </td>
                                        <td>{{ Carbon::parse($order->created_at)->toFormattedDateString() }}</td>
                                        <td>
                                            <a
                                                @if (strpos($routeName, 'admin.') === 0)
                                                href="{{ route('admin.OrderDetail',$order->id) }}"
                                                @else
                                                href="{{ route('seller.OrderDetail',$order->id) }}"
                                                @endif
                                            class="btn btn-sm btn-outline-primary">View</a>

                                        </td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="9"><center>No Orders Placed Yet.</center></td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                        <div class="mt-2 float-right">
                            @if(count($GetOrderDetails) != 0)
                            {!! $GetOrderDetails->links() !!}
                            @endif
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection

@section('pagescript')
<script>
    $(document).ready(function(){
        $("body").on("click", "#check_filter_btn", function(e) {
            e.preventDefault();
            startdate = $("#startdateid").val();
            if(startdate != ''){
                enddate = $("#enddateid").val();
                if(enddate == ''){
                    iziToast.error({
                        title: 'Error',
                        message: 'Please Select End Date to Continue',
                        position: 'topRight',
                    });
                }
                else{
                    const x = new Date(startdate);
                    const y = new Date(enddate);
                    if(x>y){
                        iziToast.error({
                            title: 'Error',
                            message: 'Please Select End Date is Greater than Start Date',
                            position: 'topRight',
                        });
                    } else {
                        $("#filter_orders_form").submit();
                    }
                }
            } else {
                $("#filter_orders_form").submit();
            }
        });
    });
</script>
@endsection
