@extends('layouts.dashboard_layout')
@section('pagecss')
<style>

</style>
@php
// $seller_id = Auth::guard('seller')->user()->id;
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
                <li class="breadcrumb-item"><a href="{{ Route('seller.ProductList') }}">Product List</a></li>
                <li class="breadcrumb-item active" aria-current="page">Custom Price</li>
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
        {{-- Product Lists --}}
        <div class="col-md-6 col-sm-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex mb-2">
                        <div class="p-2 flex-grow-1">
                            <h4 class="card-title">Custom Price Form</h4>
                        </div>
                    </div>
                    <form action="{{ route('seller.PostCustomPrice') }}" method="post" autocomplete="off">
                        @csrf
                        <div class="table-responsive">
                            <table class="table ">
                                <thead class="thead-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Attribute </th>
                                        <th>Select Option</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($product_attributes as $key=>$attr)
                                        @if(($attr->custom == 1 && $attr->custom_values != null) || $attr->custom == 0 )
                                        <input type="hidden" name="price_combo[{{ $key }}][product_id]" value="{{ $product_det->id }}">
                                        <tr>
                                            <td class="select_service_class"><input type="checkbox" name="price_combo[{{ $key }}][attribute_id]"
                                                value="{{ $attr->attribute_id }}"/></td>

                                            <td>{{ $attr->attr_name }}</td>

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
                                                    @endif
                                                @elseif($attr->custom == 0)
                                                    @php
                                                        $sub_id_arr     = explode(',',$attr->sub_attr_ids);
                                                        $sub_id_name    = explode(',',$attr->sub_attr_names);
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
                                </tbody>
                            </table>
                        </div>
                        <div class="row mt-2" id="custom_value_submit_form">
                            <div class="col">
                                <input type="hidden" name="product_stock[product_id]" value="{{ $product_det->id }}">
                                <input type="hidden" name="product_stock[price_type]" value="0">
                                <div class="form-group">
                                    <label for="">Quantity</label>
                                    <input type="number" name="product_stock[quantities]" value="{{ $product_det->quantities }}" min="0" max="{{ $product_det->quantities }}" class="form-control" placeholder="">
                                </div>
                                <div class="form-group">
                                    <label for="">Price</label>
                                    <input type="number" name="product_stock[product_price]" value="" min="{{ $product_det->product_price }}" class="form-control" placeholder="" required>
                                    <small class="form-text text-muted">Please Add Price More than Base Price <b>{{ $product_det->product_price }}</b></small>
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
        <div class="col-md-6 col-sm-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
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
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th>Update</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($product_stocks as $stock)
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

                                                    // dd($attr_det_arr);
                                                @endphp

                                                @foreach ($attr_det_arr as $val)
                                                    <p>
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
                                        <td>{{ $stock->product_price }}</td>
                                        <td>{{ $stock->quantities }}</td>
                                        <td>
                                            @if($stock->price_type != '1')
                                            <button type="button" class="btn btn-inverse-warning btn-sm" data-toggle="tooltip" data-title="Update Stocks"><i class="mdi mdi-pencil"></i></button>
                                            <button type="button" class="btn btn-inverse-danger btn-sm" data-toggle="tooltip" data-title="Delete Stock"><i class="mdi mdi-delete"></i></button>
                                            @else
                                            <button type="button" class="btn btn-inverse-warning btn-sm" data-toggle="tooltip" data-title="Update Stocks"><i class="mdi mdi-pencil"></i>Update</button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
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
        $("#custom_value_submit_form").hide();
        $("body").on("change", '.select_service_class', function() {
            var checkboxes = $('.select_service_class input[type="checkbox"]');
            var countCheckedCheckboxes = checkboxes.filter(':checked').length;
            if (countCheckedCheckboxes > 0) {
                $("#custom_value_submit_form").show();
            } else {
                $("#custom_value_submit_form").hide();
            }
        });
    });
</script>
@endsection
