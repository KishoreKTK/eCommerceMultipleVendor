<?php
use Illuminate\Support\Carbon;
?>
@extends('layouts.dashboard_layout')
@section('content')
<div class="content-wrapper">
    <div class="page-header">
        <div class="d-lg-flex w-100 justify-content-between align-items-center">
            <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white mr-2">
                    <i class="mdi mdi-home"></i>
                </span> Dashboard
            </h3>
            <p class="mb-0 text-muted">{{ Carbon::now()->format('l \\, jS \\of F Y'); }}</p>
        </div>

    </div>

    <div class="row">
        <div class="col-md-3 stretch-card grid-margin">
            <div class="card bg-gradient-info card-img-holder text-white">
                <a href="{{ route('admin.SellerList') }}" class="card-body text-decoration-none text-reset">
                    <img src="{{ asset('assets/images/dashboard/circle.svg') }}" class="card-img-absolute" alt="circle-image" />
                    <h4 class="font-weight-normal mb-3">Total Seller <i class="mdi mdi-nature-people mdi-24px float-right"></i>
                    </h4>
                    <h2>{{ $dashboard['counts']['sellers'] }}</h2>
                </a>
            </div>
        </div>
        <div class="col-md-3 stretch-card grid-margin">
            <div class="card bg-gradient-danger card-img-holder text-white">
                <a href="{{ route('admin.ProductList') }}" class="card-body text-decoration-none text-reset">
                    <img src="{{ asset('assets/images/dashboard/circle.svg') }}" class="card-img-absolute" alt="circle-image" />
                    <h4 class="font-weight-normal mb-3">Total Products <i class="mdi mdi-shopping mdi-24px float-right"></i>
                    </h4>
                    <h2>{{ $dashboard['counts']['products'] }}</h2>
                </a>
            </div>
        </div>
        <div class="col-md-3 stretch-card grid-margin">
            <div class="card bg-gradient-success card-img-holder text-white">
                <a href="{{ route('admin.CustomerList') }}" class="card-body text-decoration-none text-reset">
                    <img src="{{ asset('assets/images/dashboard/circle.svg') }}" class="card-img-absolute" alt="circle-image" />
                    <h4 class="font-weight-normal mb-3">Total Customers<i class="mdi mdi-human-male-female mdi-24px float-right"></i>
                    </h4>
                    <h2>{{ $dashboard['counts']['customers'] }}</h2>
                </a>
            </div>
        </div>
        <div class="col-md-3 stretch-card grid-margin">
            <div class="card bg-gradient-primary card-img-holder text-white">
                <a href="{{ route('admin.OrderList') }}" class="card-body text-decoration-none text-reset">
                    <img src="{{ asset('assets/images/dashboard/circle.svg') }}" class="card-img-absolute" alt="circle-image" />
                    <h4 class="font-weight-normal mb-3">Total Order <i class="mdi mdi-cart-plus mdi-24px float-right"></i>
                    </h4>
                    <h2>{{ $dashboard['counts']['orders'] }}</h2>
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <h4 class="card-title">Popular Sellers</h4>
                        <a class="text-info" href="{{ route('admin.SellerList') }}" style="text-decoration: none;">View all</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th> # </th>
                                    <th> Name </th>
                                    <th> Total Orders </th>
                                    {{-- <th> Since </th> --}}
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($dashboard['popular_sellers'] as $key=>$seller)
                                <tr>
                                    <td> {{ $key+1 }} </td>
                                    <td> <img src="{{ asset($seller->image) }}" class="mr-2"
                                        alt="image"> {{ $seller->name }}</td>
                                    <td>{{ $seller->sellerorders }}</td>
                                    {{-- <td>{{ Carbon::parse($seller->created_at)->toFormattedDateString() }} </td> --}}
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <h4 class="card-title">Popular Products</h4>
                        <a class="text-info" href="{{ route('admin.ProductList') }}" style="text-decoration: none;">View all</a>
                    </div>

                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th> # </th>
                                    <th> Name </th>
                                    <th> Seller </th>
                                    <th> Orders </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($dashboard['popular_products'] as $key=>$featured)
                                <tr>
                                    <td>{{ $key+1 }} </td>
                                    <td>

                                            <img src="{{ asset($featured->image) }}" class="mr-2"
                                            alt="image"> {{ $featured->name }}
                                        </a>
                                    </td>
                                    <td> {{ $featured->sellername }} </td>
                                    <td> {{ $featured->productorders }} </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 grid-margin">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <h4 class="card-title">Recent Orders</h4>
                        <a class="text-info" href="{{ route('admin.OrderList') }}" style="text-decoration: none;">View all</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Seller</th>
                                    <th>Product</th>
                                    <th>Qty</th>
                                    <th>Order Date</th>
                                    <th>Status</th>
                                    <th>Order Id</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($dashboard['order_vendors'] as $key=>$order)
                                <tr>
                                    <td> <img src="{{ asset($order->sellerimage) }}" class="mr-2"
                                        alt="image">{{ $order->sellername }}</td>
                                    <td>{{ $order->productname }}</td>
                                    <td>{{ $order->prod_qty }}</td>
                                    <td>{{ Carbon::parse($order->created_at)->toFormattedDateString() }}</td>
                                    <td><label class="badge badge-gradient badge-info">{{ $order->statusname }}</label></td>
                                    {{-- <td>{{ Carbon::parse($order->updated_at)->toFormattedDateString() }}</td> --}}
                                    <td>{{ $order->orderid }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>




<!-- Button trigger modal -->
{{-- <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
  Launch demo modal
</button> --}}

<!-- Modal -->
{{-- <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        ...
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div> --}}





@endsection



