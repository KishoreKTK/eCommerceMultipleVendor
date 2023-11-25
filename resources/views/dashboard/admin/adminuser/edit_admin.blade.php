@extends('layouts.dashboard_layout')
@section('pagecss')


@endsection
@section('content')
<div class="content-wrapper">
    <div class="page-header">
        <h3 class="page-title">
            <a href="{{ Route('admin.home') }}"><span class="page-title-icon bg-gradient-primary text-white mr-2">
                <i class="mdi mdi-home"></i>
            </span></a>Admin User Management
        </h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ Route('admin.home') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ Route('admin.adminusers') }}">Admin User List</a></li>
                <li class="breadcrumb-item active" aria-current="page">Add New Admin User</li>
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
        <div class="col-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Edit Admin</h4>
                    <hr class="my-4">
                    <form action="{{ route('admin.UpdateAdminProfile') }}" method="POST" class="forms-sample" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="admin_id" value="{{ $admin->id }}">
                        <div class="form-group">
                            <label for="exampleInputEmail3">Email address</label>
                            <input type="email" name="email" class="form-control" id="exampleInputEmail3" placeholder="Email" readonly
                            value="{{ $admin->email }}">
                        </div>
                        <div class="form-group">
                            <label for="exampleInputName1">Name</label>
                            <input type="text" name="name" class="form-control" id="exampleInputName1" placeholder="Name"
                            value="{{ $admin->name }}" required>
                        </div>

                        <div class="form-group">
                            <label for="exampleInputName1">Mobile</label>
                            <input type="text" name="phone" class="form-control mobile-nub" id="exampleInputName1" placeholder="Enter Mobile Number"
                            value="{{ $admin->phone }}" required>
                        </div>

                        <div class="form-group">
                            <label>Profile</label>
                            <input type="file" class="form-control" name="profile" accept="image/png, image/gif, image/jpeg">
                        </div>
                        <p>
                            @if(!empty($admin->profile))
                                <img src="{{ $admin->profile }}" alt="image" height="150px">
                            @endif
                        </p>

                        <button type="submit" class="btn btn-gradient-primary mr-2 float-right">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- <div class="row">
        <div class="col-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Add New Admin User</h4>
                    <hr>
                    <form action="{{ route('admin.AddAdminUser') }}" method="POST" autocomplete="off" class="forms-sample" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="exampleInputName1">Name</label>
                            <input type="text" name="name" class="form-control" id="exampleInputName1" placeholder="Name"
                            value="" required>
                        </div>
                        <div class="form-group">
                            <label for="exampleInputEmail3">Email</label>
                            <input type="email" name="email" class="form-control" id="exampleInputEmail3"
                                placeholder="Email" value="" required>
                        </div>
                        <div class="form-group">
                            <label for="exampleInputName1">Mobile</label>
                            <input type="text" name="phone" class="form-control mobile-nub" id="exampleInputName1" placeholder="Enter Mobile Number"
                            value="" required>
                        </div>
                        <button type="submit" class="btn btn-gradient-primary mr-2 float-right">Submit</button>
                    </form>

                </div>
            </div>
        </div>
    </div> --}}
</div>


@endsection

@section('pagescript')

@endsection
