@extends('layouts.dashboard_layout')
@section('pagecss')
<style>
/* .card-horizontal {
    display: flex;
    flex: 1 1 auto;
}

img {
    width: 218px;
    height: 268px;
    object-fit: cover ;
} */
.card-horizontal {
    display: flex;
    flex: 1 1 auto;
}
.modal-lg {
    max-width: 50% !important;
}

.card-horizontal img {
  width: 50%;
}
</style>
@php
$seller_id = Auth::guard('seller')->user()->id;
// dd($seller_id);
@endphp
@endsection
@section('content')
<div class="content-wrapper">
    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white mr-2">
                <i class="mdi mdi-home"></i>
            </span> Category Management
        </h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="{{ Route('seller.home') }}">Dashboard</a></li>
              <li class="breadcrumb-item active" aria-current="page">Categories</li>
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
    @if ($message = Session::get('fail'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>{{ $message }}</strong>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    @if($seller_assigned_count == 0)
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Please Assign at least 1 Category to get listed Shops in App</strong>
    </div>
    @endif
    <div class="row">
        {{-- Add New Category --}}
        <div class="col-md-5">
            <div class="row">
                <div class="col-md-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4>Request New Category</h4>
                            <form class="forms-sample" action="{{ route('seller.NewCategory') }}" method="post" enctype="multipart/form-data" autocomplete="off">
                                @csrf

                                <div class="form-group">
                                    <label for="exampleInputUsername1">Icon</label>
                                    <input type="file" class="form-control mb-2 mr-sm-2" name="image" accept="image/*" id=""  required>
                                    <small class="form-text text-muted">Upload Only PNG | SVG</small>
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputUsername1">Name</label>
                                    <input type="text" class="form-control" name="name" value="{{ old('name') }}" id="exampleInputUsername1" placeholder="Category Name" required>
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputUsername1">Description</label>
                                    <textarea type="text" name="description" class="form-control mb-2 mr-sm-2" rows="10" cols="50" required>{{ old('description') }}</textarea>
                                </div>
                                <input type="hidden" name="seller_id" value="{{ Auth::guard('seller')->user()->id }}">
                                <div class="form-group">
                                    <label for="exampleInputUsername1">Reason</label>
                                    <textarea type="text" name="reason" class="form-control mb-2 mr-sm-2" rows="10" cols="50" required>{{ old('reason') }}</textarea>
                                </div>
                                <button type="submit" class="btn btn-gradient-primary mr-2">Submit</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Category Lists --}}
        <div class="col-md-7 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex mb-4 justify-content-end">
                        <ul class="nav nav-tabs card-header-tabs" id="category-detail-list" role="tablist">
                            <li class="nav-item">
                                <a class="btn btn-outline-primary nav-link active" href="#categorylist" role="tab" aria-controls="categorylist" aria-selected="true">Category List</a>
                            </li>
                            <li class="nav-item">
                                <a class="btn btn-outline-primary nav-link"  href="#sellercategories" role="tab" aria-controls="sellercategories" aria-selected="false">My Category</a>
                            </li>
                        </ul>
                    </div>

                    <div class="tab-content mt-3">
                        {{-- All Categories List --}}
                        <div class="tab-pane active" id="categorylist" role="tabpanel">
                            <h4 class="card-title">Category List</h4>
                            <div class="table-responsive">
                                <table class="table table-inverse">
                                    <thead class="thead-inverse">
                                        <tr>
                                            <th>#</th>
                                            <th>Title</th>
                                            {{-- <th>Products</th> --}}
                                            <th>View</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(count($categories) > 0)
                                            @foreach ($categories as $key=>$category)
                                                <tr>
                                                    <td scope="row">{{ $key+1 }}</td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <img class="img-sm" src="{{ asset($category->image_url) }}" alt="{{ $category->name }}">
                                                            <div class="wrapper ml-3">
                                                                <h5 class="ml-1 mb-1 font-weight-normal">{{ $category->name }}</h5>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    {{-- <td>{{ $category->ProductCount }}</td> --}}
                                                    <td>
                                                        <button type="button" class="btn btn-inverse-info btn-icon viewcatdet"
                                                                data-categoryname="{{ $category->name }}"
                                                                data-categorydesc="{{ $category->description }}"
                                                                data-cat_img="{{ asset($category->image_url) }}">
                                                            <i class="mdi mdi-eye"></i>
                                                        </button>
                                                    </td>
                                                    <td>
                                                        @if($category->seller_cat == true)
                                                            @if($category->seller_cat_status == '1')
                                                                <span class="badge badge-danger">Unassign <br>Request Pending</span>
                                                            @elseif($category->seller_cat_status == '0')
                                                                <span class="badge badge-warning">Assign <br>Request Pending</span>
                                                            @else
                                                            <form action="{{  Route('seller.AssignCategory') }}" method="POST">
                                                                @csrf
                                                                <input type="hidden" name="cat_id" value="{{ $category->id }}">
                                                                <input type="hidden" name="assign" value="0">
                                                                <button type="submit" class="btn btn-danger btn-sm">Remove</button>
                                                            </form>
                                                            @endif
                                                        @else
                                                            <form action="{{  Route('seller.AssignCategory') }}" method="POST">
                                                                @csrf
                                                                <input type="hidden" name="cat_id" value="{{ $category->id }}">
                                                                <input type="hidden" name="assign" value="1">
                                                                <button type="submit" class="btn btn-success btn-sm">Assign</button>
                                                            </form>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="6" class="align-center">No Categories Found</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- My Category List--}}
                        <div class="tab-pane" id="sellercategories" role="tabpanel">
                            <h4 class="card-title">My Categories</h4>
                            <div class="table-responsive">
                                <table class="table table-inverse ">
                                    <thead class="thead-inverse">
                                        <tr>
                                            <th>#</th>
                                            <th>Title</th>
                                            <th>View</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(count($my_category) > 0)
                                            @foreach ($my_category as $key=>$category)
                                                <tr>
                                                    <td scope="row">{{ $key+1 }}</td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <img class="img-sm" src="{{ asset($category->image_url) }}" alt="{{ $category->name }}">
                                                            <div class="wrapper ml-3">
                                                                <h5 class="ml-1 mb-1 font-weight-normal">{{ $category->name }}</h5>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-inverse-info btn-icon viewcatdet"
                                                                data-categoryname="{{ $category->name }}"
                                                                data-categorydesc="{{ $category->description }}"
                                                                data-cat_img="{{ asset($category->image_url) }}"
                                                                data-active_status = "{{ $category->is_active }}"
                                                                data-remarks="{{ $category->remarks }}">
                                                            <i class="mdi mdi-eye"></i>
                                                        </button>
                                                    </td>
                                                    <td>
                                                        @if($category->is_active != '2')
                                                            {{-- <button type="button" class="btn btn-inverse-warning btn-icon">
                                                                <i class="mdi mdi-lead-pencil"></i>
                                                            </button> --}}
                                                            @if($category->is_active == '1')
                                                                {{-- <button type="button" class="btn btn-inverse-danger btn-icon">
                                                                    <i class="mdi mdi-email-open"></i>
                                                                </button> --}}
                                                                <label class="badge badge-success">Active</label>

                                                            @elseif($category->is_active == '0')
                                                                <label class="badge badge-warning">Inactive</label>
                                                                {{-- <button type="button" class="btn btn-inverse-danger btn-icon">
                                                                    <i class="mdi mdi-email-open"></i>
                                                                </button> --}}
                                                            @elseif($category->is_active == '3')
                                                                <label class="badge badge-danger">Rejected</label>
                                                            @endif
                                                        @else
                                                            <label class="badge badge-secondary">Pending</label>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="6"><center>No Categories Found</center></td>
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
    </div>
</div>
<div class="modal fade" id="ShowCategoryDetail" tabindex="-1" aria-labelledby="ShowCategoryDetailLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="category_title"></h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <div class="wp-block-group bs-card-image-left-step-1">
                <div class="wp-block-group__inner-container">
                    <div class="card-horizontal">
                        <img id="cat_img_show_id" src="" class="card-img-top" alt="Category Image" ezimgfmt="rs rscb1 src ng ngcb1" loading="eager" srcset="" sizes="">
                        <div class="card-body">
                            <h5 class="card-title" id="cat_title_show"></h5>
                            <p class="card-text" id="cat_desc_show"></p>
                            <div class="mt-3" id="ErrorMessage">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
</div>
@endsection

@section('pagescript')

<script>
    $(document).ready(function(){
        $('#category-detail-list a').on('click', function (e) {
            e.preventDefault()
            $(this).tab('show')
        })
        $("body").on("click",".viewcatdet",function(){
            var catname = $(this).attr("data-categoryname");
            var catdesc = $(this).attr("data-categorydesc");
            var cat_img = $(this).attr("data-cat_img");
            var status  = $(this).attr("data-active_status");
            var remarks  = $(this).attr("data-remarks");
            $("#category_title").html(catname+" Details");
            $("#cat_img_show_id").attr("src",cat_img);
            $("#cat_title_show").html(catname);
            $("#cat_desc_show").html(catdesc);
            $("#ShowCategoryDetail").modal("show");
            if(status == '3')
            {
                html = '';
                html +='<div class="alert alert-danger alert-dismissible fade show" role="alert">';
                html +='    <strong>Rejected</strong>';
                html +='    <p>'+remarks+'</p>';
                html +='</div>';
                $("#ErrorMessage").html(html);
            }
            else{
                $("#ErrorMessage").html('');
            }
        });
    });
</script>
@endsection
