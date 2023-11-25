@extends('layouts.dashboard_layout')
@section('pagecss')
<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">


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
              <li class="breadcrumb-item" aria-current="page"><a href="{{ Route('admin.ProductList') }}">Product Lists</a></li>
              <li class="breadcrumb-item active" aria-current="page">Product Stocks</li>
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
                        <table class="table mt-4 ">
                            <tbody>
                                <tr>
                                    <th>Available Quantity</th>
                                    <td>{{ $product_det->total_qty }}</td>
                                </tr>
                                <tr>
                                    <th>Minimum Order</th>
                                    <td>{{ $product_det->min_order_qty }}</td>
                                </tr>
                                <tr>
                                    <th>Standard Price</th>
                                    <td>{{ $product_det->starndard_price }}</td>
                                </tr>
                                {{-- <tr> --}}
                                    {{-- <input type="checkbox" data-toggle="toggle" data-on="Enabled" data-off="Disabled">
                                        <input type="checkbox" id="toggle-two"> --}}
                                    {{-- <td colspan="2">
                                        <div class="d-flex flex-row justify-content-between">
                                            <p>
                                                Price Type
                                            </p>
                                            <div class="d-flex flex-row justify-content-end">
                                                <form action="{{  Route('admin.UpdatePriceType') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="product_id" value="{{ $product_det->id }}">
                                                    <input type="hidden" name="main_price_type" value="1">
                                                    <button type="submit"
                                                        @if($product_det->main_price_type == '1')
                                                        class="btn btn-primary ml-1 btn-sm"
                                                        @else
                                                        class="btn btn-outline-primary ml-1 btn-sm"
                                                        @endif
                                                        data-toggle="tooltip"
                                                    data-placement="top" title="Standard Price">
                                                        <i class="mdi mdi-shape-circle-plus" aria-hidden="true"></i> Standard
                                                    </button>
                                                </form>
                                                <form action="{{  Route('admin.UpdatePriceType') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="product_id" value="{{ $product_det->id }}">
                                                    <input type="hidden" name="main_price_type" value="2">
                                                    <button type="submit"
                                                        @if($product_det->main_price_type == '1')
                                                        class="btn btn-outline-primary ml-1 btn-sm"
                                                        @else
                                                        class="btn btn-primary ml-1 btn-sm"
                                                        @endif
                                                        data-toggle="tooltip" data-placement="top" title="Custom Price">
                                                        <i class="mdi mdi-shape-plus" aria-hidden="true"></i> Custom
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </td> --}}
                                {{-- </tr> --}}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-7 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex mb-4 justify-content-between mb-3">
                        <h5 class="card-title mb-2">{{ $product_det->name }}</h5>
                        <ul class="nav nav-tabs card-header-tabs" id="prodict-detail-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="btn btn-outline-primary nav-link active" href="#stocklist" role="tab"
                                    aria-controls="stocklist" aria-selected="true">Stocks</a>
                            </li>
                            <li class="nav-item">
                                <a class="btn btn-outline-primary nav-link" href="#custom_price_form" role="tab"
                                    aria-controls="custom_price_form" aria-selected="true">Custom Price Form</a>
                            </li>
                        </ul>
                    </div>

                    <div class="tab-content mt-5">
                        {{-- Product Option --}}
                        <div class="tab-pane active" id="stocklist" role="tabpanel">
                            <div id="stock_list_table">
                                <div class="d-flex mb-2">
                                    <div class="p-2 flex-grow-1">
                                        <h4 class="card-title">Stock Lists</h4>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table 
                                        <thead class="thead-light">
                                            <tr>
                                                <th class="w-100">Price Type</th>
                                                <th>Min. Order</th>
                                                <th>Quantity</th>
                                                <th>Price</th>
                                                <th>Update</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($stock_list as $stock)
                                                <tr>
                                                    <td>
                                                        @if($stock->price_type == '1')
                                                        <span class="badge badge-pill badge-success">Standard</span>
                                                        @else
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
                                                        @endif
                                                    </td>
                                                    <td>{{ $stock->min_order_qty }}</td>
                                                    <td>{{ $stock->quantities }}</td>
                                                    <td>{{ $stock->product_price }}</td>
                                                    <td>
                                                        @if($stock->price_type != '1')
                                                            <div class="d-flex justify-content-center">
                                                                <button type="button" class="btn btn-inverse-warning btn-sm update_custom_stock"
                                                                    data-toggle="tooltip"  data-title="Update Stocks"
                                                                    data-entry_id="{{ $stock->product_entry }}"
                                                                    data-min_order_qty="{{ $stock->min_order_qty }}"
                                                                    data-product_price="{{ $stock->product_price }}"
                                                                    data-qty="{{ $stock->quantities }}"
                                                                    data-price_type = "2"
                                                                    {{-- data-prdt_id = "{{ $stock->product_id }}" --}}
                                                                    data-attrname="{{ json_encode($attribute_name) }}">
                                                                    <i class="mdi mdi-pencil"></i>
                                                                </button>

                                                                <form action="{{  Route('admin.DeleteCustomStock') }}" method="POST">
                                                                    @csrf
                                                                    <input type="hidden" name="product_entry_id" value="{{ $stock->product_entry }}">
                                                                    <button type="submit" class="btn btn-outline-danger ml-1 btn-sm"
                                                                        data-toggle="tooltip" data-title="Delete Stock">
                                                                        <i class="mdi mdi-delete"></i>
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        @else
                                                            <button type="button" class="btn btn-inverse-warning btn-sm update_custom_stock"
                                                                data-toggle="tooltip" data-title="Update Stocks"
                                                                data-entry_id="{{ $stock->product_entry }}"
                                                                data-min_order_qty="{{ $stock->min_order_qty }}"
                                                                data-product_price="{{ $stock->product_price }}"
                                                                data-qty="{{ $stock->quantities }}"
                                                                data-price_type = "1"
                                                                 {{-- data-prdt_id = "{{ $stock->product_id }}" --}}
                                                                data-attrname="Standard">
                                                                <i class="mdi mdi-pencil"></i>Update
                                                            </button>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="row mt-2" id="update_stock_list_form">
                                <div class="col-12 p-2">
                                    <div class="d-flex justify-content-between">
                                        <h6>Update Stock Detail</h6>
                                        <button type="button" class="btn btn-sm btn-outline-secondary" id="back_to_stock_list">Back</button>
                                    </div>
                                    <p>Stock Type</p>
                                    <div id="UpdateStockTypeId">
                                    </div>
                                    <hr>
                                    <form action="{{ Route('admin.UpdateStockInfo') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="product_entry_id" id="product_entry_id" value="">
                                        <input type="hidden" name="product_id" value="{{ $product_det->id }}">
                                        <input type="hidden" name="product_type" id="product_main_type" value="">
                                        <div class="form-group">
                                            <label for="">Quantity</label>
                                            <input type="number" name="quantities" id="update_entry_qty" value="" class="form-control" placeholder="">
                                        </div>
                                        <div class="form-group">
                                            <label for="">Minimum Order</label>
                                            <input type="number" name="min_order_qty" id="update_min_order_qty" value="" min="1" max="{{ $product_det->total_qty }}" class="form-control" placeholder="">
                                        </div>
                                        <div class="form-group">
                                            <label for="">Price</label>
                                            <input type="number" name="product_price" id="update_price" value="" min="{{ $product_det->starndard_price }}" class="form-control" placeholder="" required>
                                            <small class="form-text text-muted">Please Add Price More than Base Price <b>{{ $product_det->starndard_price }}</b></small>
                                        </div>

                                        <div class="mt-2">
                                            <button class="btn btn-inverse-success btn-fw float-right" type="submit">Submit</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        {{-- Product Stock Form --}}
                        <div class="tab-pane" id="custom_price_form" role="tabpanel">
                            <div class="border border-2 border-secondary m-2 p-2 bg-white rounded ">
                                <div class="d-flex mb-2">
                                    <div class="p-2 flex-grow-1">
                                        <h4 class="card-title">Custom Price Form</h4>
                                    </div>
                                </div>
                                <form action="{{ route('admin.PostCustomPrice') }}" method="post" autocomplete="off">
                                    @csrf
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="table-responsive">
                                                <table class="table 
                                                    <thead class="thead-light">
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Attribute </th>
                                                            <th>Select Option</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @if(count($attributelist) > 0)
                                                        @foreach ($attributelist as $key=>$attr)
                                                            @if(($attr->custom == 1 && $attr->custom_values != null) || $attr->custom == 0 )
                                                            <input type="hidden" name="price_combo[{{ $key }}][product_id]" value="{{ $product_det->id }}">
                                                            <tr>
                                                                <td class="select_service_class">
                                                                    <input type="checkbox" name="price_combo[{{ $key }}][attribute_id]" value="{{ $attr->attribute_id }}"/>
                                                                </td>
                                                                <td>{{ $attr->attrname }}</td>
                                                                <td>
                                                                    @if($attr->custom == 1 && $attr->custom_values != null)
                                                                        @php
                                                                            $custom_values = explode(',',$attr->custom_values);
                                                                        @endphp
                                                                        @if(count($custom_values) > 1)
                                                                            <input type="hidden" name="price_combo[{{ $key }}][sub_attr_id]" value="{{ $attr->sub_attr_ids }}">
                                                                            <select class="form-control no_change_guest" name="price_combo[{{ $key }}][custom_values]">
                                                                                @foreach ($custom_values as $values)
                                                                                <option value="{{ $values }}">{{ $values }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                        @else
                                                                            <span>{{ $attr->custom_values }}</span>
                                                                            <input type="hidden" name="price_combo[{{ $key }}][sub_attr_id]" value="{{ $attr->sub_attr_ids }}">
                                                                            <input type="hidden" name="price_combo[{{ $key }}][custom_values]" value="{{ $attr->custom_values }}">
                                                                        @endif
                                                                    @elseif($attr->custom == 0)
                                                                        @php
                                                                            $sub_id_arr     = explode(',',$attr->sub_attr_ids);
                                                                            $sub_id_name    = explode(',',$attr->sub_attr_name);
                                                                            $optionvalues   = array_combine($sub_id_arr, $sub_id_name);
                                                                        @endphp
                                                                        <select class="form-control" name="price_combo[{{ $key }}][sub_attr_id]">
                                                                            @foreach ($optionvalues as $value=>$name)
                                                                            <option value="{{ $value }}">{{ $name }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                            @endif
                                                        @endforeach
                                                        @else
                                                        <tr>
                                                            <td colspan="3">No Options Found</td>
                                                        </tr>
                                                        @endif
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-2" id="custom_value_submit_form">
                                        <div class="col">
                                            <input type="hidden" name="product_stock[product_id]" value="{{ $product_det->id }}">
                                            <input type="hidden" name="product_stock[price_type]" value="0">
                                            <div class="form-group">
                                                <label for="">Quantity</label>
                                                <input type="number" name="product_stock[quantities]" value="{{ $product_det->total_qty }}" min="{{ $product_det->min_order_qty }}" max="{{ $product_det->total_qty }}" class="form-control" placeholder="">
                                            </div>
                                            <div class="form-group">
                                                <label for="">Minimum Order</label>
                                                <input type="number" name="product_stock[min_order_qty]" value="{{ $product_det->min_order_qty }}" min="1" max="{{ $product_det->total_qty }}" class="form-control" placeholder="">
                                            </div>
                                            <div class="form-group">
                                                <label for="">Price</label>
                                                <input type="number" name="product_stock[product_price]" value="" min="{{ $product_det->starndard_price }}" class="form-control" placeholder="" required>
                                                <small class="form-text text-muted">Please Add Price More than Base Price <b>{{ $product_det->starndard_price }}</b></small>
                                            </div>

                                            <div class="mt-2">
                                                <button class="btn btn-inverse-success btn-fw float-right" type="submit">Submit</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>
                {{-- <div class="card-body" id="show_table_list">
                    <div class="d-flex mb-2">
                        <div class="p-2 flex-grow-1">
                            <h4 class="card-title">Product Stock Lists</h4>
                        </div>
                    </div>

                </div>
                <div class="card-body" id="edit_product_stock">
                    <div class="d-flex justify-content-between">
                        <h4>Update Stock</h4>
                        <button type="button" id="" class="btn btn-sm btn-inverse-warning">Back</button>
                    </div>
                    <hr>
                    <div class="row justify-content-center">
                        <div class="col-md-6 col-sm-12">
                            <form method="post" action="">
                                <div class="form-group">
                                    <label for="my-input">Quantity</label>
                                    <input id="my-input" class="form-control" type="text" name="quantity">
                                </div>

                                <div class="form-group">
                                    <label for="my-input">Price</label>
                                    <input id="my-input" class="form-control" type="text" name="price">
                                </div>

                                <div class="form-group">
                                    <label for="my-input">Minimum Order</label>
                                    <input id="my-input" class="form-control" type="text" name="minimum_order">
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-warning">Update</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div> --}}
            </div>
        </div>
    </div>
</div>
@endsection

@section('pagescript')
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>

<script>

    $(document).ready(function(){
        $("#custom_value_submit_form").hide();
        $("#update_stock_list_form").hide();

        $('input[type=radio]').on('change', function() {
            $(this).closest("form").submit();
        });

        $("body").on("change", '.select_service_class', function() {
            var checkboxes = $('.select_service_class input[type="checkbox"]');
            var countCheckedCheckboxes = checkboxes.filter(':checked').length;
            if (countCheckedCheckboxes > 0) {
                $("#custom_value_submit_form").show();
            } else {
                $("#custom_value_submit_form").hide();
            }
        });

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
