@extends('layouts.dashboard_layout')

@section('pagecss')
<style>
    .modal-lg {
        max-width: 50% !important;
    }
</style>
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
              <li class="breadcrumb-item"><a href="{{ Route('admin.sellerrequest') }}">Seller Request Lists</a></li>
              <li class="breadcrumb-item active" aria-current="page">Seller</li>
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
        <div class="col-lg-5 grid-margin stretch-card">
            @include('dashboard.commonly_used.seller_profile_card')
        </div>
        <div class="col-lg-7 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6>Seller Categories</h6>
                    <hr>
                    @if(count($seller_categories) > 0)
                        <div class="d-flex justify-content-start">
                            @foreach ($seller_categories as $seller_cat)
                            <span class="badge badge-pill badge-info mx-2">{{ $seller_cat->name }}</span>
                            @endforeach
                        </div>
                    @else
                        <p class="text-center">No Categories Selected</p>
                    @endif

                    <h6 class="mt-5">Seller Details</h6>
                    <hr>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap"
                            data-toggle="tooltip" data-placement="top" title="Contact Person Name">
                            <h6 class="mb-0">
                                <i class="mdi mdi-briefcase" aria-hidden="true"></i><span class="text-secondary"> Contact Person Name
                                </span></h6>
                            <span class="font-weight-bold">
                                {{ $seller_det->sellername }}
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap"
                            data-toggle="tooltip" data-placement="top" title="Business Category">
                            <h6 class="mb-0">
                                <i class="mdi mdi-lan" aria-hidden="true"></i><span class="text-secondary"> Business Category
                                </span></h6>
                            <span class="font-weight-bold">
                                {{ $seller_det->seller_buss_type }}
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap"
                            data-toggle="tooltip" data-placement="top" title="Seller Area">
                            <h6 class="mb-0">
                                <i class="mdi mdi-lan" aria-hidden="true"></i><span class="text-secondary"> Area </span>
                            </span></h6>
                            <span class="font-weight-bold">
                                {{ $seller_det->sellerarea }}
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap"
                            data-toggle="tooltip" data-placement="top" title="City">
                            <h6 class="mb-0">
                                <i class="mdi mdi-lan" aria-hidden="true"></i><span class="text-secondary"> City
                                </span></h6>
                            <span class="font-weight-bold">
                                {{ $seller_det->city_name }}
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap"
                            data-toggle="tooltip" data-platcement="top" title="Seller Requested Date">
                            <h6 class="mb-0">
                                <i class="mdi mdi-calendar-today" aria-hidden="true"></i><span class="text-secondary"> Requested Date
                                </span></h6>
                            <span class="font-weight-bold">
                                {{ $seller_det->created_at }}
                            </span>
                        </li>
                    </ul>
                    {{-- <hr class="my-6" size="3"> --}}
                    <div class="border border-2 mt-5 shadow border-primary rounded shadow shadow-1 bg-white p-3">
                        <h4 class="card-title mb-2">Seller Request Approval Form</h4>
                        <form action="{{  Route('admin.SellerApproval') }}" method="POST" class="form-sample">
                            @csrf
                            <input type="hidden" name="sellerid" value="{{ $seller_det->id }}">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Membership</label>
                                <div class="col-sm-4">
                                    <div class="form-check">
                                    <label class="form-check-label">
                                        <input type="radio" class="form-check-input check_approval_status" name="sellermembership" id="membershipRadios1" value="1"> Approve <i class="input-helper"></i></label>
                                    </div>
                                </div>
                                <div class="col-sm-5">
                                    <div class="form-check">
                                    <label class="form-check-label">
                                        <input type="radio" class="form-check-input check_approval_status" name="sellermembership" id="membershipRadios2" value="2"> Reject <i class="input-helper"></i></label>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="commission" value="1">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Remarks</label>
                                <div class="col-sm-9">
                                    <textarea class="form-control" name="actionremarks" id="exampleTextarea1" rows="4" required></textarea>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-gradient-primary btn-sm">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="ViewLicenceModel" tabindex="-1" aria-labelledby="ViewLicenceModelLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ViewLicenceModelLabel"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="ViewLicencePdf">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('pagescript')
<script src="{{ asset('assets/js/seller.js') }}"></script>
<script>
    // $(document).ready(function(){
    //     $("#commission_field").hide();
    //     $("body").on("click",".check_approval_status",function(){
    //         approval_type = $(this).val();
    //         if(approval_type == 1){
    //             $("#commission_field").show();
    //         }
    //         else{
    //             $("#commission_field").hide();
    //         }
    //     });
    // });
</script>
@endsection
