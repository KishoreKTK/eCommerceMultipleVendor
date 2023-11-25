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
              <li class="breadcrumb-item"><a href="{{ Route('admin.SellerList') }}">Seller Lists</a></li>
              <li class="breadcrumb-item active" aria-current="page">Create New Seller</li>
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
                <div class="card-body justify-content-center">
                    <h4 class="card-title">Create New Seller </h4>
                    <form class="pt-3" action="{{ route('admin.SellerCreate') }}" method="post" autocomplete="off" enctype="multipart/form-data">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @csrf

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label" >Upload Seller Logo</label><br>
                            <div class="col-sm-9">
                            <input type="file"  class="form-control" name="SellerProfile" accept="image/*" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label" for="exampleFormControlInput2">Business Name</label>
                            <div class="col-sm-9">
                                <input type="name" class="form-control" id="exampleFormControlInput2" name="seller_full_name_buss" value="{{ old('seller_full_name_buss') }}" placeholder="Business Full Name">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label" for="exampleFormControlInput2">Contact Name</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="exampleFormControlInput2" name="sellername" value="{{ old('sellername') }}" placeholder="Contact Person Name">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label" for="exampleFormControlInput1">Email address</label>
                            <div class="col-sm-9">
                                <input type="email" class="form-control" id="exampleFormControlInput1" name="selleremail" value="{{ old('selleremail') }}" placeholder="Contact Email Address">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label" for="exampleFormControlInput2">Contact Number</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="exampleFormControlInput2" name="mobile" value="{{ old('mobile') }}" placeholder="Contact Number">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label" for="exampleFormControlSelect1">Business Types</label>
                            <div class="col-sm-9">
                            <select class="form-control" name="seller_buss_type" id="exampleFormControlSelect1">
                                <option value="">Please Select</option>
                                <option>Public Limited Company</option>
                                <option>Private Limited Company</option>
                                <option>Joint-Venture Company</option>
                                <option>Partnership Firm</option>
                                <option>One Person Company</option>
                                <option>Sole Proprietorship</option>
                                <option>Branch Office</option>
                                <option>Non-Government Organization (NGO)</option>
                            </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label" for="exampleFormControlSelect1">Select Category</label>
                            <div class="col-sm-9">
                                <select class="form-control select_multiple" name="category_id[]" id="exampleFormControlSelect1" size="8" multiple>
                                    @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                <span class="text-danger">@error('category_id'){{ $message }}@enderror</span>
                                <small class="form-text text-muted">You can Select Multiple Categories</small>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Upload Trade License</label>
                            <div class="col-sm-9">
                            <input type="file"  class="form-control" name="seller_trade_license" accept="application/pdf,application/vnd.ms-excel" required>
                            <small class="form-text text-muted">Upload Only in PDF format</small>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Trade License Expiry Date</label>
                            <div class="col-sm-9">
                            <input type="date" class="form-control" value="{{ old('seller_trade_exp_dt') }}" required name="seller_trade_exp_dt" id="bday">
                            </div>
                        </div>

                        <input type="hidden" name="commission" value="1">
                        {{-- <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Commission</label>
                            <input type="text" class="form-control" name="commission" value="{{ old('commission') }}" placeholder="Commission" readonly>
                        </div> --}}

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label" for="exampleFormControlInput2">Address</label>
                            <div class="col-sm-9">
                            <input type="text" class="form-control" id="exampleFormControlInput2" name="sellerarea" value="{{ old('sellerarea') }}" placeholder="Seller Area">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label" for="exampleFormControlemirates">Select UAE City</label>
                            <div class="col-sm-9">
                                <select class="form-control" name="seller_city" id="exampleFormControlemirates">
                                    <option value="">Please Select</option>
                                    @foreach ($uae_cities as $emirate)
                                    <option value="{{ $emirate->id }}">{{ $emirate->city }}</option>
                                    @endforeach
                                    {{-- <option>Ajman</option>
                                    <option>Fujairah</option>
                                    <option>Sharjah</option>
                                    <option>Dubai</option>
                                    <option>Ras al-Khaimah</option>
                                    <option>Umm al-Quwain</option> --}}
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label" for="exampleFormControlInput2">Shop Latitude</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="exampleFormControlInput2" name="latitude" required value="{{ old('latitude') }}" placeholder="Please Enter Shop Latitude">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label" for="exampleFormControlInput2">Shop Longitude</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="exampleFormControlInput2" name="longitude" required value="{{ old('longitude') }}" placeholder="Please Enter Shop Longitude">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Shop Banners</label><br>
                            <div class="col-sm-9">
                                <input type="file"  class="form-control" name="seller_banner_images[]" accept="image/png, image/gif, image/jpeg" multiple required>
                                <small class="form-text text-muted">Select Upto 5 Images</small>
                                <small class="form-text text-muted">Upload Only JPG | JPEG. Max allowed size is 2MB</small>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label" for="exampleFormControlTextarea1">Short Description about Business</label>
                            <div class="col-sm-9">
                            <textarea class="form-control" name="sellerabout" required id="exampleFormControlTextarea1" rows="4" value="{{ old('sellerabout')}}"></textarea>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-gradient-primary float-right">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('pagescript')
<script>
    $(document).ready(function(){
        selectRefresh();

        function selectRefresh() {
            $(".select_multiple").select2({placeholder: "Select a Category",});
        }
    });
</script>
@endsection
