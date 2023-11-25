@extends('layouts.dashboard_layout')
@php
    $routeName = \Request::route()->getName();
    if (strpos($routeName, 'admin.') === 0) {
    $page_type = 'admin';
    } else {
    $page_type = 'seller';
    }
@endphp

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
                <li class="breadcrumb-item"><a href="{{  Route(''.$page_type.'.home') }}">Dashboard</a></li>
                <li class="breadcrumb-item" aria-current="page"><a href="{{  Route(''.$page_type.'.ProductList') }}">Product Lists</a></li>
                <li class="breadcrumb-item active" aria-current="page">Product Custom Stocks</li>
            </ol>
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
     

        <div class="col-lg-8 grid-margin">
            {{-- Options --}}
            <div class="card">
                <div class="p-2 d-flex justify-content-between">
                    <h4>Product Options</h4>
                    @if(count($selected_options) != 0)
                    @if(count($selected_options) < count($attributelist))
                    <a href="#" class="btn btn-sm btn-inverse-primary" data-toggle="modal" data-target="#addOptionsModel">Add Options</a>
                    @endif
                    @endif
                </div>
                @if(count($selected_options) > 0)
                    <ul class="list-group p-2">
                        @foreach ($product_attributes as $e=>$pa)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <p>{{ ucfirst($pa->attrname) }}</p>
                                <p>
                                    @php
                                        $values = explode(',',$pa->custom_values);
                                    @endphp 
                                    @foreach ($values as $v)
                                    <span class="badge rounded-pill bg-secondary">{{ $v }}</span>    
                                    @endforeach
                                </p>
                            </div>
                            <div class="d-flex justify-content-start">
                                <button class="btn btn-outline-warning btn-sm update_product_options mx-2"
                                    data-toggle="tooltip"  data-title="Update Options"
                                    data-attribute_id="{{ $pa->attribute_id }}"
                                    data-attrname="{{ $pa->attrname }}"
                                    data-sub_attr_ids="{{ $pa->sub_attr_ids }}"
                                    data-custom_values="{{ $pa->custom_values }}"
                                    data-product_id="{{ $product_det->id }}">
                                    <i class="mdi mdi-pencil" aria-hidden="true"></i>
                                </button>
                                <button type="button" class="btn btn-outline-danger ml-1 btn-sm delete-option"
                                    data-toggle="tooltip" data-product_id="{{ $product_det->id }}" data-sub_attr_id="{{ $pa->sub_attr_ids }}" 
                                    data-placement="top" title="Delete Option"><i class="mdi mdi-delete" aria-hidden="true"></i>
                                </button>
                            </div>           
                        </li>
                        @endforeach
                     
                    </ul>
                @else 
                    <div class="alert alert-light m-5 p-5 text-center" role="alert">
                        {{-- <strong class="">Please add option for your product </strong> --}}
                        <a href="#" class="btn btn-primary" data-toggle="modal" data-target="#addOptionsModel">Add Options</a>

                    </div>
                @endif
            </div>

            <hr class="my-2" size="3">

            {{-- Custom Stocks --}}
            <div class="card">
                <div class="border border-2 border-secondary m-2 p-2 bg-white rounded " id="stock_list_table mb-3">
                    <div class="d-flex justify-content-between mb-2">
                        <h4 class="my-2">Stock Lists</h4>
                        <strong class="text-warning my-2">
                            Stocks Left &nbsp; <span class="badge bg-info text-white stocks_left_id"></span>
                        </strong>
                        {{-- <div class="flex-grow-1">
                            <h4 >Stock Lists</h4>
                            <strong class="text-warning float-right">
                                Stocks Left &nbsp; <span class="badge bg-info text-white stocks_left_id"></span>
                            </strong>
                        </div> --}}
                    </div>
                    <div class="table-responsive p-2">
                        <table class="table table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th>Price Type</th>
                                    <th>Min. Order</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th>Update</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(count($stock_list) > 0)
                                    @foreach ($stock_list as $stock)
                                        @if($stock->price_type != '1')
                                        <tr>
                                            <form action="{{  Route(''.$page_type.'.UpdateStockInfo') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="product_id" value="{{ $product_det->id }}">
                                                <input type="hidden" name="product_type" value="2">
                                                <input type="hidden" name="product_entry_id" value="{{ $stock->product_entry }}">
                                                <td>
                                                    @php
                                                        $attr_id_arr    = explode(',',$stock->attribute_ids);
                                                        $attr_name_arr  = explode(',',$stock->attr_names);
                                                        $sub_id_arr     = explode(',',$stock->sub_attr_ids);
                                                        $sub_name_arr   = explode(',',$stock->sub_attr_names);
                                                        $attr_custom_arr= explode(',',$stock->custom);
                                                        $custom_val_arr = explode(',',$stock->custom_values);
                                                        $attr_det_arr   = [];

                                                        foreach ($attr_id_arr as $key => $attr_id) {
                                                            $attr_det_arr[$key]['attr_id']      = $attr_id;
                                                            $attr_det_arr[$key]['attr_name']    = $attr_name_arr[$key];
                                                            $attr_det_arr[$key]['sub_attr_id']  = $sub_id_arr[$key];
                                                            $attr_det_arr[$key]['sub_attr_name']= $sub_name_arr[$key];
                                                            $attr_det_arr[$key]['custom']       = $attr_custom_arr[$key];
                                                            $attr_det_arr[$key]['custom_values']= $custom_val_arr[$key];
                                                        }
                                                        $attribute_name = [];
                                                    @endphp
                                                    @foreach ($attr_det_arr as $val)
                                                        <p>
                                                            @php
                                                                if($val['custom'] == '1') {
                                                                    $cust_name  =    $val['custom_values'];
                                                                } else {
                                                                    $cust_name  =   strtoUpper($val['sub_attr_name']);
                                                                }
                                                                $attr_name = $val['attr_name'];
                                                                $attribute_name[$attr_name] =    $cust_name;
                                                            @endphp
                                                            <span class="badge badge-outline-success">{{ $val['attr_name'] }}</span>
                                                            @if($val['custom'] == '1')
                                                            <span class="badge badge-outline-secondary">{{ $val['custom_values'] }}</span>
                                                            @else
                                                            <span class="badge badge-outline-secondary">{{ strtoUpper($val['sub_attr_name']) }}</span>
                                                            @endif
                                                        </p>
                                                    @endforeach
                                                </td>
                                                <td><input type="text" name="min_order_qty" class="form-control min_order_qty" value="{{ $stock->min_order_qty }}" required></td>
                                                <td><input type="text" name="quantities" class="form-control assigned_qty" value="{{ $stock->quantities }}" required></td>
                                                <td><input type="text" name="product_price" class="form-control product_price" value="{{ $stock->product_price }}" required></td>
                                                <td>
                                                    <div class="d-flex justify-content-center">
                                                        <button type="submit" class="btn btn-inverse-warning btn-sm">
                                                            <i class="mdi mdi-check-circle"></i> Update  
                                                        </button>
                                                        <button type="button" class="btn btn-outline-danger ml-1 btn-sm delete_stock"
                                                            data-entry_id="{{ $stock->product_entry }}" data-toggle="tooltip" data-title="Delete Stock">
                                                            <i class="mdi mdi-delete"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </form>
                                        </tr>
                                        @endif
                                    @endforeach
                                @else
                                    <tr class="text-center">
                                        <td colspan="5">No Stocks Added Yet</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <hr size="3" class="my-2">
            {{-- Task to Assign --}}
            <div class="card">
                {{-- Un Assigned Stocks --}}
                <div class="border border-2 border-secondary m-2 p-2 bg-white rounded ">
                    <div class="d-flex justify-content-between mb-2">
                        <h4 class="my-2">Combinations to Assign</h4>
                        <strong class="text-warning my-2">
                            Stocks Left &nbsp; <span class="badge bg-info text-white stocks_left_id"></span>
                        </strong>
                    </div>

                    <form action="{{  Route(''.$page_type.'.AddProductStock') }}" method="post" autocomplete="off">
                        <input type="hidden" id="total_qty_id" value="{{ $product_det->total_qty }}">
                        <input type="hidden" id="qty_left_id" value="">
                        @csrf
                      
                    
                        <div class="row">
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="table"> 
                                        <thead class="thead-light">
                                            <tr>
                                                <th>#</th>
                                                <th>Combo </th>
                                                <th>Price</th>
                                                <th>Min Order</th>
                                                <th>Qty</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(count($combinations) > 0)
                                                @foreach ($combinations as $key=>$combo)
                                                    <input type="hidden" name="price_combo[{{ $key }}][product_id]" value="{{ $product_det->id }}">
                                                    <tr id="price_combo_row_{{ $key }}">
                                                        @php
                                                            $opt_str    = implode(",",$option_ids);
                                                            if(count($option_ids) > 1){
                                                                $combo_str  = implode(" / ",$combo);
                                                            } else {
                                                                $combo_str  = $combo;
                                                            }
                                                        @endphp
                                                        <td class="select_service_class">
                                                            <input type="checkbox" name="price_combo[{{ $key }}][option_ids]" value="{{ $opt_str }}"/>
                                                        </td>
                                                        <td>
                                                            {{ $combo_str }}
                                                        </td>
                                                        <input type="hidden" name="price_combo[{{ $key }}][combo]" value="{{ $combo_str }}">
                                                        <td>
                                                            <input class="form-control mobile-nub" type="text" name="price_combo[{{ $key }}][price]" required disabled value="{{ $product_det->starndard_price }}" min="{{ $product_det->starndard_price }}">
                                                        </td>

                                                        <td>
                                                            <input class="form-control mobile-nub" type="text" name="price_combo[{{ $key }}][min_order]" required disabled value="{{ $product_det->min_order_qty }}" min="1" max="{{ $product_det->total_qty }}">
                                                        </td>

                                                        <td>
                                                            <input class="form-control mobile-nub sub_prd_avail_qty" type="text" name="price_combo[{{ $key }}][avail_qty]" value="" required disabled  min="{{ $product_det->min_order_qty }}" max="{{ $product_det->total_qty }}">
                                                        </td>

                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="5" class="text-center">No Combinations Found</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-center">
                            <button class="btn btn-inverse-success btn-sm float-right my-2" id="custom_value_submit_form" type="submit">Add New Stocks</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    {{-- Product Details --}}
                    <div class="d-flex mb-2 justify-content-start">
                        <img src="{{ asset($product_det->image) }}" alt="admin"
                        class="img-thumb p-1 bg-secondary" width="100">
                        <div class="ml-2">
                            <h4>{{ $product_det->name }}</h4>
                            <p>
                                @if($page_type == 'admin')
                                <a href="{{ Route(''.$page_type.'.SellerDetail',[$product_det->seller_id])}}" target="_blank" class="badge bg-info text-white">{{ $product_det->sellername }}</a>
                                @endif
                                <span class="badge bg-dark text-white">{{ $product_det->categoryname }}</span>
                            </p>
                        </div>
                    </div>

                    <hr size="3" class="bg-dark">

                    {{-- Standard  Price Update Form --}}
                    <div class="alert alert-dark" role="alert">
                        <form action="{{  Route(''.$page_type.'.UpdateStandardPrice') }}" method="POST">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product_det->id }}">
                            <h4 class="alert-heading">Standary Product Price</h4>  
                            <hr>
                            <div class="mb-3">
                                <label for="" class="form-label">Minimum Price</label>
                                <input type="text" name="product_price" id="" value="{{ $product_det->starndard_price }}" class="form-control" placeholder="" aria-describedby="helpId">
                                <small id="helpId" class="text-muted">Minimum Standard Price</small>
                            </div>

                            <div class="mb-3">
                                <label for="" class="form-label">Total Quantity</label>
                                <input type="text" name="quantities" id="" class="form-control" value="{{ $product_det->total_qty }}" placeholder="" aria-describedby="helpId">
                                <small id="helpId" class="text-muted">Total Stock</small>
                            </div>

                            <div class="mb-3">
                                <label for="" class="form-label">Minimum Order</label>
                                <input type="text" name="min_order_qty" id="" class="form-control" value="{{ $product_det->min_order_qty }}" placeholder="" aria-describedby="helpId">
                                <small id="helpId" class="text-muted">Minimum Stock</small>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">Update</button>
                        </form>
                    </div>

                    {{-- Notes --}}
                    <div class="alert alert-light" role="alert">
                        <h4 class="alert-heading">Note:</h4>
                        <hr>
                        <ul class="list-star">
                            <li>Adding | Updating | Deleting Options will reset combination and it's stocks</li>
                            <li>Updating standard price will reset combination and it's stocks</li>
                            <li>Please add atleast 1 option & 1 custom stock to view this product</li>
                        </ul>
                    </div>

                    <div class="text-center">
                        {{-- <a class="btn btn-inverse-primary btn-block my-1" href="Route(''.$page_type.'.CreateProduct')" role="button"> Add New Product </a>
                        <a class="btn btn-inverse-primary btn-block my-1" href="route(''.$page_type.'.productlist')" role="button"> List Product </a> --}}
                        @if (count($stock_list) > 0)
                        {{-- <a href="{{  Route(''.$page_type.'.ProductDetails',[$product_det->id]) }}"
                            class="btn btn-inverse-dark my-4 btn-block"  
                            data-toggle="tooltip" data-placement="top" title="View Product">
                            <i class="mdi mdi-eye" aria-hidden="true"></i> View Product
                        </a> --}}
                        <a href="{{  Route(''.$page_type.'.ProductDetails',[$product_det->id]) }}"
                            class="btn btn-gradient-info btn-icon-text"
                            data-toggle="tooltip" data-placement="top" title="View Product">
                            View <i class="mdi mdi-eye btn-icon-append"></i>
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Add Option --}}
<div class="modal"  id="addOptionsModel" tabindex="-1" aria-labelledby="addOptionsModel"  tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Product Option</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="card p-2">
                    <div id="init_select_options" class="text-center">
                        <p><strong>Select Options</strong></p>
                        <select class="select_multiple" id="select_avail_options" required multiple>
                            @foreach ($attributelist as $attr)
                                @if(!in_array($attr['id'], $selected_options))
                                    <option value="{{ $attr['id'] }}">{{ ucfirst($attr['name']) }}</option>
                                @endif
                            @endforeach
                        </select><br>
                        <small class="text-muted">you can choose multiple options</small>
                        <div class="my-3">
                            <button type="submit" class="btn btn-dark btn-sm show_values_div">Add Values</button>
                        </div>
                    </div>
                    <div id="add_values_to_selected" class="p-3">
                        <form action="{{  Route(''.$page_type.'.addoptionsv2') }}" id="addprdtopt_id" method="POST">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product_det->id }}">
                            <div  class="text-center">
                                <button class="btn btn-sm btn-inverse-secondary back_to_update_options" type="button">Back to Update Options</button>
                            </div>
                            <hr class="my-2" size="3">
                            <div class="selected_options">                            
                            </div>
                            <hr class="my-2" size="3">
                            <button type="submit" id="submit_add_opt_form" class="btn mt-2 btn-block btn-inverse-primary">Submit</button>
                        </form>   
                    </div>             
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Edit Option --}}
<div class="modal"  id="UpdateOptionModel" tabindex="-1" aria-labelledby="UpdateOptionModel"  tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Edit Option</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <div  class="card p-5">
                <form action="{{  Route(''.$page_type.'.UpdateProductOption') }}" method="post">
                    @csrf
                    <p id="update_attr_name"></p>
                    <div class="edit_options">

                    </div>
                    <input type="hidden" id="attribute_id" name="attribute_id" value="">
                    <input type="hidden" id="update_sub_attr_id" name="sub_attr_id" value="">
                    <input type="hidden" id="option_product_id" name="product_id" value="">
                    <button class="btn mt-3 btn-inverse-primary float-right">Update</button>
                </form>
            </div>
        </div>
        {{-- <div class="modal-footer">
          <button type="button" id="delete_the_option" class="btn btn-danger">Update</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        </div> --}}
      </div>
    </div>
</div>


{{-- Delete Option --}}
<div class="modal"  id="deleteproductoption" tabindex="-1" aria-labelledby="deleteproductoption"  tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Delete Option</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="card">   
            <div class="card-body">
                <p class="my-4">Are you sure you want to delete this Option?</p>
                <p class="mb-2 text-danger text-small">Deleting the product options will also delete the combination prices of the stocks.</p>

                <hr size="3" class="my-2">
                <form action="{{   Route(''.$page_type.'.DeleteProductOption') }}" method="POST">
                    @csrf
                    <input type="hidden" id="delete_product_id" name="product_id" value="">
                    <input type="hidden" id="delete_sub_attr_id" name="sub_attr_id" value="">
                    <div class="text-center my-2">
                        <button type="submit" class="btn btn-sm btn-danger" >Yes</button>
                        <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal" aria-label="Close">
                            No
                        </button>
                    </div>
                </form>
            </div> 
          </div>
        </div>
       
      </div>
    </div>
</div>

{{-- Delete Stock --}}
<div class="modal"  id="deleteproductstock" tabindex="-1" aria-labelledby="deleteproductstock"  tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Delete Stock</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="card">   
            <div class="card-body">
                <p class="my-4">Are you sure you want to delete this Stock?</p>
                <hr size="3" class="my-2">
                <form action="{{   Route(''.$page_type.'.DeleteCustomStock') }}" method="POST">
                    @csrf
                    <input type="hidden" id="delete_product_entry_id" name="product_entry_id" value="">
                    <div class="text-center my-2">
                        <button type="submit" class="btn btn-sm btn-danger" >Yes</button>
                        <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal" aria-label="Close">
                            No
                        </button>
                    </div>
                </form>
            </div> 
          </div>
        </div>
       
      </div>
    </div>
</div>

@endsection

@section('pagescript')
<script>
$(document).ready(function(){
    selectRefresh();
    $("#add_values_to_selected").hide();
    $("#custom_value_submit_form").hide();
    
    var assigned_qty = 0;
    $("input[class *= 'assigned_qty']").each(function(){
        assigned_qty += +$(this).val();
    });
    total_qty   =   $("#total_qty_id").val();
    qty_left    =   total_qty - assigned_qty;
    $("#qty_left_id").val(qty_left);
    $(".stocks_left_id").html(qty_left);

    function selectRefresh() {
        $(".select_multiple").select2({placeholder: "Select options"});
    }
    
    $("body").on("click",'.show_values_div',function(){
        var check_selected = $("#select_avail_options").val();
        if(check_selected.length > 0){
            $("#add_values_to_selected").show();
            $("#init_select_options").hide();
        } else {
            iziToast.error({
                title: 'Error',
                message: 'Please Select Options to Continue',
                position: 'topRight',
            });
        }
    });

    $("body").on("click",'.back_to_update_options',function(){
        $("#add_values_to_selected").hide();
        $("#init_select_options").show();
    });

    $("body").on("change","#select_avail_options",function(){
        selected_values =   $(this).val();
        var attr_html   =   '';
        $.each(selected_values, function(e,attr_id)
        {
            attr_name   =  $("#select_avail_options option[value="+attr_id+"]").text();
            attr_html   +='<div class="update_options_'+attr_id+'">'
                        +'    <label for="" class="form-label">'+attr_name+'</label>'
                        +'    <input type="hidden" name="product_attr['+attr_id+'][sub_attr_id]" value="'+attr_id+'">'
                        +'    <input type="hidden" name="product_attr['+attr_id+'][attribute_id]" value="'+attr_id+'">'
                        +'    <div class="input-group option_field_row_'+attr_id+' mt-1">'
                        +'      <input type="text" class="form-control" name="product_attr['+attr_id+'][custom_values][]" value="" required placeholder="Values">'
                        +'      <button class="btn btn-sm btn-success add_options" data-attr_id="'+attr_id+'" type="button">'
                        +'        <i class="mdi mdi-plus"></i>'
                        +'      </button>'
                        +'    </div>'
                        +'</div>';
            $(".selected_options").empty().append(attr_html);  
        });
    });

    $("body").on("click",".add_options", function(){
        attr_id     =   $(this).attr("data-attr_id");
        fieldHTML   =   ' <div class="input-group option_field_row_'+attr_id+' mt-1">'
                        +'   <input type="text" class="form-control" name="product_attr['+attr_id+'][custom_values][]" placeholder="Product Option" required>'
                        +'   <button class="btn btn-sm btn-danger remove_options" data-attr_id="'+attr_id+'" type="button">'
                        +'     <i class="mdi mdi-minus"></i>'
                        +'   </button>'
                        +'</div>';
        $('.update_options_'+attr_id+'').append(fieldHTML); //Add field html
    });

    $("body").on('click', '.remove_options', function(e){
        e.preventDefault();
        attr_id     =   $(this).attr("data-attr_id");
        console.log(attr_id);
        $(this).parent('.option_field_row_'+attr_id+'').remove(); //Remove field html
    });


    $("body").on("click",".update_product_options",function()
    {
        $("#cover-spin").show();
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
        $.each(custom_val_arr, function(e,x)
        {
            if(e == 0){
                fieldHTML   +='<div class="input-group edit_option_row mt-1">'
                            +'  <input type="text" class="form-control" name="custom_values[]" value="'+x+'" placeholder="Product Option" >'
                            +'      <button class="btn btn-sm btn-success add_edit_options" type="button">'
                            +'          <i class="mdi mdi-plus"></i>'
                            +'      </button>'
                            +' </div>';
            } else {
                fieldHTML   +='<div class="input-group edit_option_row mt-1">'
                            +'  <input type="text" class="form-control" name="custom_values[]" value="'+x+'" placeholder="Product Option" >'
                            +'      <button class="btn btn-sm btn-danger remove_edit_options" type="button">'
                            +'          <i class="mdi mdi-minus"></i>'
                            +'      </button>'
                            +' </div>';
            }
        });

        $(".edit_options").html(fieldHTML);
        $("#UpdateOptionModel").modal('toggle');
        $("#cover-spin").hide();
    });

    $("body").on("click",".add_edit_options", function(){
        fieldHTML =      '<div class="input-group edit_option_row mt-1">'
                        +'  <input type="text" class="form-control" name="custom_values[]" placeholder="Product Option" >'
                        +'      <button class="btn btn-sm btn-danger remove_edit_options" type="button">'
                        +'          <i class="mdi mdi-minus"></i>'
                        +'      </button>'
                        +'</div>';
        $('.edit_options').append(fieldHTML); //Add field html
    });

    $('.edit_options').on('click', '.remove_edit_options', function(e){
        e.preventDefault();
        $(this).parent('.edit_option_row').remove(); //Remove field html
    });

    // Delete Option
    $(".delete-option").on('click',function(){
        $("#delete_product_id").val($(this).attr('data-product_id'));
        $("#delete_sub_attr_id").val($(this).attr('data-sub_attr_id'));
        $("#deleteproductoption").modal('toggle');
    });

    $("body").on("change", '.select_service_class', function() {
        var checkboxes = $('.select_service_class input[type="checkbox"]');
        var chx = checkboxes.is(':checked');
        
        if(chx == true)
            $(this).closest('tr').find('input:text').prop("disabled", false);
        else
            $(this).closest('tr').find('input:text').prop("disabled", true);

        var countCheckedCheckboxes = checkboxes.filter(':checked').length;
        if (countCheckedCheckboxes > 0) {
            $("#custom_value_submit_form").show();
        } else {
            $("#custom_value_submit_form").hide();
        }
        $("#qty_left_id").val()
        $(".stocks_left_id").html();
    });

    $('body').on('keyup','.sub_prd_avail_qty',function(){
        var sum = 0;
        $("input[class *= 'sub_prd_avail_qty']").each(function(){
            sum += +$(this).val();
        });
        stocks_left = parseInt($("#qty_left_id").val());
        remaining_stock = stocks_left - sum;
        if(remaining_stock < 0){
            iziToast.error({
                title: 'Error',
                message: 'Entered Quantities are greater than stock available. please check',
                position: 'topRight',
            });
        }
        $(".stocks_left_id").html(remaining_stock);
        // console.log(remaining_stock);
    });

    $('body').on('keyup','.assigned_qty',function(){
        var assigned_qty = 0;
        $("input[class *= 'assigned_qty']").each(function(){
            assigned_qty += +$(this).val();
        });
        stocks_left     =   parseInt($("#qty_left_id").val());
        total_qty_id    =   parseInt($("#total_qty_id").val());
     
        if(assigned_qty > total_qty_id){
            iziToast.error({
                title: 'Error',
                message: 'Assigned Qty is greater than total qty',
                position: 'topRight',
            });
        }
        remaining_stock = total_qty_id - assigned_qty;
        $(".stocks_left_id").html(remaining_stock);
    });


    $("body").on('click', '.delete_stock', function(e){
        $("#delete_product_entry_id").val($(this).attr('data-entry_id'));
        $("#deleteproductstock").modal('toggle');
    });

});
</script>
@endsection
