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
              <li class="breadcrumb-item active" aria-current="page">Product Filters</li>
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
        <div class="col-md-12 col-sm-12 grid-margin stretch-card">
            <div class="card">
              <div class="card-body">
                <h4 class="card-title">Filter List</h4>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                        <tr>
                            <th class="col-md-1">#</th>
                            <th class="col-md-2">Name</th>
                            <th class="col-md-6">Options</th>
                            <th class="col-md-2">Action</th>
                            <th class="col-md-1">Test</th>
                        </tr>
                        </thead>
                        <tbody>
                            @if (count($attributes)>0)
                                @foreach ($attributes as $key=>$attr)
                                <tr>
                                    <td>{{ $key+1 }}</td>
                                    <td>{{ $attr->name }}</td>
                                    <td class="col-md-6" style="white-space: normal !important">
                                    @if($attr->custom == '0')
                                        @php
                                            $subattr    =   explode(',',$attr->sub_attribute_id);
                                            $options    =   explode(',',$attr->sub_attribute_names);
                                        @endphp
                                        @foreach ($options as $optname)
                                        <span class="badge badge-secondary m-1">{{ $optname }}</span>
                                        @endforeach
                                    @else
                                    <button type="button" class="btn btn-sm btn-outline-primary">Add Custom Filter</button>
                                        {{-- <span class="badge badge-primary"></span> --}}
                                    @endif

                                    </td>
                                    <td>
                                        <div class="d-flex flex-row">
                                            <form action="#" method="POST">
                                                <input type="hidden" name="_token" value="AWG3g3JWY2ojQzGLLeLIs8PxVb5V0osdV3y2QYci">                                                <input type="hidden" name="sellerid" value="6">
                                                <input type="hidden" name="active_status" value="1">
                                                <button type="submit" class="btn btn-outline-success btn-sm">
                                                    <i class="mdi mdi-lock-open" aria-hidden="true"></i> Show in Filter List
                                                </button>
                                            </form>
                                        </div>
                                    </td>
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
        <div class="col-md-6 col-sm-12 grid-margin stretch-card">
            <div class="card">
              <div class="card-body">
                <h4 class="card-title">Filter List</h4>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Options</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                            @if (count($filters)>0)
                                @foreach ($filters as $attribute)
                                <tr>
                                    <td>{{ $attribute->name }}</td>
                                    <td>
                                        <button type="button" class="btn btn-inverse-info btn-icon viewattrdet"
                                                data-attr_name="{{ $attribute->name }}"
                                                data-cat_names="{{ $attribute->cat_name }}"
                                                data-sub_attr_names="{{ $attribute->sub_attr_name }}">
                                            <i class="mdi mdi-eye"></i>
                                        </button>
                                    </td>

                                    <td>
                                        <div class="d-flex flex-row">
                                            <form action="#" method="POST">
                                                <input type="hidden" name="_token" value="AWG3g3JWY2ojQzGLLeLIs8PxVb5V0osdV3y2QYci">                                                <input type="hidden" name="sellerid" value="6">
                                                <input type="hidden" name="active_status" value="1">
                                                <button type="submit" class="btn btn-outline-success btn-sm">
                                                    <i class="mdi mdi-lock-open" aria-hidden="true"></i> Show in Filter List
                                                </button>
                                            </form>
                                        </div>
                                    </td>
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
