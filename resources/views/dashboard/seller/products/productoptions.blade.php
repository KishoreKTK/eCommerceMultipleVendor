@extends('layouts.dashboard_layout')
@section('pagecss')


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
              <li class="breadcrumb-item" aria-current="page"><a href="{{ Route('seller.ProductList') }}">Product Lists</a></li>
              <li class="breadcrumb-item active" aria-current="page">Product Options</li>
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
        <div class="col-lg-5 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-column align-items-center text-center">
                        <img src="{{ asset($product_det->image) }}" alt="Seller"
                            class="img-thumb p-1 bg-secondary" width="250">
                        <div class="mt-3">
                            <h4>{{ $product_det->name }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-7 grid-margin stretch-card">
            <div class="card">
                <div class="d-flex justify-content-start border bg-light">
                    <ul class="nav nav-pills nav-fill">
                        <li class="nav-item">
                          <a class="nav-link" href="{{ Route('seller.ProductSpecificationPage' ,[$product_det->id]) }}">Specifications</a>
                        </li>
                        <li class="nav-item">
                          <a class="nav-link active" href="#">Options</a>
                        </li>
                        <li class="nav-item">
                          <a class="nav-link" href="{{ Route('seller.ProductStocksPage', [$product_det->id]) }}">Stocks</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body" id="show_table_list">
                    <div class="d-flex mb-4 justify-content-between mb-3">
                        <h5 class="card-title mb-2">{{ $product_det->name }}</h5>
                    </div>

                    @if(count($attributelist) != count($product_attributes))
                    <h6 class="mt-2">Product Options Form</h6>

                    <div class="alert alert-warning" role="alert">
                        <strong>Note:</strong>
                        <small>for Options with Multiple values please comma and add multiple values.
                        Ex: if the product has multiple colors then add color option as red, blue, black</small>
                    </div>
                    <div class="border border-2 border-primary m-2 p-2 bg-white rounded ">
                        <div class="row">
                            <div class="col-12">
                                <form action="{{ Route("seller.AddProductOptions") }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product_det->id }}">
                                    <div class="row">
                                        @if(count($attributelist) > 0)
                                            @foreach ($attributelist as $key=>$attribute)
                                                @if (!in_array($attribute['id'], $selected_options))
                                                    <div class="col-md-6 col-sm-12">
                                                        @if(count($attribute['custom_attr']) > 0)
                                                            @foreach ($attribute['custom_attr'] as $e=>$item)
                                                            <div class="form-group">
                                                                <label for="">{{ $attribute['name'] }}</label>
                                                                <input type="hidden" name="product_attr[{{ $key }}][sub_attr_id]" value="{{ $item->id }}">
                                                                <input type="hidden" name="product_attr[{{ $key }}][attribute_id]" value="{{ $item->attr_id }}">
                                                                <input type="text" name="product_attr[{{ $key }}][custom_values]" class="form-control form_attr_fields" value="" placeholder="" aria-describedby="helpId_{{ $item->id }}">
                                                                <small id="helpId_{{ $item->id }}" class="form-text text-muted">Hint:{{ $item->summary }} </small>
                                                            </div>
                                                            @endforeach
                                                        @endif
                                                    </div>
                                                @endif
                                            @endforeach

                                            <div class="col-sm-12 col-md-12 d-flex justify-content-end">
                                                <button type="submit" class="btn btn-inverse-primary btn-sm">Submit</button>
                                            </div>
                                        @else
                                            <div class="col-md-12 col-sm-12">
                                                <p class="text-center">No Options Available. Please Add Options to Continue</p>
                                            </div>
                                        @endif
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endif

                    <h6 class="mt-4">Options List</h6>
                    <div class="table-responsive">
                        <table class="table ">
                            <thead>
                                <tr>
                                    <th>sno</th>
                                    <th>Option Name</th>
                                    <th>Options Values</th>
                                    <th class="col-sm-2">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($product_attributes as $e=>$pa)
                                <tr>
                                    <td scope="row">{{ $e+1 }}</td>
                                    <td>{{ $pa->attrname }}</td>
                                    @if($pa->custom_values == null)
                                    <td>{{ $pa->sub_attr_name }}</td>
                                    @else
                                    <td>{{ $pa->custom_values }}</td>
                                    @endif
                                    <td>
                                        <div class="d-flex justify-content-end">
                                            <button class="btn btn-outline-warning btn-sm update_product_options"
                                                data-toggle="tooltip"  data-title="Update Options"
                                                data-attribute_id="{{ $pa->attribute_id }}"
                                                data-attrname="{{ $pa->attrname }}"
                                                data-sub_attr_ids="{{ $pa->sub_attr_ids }}"
                                                data-custom_values="{{ $pa->custom_values }}"
                                                data-product_id="{{ $product_det->id }}">
                                                <i class="mdi mdi-pencil" aria-hidden="true"></i>
                                            </button>

                                            <form action="{{  Route('seller.DeleteProductOption') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="product_id" value="{{ $product_det->id }}">
                                                <input type="hidden" name="sub_attr_id" value="{{ $pa->sub_attr_ids }}">
                                                <button type="submit" class="btn btn-outline-danger ml-1 btn-sm"
                                                data-toggle="tooltip" data-placement="top" title="Delete Product">
                                                    <i class="mdi mdi-delete" aria-hidden="true"></i>
                                                </button>
                                            </form>

                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card-body" id="UpdateAttributeID">
                    <div class="d-flex mb-4 justify-content-between mb-3">
                        <h5 class="card-title mb-2">{{ $product_det->name }}</h5>
                    </div>
                    <div class="mt-2 d-flex justify-content-between">
                        <h4>Update Product Option</h4>
                        <button class="btn btn-sm btn-inverse-danger"id="back_to_option_list">Back</button>
                    </div>
                    <hr>
                    <form action="{{ Route('seller.UpdateProductOption') }}" method="post">
                        @csrf
                        <p id="update_attr_name"></p>
                        <div class="update_options">

                        </div>
                        <input type="hidden" id="attribute_id" name="attribute_id" value="">
                        <input type="hidden" id="update_sub_attr_id" name="sub_attr_id" value="">
                        <input type="hidden" id="option_product_id" name="product_id" value="">
                        <button class="btn mt-3 btn-inverse-primary float-right">Update</button>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- <div class="modal"  id="deleteproductoption" tabindex="-1" aria-labelledby="deleteproductoption"  tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Delete Option</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <p>Are you sure you want to delete this Option?</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger">Delete</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
        </div>
      </div>
    </div>
</div> --}}
@endsection

@section('pagescript')
<script>
$(document).ready(function(){

    $("#custom_value_submit_form").hide();
    $("#UpdateAttributeID").hide();

    $(".my_select_box").select2({placeholder: "Select a Option"});

    // Tabs Change
    $('#prodict-detail-tabs a').on('click', function (e) {
        e.preventDefault()
        $(this).tab('show')
    })

    $("body").on("click",".update_product_options",function(){
        attribute_id    =   $(this).attr("data-attribute_id");
        attrname        =   $(this).attr("data-attrname");
        sub_attr_ids    =   $(this).attr("data-sub_attr_ids");
        custom_values   =   $(this).attr('data-custom_values');
        product_id      =   $(this).attr("data-product_id");


        $("#update_attr_name").html(attrname);
        $("#attribute_id").val(attribute_id);
        $("#update_sub_attr_id").val(sub_attr_ids);
        $("#option_product_id").val(product_id);

        var custom_val_arr = custom_values.split(',');
        fieldHTML   = '';
        $.each(custom_val_arr, function(e,x){
            if(e == 0){
                fieldHTML   +='<div class="input-group option_field_row mt-1">'
                            +'  <input type="text" class="form-control" name="custom_values[]" value="'+x+'" placeholder="Product Option" >'
                            +'      <button class="btn btn-sm btn-success add_options" type="button">'
                            +'          <i class="mdi mdi-plus"></i>'
                            +'      </button>'
                            +' </div>';
            } else {
                fieldHTML   +='<div class="input-group option_field_row mt-1">'
                            +'  <input type="text" class="form-control" name="custom_values[]" value="'+x+'" placeholder="Product Option" >'
                            +'      <button class="btn btn-sm btn-danger remove_options" type="button">'
                            +'          <i class="mdi mdi-minus"></i>'
                            +'      </button>'
                            +' </div>';
            }
        });
        $(".update_options").html(fieldHTML);
        $("#show_table_list").hide();
        $("#UpdateAttributeID").show();
    });

    $("#back_to_option_list").on("click", function(){
        $("#show_table_list").show();
        $("#UpdateAttributeID").hide();
    });


    $("body").on("click",".add_options", function(){
        fieldHTML =      '<div class="input-group option_field_row mt-1">'
                        +'  <input type="text" class="form-control" name="custom_values[]" placeholder="Product Option" >'
                        +'      <button class="btn btn-sm btn-danger remove_options" type="button">'
                        +'          <i class="mdi mdi-minus"></i>'
                        +'      </button>'
                        +'</div>';
        $('.update_options').append(fieldHTML); //Add field html
    });

    $('.update_options').on('click', '.remove_options', function(e){
        e.preventDefault();
        $(this).parent('.option_field_row').remove(); //Remove field html
    });
});
</script>
@endsection
