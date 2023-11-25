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
              <li class="breadcrumb-item"><a href="{{ Route('seller.home') }}">Dashboard</a></li>
              <li class="breadcrumb-item" aria-current="page"><a href="{{ Route('seller.ProductList') }}">Product Lists</a></li>
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

                            </tbody>
                        </table>
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
                          <a class="nav-link" href="{{ Route('seller.ProductOptionsPage' ,[$product_det->id]) }}">Options</a>
                        </li>
                        <li class="nav-item">
                          <a class="nav-link active" href="#">Stocks</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">


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
                                                <th class="w-100">Option</th>
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
                                                    @if(isset($product_entry_id) && $product_entry_id!=Null && $stock->price_type == '1' && isset($stk))
                                                    <form action="{{ Route('seller.UpdateStockInfo') }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="product_entry_id" value="{{$product_entry_id}}">
                                                        <input type="hidden" name="product_id" value="{{ $id }}">
                                                        <input type="hidden" name="product_type" id="product_main_type" value="1">
                                                        <td >
                                                    <input type="text" class="form-control" name="min_order_qty"  value="{{ isset($stk->min_order_qty)?$stk->min_order_qty:'' }}"  >
                                                        </td>
                                                    <td >
                                                        <input type="text" class="form-control" name="quantities" value="{{ isset($stk->quantities)?$stk->quantities:'' }}" >
                                                    </td>
                                                    <td >
                                                        <input type="text" class="form-control" name="product_price" value="{{ isset($stk->product_price)?$stk->product_price:'' }}" >
                                                    </td>

                                                        <td>
                                                        <button type="submit" class="btn btn-inverse-warning btn-sm update_custom_stock"
                                                                data-toggle="tooltip"  >
                                                            Update</button></td>
                                                    </form>

                                                    @else
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

                                                                <form action="{{  Route('seller.DeleteCustomStock') }}" method="POST">
                                                                    @csrf
                                                                    <input type="hidden" name="product_entry_id" value="{{ $stock->product_entry }}">
                                                                    <button type="submit" class="btn btn-outline-danger ml-1 btn-sm"
                                                                        data-toggle="tooltip" data-title="Delete Stock">
                                                                        <i class="mdi mdi-delete"></i>
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        @else
                                                        <form action="{{Route('seller.ProductStocksPage' ,[$product_det->id])}}" method="GET">
                                                            <input type="hidden" name="product_entry_id" value="{{ $stock->product_entry }}">
                                                            <button type="submit" class="btn btn-inverse-warning btn-sm update_custom_stock"
                                                            data-toggle="tooltip"  title="Update Specification">
                                                        <i class="mdi mdi-pencil"></i></button>
                                                        </form>

                                                        @endif
                                                    </td>
                                                    @endif
                                                </tr>
                                            @endforeach
                                                {{-- custom price --}}

                                                @if(count($combinations) > 0)

                                                @foreach ($combinations as $key=>$combo)
                                                <form action="{{ route('seller.AddProductStock') }}" method="post" autocomplete="off">
                                                    @csrf
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


                                                        <td>
                                                            {{ $combo_str }}
                                                        </td>
                                                        <input type="hidden" name="price_combo[{{ $key }}][combo]" value="{{ $combo_str }}">
                                                        <td>
                                                            <input class="form-control mobile-nub" type="text" name="price_combo[{{ $key }}][min_order]"  required value="{{ $product_det->min_order_qty }}" min="1" max="{{ $product_det->total_qty }}">
                                                        </td>
                                                        <td>
                                                            <input class="form-control mobile-nub sub_prd_avail_qty" type="text" name="price_combo[{{ $key }}][avail_qty]" value="" required min="{{ $product_det->min_order_qty }}" max="{{ $product_det->total_qty }}">
                                                        </td>
                                                        <td>
                                                            <input class="form-control mobile-nub" type="text" name="price_combo[{{ $key }}][price]"  required value="{{ $product_det->starndard_price }}" min="{{ $product_det->starndard_price }}">
                                                        </td>
                                                        <td class="select_service_class">
                                                            <a  class="btn btn-inverse-warning btn-sm update_custom_stock" id="editCustom($opt_str)"
                                                            data-toggle="tooltip"  title="Update Specification" name="price_combo[{{ $key }}][option_ids]" value="{{ $opt_str }}">
                                                            <i class="mdi mdi-pencil"></i></a>
                                                        {{-- <button class="b tn btn-inverse-success btn-fw float-right" type="submit">Submit</button> --}}
                                                            <input type="checkbox" name="price_combo[{{ $key }}][option_ids]" value="{{ $opt_str }}"/>
                                                        </td>


                                                    </tr>
                                                    </form>

                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="5" class="text-center">No Options Found</td>
                                                </tr>
                                            @endif

                                                {{-- custom price --}}

                                        </tbody>
                                    </table>
                                </div>
                            </div>


                            <br>
                            <div class="border border-2 border-secondary m-2 p-2 bg-white rounded ">
                                <div class="d-flex mb-2">
                                    <div class="p-2 flex-grow-1">
                                        <h4 class="card-title">Custom Price Form</h4>
                                        <h6>Available Qty to Assign- <strong id="remaing_prod_qty">{{ $product_det->total_qty }}</strong></h6>
                                    </div>
                                </div>
                                <form action="{{ route('seller.AddProductStock') }}" method="post" autocomplete="off">
                                    @csrf
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="table-responsive">
                                                <table class="table 
                                                    <thead class="thead-light">
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Combo </th>
                                                            <th>Min Order</th>
                                                            <th>Qty</th>
                                                            <th>Price</th>
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
                                                                        <input class="form-control mobile-nub" type="text" name="price_combo[{{ $key }}][min_order]" disabled required value="{{ $product_det->min_order_qty }}" min="1" max="{{ $product_det->total_qty }}">
                                                                    </td>
                                                                    <td>
                                                                        <input class="form-control mobile-nub sub_prd_avail_qty" type="text" name="price_combo[{{ $key }}][avail_qty]" value="" required disabled min="{{ $product_det->min_order_qty }}" max="{{ $product_det->total_qty }}">
                                                                    </td>
                                                                    <td>
                                                                        <input class="form-control mobile-nub" type="text" name="price_combo[{{ $key }}][price]" disabled required value="{{ $product_det->starndard_price }}" min="{{ $product_det->starndard_price }}">
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        @else
                                                            <tr>
                                                                <td colspan="5" class="text-center">No Options Found</td>
                                                            </tr>
                                                        @endif
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-2" id="custom_value_submit_form">
                                        <div class="col">
                                            <div class="mt-2">
                                                <button class="btn btn-inverse-success btn-fw float-right" type="submit">Submit</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        {{-- <div class="tab-pane" id="custom_price_form" role="tabpanel">
                            <div class="border border-2 border-secondary m-2 p-2 bg-white rounded ">
                                <div class="d-flex mb-2">
                                    <div class="p-2 flex-grow-1">
                                        <h4 class="card-title">Custom Price Form</h4>
                                        <h6>Available Qty to Assign- <strong id="remaing_prod_qty">{{ $product_det->total_qty }}</strong></h6>
                                    </div>
                                </div>
                                <form action="{{ route('seller.AddProductStock') }}" method="post" autocomplete="off">
                                    @csrf
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="table-responsive">
                                                <table class="table 
                                                    <thead class="thead-light">
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Combo </th>
                                                            <th>Min Order</th>
                                                            <th>Qty</th>
                                                            <th>Price</th>
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
                                                                        <input class="form-control mobile-nub" type="text" name="price_combo[{{ $key }}][min_order]" disabled required value="{{ $product_det->min_order_qty }}" min="1" max="{{ $product_det->total_qty }}">
                                                                    </td>
                                                                    <td>
                                                                        <input class="form-control mobile-nub sub_prd_avail_qty" type="text" name="price_combo[{{ $key }}][avail_qty]" value="" required disabled min="{{ $product_det->min_order_qty }}" max="{{ $product_det->total_qty }}">
                                                                    </td>
                                                                    <td>
                                                                        <input class="form-control mobile-nub" type="text" name="price_combo[{{ $key }}][price]" disabled required value="{{ $product_det->starndard_price }}" min="{{ $product_det->starndard_price }}">
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        @else
                                                            <tr>
                                                                <td colspan="5" class="text-center">No Options Found</td>
                                                            </tr>
                                                        @endif
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-2" id="custom_value_submit_form">
                                        <div class="col">
                                            <div class="mt-2">
                                                <button class="btn btn-inverse-success btn-fw float-right" type="submit">Submit</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('pagescript')
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>

<script>

    $(document).ready(function(){

        $("#editCustom").click(function(){
            var editCustom = $('#editCustom').val();
            console.log(editCustom);

    });
        $("#custom_value_submit_form").hide();
        $("#update_stock_list_form").hide();

        $('input[type=radio]').on('change', function() {
            $(this).closest("form").submit();
        });

        $('body').on('keyup','.sub_prd_avail_qty',function(){
            entered_qty = $(this).val();
            console.log(entered_qty);
            calc_total();
        });
        function calc_total(){
            var sum = 0;
            total_qty = parseInt($("#remaing_prod_qty").text());
            console.log(total_qty);
            $(".sub_prd_avail_qty").each(function(){
                sum += parseFloat($(this).text());
            });

            remaining_qty = total_qty - sum;

            console.log(remaining_qty);
        }

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
        });

        // Tabs Change
        $('#prodict-detail-tabs a').on('click', function (e) {
            e.preventDefault()
            $(this).tab('show')
        })

        $("body").on("click",".update_custom_stock",function(){
            qty         =   $(this).attr("data-qty");
            price       =   $(this).attr("data-product_price");
            attr_name   =   $(this).attr('data-attrname');
            min_order_qty   =   $(this).attr('data-min_order_qty');
            price_type = $(this).attr("data-price_type");
            console.log(qty);
            console.log(price);
            console.log(attr_name);
            console.log(min_order_qty);
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
