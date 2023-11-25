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
              <li class="breadcrumb-item active" aria-current="page">Product Lists</li>
            </ol>
        </nav>
    </div>
    <div class="alert alert-info" role="alert">
        <strong>Note:</strong>
        <small>After adding new product please update it's specification, options and stocks to view and get listed in the app</small>
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
                    <div class="d-flex mb-2">
                        <div class="p-2 flex-grow-1">
                            <h4 class="card-title">Product Lists</h4>
                        </div>
                        {{-- <div class="p-2">
                            <a href="#" class="btn btn-outline-secondary btn-fw btn-sm">
                                <i class="mdi mdi-download btn-icon-prepend"></i> Download Report</a>
                        </div> --}}
                        <div class="p-2">
                            <a href="{{ route('admin.AddProduct') }}" class="btn btn-outline-primary btn-fw btn-sm">
                                <i class="mdi mdi-account-plus btn-icon-prepend"></i> Add New Product</a>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th># </th>
                                    <th>Name</th>
                                    <th>Seller</th>
                                    <th>Category</th>
                                    <th>View</th>
                                    <th class="col-sm-2">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            @if (count($products) > 0)
                                @foreach ($products as $key=>$product)
                                <tr>
                                    <td>{{ $key + $products->firstItem() }}</td>
                                    <td>
                                        <p>
                                            <img src= "{{ asset($product->image) }}" alt=""> <b>{{ $product->name }}</b>
                                        </p>
                                    </td>
                                    <td>{{ $product->seller_full_name_buss }}</td>
                                    <td>{{ $product->categoryname }}</td>
                                    <td>
                                        @if ($product->spec_count == 0 || $product->option_count == 0 || $product->product_combo == 0)
                                            <button type="button" class="btn btn-inverse-danger btn-icon">
                                                <i class="mdi mdi-eye-off"></i>
                                            </button>
                                        @else
                                            <a href="{{ Route('admin.ProductDetails',[$product->id]) }}"
                                                class="btn btn-outline-primary btn-sm mr-1"  data-toggle="tooltip"
                                                data-placement="top" title="View Product">
                                                <i class="mdi mdi-eye" aria-hidden="true"></i>
                                            </a>
                                        @endif

                                    </td>
                                    <td>
                                        <p>
                                            <div class="d-flex flex-row justify-content-center">
                                                <a href="{{ Route('admin.ProductSpecificationPage' ,[$product->id]) }}"
                                                    @if ($product->spec_count == 0)
                                                        class="btn btn-outline-secondary mx-1 btn-sm">
                                                    @else
                                                        class="btn btn-outline-info mx-1 btn-sm">
                                                    @endif
                                                    Specification</a>
                                                <a href="{{ Route('admin.ProductOptionsPage' ,[$product->id]) }}"
                                                    @if ($product->option_count == 0)
                                                        class="btn btn-outline-secondary mx-1 btn-sm">
                                                    @else
                                                        class="btn btn-outline-info mx-1 btn-sm">
                                                    @endif
                                                    Options</a>
                                                <a href="{{ Route('admin.ProductStocksPage', [$product->id]) }}"
                                                    @if ($product->product_combo == 0)
                                                        class="btn btn-outline-secondary mx-1 btn-sm">
                                                    @else
                                                        class="btn btn-outline-info mx-1 btn-sm">
                                                    @endif
                                                Stocks</a>
                                            </div>
                                        </p>
                                        <p>
                                            <div class="d-flex flex-row justify-content-center">
                                                @if ($product->option_count != 0)
                                                <a href="{{ Route('admin.EditProductPage',[$product->id]) }}" class="btn btn-outline-warning btn-sm mr-1"  data-toggle="tooltip" data-placement="top" title="Edit Product">
                                                    <i class="mdi mdi-pencil" aria-hidden="true"></i> Edit
                                                </a>
                                                @endif
                                                @if ($product->status == '1')
                                                    <form action="{{ Route('admin.productstatus') }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                                                        <input type="hidden" name="active_status" value="0">
                                                        <button class="btn btn-outline-success btn-sm ml-1"  data-toggle="tooltip" data-placement="top" title="Active Product">
                                                            <i class="mdi mdi-lock-open" aria-hidden="true"></i> Active
                                                        </button>
                                                    </form>
                                                @else
                                                    <form action="{{  Route('admin.productstatus') }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                                                        <input type="hidden" name="active_status" value="1">
                                                        <button type="submit" class="btn btn-outline-secondary ml-1 btn-sm"  data-toggle="tooltip" data-placement="top" title="Inactive Product">
                                                            <i class="mdi mdi-lock" aria-hidden="true"></i> Inactive
                                                        </button>
                                                    </form>
                                                @endif
                                                <form action="{{  Route('admin.productstatus') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                                    <input type="hidden" name="active_status" value="2">
                                                    <button type="submit" class="btn btn-outline-danger ml-1 btn-sm"
                                                    data-toggle="tooltip" data-placement="top" title="Delete Product">
                                                        <i class="mdi mdi-delete" aria-hidden="true"></i> Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </p>
                                    </td>
                                </tr>
                                @endforeach
                                @else
                                <tr>
                                    <td colspan="7"><center>No Products Found</center></td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

