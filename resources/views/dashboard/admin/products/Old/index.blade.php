@extends('layouts.dashboard_layout')
@section('pagecss')
<style>
/* .card-horizontal {
    display: flex;
    flex: 1 1 auto;
}

img {
    width: 218px;
    height: 268px;
    object-fit: cover ;
} */
.card-horizontal {
    display: flex;
    flex: 1 1 auto;
}
.modal-lg {
    max-width: 50% !important;
}

.card-horizontal img {
  width: 50%;
}
</style>
@php
$seller_id = Auth::guard('seller')->user()->id;
// dd($seller_id);
@endphp
@endsection
@section('content')
<div class="content-wrapper">
    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white mr-2">
                <i class="mdi mdi-home"></i>
            </span> Product Management
        </h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ Route('seller.home') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Product List</li>
            </ol>
        </nav>
    </div>

    <div class="alert alert-info" role="alert">
        <strong>Note:</strong>
        <small>After adding new product please update it's specification, options and stocks to view and get listed in the app</small>
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
        {{-- Product Lists --}}
        <div class="col-md-12 col-sm-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex mb-2">
                        <div class="p-2 flex-grow-1">
                            <h4 class="card-title">Product Lists</h4>
                        </div>
                        {{-- <div class="p-2">
                            <a href="#" class="btn btn-outline-secondary btn-fw btn-sm">
                                <i class="mdi mdi-download btn-icon-prepend"></i> Download Report</a>
                        </div> --}}
                        <div class="p-2">
                            <a href="{{ route('seller.AddProduct') }}" class="btn btn-outline-primary btn-fw btn-sm">
                                <i class="mdi mdi-account-plus btn-icon-prepend"></i> Add New Product</a>
                        </div>
                    </div>
                    <div class="table-responsive">
                        @include('dashboard.seller.products.productlist')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('pagescript')

@endsection
