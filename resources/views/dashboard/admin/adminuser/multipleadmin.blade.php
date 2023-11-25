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
                <li class="breadcrumb-item active" aria-current="page">Admin Users List</li>
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
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
              <div class="card-body">
                <div class="d-flex mb-2">
                    <div class="p-2 flex-grow-1">
                        <h4 class="card-title">Admin Users List</h4>
                    </div>
                    <div class="p-2">
                        <a href="{{ route('admin.addnewadmin') }}" class="btn btn-outline-primary btn-fw btn-sm">
                            <i class="mdi mdi-account-plus btn-icon-prepend"></i> Add New Admin</a>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table ">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Mobile</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (count($adminlist) > 0)
                                @foreach ($adminlist as $key=>$admin)
                                <tr>
                                    <td>{{ $key+1 }}</td>
                                    <td>
                                        <p>
                                            @if(!empty($admin->profile))
                                                <img src="{{ $admin->profile }}" alt="image">
                                            @else
                                                <img src="{{ asset('assets/images/faces/blankuser.png') }}" alt="image">
                                            @endif
                                            &nbsp; <b>{{ $admin->name }}</b>
                                        </p>
                                    </td>
                                    <td>{{ $admin->phone }}</td>
                                    <td>{{ $admin->email }}</td>

                                    <td>
                                        <p>
                                            <div class="d-flex flex-row justify-content-start">
                                                <a href="{{ Route('admin.EditadminPage',[$admin->id]) }}" class="btn btn-outline-warning btn-sm mr-1"  data-toggle="tooltip" data-placement="top" title="Edit admin">
                                                    <i class="mdi mdi-pencil" aria-hidden="true"></i> Edit
                                                </a>
                                                @if($admin->is_super == 0)
                                                    @if ($admin->is_active == '1')
                                                        <form action="{{ Route('admin.adminstatus') }}" method="POST">
                                                            @csrf
                                                            <input type="hidden" name="admin_id" value="{{ $admin->id }}">
                                                            <input type="hidden" name="is_active" value="0">
                                                            <button class="btn btn-outline-success btn-sm ml-1"  data-toggle="tooltip" data-placement="top" title="Active admin">
                                                                <i class="mdi mdi-lock-open" aria-hidden="true"></i> Active
                                                            </button>
                                                        </form>
                                                    @else
                                                        <form action="{{  Route('admin.adminstatus') }}" method="POST">
                                                            @csrf
                                                            <input type="hidden" name="admin_id" value="{{ $admin->id }}">
                                                            <input type="hidden" name="is_active" value="1">
                                                            <button type="submit" class="btn btn-outline-secondary ml-1 btn-sm"  data-toggle="tooltip" data-placement="top" title="Inactive admin">
                                                                <i class="mdi mdi-lock" aria-hidden="true"></i> Inactive
                                                            </button>
                                                        </form>
                                                    @endif
                                                    {{-- <form action="{{  Route('admin.adminstatus') }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="admin_id" value="{{ $admin->id }}">
                                                        <input type="hidden" name="is_active" value="2">
                                                        <button type="submit" class="btn btn-outline-danger ml-1 btn-sm"
                                                        data-toggle="tooltip" data-placement="top" title="Delete admin">
                                                            <i class="mdi mdi-delete" aria-hidden="true"></i> Delete
                                                        </button>
                                                    </form> --}}
                                                @endif
                                            </div>
                                        </p>
                                    </td>
                                </tr>
                                @endforeach
                            @else
                                <tr><td colspan="6"><center>No Record Found</center></td></tr>
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

@section('pagescript')

@endsection
