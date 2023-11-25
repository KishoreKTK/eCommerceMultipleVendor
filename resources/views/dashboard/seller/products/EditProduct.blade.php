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
              <li class="breadcrumb-item"><a href="{{ Route('seller.home') }}">Dashboard</a></li>
              <li class="breadcrumb-item"><a href="{{ route('seller.ProductList') }}">Product Lists</a></li>
              <li class="breadcrumb-item active" aria-current="page">Edit Product</li>
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
                    <form class="form-sample mt-3" id="product_form_data" action="{{ route('seller.UpdateProduct') }}" method="POST" autocomplete="off" enctype="multipart/form-data">
                        @csrf
                        <section id="basic_product_details_section">
                            <p class="card-description mt-3"> Basic Details</p>
                            <hr size="3">

                            <input type="hidden" name="product_id" value="{{ $product_det->id }}">

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="exampleFormControlInput1">Name</label>
                                        <input type="text" name="name" value="{{ $product_det->name }}" class="form-control"  id="exampleFormControlInput1"attributes>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="exampleFormControlSelect1">Select Category</label>
                                        <select class="form-control" name="category_id" id="category_field_id">
                                            <option value="">Please Select Category</option>
                                            @foreach ($categorylist as $category)
                                            <option value="{{ $category->id }}" @if($product_det->category_id == $category->id)
                                                selected
                                            @endif>{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="exampleFormControlTextarea1">Short Bio (in 30 Letters)</label>
                                        <input type="text" name="short_bio" value="{{ $product_det->short_bio }}" class="form-control"  id="exampleFormControlInput12" attributes>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="exampleFormControlInput531">Processing time (In Days)</label>
                                        <input type="number" class="form-control" name="processing_time" min="0" max="30" value="{{ $product_det->processing_time }}" required id="exampleFormControlInput531"attributes>
                                        <small class="form-text text-muted">If Out of Stock Please Enter the Time Limit Needed Restock this Product</small>
                                    </div>
                                </div>


                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="my-input">Product Display Image</label>
                                        <input id="my-input" name="image" class="form-control" type="file" accept="image/*">
                                        <small class="form-text text-muted">Upload Only JPG | JPEG. Max allowed size is 2MB</small>
                                    </div>
                                    @if(!@empty($product_det->image))
                                    <img src="{{ $product_det->image }}" alt="Display Image" height="150px">
                                    @endif
                                </div>


                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="exampleFormControlTextarea1">Description</label>
                                        <textarea class="form-control" id="exampleFormControlTextarea1" name="description" required rows="3">{{ $product_det->description }}</textarea>
                                    </div>
                                </div>


                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <button type="submit" id="update_profile_btn" class="float-right btn btn-gradient-primary mr-2 submit_products_form">Submit</button>
                                </div>
                            </div>

                        </section>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection

@section('pagescript')

{{-- <script src="{{ asset('assets/js/product_form.js') }}"></script> --}}

<script>
    $(document).ready(function()
    {
        // $('#seller_category_id').hide();

        var base_url = window.location.origin;
        ajax_url = base_url + '/admin/products/';

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
                            $("#update_profile_btn").show();

                        } else {
                            $('#seller_category_id').hide();
                            $("#update_profile_btn").hide();
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
