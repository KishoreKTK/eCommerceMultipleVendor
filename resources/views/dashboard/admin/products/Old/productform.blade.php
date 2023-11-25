@extends('layouts.dashboard_layout')
@section('pagecss')


@endsection
@section('content')
<div class="content-wrapper">
    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white mr-2">
                <i class="mdi mdi-home"></i>
            </span> Dashboard / Seller List
        </h3>
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
                <h4 class="card-title">Seller List</h4>
                <table class="table table-bordered">
                <thead>
                    <tr>
                    <th># </th>
                    <th>Seller name </th>
                    <th>Trade License </th>
                    <th>Trade Expiry Date</th>
                    <th>view</th>
                    <th>Edit</th>
                    <th>Change Status</th>
                    </tr>
                </thead>
                <tbody>
                    @if (count($sellerlist) >0)
                    @foreach ($sellerlist as $key=>$seller)
                    <tr>
                        <td>{{ $key + $sellerlist->firstItem() }}</td>
                        <td>{{ $seller->sellername }}</td>
                        <td>
                            <button type="button" class="btn btn-outline-secondary btn-sm view_trade_license" data-seller_name="{{ $seller->sellername }}"
                                data-trade_licenseurl='{{ asset($seller->seller_trade_license) }}'>View License</button>
                        </td>
                        <td>{{ $seller->seller_trade_exp_dt }}</td>
                        <td>
                            <a class="btn btn-outline-info btn-sm" href="{{url('admin/seller/display',[$seller->id])}}">
                                <i class="mdi mdi-eye" aria-hidden="true"></i></a>
                        </td>
                        <td>
                            <a class="btn btn-outline-info btn-sm" href="{{url('admin/seller/display',[$seller->id])}}">
                                <i class="mdi mdi-tooltip-edit" aria-hidden="true"></i></a>
                        </td>
                        <td>
                            @if ($seller->is_active == '1')
                                <form action="{{ Route('admin.SellerApproval') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="sellerid" value="{{ $seller->id }}">
                                    <input type="hidden" name="approval" value="1">
                                    <input type="hidden" name="active" value="1">
                                    <button class="btn btn-outline-warning btn-sm">
                                        <i class="mdi mdi-account-plus" aria-hidden="true"></i>
                                    </button>
                                </form>
                            @else
                                <form action="{{  Route('admin.SellerApproval') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="sellerid" value="{{ $seller->id }}">
                                    <input type="hidden" name="approval" value="1">
                                    <input type="hidden" name="active" value="0">
                                    <button type="submit" class="btn btn-outline-danger btn-sm">
                                        <i class="mdi mdi-account-off" aria-hidden="true"></i>
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                    @else
                    <tr><td colspan="7"><center>No Requests Found</center></td></tr>
                    @endif
                </tbody>
                </table>
            </div>
            </div>
        </div>
    </div>
</div>


@endsection

@section('pagescript')

@endsection
