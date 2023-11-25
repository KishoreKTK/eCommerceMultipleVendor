@extends('layouts.dashboard_layout')
@section('pagecss')
<style>
.card-horizontal {
    display: flex;
    flex: 1 1 auto;
}
.modal-lg {
    max-width: 75% !important;
}
.card-horizontal img {
  width: 50%;
}
</style>

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
              <li class="breadcrumb-item"><a href="{{ Route('admin.home') }}">Dashboard</a></li>
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
    @if ($message = Session::get('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>{{ $message }}</strong>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        {{-- Category Lists --}}
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <h4>Category List</h4>
                        <button class="btn btn-inverse-primary btn-sm" type="button" data-toggle="modal" data-target="#AddCategoryModel">Add New Category</button>
                    </div>

                    <div class="table-responsive">
                        <table class="table ">
                            <thead class="thead-inverse">
                                <tr>
                                    <th>#</th>
                                    <th>Title</th>
                                    <th>View</th>
                                    <th>Shops</th>
                                    <th>Products</th>
                                    <th>Uploaded By</th>
                                    <th>Status</th>
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
                                                    <img class="img-sm" src="{{ $category->image_url }}" alt="{{ $category->name }}">
                                                    <div class="wrapper ml-3">
                                                        <h5 class="ml-1 mb-1 font-weight-normal">{{ $category->name }}</h5>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-inverse-info mx-1 btn-icon viewcatdet"
                                                    data-category_id="{{ $category->id }}"
                                                    data-categoryname="{{ $category->name }}"
                                                    data-categorydesc="{{ $category->description }}"
                                                    data-cat_img="{{ asset($category->image_url) }}"
                                                    data-cat_uploader="{{ $category->sellername }}"
                                                    data-cat_products="{{ $category->ProductCount }}"
                                                    data-cat_created_dt="{{ $category->created_at }}"
                                                    data-cat_remarks="{{ $category->remarks }}"
                                                    data-cat_reason="{{ $category->reason }}">
                                                    <i class="mdi mdi-eye"></i>
                                                </button>
                                            </td>
                                            <td>{{ $category->shopcount }}</td>
                                            <td>{{ $category->ProductCount }}</td>
                                            <td>

                                                    @if($category->sellername != null)
                                                    <span class="badge badge-pill badge-info">
                                                        {{ $category->sellername }}
                                                    </span>
                                                    @else
                                                    <span class="badge badge-pill badge-dark">
                                                        Admin
                                                    </span>
                                                    @endif
                                                </td>
                                            <td>
                                                @if($category->is_active == '1')
                                                    <span class="badge badge-success">Active</span>
                                                @else
                                                    <span class="badge badge-danger">Inactive</span>
                                                @endif
                                            </td>
                                            <td id="category_action_td_{{ $category->id }}">
                                                @if($category->is_active != '2')
                                                <div class="d-flex flex-row">

                                                    <button type="button" class="btn btn-inverse-warning btn-icon edit_category"
                                                        data-category_id="{{ $category->id }}"
                                                        data-categoryname="{{ $category->name }}"
                                                        data-categorydesc="{{ $category->description }}"
                                                        data-cat_img="{{ asset($category->image_url) }}"
                                                        data-cat_uploader="{{ $category->sellername }}"
                                                        data-cat_products="{{ $category->ProductCount }}"
                                                        data-cat_created_dt="{{ $category->created_at }}"
                                                        data-cat_remarks="{{ $category->remarks }}"
                                                        data-cat_reason="{{ $category->reason }}">
                                                        <i class="mdi mdi-lead-pencil"></i>
                                                    </button>
                                                    @if($category->is_active == '1')
                                                    <form action="{{  Route('admin.ChangeCatStatus') }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="cat_id" value="{{ $category->id }}">
                                                        <input type="hidden" name="is_active" value="0">
                                                        <button type="submit" class="btn btn-inverse-secondary btn-icon mx-1">
                                                            <i class="mdi mdi-lock"></i>
                                                        </button>
                                                    </form>

                                                    @elseif($category->is_active == '0')
                                                    <form action="{{  Route('admin.ChangeCatStatus') }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="cat_id" value="{{ $category->id }}">
                                                        <input type="hidden" name="is_active" value="1">
                                                        <button type="submit" class="btn btn-inverse-success btn-icon mx-1">
                                                            <i class="mdi mdi-lock-open"></i>
                                                        </button>
                                                    </form>
                                                    @endif

                                                    {{-- <form action="{{  Route('admin.DeleteCategory') }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="cat_id" value="{{ $category->id }}">
                                                        <button type="submit" class="btn btn-inverse-danger btn-icon">
                                                            <i class="mdi mdi-delete" aria-hidden="true"></i>
                                                        </button>
                                                    </form> --}}
                                                </div>
                                                @else
                                                    <button type="button" class="btn btn-outline-danger
                                                        btn-block btn-icon-text btn-sm verify_category"
                                                        data-category_id="{{ $category->id }}"
                                                        data-categoryname="{{ $category->name }}"
                                                        data-categorydesc="{{ $category->description }}"
                                                        data-cat_img="{{ $category->image_url }}"
                                                        data-cat_uploader="{{ $category->sellername }}"
                                                        data-cat_products="{{ $category->ProductCount }}"
                                                        data-cat_created_dt="{{ $category->created_at }}"
                                                        data-cat_remarks="{{ $category->remarks }}"
                                                        data-cat_reason="{{ $category->reason }}">
                                                        <i class="mdi mdi-account-convert" aria-hidden="true"></i> Verify
                                                    </button>
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
                    <div class="d-flex justify-content-end">
                        {!! $categories->links() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Category Details --}}

{{-- Add New Category --}}
<div class="modal fade" id="AddCategoryModel" tabindex="-1" aria-labelledby="AddCategoryModel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Add New Category</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <div class="card">
                <div class="card-body">
                    <h4 class="mb-4">Add New Category</h4>
                    <form class="forms-sample" action="{{ route('admin.NewCategory') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        @if ($message = Session::get('fail'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong>{{ $message }}</strong>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        <div class="form-group">
                            <label for="exampleInputUsername1">Icon</label>
                            <input type="file" class="form-control mb-2 mr-sm-2" name="image" accept="image/*" required>
                            <small class="form-text text-muted">Upload Only Png | SVG, Max Size Allowed is 1 mb</small>
                        </div>
                        <div class="form-group">
                            <label for="exampleInputUsername1">Name</label>
                            <input type="text" class="form-control" name="name" value="{{ old('name') }}" id="exampleInputUsername1" placeholder="Category Name" required>
                        </div>
                        <div class="form-group">
                            <label for="exampleInputUsername1">Description</label>
                            <textarea type="text" name="description" class="form-control mb-2 mr-sm-2" rows="10" cols="50" required>{{ old('description') }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-gradient-primary mr-2">Submit</button>
                    </form>
                </div>
            </div>
        </div>
      </div>
    </div>
</div>

{{-- Edit Category --}}
<div class="modal fade" id="EditCategoryModel" tabindex="-1" aria-labelledby="EditCategoryModelLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Edit Category</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <div class="card">
                <div class="card-body">
                    <form class="forms-sample" id="EditCategory" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="category_id" id="edit_category_id" value="">
                        <div class="form-group">
                            <label for="exampleInputUsername1">Name</label>
                            <input type="text" class="form-control" name="name" value="" id="Edit_cat_name" placeholder="Category Name" required="" >
                        </div>
                        <div class="form-group">
                            <label for="exampleInputUsername1">Icon</label>
                            <input type="file" class="form-control mb-2 mr-sm-2" name="image">
                            <small class="form-text text-muted">Upload Only Png | SVG, Max Size Allowed is 1 mb</small>
                            <img src="" id="cat_curr_img" class="img-thumb" height="120" width="80">
                            <br>
                        </div>
                        <div class="form-group">
                            <label for="exampleInputUsername1">Description</label>
                            <textarea type="text" name="description" id="edit_desc_id" class="form-control mb-2 mr-sm-2" rows="10" cols="50" required=""></textarea>
                        </div>
                        <button type="submit" class="btn btn-gradient-primary mr-2">Submit</button>
                    </form>
                </div>
            </div>
        </div>
      </div>
    </div>
</div>

{{-- View Category Details --}}
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
                            <p>Category Details</p>
                            <table class="table table-dark">
                                <tbody>
                                    <tr id="product_count_row">
                                        <th>No of Products</th>
                                        <td id="product_count_show"></td>
                                    </tr>
                                    <tr>
                                        <th>Uploaded By</th>
                                        <td id="uploaded_show_id"></td>
                                    </tr>
                                    {{-- <tr>
                                        <th>Reason</th>
                                        <td id="new_cat_reason"></td>
                                    </tr> --}}
                                    {{-- <tr>
                                        <th>Approved By</th>
                                        <td></td>
                                    </tr> --}}
                                    {{-- <tr>
                                        <th>Approved Date</th>
                                        <td></td>
                                    </tr> --}}
                                    <tr>
                                        <th>Created Date</th>
                                        <td id="created_dt_show"></td>
                                    </tr>
                                </tbody>
                            </table>

                            <div class="card card-grey mt-2" id="verify_form">
                                <div class="card-body">
                                    <div id="approvalerror" class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <strong id="approvalerrormsg"></strong>
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <h6 class="mr-2">Reason ?</h6>
                                    <p class="font-weight-normal new_cat_request_reason"></p>
                                    <form>
                                        <input type="hidden" id="update_category_id" value="">
                                        <div class="form-group">
                                            <div class="form-check">
                                              <label class="form-check-label">
                                                <input type="radio" class="form-check-input check_approval" name="approval" value="1"> Approve <i class="input-helper"></i></label>
                                            </div>
                                            <div class="form-check">
                                              <label class="form-check-label">
                                                <input type="radio" class="form-check-input check_approval" name="approval" value="3"> Reject<i class="input-helper"></i></label>
                                            </div>

                                            <div class="form-group">
                                                <label for="exampleInputUsername1">Remarks</label>
                                                <textarea type="text" name="remarks" class="form-control mb-2 mr-sm-2 approval_remarks" rows="10" cols="50" required=""></textarea>
                                            </div>

                                            <button type="button" id="submit_verified_form" class="btn btn-gradient-primary float-right mr-2">Submit</button>
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
<script src="{{ asset('assets/js/categories.js') }}"></script>
@endsection
