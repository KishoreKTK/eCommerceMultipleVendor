@extends('layouts.dashboard_layout')
@section('pagecss')


@endsection
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
              <li class="breadcrumb-item active" aria-current="page">Seller Request Lists</li>
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
                    <h4 class="card-title">Seller Requests</h4>
                    <div class="table-responsive">
                        <table class="table ">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Profile</th>
                                <th>Business Name</th>
                                {{-- <th>Name</th> --}}
                                <th>Mail</th>
                                <th>Mobile</th>
                                <th>Business Type</th>
                                <th>Requested Date</th>
                                <th>Approve</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (count($new_sellers) >0)
                            @foreach ($new_sellers as $key=>$seller)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td><img src="{{ asset($seller->sellerprofile) }}" alt="sellerimage"></td>
                                <td>{{ $seller->seller_full_name_buss }}</td>
                                {{-- <td>{{ $seller->sellername }}</td> --}}
                                <td><a href = "mailto: {{ $seller->selleremail }}" class="badge badge-success">{{ $seller->selleremail }}</a></td>
                                <td><a href="tel:+{{ $seller->mobile }}" class="badge badge-dark">{{ $seller->mobile }}</a></td>
                                <td>{{ $seller->seller_buss_type }}</td>
                                <td>
                                    {{ $seller->created_at }}
                                </td>
                                <td>
                                    <a href="{{ route('admin.VerifySeller', ['sellerid'=>$seller->id]) }}"
                                        class="btn btn-outline-danger btn-icon-text btn-sm">
                                        <i class="mdi mdi-account-convert" aria-hidden="true"></i> Verify
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                            @else
                            <tr><td colspan="8"><center>No Requests Found</center></td></tr>
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
