@extends('layouts.dashboard_layout')
@section('pagecss')
<link rel="stylesheet"  href="{{ asset('assets/vendors/OwlCorousal/owl.carousel.min.css') }}"/>
<link rel="stylesheet"  href="{{ asset('assets/vendors/OwlCorousal/owl.theme.default.min.css') }}"/>

<style>
/* .modal-lg {
    max-width: 50% !important;
} */
/* ul{
list-style: none outside none;
    padding-left: 0;
    margin: 0;
} */
/* .demo .item{
    margin-bottom: 60px;
}
.content-slider li{
    background-color: #ed3020;
    text-align: center;
    color: #FFF;
}
.content-slider h3 {
    margin: 0;
    padding: 70px 0;
}
.demo{
    width: 800px;
}
 */
.img-responsive {
    width: auto;
    height: 100px;
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
              <li class="breadcrumb-item"><a href="{{ Route('admin.home') }}">Dashboard</a></li>
              <li class="breadcrumb-item"><a href="{{ Route('admin.ProductList') }}">Product List</a></li>
              <li class="breadcrumb-item active" aria-current="page">Product Detail</li>
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
        <div class="col-lg-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="owl-carousel owl-theme">
                        @foreach ($product_images as $images)
                            <img src="{{ asset($images) }}"
                            height="400px" width="400px"/>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex mb-4 justify-content-between mb-3">
                        <h5 class="card-title mb-2">{{ $product_det->name }}</h5>
                        <ul class="nav nav-tabs card-header-tabs" id="prodict-detail-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="btn btn-outline-primary nav-link active" href="#productlist" role="tab"
                                     aria-controls="productlist" aria-selected="true">Overview</a>
                            </li>
                            <li class="nav-item">
                                <a class="btn btn-outline-primary nav-link" href="#productdetails" role="tab"
                                     aria-controls="productdetails" aria-selected="true">Details</a>
                            </li>
                            {{-- <li class="nav-item">
                                <a class="btn btn-outline-primary nav-link"  href="#productreviews" role="tab"
                                    aria-controls="productreviews" aria-selected="false">Reviews</a>
                            </li> --}}
                        </ul>
                    </div>

                    <div class="tab-content mt-5">
                        {{-- My Product List --}}
                        <div class="tab-pane active" id="productlist" role="tabpanel">
                            <p>{{ $product_det->description }}</p>
                            <hr>
                            <table class="table table-light">
                                <tbody>
                                    <tr>
                                        <th>Seller</th>
                                        <td>{{ $product_det->sellername }}</td>
                                    </tr>
                                    <tr>
                                        <th>Category</th>
                                        <td>{{ $product_det->categoryname }}</td>
                                    </tr>
                                    <tr>
                                        <th>Price</th>
                                        <td>{{ $product_det->product_price }}</td>
                                    </tr>
                                    <tr>
                                        <th>Quantity</th>
                                        <td>{{ $product_det->quantities }}</td>
                                    </tr>
                                </tbody>
                            </table>
                            {{-- <h4 class="card-title">Product Details</h4> --}}
                        </div>

                        <div class="tab-pane" id="productdetails" role="tabpanel">
                            <h6>Specifications</h6>
                            <table class="table table-secondary">
                                <tbody>
                                    @foreach ($attributes as $key=>$attr)
                                    <tr>
                                        <th>{{ $key }}</th>
                                        <td>{{ $attr }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <hr>
                            <h6>More Details</h6>
                            <table class="table table-secondary">
                                <tbody>
                                    <tr>
                                        <th>Status</th>
                                        <td>@if($product_det->status == '1')Active @else Inactive @endif</td>
                                    </tr>
                                    <tr>
                                        <th>Featured</th>
                                        <td>@if($product_det->is_featured == '1')Yes @else No @endif</td>
                                    </tr>
                                    <tr>
                                        <th>Products Sold</th>
                                        <td>{{ $product_det->order_count }}</td>
                                    </tr>
                                    <tr>
                                        <th>Likes</th>
                                        <td>{{ $product_det->like_count }}</td>
                                    </tr>
                                    <tr>
                                        <th>Saves</th>
                                        <td>{{ $product_det->save_count }}</td>
                                    </tr>
                                    <tr>
                                        <th>Created At</th>
                                        <td>{{ $product_det->created_at }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        {{-- My Product Reviews--}}
                        <div class="tab-pane" id="productreviews" role="tabpanel">
                            <h4 class="card-title">Product Reviews</h4>
                        </div>
                    </div>

                </div>
            </div>
            <p></p>
        </div>
    </div>
</div>
@endsection


@section('pagescript')
<script src="{{ asset('assets/vendors/OwlCorousal/owl.carousel.min.js') }}"></script>
<script>
    $(document).ready(function(){

        // Tabs Change
        $('#prodict-detail-tabs a').on('click', function (e) {
            e.preventDefault()
            $(this).tab('show')
        })

        // Owl Carousel
        $('.owl-carousel').owlCarousel({
            items:1,
            loop:true,
            margin:10,
            nav:true,
        });
    });
</script>
@endsection
