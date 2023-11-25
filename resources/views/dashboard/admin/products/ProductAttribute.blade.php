@extends('layouts.dashboard_layout')
@section('pagecss')
    {{-- <script src="{{ asset('assets/vendors/select2/select2.min.js') }}"></script> --}}
    <link rel="stylesheet" href="{{ asset('assets/vendors/select2/select2.min.css') }}">
    <style>
        .modal-lg{
            max-width: 60%;
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
            <div class="col-md-5 grid-margin stretch-card">
                <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Add Product Attributes</h4>
                    <form class="forms-sample" action="{{ route('admin.AddProductAttributes') }}" method="post" autocomplete="off">
                        @csrf
                        <div class="form-group">
                            <label for="exampleInputUsername1">Attribute Name</label>
                            <input type="text" class="form-control" id="exampleInputUsername1" name="name" placeholder="Attribute Name" value="">
                            <span class="text-danger">@error('name'){{ $message }}@enderror</span>
                        </div>

                        <div class="form-group">
                            <label for="exampleFormControlSelect1">Select Category</label>
                            <select class="form-control" name="category_id" id="exampleFormControlSelect1">
                                <option value="">Please Select Category</option>
                                @foreach ($category_list as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                            <span class="text-danger">@error('category_id'){{ $message }}@enderror</span>
                        </div>

                        {{-- <div class="form-group row">
                            <div class="col-sm-6">
                                <div class="form-check">
                                <label class="form-check-label">
                                    <input type="radio" class="form-check-input check_approval_status"
                                        name="attribute_type" id="membershipRadios1" value="1"> ReadyMade Values <i class="input-helper"></i><i class="input-helper"></i></label>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-check">
                                <label class="form-check-label">
                                    <input type="radio" class="form-check-input check_approval_status"
                                        name="attribute_type" id="membershipRadios2" value="2"> Custom Values <i class="input-helper"></i><i class="input-helper"></i></label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group" id="suggessions_field_id">
                            <label for="exampleInputEmail1">Suggessions</label>
                            <textarea class="form-control" name="suggesstions" cols="30" rows="4"></textarea>
                            <small id="emailHelp" class="form-text text-muted">if attribute have Multiple values add in comma separated</small>
                            <small id="emailHelp" class="form-text text-muted">Ex: red, green, blue </small>
                        </div>
                        <div class="form-group" id="help_field_id">
                            <label for="exampleInputEmail1">Help</label>
                            <textarea class="form-control" name="summary" cols="30" rows="4"></textarea>
                            <small id="emailHelp" class="form-text text-muted">custom values are entered directly while adding products.<br>
                                This is to help to understand quantity type</small>
                            <small id="emailHelp" class="form-text text-muted">Ex: Add quantity weight in Grams or Add quantity in litres  </small>
                        </div> --}}
                    <button type="submit" class="btn btn-gradient-primary mr-2">Submit</button>
                    </form>
                </div>
                </div>
            </div>
            <div class="col-md-7 grid-margin stretch-card">
                <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Attribute List</h4>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Category</th>
                                {{-- <th>view</th> --}}
                                {{-- <th>Status</th> --}}
                                {{-- <th>Action</th> --}}
                            </tr>
                            </thead>
                            <tbody>
                                @if (count($attributes)>0)
                                    @foreach ($attributes as $attribute)
                                    <tr>
                                        <td>{{ $attribute->name }}</td>
                                        <td><a href="{{ Route('admin.categories') }}" class="badge badge-primary badge-pill" target="_blank">{{ $attribute->cat_name }}</a></td>
                                        {{-- <td>
                                            <button type="button" class="btn btn-inverse-info btn-icon viewattrdet"
                                                    data-attr_name="{{ $attribute->name }}"
                                                    data-cat_names="{{ $attribute->cat_name }}"
                                                    data-sub_attr_names="{{ $attribute->sub_attr_name }}">
                                                <i class="mdi mdi-eye"></i>
                                            </button>
                                        </td> --}}
                                        {{-- <td>
                                            @if ($attribute->is_active == '1')
                                                <label class="badge badge-success">Active</label>
                                            @else
                                                <label class="badge badge-danger">InActive</label>
                                            @endif
                                        </td> --}}
                                        {{-- <td>
                                            <div class="d-flex flex-row">
                                                <button class="btn btn-outline-warning btn-sm">
                                                    <i class="mdi mdi-pencil" aria-hidden="true"></i>
                                                </button>

                                                <form action="http://127.0.0.1:8000/admin/seller/ChangeSellerStatus" method="POST">
                                                    <input type="hidden" name="_token" value="AWG3g3JWY2ojQzGLLeLIs8PxVb5V0osdV3y2QYci">                                                <input type="hidden" name="sellerid" value="6">
                                                    <input type="hidden" name="active_status" value="1">
                                                    <button type="submit" class="btn btn-outline-danger btn-sm">
                                                        <i class="mdi mdi-delete" aria-hidden="true"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td> --}}
                                    </tr>
                                    @endforeach
                                @else
                                    <tr><td colspan="4"><center>No Record Found</center></td></tr>
                                @endif
                            </tbody>
                        </table>
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
            $("#suggessions_field_id").hide();
            $("#help_field_id").hide();

            function selectRefresh() {
                $(".select_multiple").select2({placeholder: "Select a Category",});
            }

            selectRefresh();
            $("body").on("click",".viewattrdet",function()
            {
                var Attr_title  =   $(this).attr("data-attr_name");
                $("#Attr_title").html(Attr_title);

                var catnames     =   $(this).attr("data-cat_names");
                var cat_html    =   '';
                cats = catnames.split(',');
                $.each(cats,function(e, cat){
                    // cat_html+='<tr><td>'+cat+'</td></tr>';
                    cat_html+='<li class="list-group-item">'+cat.toUpperCase()+'</li>';
                });
                $("#attribute_category_rows").html(cat_html);

                var subnames     =   $(this).attr("data-sub_attr_names");
                var sub_attr_html = '';
                subs = subnames.split(',');
                $.each(subs,function(e, sub){
                    // sub_attr_html+='<tr><td>'+sub.toUpperCase()+'</td></tr>';
                    sub_attr_html+='<li class="list-group-item">'+sub.toUpperCase()+'</li>';
                });
                $("#attribute_sub_attr_names").html(sub_attr_html);

                $("#ShowCategoryDetail").modal("show");

            });

            $("body").on("click",".check_approval_status",function(){
                approval_type = $(this).val();
                if(approval_type == 1){
                    $("#suggessions_field_id").show();
                    $("#help_field_id").hide();
                }
                else{
                    $("#suggessions_field_id").hide();
                    $("#help_field_id").show();
                }
            });
        });
    </script>
@endsection
