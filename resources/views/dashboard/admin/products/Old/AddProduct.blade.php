@extends('layouts.dashboard_layout')
@section('pagecss')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/css/bootstrap-multiselect.css">

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
              <li class="breadcrumb-item"><a href="{{ Route('admin.home') }}">Dashboard</a></li>
              <li class="breadcrumb-item"><a href="{{ route('admin.ProductList') }}">Product Lists</a></li>
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
                    <form class="form-sample mt-3" id="product_form_data" action="{{ route('admin.CreateProduct') }}" method="POST" autocomplete="off" enctype="multipart/form-data">
                        @csrf
                        @include('dashboard.commonly_used.v2.product_form')
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection

@section('pagescript')

<script src="{{ asset('assets/js/product_form.js') }}"></script>

<script>
    $(document).ready(function()
    {
        $('#seller_category_id').hide();

        var base_url = window.location.origin;
        var page_type = $("#current_page_type").val();

        if (page_type == "admin")
            ajax_url = base_url + '/admin/products/';
        else
            ajax_url = base_url + '/seller/products/';


        $("body").on("change", "#category_field_id", function() {
            let cat_id = $(this).val();
            if (cat_id != '')
            {
                data    = {}
                data.category_id    = cat_id;
                console.log(data);
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: ajax_url + 'seller-categories',
                    type: "POST",
                    data: {category_id    : cat_id},
                    dataType: 'json',
                    // processData: false,
                    // contentType: false,
                    // beforeSend  : function () {
                    //     $('.theme-loader1').show();
                    // },
                    success: function(result)
                    {
                        if (result['status'] == true)
                        {
                            option_html = '<option value="">Please Select Seller</option>';

                            $.each(result['data'].seller_list, function(ex, seller)
                            {
                                option_html +='<option value="'+seller.id+'">'+seller.seller_full_name_buss+'</option>';
                            });

                            $("#seller_based_on_cat").html(option_html);
                            $('#seller_category_id').show();
                        } else {
                            $('#seller_category_id').hide();
                            iziToast.error({
                                title: 'Error',
                                message: result['message'],
                                position: 'topRight',
                            });
                        }
                    }
                });
            } else {
                $('#seller_category_id').hide();
                attr_html   = '<div class="col-md-12 col-sm-12">';
                attr_html   = '     <p class="text-center">No Options Available. You Can Continue to Add Stocks</p>';
                attr_html   = '     <input type="hidden" name="product_attr[]" value="">';
                attr_html   = '</div>';
                $("#option_dynamic_values").html(attr_html);
            }
        });

    });
</script>
@endsection
