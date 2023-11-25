@extends('layouts.dashboard_layout')
@section('pagecss')
<link rel="stylesheet"  href="{{ asset('assets/vendors/OwlCorousal/owl.carousel.min.css') }}"/>
<link rel="stylesheet"  href="{{ asset('assets/vendors/OwlCorousal/owl.theme.default.min.css') }}"/>
<style>
    .modal-lg {
        max-width: 50% !important;
    }
</style>
@endsection
@section('content')
<div class="content-wrapper">
    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white mr-2">
                <i class="mdi mdi-home"></i>
            </span> Seller Management
        </h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="{{ Route('admin.home') }}">Dashboard</a></li>
              <li class="breadcrumb-item"><a href="{{ Route('admin.SellerList') }}">Seller Lists</a></li>
              <li class="breadcrumb-item active" aria-current="page">Seller</li>
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
        <div class="col-lg-4 grid-margin stretch-card">
            @include('dashboard.commonly_used.seller_profile_card')
        </div>
        <div class="col-lg-8 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex mb-4 justify-content-end">
                        <ul class="nav nav-tabs card-header-tabs" id="seller-detailed-list" role="tablist">
                            <li class="nav-item">
                                <a class="btn btn-outline-primary nav-link active" href="#sellerdetails" role="tab" aria-controls="sellerdetails" aria-selected="true">Details</a>
                            </li>
                            <li class="nav-item">
                                <a class="btn btn-outline-primary nav-link" href="#sellerbanners" role="tab" aria-controls="sellerbanners" aria-selected="true">Banners</a>
                            </li>
                            <li class="nav-item">
                                <a class="btn btn-outline-primary nav-link" href="#sellerproducts" role="tab" aria-controls="sellerproducts" aria-selected="true">Products</a>
                            </li>
                            {{-- <li class="nav-item">
                                <a class="btn btn-outline-primary nav-link" href="#seller_transactions" role="tab" aria-controls="seller_transactions" aria-selected="false">Trasanctions</a>
                            </li> --}}
                        </ul>
                    </div>
                    <div class="tab-content mt-3">

                        {{-- Seller Details --}}
                        <div class="tab-pane active" id="sellerdetails" role="tabpanel" aria-labelledby="seller_transactions-tab">
                            <h6 class="mt-2">Seller Categories</h6>
                            <hr>
                            @if(count($seller_categories) > 0)
                                <div class="d-flex justify-content-start">
                                    @foreach ($seller_categories as $seller_cat)
                                    <span class="badge badge-pill badge-info mx-2">{{ $seller_cat->name }}</span>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-center">No Categories Selected</p>
                            @endif
                            <h6 class="mt-5">Seller Details</h6>
                            <hr>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap"
                                    data-toggle="tooltip" data-placement="top" title="Contact Person Name">
                                    <h6 class="mb-0">
                                        <i class="mdi mdi-briefcase" aria-hidden="true"></i><span class="text-secondary"> Contact Person Name
                                        </span></h6>
                                    <span class="font-weight-bold">
                                        {{ $seller_det->sellername }}
                                    </span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap"
                                    data-toggle="tooltip" data-placement="top" title="Business Category">
                                    <h6 class="mb-0">
                                        <i class="mdi mdi-lan" aria-hidden="true"></i><span class="text-secondary"> Business Category
                                        </span></h6>
                                    <span class="font-weight-bold">
                                        {{ $seller_det->seller_buss_type }}
                                    </span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap"
                                    data-toggle="tooltip" data-placement="top" title="Seller Area">
                                    <h6 class="mb-0">
                                        <i class="mdi mdi-account-card-details" aria-hidden="true"></i><span class="text-secondary"> Address </span>
                                    </span></h6>
                                    <span class="font-weight-bold">
                                        {{ $seller_det->sellerarea }}
                                    </span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap"
                                    data-toggle="tooltip" data-placement="top" title="Emirate">
                                    <h6 class="mb-0">
                                        <i class="mdi mdi-city" aria-hidden="true"></i><span class="text-secondary"> UAE City
                                        </span></h6>
                                    <span class="font-weight-bold">
                                        {{ $seller_det->city_name }}
                                    </span>
                                </li>
                                {{-- "shipping_charges": "0",
                                "tax_handle": "0",
                                "pickup": "1",
                                "delivery": "1", --}}
                                <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap"
                                    data-toggle="tooltip" data-placement="top" title="Pickup Availability">
                                    <h6 class="mb-0">
                                        <i class="mdi mdi-nature-people" aria-hidden="true"></i>
                                        <span class="text-secondary"> Pickup Availability</span>
                                    </h6>
                                    <span class="font-weight-bold">
                                        @if ($seller_det->pickup == '1')
                                            <span class="badge badge-pill badge-success">Yes</span>
                                        @else
                                            <span class="badge badge-pill badge-danger">No</span>
                                        @endif
                                    </span>
                                </li>

                                <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap"
                                    data-toggle="tooltip" data-placement="top" title="Delivery Availability">
                                    <h6 class="mb-0">
                                        <i class="mdi mdi-truck-delivery" aria-hidden="true"></i>
                                        <span class="text-secondary"> Delivery Availability</span>
                                    </h6>
                                    <span class="font-weight-bold">
                                        @if ($seller_det->delivery == '1')
                                            <span class="badge badge-pill badge-success">Yes</span>
                                        @else
                                            <span class="badge badge-pill badge-danger">No</span>
                                        @endif
                                    </span>
                                </li>

                                <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap"
                                    data-toggle="tooltip" data-placement="top" title="Shipping Charges Availability">
                                    <h6 class="mb-0">
                                        <i class="mdi mdi-cash" aria-hidden="true"></i>
                                        <span class="text-secondary">Shipping Charges Available</span>
                                    </h6>
                                    <span class="font-weight-bold">
                                        @if ($seller_det->shipping_charges == '1')
                                            <span class="badge badge-pill badge-success">Yes</span>
                                        @else
                                            <span class="badge badge-pill badge-danger">No</span>
                                        @endif
                                    </span>
                                </li>


                                <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap"
                                    data-toggle="tooltip" data-placement="top" title="Tax Availability">
                                    <h6 class="mb-0">
                                        <i class="mdi mdi-cash-multiple" aria-hidden="true"></i>
                                        <span class="text-secondary"> Tax Availability</span>
                                    </h6>
                                    <span class="font-weight-bold">
                                        @if ($seller_det->shipping_charges == '1')
                                            <span class="badge badge-pill badge-success">Yes</span>
                                        @else
                                            <span class="badge badge-pill badge-danger">No</span>
                                        @endif
                                    </span>
                                </li>


                                <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap"
                                    data-toggle="tooltip" data-platcement="top" title="Seller Requested Date">
                                    <h6 class="mb-0">
                                        <i class="mdi mdi-calendar-today" aria-hidden="true"></i><span class="text-secondary"> Joined At
                                        </span></h6>
                                    <span class="font-weight-bold">
                                        {{ $seller_det->created_at }}
                                    </span>
                                </li>
                            </ul>
                        </div>

                        {{-- Seller Banner Images --}}
                        <div class="tab-pane" id="sellerbanners" role="tabpanel" aria-labelledby="seller_transactions-tab">
                            <h4 class="card-title">Shop Banners</h4>
                            @if(count($seller_banners) > 0)
                            @else
                            <p class="text-center mt-2">No Banners Images Uploaded Yet. Please Update to View Banners</p>
                            @endif
                            <div class="owl-carousel owl-theme">
                                @foreach($seller_banners as $images)
                                    <img src="{{ asset($images->image_urls) }}"
                                    height="400px" width="400px"/>
                                @endforeach
                            </div>
                            {{-- <p>No Transactions from this Seller Yet</p> --}}
                            {{-- seller_banners --}}
                        </div>

                        {{-- Product Details --}}
                        <div class="tab-pane" id="sellerproducts" role="tabpanel">
                            <h4 class="card-title">Product Lists</h4>
                            <div class="table-responsive">
                                <table class="table ">
                                    <thead>
                                        <tr>
                                            <th># </th>
                                            <th>Image</th>
                                            <th>Name</th>
                                            <th>Category</th>
                                            <th>Price</th>
                                            <th>Stock</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(count($product_list) > 0)
                                            @foreach ($product_list as $key=>$product)
                                            <tr>
                                                <td>{{ $key+1 }}</td>
                                                <td><img src="{{ asset($product->image) }}" alt=""></td>
                                                <td>
                                                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                                                        {{-- <a href="#" style="text-decoration: none; color:inherit">{{ $product->name }}</a> --}}
                                                        <b>{{ $product->name }}</b>
                                                        @if($product->is_featured == '1')
                                                            <i class="mdi mdi mdi-star text-warning icon-sm" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="Featured Product"></i>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td>{{ $product->categoryname }}</td>
                                                <td>{{ $product->price }}</td>
                                                <td>{{ $product->available_qty }}</td>
                                            </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="7">
                                                    <center>No Products from this Seller Yet</center>
                                                </td>
                                            </tr>
                                        @endif

                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Seller Transactions --}}
                        <div class="tab-pane" id="seller_transactions" role="tabpanel" aria-labelledby="seller_transactions-tab">
                            <h4 class="card-title">Seller Trasactions</h4>
                            <p>No Transactions from this Seller Yet</p>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="ViewLicenceModel" tabindex="-1" aria-labelledby="ViewLicenceModelLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ViewLicenceModelLabel"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="ViewLicencePdf">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('pagescript')
<script src="{{ asset('assets/js/seller.js') }}"></script>
<script src="{{ asset('assets/vendors/OwlCorousal/owl.carousel.min.js') }}"></script>
@endsection
