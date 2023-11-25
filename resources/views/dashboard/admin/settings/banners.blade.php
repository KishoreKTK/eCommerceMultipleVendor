@extends('layouts.dashboard_layout')

@section('content')
<div class="content-wrapper">
    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white mr-2">
                <i class="mdi mdi-home"></i>
            </span>Settings
        </h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="{{ Route('admin.home') }}">Dashboard</a></li>
              <li class="breadcrumb-item active" aria-current="page">Banners</li>
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
        <div class="col-md-5 grid-margin stretch-card">
            <div class="card">
              <div class="card-body">
                <h4 class="card-title">Add Banner</h4>
                <form class="forms-sample" action="{{ route('admin.postbanners') }}" method="post" autocomplete="off" enctype="multipart/form-data">
                    @csrf

                    {{-- <div class="form-group">
                        <label for="exampleInputUsername1">Image</label>
                        <input type="file" class="form-control mb-2 mr-sm-2" name="banner_image" id="" required>
                    </div> --}}

                    <div class="form-group">
                        <label for="exampleInputUsername1">Title</label>
                        <input type="text" class="form-control" id="exampleInputUsername1" name="title" required placeholder="Banner Title" value="">
                        <span class="text-danger">@error('title'){{ $message }}@enderror</span>
                    </div>

                    <div class="form-group">
                        <label for="exampleInputUsername1">Description</label>
                        <textarea type="text" name="description" class="form-control mb-2 mr-sm-2" rows="10" cols="50" required>{{ old('description') }}</textarea>
                    </div>


                    <div class="form-group">
                        <label for="exampleFormControlSelect1">Banner Type</label>
                        <select class="form-control" name="banner_type" id="banner_type">
                            <option value="">Select Banner Type</option>
                            <option value="1">Products</option>
                            <option value="2">Sellers</option>
                            <option value="3">Categories</option>
                            <option value="4">Link</option>
                        </select>
                        <span class="text-danger">@error('banner_type'){{ $message }}@enderror</span>
                    </div>

                    <div class="form-group" id="sub_banner">
                        <label for="exampleFormControlSelect1">Sub Order</label>
                        <select class="form-control" name="sub_banner" id="sub_banner_id">
                        </select>
                        <span class="text-danger">@error('sub_banner'){{ $message }}@enderror</span>
                    </div>

                    <div class="form-group" id="banner_link_add">
                        <label for="exampleInputUsername1">Banner Link</label>
                        <input type="text" class="form-control" id="exampleInputUsername1" name="link" placeholder="Redirect Url" value="">
                        <span class="text-danger">@error('link'){{ $message }}@enderror</span>
                    </div>
                    <button type="submit" class="btn btn-gradient-primary mr-2">Submit</button>
                </form>
              </div>
            </div>
        </div>
        <div class="col-md-7 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Banners List</h4>
                    <div class="row">
                        @foreach ($banners as $banner)
                        <div class="col-xl-6 col-sm-12 stretch-card grid-margin">
                            <div class="card border border-warning rounded">
                                <img src="{{ asset($banner->banner_image) }}" class="w-100" alt="Product 1">
                                <div class="px-3 py-4">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h4 class="m-0">{{ $banner->title }}</h4>
                                    </div>
                                    <hr>
                                    <p>{{ $banner->description }}</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="badge badge-gradient-info">
                                            @if($banner->banner_type == 1)
                                                <a href="{{ route('admin.ProductList') }}" target="_blank" class="text-reset text-decoration-none">Product</a>
                                            @elseif($banner->banner_type == 2)
                                                <a href="{{ route('admin.SellerList') }}" target="_blank" class="text-reset text-decoration-none">Seller</a>
                                            @elseif($banner->banner_type == 3)
                                                <a href="{{ route('admin.categories') }}" target="_blank" class="text-reset text-decoration-none">Categories</a>
                                            @else
                                                WebLink
                                            @endif
                                        </div>
                                        <div>
                                            @if($banner->banner_type == 1)
                                                <a href="{{ route('admin.ProductDetails',[$banner->sub_banner_id]) }}" target="_blank" class="badge btn-inverse-info btn-icon"><i class="mdi mdi-eye"></i></a>
                                            @elseif($banner->banner_type == 2)
                                                <a href="{{ route('admin.SellerDetail',[$banner->sub_banner_id]) }}" target="_blank" class="badge btn-inverse-info btn-icon"><i class="mdi mdi-eye"></i></a>
                                            @elseif($banner->banner_type == 3)
                                                <a href="{{ route('admin.categories') }}" target="_blank" class="badge btn-inverse-info btn-icon"><i class="mdi mdi-eye"></i></a>
                                            @else
                                                <a href="{{ $banner->link }}" target="_blank" class="badge badge-gradient-success">Visit</a>
                                            @endif

                                            {{-- <a href="#" class="badge btn-inverse-warning btn-icon"><i class="mdi mdi-lead-pencil"></i></a> --}}
                                            <a href="{{ Route('admin.deletebanner',[$banner->id]) }}" class="badge btn-inverse-danger btn-icon"><i class="mdi mdi-delete"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="ShowCategoryDetail" tabindex="-1" aria-labelledby="ShowCategoryDetailLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"  id="Attr_title"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 col-sm-12 grid-margin stretch-card">
                        <div class="card">
                            <div class="card-body">
                                <h5>Categories</h5>
                                <ul class="list-group border-2 border-primary" id="attribute_category_rows">
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12 grid-margin stretch-card">
                        <div class="card">
                            <div class="card-body">
                                <h5>Options</h5>
                                <ul class="list-group" id="attribute_sub_attr_names">
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-gradient-danger btn-sm float-right" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('pagescript')
<script src="{{ asset('assets/vendors/select2/select2.min.js') }}"></script>
<script>
    $(document).ready(function()
    {
        var base_url = window.location.origin;
        $("#sub_banner").hide();
        $("#banner_link_add").hide();

        $("body").on("change","#banner_type",function(){
            banner_type = $(this).val();
            if(banner_type  == ''){
                $("#sub_banner").hide();
                $("#banner_link_add").hide();
            } else {
                if(banner_type  != 4){
                    $.ajax({
                        type: "POST",
                        url: base_url + '/admin/settings/changebannertype',
                        data: { 'banner_type' :   banner_type},
                        dataType: "JSON",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(msg)
                        {
                            opthtml     =   '';
                            opthtml     +=  '<option value="">Please Select to Show Banner</option>';
                            if (msg['status'] == true) {
                                $.each(msg['data'], function(ex, val) {
                                    opthtml+='<option value='+ex+'>'+val+'</option>';
                                });
                                $("#sub_banner_id").html(opthtml);
                                $("#sub_banner").show();
                                $("#banner_link_add").hide();
                            } else {
                                $("#sub_banner").hide();
                                $("#banner_link_add").hide();
                            }
                        }
                    });
                } else {
                    $("#sub_banner").hide();
                    $("#banner_link_add").show();
                }
            }
        });
    });
</script>
@endsection
