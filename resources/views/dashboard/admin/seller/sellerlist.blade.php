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
              <li class="breadcrumb-item active" aria-current="page">Seller Lists</li>
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
                        <div class="p-2 flex-grow-1"><h4 class="card-title">Seller List </h4></div>
                        <div class="p-2">
                            @if(count($sellerlist) > 0)
                                <a href="{{ Route('admin.export-sellers',['status'=>$status,
                                'keyword'=>$keyword]) }}" class="btn btn-outline-dark btn-fw btn-sm">
                                <i class="mdi mdi-download btn-icon-prepend"></i> Download</a>
                            @endif
                            {{-- <a href="#" class="btn btn-outline-secondary btn-fw btn-sm">
                                <i class="mdi mdi-download btn-icon-prepend"></i> Download</a> --}}
                        </div>
                        <div class="p-2">
                            <a href="{{ route('admin.CreateSellerPage') }}" class="btn btn-outline-primary btn-fw btn-sm">
                                <i class="mdi mdi-account-plus btn-icon-prepend"></i> Seller</a>
                        </div>
                    </div>
                    <div class="p-2 my-2 border rouned border-secondary border-2 shadow-2">
                        <p>Filter Seller</p>
                        <form accept="{{ Route('admin.SellerList') }}" method="GET" class="form-inline">
                            <select name="status" class="form-control mx-1 form-control-sm">
                                <option value="" {{ $status == '' ? 'selected' : '' }}>Select Status</option>
                                <option value="1" {{ $status == '1' ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ $status == '0' ? 'selected' : '' }}>Inactive</option>
                                <option value="2" {{ $status == '2' ? 'selected' : '' }}>Deleted</option>
                            </select>
                            <input type="text" name="keyword" class="form-control form-control-sm" value="{{ $keyword }}" id="inlineFormInputName2" placeholder="KeyWords">
                            <button type="submit" class="btn btn-outline-primary btn-fw mx-2 btn-sm">Submit</button>
                            <a href="{{ Route('admin.SellerList') }}" class="btn btn-outline-secondary btn-fw mr-2 btn-sm">Reset</a>
                        </form>
                    </div>
                    <div class="table-responsive">
                        <table class="table ">
                            <thead>
                                <tr>
                                    <th># </th>
                                    <th>Business</th>
                                    <th>Email</th>
                                    <th>Mobile</th>
                                    <th>View</th>

                                    {{-- <th>Trade License & Expiry</th> --}}
                                    {{-- <th>License Expiry Date</th> --}}
                                    @if($status != '2')
                                    <th>Featured</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @if (count($sellerlist) >0)
                                @foreach ($sellerlist as $key=>$seller)
                                <tr>
                                    <td>{{ $key + $sellerlist->firstItem() }}</td>
                                    <td>{{ $seller->seller_full_name_buss }}</td>

                                    <td><a href = "mailto: {{ $seller->selleremail }}" class="badge badge-secondary">{{ $seller->selleremail }}</a></td>
                                    <td><a href="tel:+ {{ $seller->mobile }}" class="badge badge-dark">{{ $seller->mobile }}</a></td>
                                    <td>
                                        <a @if(strtotime($seller->seller_trade_exp_dt) > strtotime('now'))
                                            class="btn btn-outline-info btn-sm"
                                           @else
                                           class="btn btn-outline-danger btn-sm"
                                           @endif
                                           href="{{Route('admin.SellerDetail',[$seller->id])}}">
                                            <i class="mdi mdi-eye" aria-hidden="true"></i>
                                        </a>
                                    </td>
                                    {{-- <td class="text-left">
                                        <p>{{ $seller->sellername }}</p>
                                        <p>
                                            <a href = "mailto: {{ $seller->selleremail }}" class="badge badge-secondary">{{ $seller->selleremail }}</a>
                                            <a href="tel:+ {{ $seller->mobile }}" class="badge badge-dark">{{ $seller->mobile }}</a>
                                        </p>
                                    </td> --}}
                                    {{-- <td class="text-center">
                                            <button type="button" class="btn btn-outline-secondary btn-sm view_trade_license"
                                            data-seller_name="{{ $seller->sellername }}"
                                                data-trade_licenseurl='{{ asset($seller->seller_trade_license) }}'>View License</button>
                                            <p class="mt-2">
                                                @if(strtotime($seller->seller_trade_exp_dt) > strtotime('now'))
                                                <span class="badge badge-pill badge-success" data-toggle="tooltip" data-placement="top" title="Trade License Expiry Date">{{ $seller->seller_trade_exp_dt }}</span>
                                                @else
                                                <span class="badge badge-pill badge-danger" data-toggle="tooltip" data-placement="top" title="Trade License Expiry Date">{{ $seller->seller_trade_exp_dt }}</span>
                                                @endif
                                            </p>
                                    </td> --}}
                                    @if($status != '2')
                                    <td>
                                        @if($seller->is_featured == '1')
                                            <form action="{{ route('admin.featuredsellers') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="seller_id" value="{{ $seller->id }}">
                                                <input type="hidden" name="is_featured" value="0">
                                                <button class="btn btn-outline-danger btn-sm">
                                                    <i class="mdi mdi mdi-star text-warning icon-sm" aria-hidden="true"></i>
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('admin.featuredsellers') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="seller_id" value="{{ $seller->id }}">
                                                <input type="hidden" name="is_featured" value="1">
                                                <button class="btn btn-outline-secondary btn-sm">
                                                    <i class="mdi mdi-star-outline text-secondary icon-sm" aria-hidden="true"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($seller->is_active == '1')
                                        <label class="badge badge-success">Active</label>
                                        @else
                                        <label class="badge badge-danger">InActive</label>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex flex-row">
                                            <a href="{{ Route('admin.EditSellerPage',[$seller->id]) }}" class="btn btn-outline-warning btn-sm mr-1">
                                                <i class="mdi mdi-pencil" aria-hidden="true"></i>
                                            </a>
                                            {{-- <button class="btn btn-outline-warning btn-sm mr-1">
                                                <i class="mdi mdi-account-key" aria-hidden="true"></i>
                                            </button> --}}
                                            @if ($seller->is_active == '1')
                                                <form action="{{ Route('admin.ChangeSellerStatus') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="sellerid" value="{{ $seller->id }}">
                                                    <input type="hidden" name="active_status" value="0">
                                                    <button class="btn btn-outline-secondary btn-sm">
                                                        <i class="mdi mdi mdi-lock" aria-hidden="true"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <form action="{{  Route('admin.ChangeSellerStatus') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="sellerid" value="{{ $seller->id }}">
                                                    <input type="hidden" name="active_status" value="1">
                                                    <button type="submit" class="btn btn-outline-success btn-sm">
                                                        <i class="mdi mdi-lock-open" aria-hidden="true"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            <form action="{{  Route('admin.DeleteSeller') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="sellerid" value="{{ $seller->id }}">
                                                <button type="submit" class="btn btn-outline-danger ml-1 btn-sm">
                                                    <i class="mdi mdi-delete" aria-hidden="true"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                    @endif

                                </tr>
                                @endforeach
                                @else
                                    <tr>
                                        <td colspan="8"><center>No Sellers Found</center></td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                        <div class="mt-2 float-right">
                            @if(count($sellerlist) != 0)
                            {!! $sellerlist->links() !!}
                            @endif
                        </div>
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
<script>
    $(document).ready(function(){
        $("body").on("click",".view_trade_license",function(){
            var sellername      = $(this).attr("data-seller_name");
            var sellerlicence   = $(this).attr("data-trade_licenseurl");
            $("#ViewLicenceModelLabel").html(sellername+' Trade Licence');
            $("#ViewLicencePdf").html('<iframe src="'+sellerlicence+'" frameborder="0" width="700" height="600"></iframe>');
            $("#ViewLicenceModel").modal('show');
        });
    });
</script>


@endsection
