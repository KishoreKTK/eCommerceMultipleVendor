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
              <li class="breadcrumb-item active" aria-current="page">Product Attributes</li>
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
                <div class="card-body" id="show_table_list">
                    <div class="d-flex mb-4 justify-content-between mb-3">
                        <h5 class="card-title mb-2">{{ $product_det->name }}</h5>
                    </div>
                    <h6 class="mt-2">Product Options Form</h6>
                    <div class="border border-2 border-primary m-2 p-2 bg-white rounded ">
                        <div class="row">
                            <div class="col-12">
                                <form action="{{ Route("seller.AddProductOptions") }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product_det->id }}">
                                    <div class="row">
                                        @if(count($attributelist) > 0)
                                            @foreach ($attributelist as $key=>$attribute)
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
                                                    @if (count($attribute['sub_attr']) > 0)
                                                        <div class="form-group">
                                                            <label for="">{{ $attribute['name'] }}</label>
                                                            {{-- <input type="hidden" name="product_attr[sub_attr][{{ $key }}][sub_attribute_id]" value="{{ $item->id }}"> --}}
                                                            <input type="hidden" name="product_attr[{{ $key }}][attribute_id]" value="{{  $attribute['id'] }}">
                                                            <select class="form-control my_select_box" name="product_attr[{{ $key }}][sub_attr_id][]" multiple>
                                                                @foreach ($attribute['sub_attr']  as $key=>$sub_attr)
                                                                <option value="{{ $sub_attr->id }}">{{ Str::upper($sub_attr->sub_attr_name) }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    @endif
                                                </div>
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
                    <h6 class="mt-4">Options List</h6>
                    <div class="table-responsive">
                        <table class="table ">
                            <thead>
                                <tr>
                                    <th>sno</th>
                                    <th>Option Name</th>
                                    <th>Options Values</th>
                                    {{-- <th class="text-right">Action</th> --}}
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
                                    {{-- <td>
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

                                            <form action="{{  Route('seller.DeleteCustomStock') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="product_entry_id" value="attribute_id">
                                                <button type="submit" class="btn btn-outline-danger ml-1 btn-sm"
                                                    data-toggle="tooltip" data-title="Delete Option">
                                                    <i class="mdi mdi-delete"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td> --}}
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- <div class="card-body" id="update_option_value">

                    <div class="d-flex justify-content-between">
                        <h6>Update Option</h6>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="back_to_stock_list">Back</button>
                    </div>
                    <div class="border border-2 border-primary m-2 p-4 bg-white rounded ">
                        <form action="{{ Route("seller.AddProductOptions") }}" method="POST">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product_det->id }}">
                            <div class="form-group">
                                <label for="">{{ $attribute['name'] }}</label>
                                <input type="hidden" name="product_attr[{{ $key }}][attribute_id]" value="{{  $attribute['id'] }}">
                                <select class="form-control my_select_box" name="product_attr[{{ $key }}][sub_attr_id][]" multiple>
                                    @foreach ($attribute['sub_attr']  as $key=>$sub_attr)
                                    <option value="{{ $sub_attr->id }}">{{ Str::upper($sub_attr->sub_attr_name) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-inverse-primary btn-sm">Update</button>
                        </form>
                    </div>

                </div> --}}

            </div>
        </div>
    </div>
</div>
@endsection

@section('pagescript')
<script>
$(document).ready(function(){

    $("#custom_value_submit_form").hide();
    $("#update_stock_list_form").hide();

    $(".my_select_box").select2({placeholder: "Select a Option"});

    // Tabs Change
    $('#prodict-detail-tabs a').on('click', function (e) {
        e.preventDefault()
        $(this).tab('show')
    })

    $("body").on("click",".update_custom_stock",function(){
        entry_id    =   $(this).attr("data-entry_id");
        qty         =   $(this).attr("data-qty");
        price       =   $(this).attr("data-product_price");
        attr_name   =   $(this).attr('data-attrname');
        min_order_qty   =   $(this).attr('data-min_order_qty');
        price_type = $(this).attr("data-price_type");
        // product_id = $(this).attr("data-prdt_id");
        console.log(price_type);
        $("#product_entry_id").val(entry_id);
        $("#update_entry_qty").val(qty);
        $("#update_min_order_qty").val(min_order_qty);
        $("#update_price").val(price);
        $("#product_main_type").val(price_type);
        // $("#product_main_id").attr(product_id);
        attr_html = '<div>';
        if(price_type == '1'){
            attr_html += '<p><span class="badge badge-outline-success">'+attr_name+'</span></p>';
        } else {
            var jsArr = JSON.parse(attr_name);
            $.each(jsArr, function(e,x){
                attr_html += '<p><span class="badge badge-outline-success mx-2">'+e+'</span>';
                attr_html += '<span class="badge badge-outline-secondary">'+x+'</span></p>';
                attr_html +='';
            });
        }
        attr_html += '</div>';
        $("#UpdateStockTypeId").html(attr_html);
        $("#stock_list_table").hide();
        $("#update_stock_list_form").show();
    });

    $("#back_to_stock_list").on("click", function(){
        $("#stock_list_table").show();
        $("#update_stock_list_form").hide();
    });
});
</script>
@endsection
