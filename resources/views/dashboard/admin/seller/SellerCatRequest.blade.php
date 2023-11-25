@extends('layouts.dashboard_layout')

@section('content')
<div class="content-wrapper">
    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white mr-2">
                <i class="mdi mdi-home"></i>
            </span> Seller Management
        </h3>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="{{ Route('admin.home') }}">Dashboard</a></li>
              <li class="breadcrumb-item active" aria-current="page">Seller Category Requests</li>
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
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex mb-2">
                        <div class="p-2 flex-grow-1"><h4 class="card-title">Seller Category Approvals</h4></div>
                    </div>
                    <div class="table-responsive">
                        <table class="table ">
                            <thead>
                                <tr>
                                    <th># </th>
                                    <th>Business</th>
                                    <th>view</th>
                                    <th>Email</th>
                                    <th>Mobile</th>
                                    <th>Categories</th>
                                    <th>Request Type</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (count($seller_category_requests) >0)
                                @foreach ($seller_category_requests as $key=>$seller)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $seller->seller_full_name_buss }}</td>
                                    <td>
                                        <a @if(strtotime($seller->seller_trade_exp_dt) > strtotime('now'))
                                            class="btn btn-outline-info btn-sm"
                                           @else
                                           class="btn btn-outline-danger btn-sm"
                                           @endif
                                           href="{{Route('admin.SellerDetail',[$seller->id])}}" target="_blank">
                                            <i class="mdi mdi-eye" aria-hidden="true"></i>
                                        </a>
                                    </td>
                                    <td>
                                        <a href = "mailto: {{ $seller->selleremail }}" class="badge badge-secondary">{{ $seller->selleremail }}</a>
                                    </td>
                                    <td>
                                        <a href="tel:+ {{ $seller->mobile }}" class="badge badge-dark">{{ $seller->mobile }}</a>
                                    </td>
                                    <td>
                                        <span class="badge badge-pill badge-primary">{{ $seller->cat_name }}</span>
                                    </td>
                                    <td>
                                        @if ($seller->status == '0')
                                        <label class="badge badge-success">Assign</label>
                                        @else
                                        <label class="badge badge-danger">Unassign</label>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex flex-row">
                                            <form action="{{ Route('admin.ApproveSellerCatRequest') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="sellerid" value="{{ $seller->id }}">
                                                <input type="hidden" name="category_id" value="{{ $seller->category_id }}">
                                                <input type="hidden" name="approve_status" value={{ $seller->status }}>
                                                <input type="hidden" name="action_status" value="1">
                                                <button class="btn btn-outline-success btn-sm mx-2">
                                                    <i class="mdi mdi-check" aria-hidden="true"></i>
                                                </button>
                                            </form>

                                            <form action="{{  Route('admin.ApproveSellerCatRequest') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="sellerid" value="{{ $seller->id }}">
                                                <input type="hidden" name="category_id" value="{{ $seller->category_id }}">
                                                <input type="hidden" name="approve_status" value={{ $seller->status }}>
                                                <input type="hidden" name="action_status" value="0">
                                                <button type="submit" class="btn btn-outline-danger btn-sm">
                                                    <i class="mdi mdi-close" aria-hidden="true"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                                @else
                                    <tr>
                                        <td colspan="8"><center>No Requests Found</center></td>
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
