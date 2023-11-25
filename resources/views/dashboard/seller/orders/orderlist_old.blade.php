<?php
use Illuminate\Support\Carbon;
?>
@extends('layouts.dashboard_layout')
@section('pagecss')
<link rel="stylesheet"  href="{{ asset('assets/vendors/OwlCorousal/owl.carousel.min.css') }}"/>
<link rel="stylesheet"  href="{{ asset('assets/vendors/OwlCorousal/owl.theme.default.min.css') }}"/>
<style>
.modal-lg {
    max-width: 60% !important;
}
</style>
@endsection
@section('content')
<div class="content-wrapper">
    <div class="page-header">
        <h3 class="page-title">
            <a href="{{ Route('seller.home') }}"><span class="page-title-icon bg-gradient-primary text-white mr-2">
                <i class="mdi mdi-home"></i>
            </span></a>Order Managment
        </h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="{{ Route('seller.home') }}">Dashboard</a></li>
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
                    <div class="d-flex mb-2">
                        <div class="p-2 flex-grow-1"><h4 class="card-title">Orders List </h4></div>
                        <div class="p-2">
                            <a href="#" class="btn btn-outline-secondary btn-fw btn-sm">
                                <i class="mdi mdi-download btn-icon-prepend"></i> Download</a>
                        </div>
                    </div>
                    <table class="table ">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>OrderId</th>
                                <th>Product</th>
                                <th>Qty</th>
                                <th>Price</th>
                                <th>Order Date</th>
                                <th>Status</th>
                                <th>Updated Date</th>
                                <th>Progress</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order_list as $key=>$order)
                            <tr>
                                <th>{{ $key+1 }}</th>
                                <td>{{ $order->orderid }}</td>
                                <td>{{ $order->productname }}</td>
                                <td>{{ $order->prod_qty }}</td>
                                <td>{{ $order->total_amount }}</td>
                                <td>{{ $order->created_at }}</td>
                                <td><label class="badge badge-info">{{ $order->statusname }}</label></td>
                                <td>{{ $order->updated_at }}</td>
                                <td>
                                    @if($order->orderstatus != 2 && $order->orderstatus < 7)
                                        <button class="btn btn-sm btn-outline-warning update_order_status"
                                            data-suborder_id="{{ $order->id }}" data-current_status="{{ $order->orderstatus }}"
                                            data-curr_statusname="{{ $order->statusname }}" data-order_id="{{ $order->orderid }}"
                                            data- data-toggle="tooltip" data-placement="top" title="Update Order Status"><i class="mdi mdi-camera-timer"></i>
                                        </button>
                                    @endif
                                    <button class="btn btn-sm btn-outline-info view_status_track_details" data-suborder_id="{{ $order->id }}" data-order_id="{{ $order->orderid }}" data-toggle="tooltip" data-placement="top" title="View Order Details"><i class="mdi mdi-eye" aria-hidden="true"></i></button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
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
                                <h5 class="card-title">Order Status</h5>
                                <ul class="gradient-bullet-list mt-4" id="ListOrderStatus">

                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-7 grid-margin stretch-card">
                        <div class="card">
                            <div class="card-body stretch-card">
                                <div class="mt-2">
                                    <h5 class="card-title" id="order_action_id"></h5>
                                    <form class="forms-sample" id="update_status_form" method="POST" enctype="multipart/form-data">
                                        <input type="hidden" name="curr_order_id" id="curr_order_id" value="">
                                        <input type="hidden" name="sub_order_id" id="sub_attr_id" value="">
                                        <input type="hidden" name="curr_status_id" id="curr_status_id" value="">
                                        <input type="hidden" name="order_status" id="new_hidden_status_id" value="">
                                        <input type="hidden" name="order_status_name" id="new_order_status_name" value="">
                                        <div class="form-group row" id="intial_order_acceptance">
                                            <div class="col-sm-6">
                                                <div class="form-check">
                                                <label class="form-check-label">
                                                    <input type="radio" class="form-check-input" name="verifyorder_status" id="membershipRadios1" value="3" checked> Accept <i class="input-helper"></i><i class="input-helper"></i><i class="input-helper"></i></label>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-check">
                                                <label class="form-check-label">
                                                    <input type="radio" class="form-check-input" name="verifyorder_status" id="membershipRadios2" value="2"> Decline <i class="input-helper"></i><i class="input-helper"></i><i class="input-helper"></i></label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group" id="status_needs_images">
                                            <label for="exampleInputUsername1">Image</label>
                                            <input type="file" class="form-control mb-2 mr-sm-2" name="images[]" required="" multiple>
                                        </div>

                                        <div class="form-group">
                                            <label for="exampleInputUsername1">Remarks</label>
                                            <textarea type="text" id="remarks" name="remarks" class="form-control mb-2 mr-sm-2" rows="10" cols="50" required=""></textarea>
                                        </div>
                                        <div class="float-right">
                                            <button type="button" class="btn btn-sm btn-gradient-secondary" data-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-sm btn-gradient-primary mr-2">Submit</button>
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
                                <h5 class="card-title">Order Status</h5>
                                <table class="table  table-responsive">
                                    <thead class="thead-inverse">
                                        <tr>
                                            <th>#</th>
                                            <th>Status</th>
                                            <th>Remarks</th>
                                            <th>Images</th>
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
                <div class="row justify-content-center" id="show_images_id">
                    <div class="col-lg-12 grid-margin stretch-card">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-3">
                                    <h6 id="status_img_heading"></h6>
                                    <button type="button" class="btn btn-sm btn-gradient-warning back_to_track">Back</button>
                                </div>
                                <div class="row display_images">
                                    {{-- owl-carousel owl-theme  --}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-sm float-right btn-gradient-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('pagescript')
<script src="{{ asset('assets/vendors/OwlCorousal/owl.carousel.min.js') }}"></script>
<script src="{{ asset('assets/js/orders.js') }}"></script>
@endsection
