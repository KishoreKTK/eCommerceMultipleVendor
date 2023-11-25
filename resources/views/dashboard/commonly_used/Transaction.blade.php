
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
              <li class="breadcrumb-item active" aria-current="page">Transaction Lists</li>
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
                        <form class="form-sample" method="GET" id="filter_transaction_form"
                            @if (strpos($routeName, 'admin.') === 0)
                                action="{{ Route('admin.TransactionList') }}"
                            @else
                                action="{{ Route('seller.TransactionList') }}"
                            @endif    >
                            <p class="card-description"> Filter Transactions </p>
                            <div class="row">

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
                                        <label for="exampleInputName1">Payment Status</label>
                                        <select name="status" class="form-control mx-1 form-control-sm">
                                            <option value="" {{ $status == '' ? 'selected' : '' }}>Payment Status</option>
                                            <option value="1" {{ $status == '1' ? 'selected' : '' }}>Paid</option>
                                            <option value="2" {{ $status == '2' ? 'selected' : '' }}>Refunded</option>
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

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="exampleInputName1">Keyword</label>
                                        <input type="text" class="form-control" name="keyword" value="{{ $keyword }}" id="exampleInputUsername1" placeholder="Search by Order id">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12 d-flex justify-content-end">
                                    <button type="button" id="check_filter_btn" class="btn btn-outline-primary btn-fw mx-2 btn-sm">Filter</button>
                                    <a  @if (strpos($routeName, 'admin.') === 0)
                                            href="{{ Route('admin.TransactionList') }}"
                                        @else
                                            href="{{ Route('seller.TransactionList') }}"
                                        @endif
                                        class="btn btn-outline-secondary btn-fw mr-2 btn-sm">Reset</a>
                                </div>
                            </div>
                        </form>
                    </div>

                    <hr>
                    <div class="d-flex mb-2">
                        <div class="p-2 flex-grow-1"><h4 class="card-title">Transaction List </h4></div>
                        @if(count($GetOrderDetails) > 0)
                            <div class="p-2">
                                <form
                                    @if (strpos($routeName, 'admin.') === 0)
                                        action="{{ route('admin.ExportTransaction') }}"
                                    @else
                                        action="{{ route('seller.ExportTransaction') }}"
                                    @endif
                                    method="POST">
                                    @csrf
                                    <input type="hidden" name="ordertype" value="{{ $ordertype }}">
                                    <input type="hidden" name="keyword" value="{{ $keyword }}">
                                    <input type="hidden" name="status" value="{{ $status }}">
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
                                    <th>Order Id</th>
                                    <th>Transaction Id</th>
                                    <th>Payment Status</th>
                                    <th>Order Type</th>
                                    <th>Shipping Charge</th>
                                    <th>Tax Amount(5%)</th>
                                    <th>Processing fee</th>
                                    <th>Sub Total</th>
                                    <th>Commission</th>
                                    <th>Transaction fee</th>
                                    <th>Grand Total</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(count($GetOrderDetails) != 0)
                                    @foreach($GetOrderDetails as $key => $order)
                                    <tr>
                                        <th>{{ $key + $GetOrderDetails->firstItem() }}</th>
                                        <td>{{ $order->order_id }}</td>
                                        <td>
                                            @if($order->transaction_id == null)
                                            -
                                            @else
                                            {{ $order->transaction_id }}
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($order->payment_status == '0')
                                                <label class="badge badge-danger">Pending</label>
                                            @elseif($order->payment_status == '1')
                                                <label class="badge badge-success">Paid</label>
                                            @else
                                                <label class="badge badge-secondary">Refunded</label>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($order->order_type == '1')
                                                <label class="badge badge-info">Pickup</label>
                                            @else
                                                <label class="badge badge-dark">Delivery</label>
                                            @endif
                                        </td>
                                        <td>{{ $order->shipping_charge }} AED</td>
                                        <td>{{ $order->tax }} AED</td>
                                        <td>{{ $order->processing_fee }} AED</td>
                                        <td>{{ $order->sub_total }} AED</td>
                                        <td>{{ $order->commission }} AED</td>
                                        <td>{{ $order->transaction_fee }} AED</td>
                                        <td>{{ $order->grand_total }} AED</td>
                                        <td>{{ $order->created_at }}</td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="10"><center>No Transactions Yet.</center></td>
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
                        $("#filter_transaction_form").submit();
                    }
                }
            } else {
                $("#filter_transaction_form").submit();
            }
        });
    });
</script>
@endsection
