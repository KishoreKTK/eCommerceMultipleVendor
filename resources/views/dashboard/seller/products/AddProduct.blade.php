@extends('layouts.dashboard_layout')
@section('pagecss')
{{-- <script src="{{ asset('assets/vendors/chosen/prism.js') }}"></script> --}}
<link rel="stylesheet" href="{{ asset('assets/vendors/chosen/prism.css') }}">
{{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/css/bootstrap-multiselect.css"> --}}

<style>
    .field_err{
        font-size: 12px;
        margin-top: 4px;
        color:red;
    }
</style>

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
              <li class="breadcrumb-item"><a href="{{ route('seller.ProductList') }}">Product Lists</a></li>
              <li class="breadcrumb-item active" aria-current="page">Add New Product</li>
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
    {{-- <div class="alert alert-danger" id="errorMessagesdiv" >
        <strong id="errmessage">{{ $message }}</strong>
        <button type="button" class="float-right close_err_msg_btn">
        <span aria-hidden="true">&times;</span>
        </button>
    </div> --}}
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    {{-- <h4 class="card-title">Add New Product</h4> --}}
                    <form class="form-sample mt-3" id="product_form_data" action="{{ route('seller.CreateProduct') }}" method="POST" autocomplete="off" enctype="multipart/form-data">
                        @csrf
                        @include('dashboard.commonly_used.v2.product_form')
                        {{-- @include('dashboard.commonly_used.product_form') --}}
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection

@section('pagescript')
{{-- <script src="{{ asset('assets/vendors/chosen/prism.js') }}"></script> --}}
<script src="{{ asset('assets/js/product_form.js') }}"></script>
<script>
    // $("document").ready(function(){
    //     (".select_attr_fields-multiple").chosen({
    //         // disable_search_threshold: 10,
    //         // no_results_text: "Oops, nothing found!",
    //         width: "95%"
    //     });
    //     $(".my_select_box").chosen({
    //         disable_search_threshold: 10,
    //         no_results_text: "Oops, nothing found!",
    //         width: "95%"
    //     });
    // });
</script>
@endsection
