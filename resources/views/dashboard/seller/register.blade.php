
@extends('layouts.login_layout')
@section('form_title', 'Seller Register')

@section('form_content')
<h6 class="text-center">Request New Seller Membership?</h6>
{{-- <h6 class="font-weight-light">Signing Up is Easy, Just Send us fill form correctly and Send Login Requests</h6> --}}
<form class="pt-3" action="{{ route('seller.create') }}" method="post" autocomplete="off" enctype="multipart/form-data">
    @if (Session::get('success'))
        <div class="alert alert-success">
            {{ Session::get('success') }}
        </div>
    @endif
    @if (Session::get('fail'))
    <div class="alert alert-danger">
        {{ Session::get('fail') }}
    </div>
    @endif
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

    <div class="form-group">
        <label for="exampleFormControlInput2">Business Name <span class="text-primary"> *</span></label>
        <input type="text" class="form-control" id="exampleFormControlInput2" required name="seller_full_name_buss" value="{{ old('seller_full_name_buss') }}" placeholder="Business Name">
    </div>

    <div class="form-group">
        <label for="exampleFormControlInput2">Contact Person Name <span class="text-primary"> *</span></label>
        <input type="text" class="form-control" id="exampleFormControlInput2" required name="sellername" value="{{ old('sellername') }}" placeholder="Contact Person Name">
    </div>

    <div class="form-group">
        <label for="exampleFormControlInput1">Email address <span class="text-primary"> *</span></label>
        <input type="email" class="form-control" id="exampleFormControlInput1" required name="selleremail" value="{{ old('selleremail') }}" placeholder="Enter Email Address">
    </div>

    <div class="form-group">
        <label for="exampleFormControlInput2">Contact Number <span class="text-primary"> *</span></label>
        <input type="text" class="form-control mobile-nub" id="exampleFormControlInput2" required name="mobile" value="{{ old('mobile') }}" placeholder="Contact Number">
    </div>

    <div class="form-group">
        <label for="exampleFormControlInput3">Password <span class="text-primary"> *</span></label>
        <input type="password" class="form-control" id="exampleFormControlInput3" required name="password" value="{{ old('password') }}" placeholder="Password">
    </div>

    <div class="form-group">
        <label for="exampleFormControlInput4">Confirm Password <span class="text-primary"> *</span></label>
        <input type="password" class="form-control" id="exampleFormControlInput4"required name="password_confirmation" value="{{ old('password_confirmation') }}" placeholder="Confirm Password">
    </div>

    <div class="form-group">
        <label for="exampleFormControlSelect1">Business Type <span class="text-primary"> *</span></label>
        <select class="form-control" name="seller_buss_type" required id="exampleFormControlSelect1">
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


    <div class="form-group">
        <label for="exampleFormControlSelect1">Select Category <span class="text-primary"> *</span></label>
        <select class="form-control select_multiple" required name="category_id[]" id="exampleFormControlSelect1" size="8" multiple>
            @foreach ($categories as $category)
            <option value="{{ $category->id }}">{{ $category->name }}</option>
            @endforeach
        </select>
        <span class="text-danger">@error('category_id'){{ $message }}@enderror</span>
        <small class="form-text text-muted">You can Select Multiple Categories</small>
    </div>


    <div class="form-group">
        <label>Upload Trade License</label><br>
        <input type="file" class="form-control" name="seller_trade_license" accept="application/pdf,application/vnd.ms-excel">
        <small class="form-text text-muted">Upload Only Pdf File</small>
    </div>

    <div class="form-group">
        <label>Trade License Expiry Date </label>
        <input type="date" class="form-control" value="{{ old('seller_trade_exp_dt') }}"  name="seller_trade_exp_dt" id="bday">
    </div>

    <div class="form-group">
        <label>Seller Profile Image <span class="text-primary"> *</span></label><br>
        <input type="file" class="form-control" name="SellerProfile" accept="image/png, image/gif, image/jpeg" required>
    </div>

    <div class="form-group">
        <label  for="exampleFormControlInput2">Address <span class="text-primary"> *</span></label>
        <input type="text" class="form-control" id="exampleFormControlInput2" required name="sellerarea" value="{{ old('sellerarea') }}" placeholder="Seller Area">
    </div>

    <div class="form-group">
        <label for="exampleFormControlemirates">Select UAE City <span class="text-primary"> *</span></label>
        <select class="form-control" name="seller_city" required id="exampleFormControlemirates">
            <option value="">Please Select</option>
            @foreach ($uae_cities as $emirate)
            <option value="{{ $emirate->id }}">{{ $emirate->city }}</option>
            @endforeach
        </select>
    </div>

    {{-- <div class="form-group">
        <label for="exampleFormControlInput2">Shop Latitude</label>
        <input type="text" class="form-control" id="exampleFormControlInput2" name="latitude" required value="{{ old('latitude') }}" placeholder="Please Enter Shop Latitude">
    </div>

    <div class="form-group">
        <label for="exampleFormControlInput2">Shop Longitude</label>
        <input type="text" class="form-control" id="exampleFormControlInput2" name="longitude" required value="{{ old('longitude') }}" placeholder="Please Enter Shop Longitude">
    </div> --}}

    <div class="form-group">
        <label for="exampleFormControlTextarea1">Short Description</label>
        <textarea class="form-control" name="sellerabout" id="exampleFormControlTextarea1" rows="3" value="{{ old('sellerabout')}}"></textarea>
    </div>




        {{-- <div class="form-check"> --}}
            <label class="form-check-label text-muted">
            <input type="checkbox" class="form-check-input" id="NeedsToBeChecked"> I agree to all <a href="{{ url('/App/seller-terms-and-conditions') }}" target="_blank">Terms & Conditions</a>.</label>
        {{-- </div> --}}
    </div>
    <div class="mt-3">
        <button class="btn btn-block btn-gradient-primary btn-lg font-weight-medium auth-form-btn" disabled id="submitform">SIGN UP</button>
    </div>
    <div class="text-center mt-4 font-weight-light">
        Already have an account? <a href="{{ route('seller.login') }}" class="text-primary">Login</a>
    </div>
</form>
@endsection

@section('pagescript')
<script>
    $(document).ready(function(){
        selectRefresh();
        function selectRefresh() {
            $(".select_multiple").select2({placeholder: "Select a Category",});
        }
        $('.mobile-nub').keyup(function()
        {
            if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'')
        });

        $('#NeedsToBeChecked').click(function() {
            if ($(this).is(':checked')) {
                $('#submitform').removeAttr('disabled');
            } else {
                $('#submitform').attr('disabled', 'disabled');
            }
        });

    });
</script>
@endsection
