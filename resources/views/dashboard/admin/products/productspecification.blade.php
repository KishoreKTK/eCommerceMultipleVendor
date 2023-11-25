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
              <li class="breadcrumb-item"><a href="{{ Route('admin.home') }}">Dashboard</a></li>
              <li class="breadcrumb-item" aria-current="page"><a href="{{ Route('admin.ProductList') }}">Product Lists</a></li>
              <li class="breadcrumb-item active" aria-current="page">Product Specifications</li>
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
                        <img src="{{ asset($product_det->image) }}" alt="admin"
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
                          <a class="nav-link active" href="#">Specifications</a>
                        </li>
                        <li class="nav-item">
                          <a class="nav-link" href="{{ Route('admin.ProductOptionsPage' ,[$product_det->id]) }}">Options</a>
                        </li>
                        <li class="nav-item">
                          <a class="nav-link" href="{{ Route('admin.ProductStocksPage', [$product_det->id]) }}">Stocks</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body" id="show_table_list">
                    <div class="d-flex mb-4 justify-content-between mb-3">
                        <h5 class="card-title mb-2">{{ $product_det->name }}</h5>
                    </div>
                    {{-- Product Specification --}}
                    <div class="d-flex justify-content-between">
                        <h4>Product Specifications</h4>
                    </div>
                    <hr>
                    <div class="border border-2 border-success m-2 p-2 bg-white rounded " id="spec_table_add_div">
                        <div class="row">
                            <div class="col-12">
                                <form action="{{ Route('admin.AddProductSpec') }}" method="POST">
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
                                            <form action="{{  Route('admin.DeleteProductSpec') }}" method="POST">
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

                    <div class="row mt-2" id="update_stock_list_form">
                        <div class="col-12 p-2">
                            <div class="d-flex justify-content-between">
                                <h6>Update Specification</h6>
                                <button type="button" class="btn btn-sm btn-outline-danger" id="back_to_stock_list">Back</button>
                            </div>
                            <div class="border border-2 border-warning m-2 p-2 bg-white rounded ">
                                <div class="row">
                                    <div class="col-12">
                                        <form action="{{ Route('admin.UpdateProductSpec') }}" method="POST">
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

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('pagescript')
<script>
    $(".my_select_box").select2({placeholder: "Select a Option"});
    $("#update_stock_list_form").hide();

    // Tabs Change
    $('#prodict-detail-tabs a').on('click', function (e) {
        e.preventDefault()
        $(this).tab('show')
    })

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
</script>
@endsection
