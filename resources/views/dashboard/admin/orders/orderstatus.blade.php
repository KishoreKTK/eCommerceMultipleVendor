@extends('layouts.dashboard_layout')
@section('pagecss')


@endsection
@section('content')
<div class="content-wrapper">
    <div class="page-header">
        <h3 class="page-title">
            <a href="{{ Route('admin.home') }}"><span class="page-title-icon bg-gradient-primary text-white mr-2">
                <i class="mdi mdi-home"></i>
            </span></a>Order Managment
        </h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ Route('admin.home') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Orders Status</li>
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
        <div class="col-md-4 grid-margin">
            <div class="card">
              <div class="card-body">
                <h4 class="card-title">Add Order Status</h4>
                <form class="forms-sample" action="{{ route('admin.OrderStatusCreate') }}" method="post" autocomplete="off">
                    @csrf
                    <div class="form-group">
                        <label for="exampleInputUsername1">Name</label>
                        <input type="text" class="form-control" id="exampleInputUsername1" name="name" placeholder="Status Name" value="">
                    </div>
                    <div class="form-group">
                        <label for="exampleFormControlSelect1">Needed Images</label>
                        <select class="form-control" name="pic_to_verify" id="pic_to_verify">
                            <option value="0">No</option>
                            <option value="1">Yes</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="exampleFormControlSelect1">Shown in Update List</label>
                        <select class="form-control" name="show_n_list" id="show_n_list">
                            <option value="1">yes</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                    {{-- <div class="form-group">
                        <label for="exampleFormControlSelect1">Status Color</label>
                        <select class="form-control" name="show_n_list" id="show_n_list">
                            @foreach ($ColorCode as $key=>$color)
                                <option class="badge badge-{{ $color }}" value="{{ $key }}">{{ $color }}</option>
                            @endforeach
                        </select>
                    </div> --}}
                    <button type="submit" class="btn btn-gradient-primary mr-2">Submit</button>
                </form>
              </div>
            </div>
        </div>
        <div class="col-md-8 grid-margin stretch-card">
            <div class="card">
              <div class="card-body">
                <h4 class="card-title">Order Status List</h4>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                          <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Needs Image</th>
                            <th>Show in List</th>
                            <th>Status</th>
                            <th>Action</th>
                          </tr>
                        </thead>
                        <tbody>
                            @if (count($OrderStatusList)>0)
                                @foreach ($OrderStatusList as $key=>$status)
                                <tr>
                                    <td>{{ $key+1 }}</td>
                                    <td>{{ $status->name }}</td>
                                    <td>
                                        @if ($status->pic_to_verify == '1')
                                        <label class="badge badge-success">Yes</label>
                                        @else
                                        <label class="badge badge-danger">No</label>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($status->show_n_list == '1')
                                        <label class="badge badge-success">Yes</label>
                                        @else
                                        <label class="badge badge-danger">No</label>
                                        @endif
                                    </td>
                                    {{-- <td>
                                        @if ($status->active_status == '1')
                                        <label class="badge badge-success">Active</label>
                                        @else
                                        <label class="badge badge-danger">InActive</label>
                                        @endif
                                    </td> --}}
                                    {{-- <td>
                                        <div class="d-flex flex-row justify-content-center">
                                            <button class="btn btn-outline-warning btn-sm mr-2">
                                                <i class="mdi mdi-account-key" aria-hidden="true"></i>
                                            </button>
                                            <form action="#" method="POST">
                                                <input type="hidden" name="_token" value="Gys4Ux0r58EKpnKg7psDSuIhd2gBmeYZNusjWVkf">                                                <input type="hidden" name="product_id" value="33">
                                                <input type="hidden" name="active_status" value="0">
                                                <button class="btn btn-outline-danger btn-sm">
                                                    <i class="mdi mdi mdi-lock" aria-hidden="true"></i>
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


@endsection

@section('pagescript')

@endsection
