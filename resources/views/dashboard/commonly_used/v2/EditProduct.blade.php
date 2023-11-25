@extends('layouts.dashboard_layout')
@php
    $routeName = \Request::route()->getName();
    if (strpos($routeName, 'admin.') === 0) {
    $page_type = 'admin';
    } else {
    $page_type = 'seller';
    }
@endphp
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
              <li class="breadcrumb-item"><a href="{{ Route(''.$page_type.'.home') }}">Dashboard</a></li>
              <li class="breadcrumb-item"><a href="{{ Route(''.$page_type.'.ProductList') }}">Product Lists</a></li>
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

    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    {{-- <h4 class="card-title">Add New Product</h4> --}}
                    <form class="form-sample mt-3" id="product_form_data" action="{{ Route(''.$page_type.'.UpdateProduct') }}" method="POST" autocomplete="off" enctype="multipart/form-data">
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
                                        <label for="exampleFormControlTextarea1">Description</label>
                                        <textarea class="form-control" id="exampleFormControlTextarea1" name="description" required rows="3">{{ $product_det->description }}</textarea>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="exampleFormControlInput531">Processing time (In Days)</label>
                                        <input type="number" class="form-control" name="processing_time" min="0" max="30" value="{{ $product_det->processing_time }}" required id="exampleFormControlInput531"attributes>
                                        <small class="form-text text-muted">If Out of Stock Please Enter the Time Limit Needed Restock this Product</small>
                                    </div>
                                </div>

                                <div class="col-md-6" id="seller_category_id">
                                    <div class="form-group">
                                        <label for="seller_based_on_cat">Select Seller</label>
                                        <select class="form-control" name="seller_id" id="seller_based_on_cat" required>
                                            <option value="">Please Select Seller</option>
                                            @foreach ($sellerlist as $seller)
                                            <option value="{{ $seller->id }}" @if($product_det->seller_id == $seller->id)
                                                selected
                                            @endif>{{ $seller->seller_full_name_buss }}</option>
                                            @endforeach
                                        </select>
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

    <div class="row">
        <div class="col-12">
            <div class="card p-5">
                {{-- Product Specification --}}
                <div class="d-flex justify-content-between">
                    <h4>Product Specifications</h4>
                </div>
                <hr>
                <div class="border border-2 border-success p-2 bg-white rounded " id="spec_table_add_div">
                    <div class="row">
                        <div class="col-12">
                            <form action="{{ Route(''.$page_type.'.AddProductSpec') }}" method="POST">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product_det->id }}">
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                    <label for="inputEmail4">Specification</label>
                                    <input type="text" class="form-control" name="specification"    id="inputEmail4" placeholder="Specification">
                                    </div>
                                    <div class="form-group col-md-6">
                                    <label for="inputPassword4">Value</label>
                                    <input type="text" class="form-control" name="value" value="" id="inputPassword4" placeholder="Value">
                                    </div>
                                </div>
                                <div class="form-row float-right">
                                    <button type="submit" class="btn btn-inverse-success btn-sm">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="table-responsive" id="spec_table_list_div">
                    <h6 class="mt-4">Specifications</h6>
                    <table class="table ">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Specifications</th>
                                <th>Values</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($product_specifications as $key=>$spec)
                            <tr>
                                <th>{{ $key+1 }}</th>
                                <th>{{ $spec->specification }}</th>
                                <td>{{ $spec->value }}</td>
                                <td>
                                    <div class="d-flex justify-content-center">
                                        <button type="button" class="btn btn-inverse-warning btn-sm update_custom_stock"
                                            data-toggle="tooltip"  title="Update Specification"
                                            data-spec_id="{{ $spec->id }}"
                                            data-specname="{{ $spec->specification }}"
                                            data-specvalue="{{ $spec->value }}">
                                            <i class="mdi mdi-pencil"></i>
                                        </button>
                                        <form action="{{  Route(''.$page_type.'.DeleteProductSpec') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="spec_id" value="{{ $spec->id }}">
                                            <button type="submit" class="btn btn-outline-danger ml-1 btn-sm"
                                                data-toggle="tooltip" title="Delete Specification">
                                                <i class="mdi mdi-delete"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="border border-2 mt-4 border-warning p-2 bg-white rounded " id="update_stock_list_form">
                    <div class="row" >
                        <div class="col-12">
                            <div class="d-flex justify-content-between my-2">
                                <h6 >Update Specification</h6>
                                <button type="button" class="btn btn-sm btn-outline-danger" id="back_to_stock_list">Back</button>
                            </div>
                            <form action="{{ Route(''.$page_type.'.UpdateProductSpec') }}" method="POST">
                                @csrf
                                <input type="hidden" name="spec_id" id="hidden_spec_id" value="">
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                    <label for="spec_name_field">Specification</label>
                                    <input type="text" class="form-control" name="specification" value="" id="spec_name_field" placeholder="Specification">
                                    </div>
                                    <div class="form-group col-md-6">
                                    <label for="spec_value_field">Value</label>
                                    <input type="text" class="form-control" name="value" value="" id="spec_value_field" placeholder="Value">
                                    </div>
                                </div>
                                <div class="form-row float-right">
                                    <button type="submit" class="btn btn-inverse-warning btn-sm">Update</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 mt-3">
            <div class="card p-2">
                <div class="d-flex justify-content-between">
                    <h4>Additional Images</h4> 
                    {{-- <button class="btn btn-success btn-sm float-right"> Add Images </button> --}}
                </div>
                <hr>
                @if(count($product_images) > 0)
                    <div class="row row-cols-1 row-cols-md-3 g-4">
                        @php
                            $count = 0;
                            $image_count = count($product_images);
                            $total_imgs_needed = 5 - $image_count;
                        // dd($total_imgs_needed);
                        @endphp
                        @foreach ($product_images as $e=>$img)
                            <div class="col-md-2 ">
                                <img class="card-img-top" src="{{ $img->image_urls }}" alt="Card image cap" height="250" width="250">
                                <form action="{{ Route(''.$page_type.'.UpdateProductImage') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="img_type" value="old">
                                    <input type="hidden" name="img_id" value="{{ $img->id }}">
                                    <input type="hidden" name="product_id" value="{{ $img->product_id }}">
                                    <div class="form-group">
                                        <input id="my-input" name="image" class="form-control" type="file" accept="image/*">
                                        <small class="form-text text-muted">Upload Only JPG | JPEG. Max allowed size is 2MB</small>
                                    </div>
                                    <button type="submit" class="btn btn-sm text-center btn-outline-primary">Update</button>
                                </form>
                                <form action="{{ Route(''.$page_type.'.DeleteProductImage') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="img_id" value="{{ $img->id }}">
                                    <button type="submit" class="btn btn-sm btn-outline-danger text-center my-2">Delete</button>
                                </form>
                                
                            </div> 
                        @endforeach
                        @if($total_imgs_needed != 0)
                            @for ($cnt = $image_count; $cnt < 5 ; $cnt++)
                            <div class="col-md-2 pt-5">
                                <form action="{{ Route(''.$page_type.'.UpdateProductImage') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="img_type" value="new">
                                    <input type="hidden" name="product_id" value="{{ $img->product_id }}">
                                    <div class="form-group">
                                        <input id="my-input" name="image" required class="form-control" type="file" accept="image/*">
                                        <small class="form-text text-muted">Upload Only JPG | JPEG. Max allowed size is 2MB</small>
                                    </div>
                                    <button type="submit" class="btn btn-outline-primary">Update</button>
                                </form>
                            </div>
                            @endfor
                        @endif
                    </div>
                @endif
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
        $("#update_stock_list_form").hide();
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

        $("body").on("click",".update_custom_stock",function(){
            specid    =   $(this).attr("data-spec_id");
            name         =   $(this).attr("data-specname");
            specval       =   $(this).attr("data-specvalue");

            $("#hidden_spec_id").val(specid);
            $("#spec_name_field").val(name);
            $("#spec_value_field").val(specval);

            $("#spec_table_list_div").hide();
            $("#spec_table_add_div").hide();
            $("#update_stock_list_form").show();
        });

        $("#back_to_stock_list").on("click", function(){
            $("#spec_table_list_div").show();
            $("#spec_table_add_div").show();
            $("#update_stock_list_form").hide();
        });
    });
</script>
@endsection
