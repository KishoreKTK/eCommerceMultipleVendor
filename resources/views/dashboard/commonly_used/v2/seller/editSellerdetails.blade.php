
@extends('layouts.dashboard_layout')
@php
    $routeName = \Request::route()->getName();
    if (strpos($routeName, 'admin.') === 0) {
    $page_type = 'admin';
    } else {
    $page_type = 'seller';
    }
@endphp
@section('pagecss')
    <link rel="stylesheet"  href="{{ asset('assets/vendors/OwlCorousal/owl.carousel.min.css') }}"/>
    <link rel="stylesheet"  href="{{ asset('assets/vendors/OwlCorousal/owl.theme.default.min.css') }}"/>
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
                    <li class="breadcrumb-item"><a href="{{ Route(''.$page_type.'.home') }}">Dashboard</a></li>
                    @if($page_type == 'admin')
                    <li class="breadcrumb-item"><a href="{{ Route(''.$page_type.'.SellerList') }}">Seller Lists</a></li>
                    @endif
                    <li class="breadcrumb-item active" aria-current="page">Edit</li>
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
                        <div class="d-flex mb-4 justify-content-end">
                            <a class="btn btn-outline-primary nav-link active" href="#">Seller Details</a>
                            <a class="nav-link text-primary" href="{{ Route(''.$page_type.'.EditSellerOrderSettingsPage',[$seller_det->id]) }}" >Order Settings</a>
                        </div>
                    
                        <div class="tab-content mt-3">
                            <div class="tab-pane active" id="sellerdetails" role="tabpanel" aria-labelledby="sellerdetails-tab">
                                <div class="border rounded border-info bg-white mb-2 py-4 px-3">
                                    <h4 class="card-title">Edit Seller Details</h4>
                                    <form class="pt-3"
                                        @if (strpos($routeName, 'admin.') === 0)
                                        action="{{ Route(''.$page_type.'.UpdateSellerDetail') }}"
                                        @else
                                        action="{{ route('seller.UpdateSellerDetail') }}"
                                        @endif
                                        method="post" autocomplete="off" enctype="multipart/form-data">
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
                                            <input type="hidden" name="seller_id" value="{{ $seller_det->id }}">                   
                                            <div class="form-group row">
                                                <label class="col-sm-3 col-form-label" for="exampleFormControlInput2">Business Name</label>
                                                <div class="col-sm-9">
                                                    <input type="name" class="form-control" id="exampleFormControlInput2" readonly required name="seller_full_name_buss" value="{{ $seller_det->seller_full_name_buss }}" placeholder="Business Full Name">
                                                </div>
                                            </div>
                        
                        
                                            <div class="form-group row">
                                                <label class="col-sm-3 col-form-label" for="exampleFormControlInput1" >Email Id</label>
                                                <div class="col-sm-9">
                                                    <input type="email" class="form-control" id="exampleFormControlInput1" readonly required name="selleremail" value="{{ $seller_det->selleremail }}" placeholder="Contact Email Address">
                                                </div>
                                            </div>
                        
                                            <div class="form-group row">
                                                <label class="col-sm-3 col-form-label" for="exampleFormControlInput2" >Contact Number</label>
                                                <div class="col-sm-9">
                                                    <input type="text" class="form-control mobile-nub" id="exampleFormControlInput2" readonly required name="mobile" value="{{ $seller_det->mobile }}" placeholder="Contact Number">
                                                </div>
                                            </div>
                        
                                            <div class="form-group row">
                                                <label class="col-sm-3 col-form-label" for="exampleFormControlInput2">Contact Person Name</label>
                                                <div class="col-sm-9">
                                                    <input type="text" class="form-control" id="exampleFormControlInput2" required name="sellername" value="{{ $seller_det->sellername }}" placeholder="Contact Person Name">
                                                </div>
                                            </div>

                                           
                                            <div class="form-group row">
                                                <label class="col-sm-3 col-form-label" for="exampleFormControlSelect1">Business Type</label>
                                                <div class="col-sm-9">
                                                    <select class="form-control" name="seller_buss_type" id="exampleFormControlSelect1">
                                                        <option value="">Please Select</option>
                                                        <option @if($seller_det->seller_buss_type == "Public Limited Company") selected @endif>Public Limited Company</option>
                                                        <option @if($seller_det->seller_buss_type == "Private Limited Company") selected @endif>Private Limited Company</option>
                                                        <option @if($seller_det->seller_buss_type == "Joint-Venture Company") selected @endif>Joint-Venture Company</option>
                                                        <option @if($seller_det->seller_buss_type == "Partnership Firm") selected @endif>Partnership Firm</option>
                                                        <option @if($seller_det->seller_buss_type == "One Person Company") selected @endif>One Person Company</option>
                                                        <option @if($seller_det->seller_buss_type == "Sole Proprietorship") selected @endif>Sole Proprietorship</option>
                                                        <option @if($seller_det->seller_buss_type == "Branch Office") selected @endif>Branch Office</option>
                                                        <option @if($seller_det->seller_buss_type == "Non-Government Organization (NGO)") selected @endif>Non-Government Organization (NGO)</option>
                                                    </select>
                                                </div>
                                            </div>
                        
                                            <div class="form-group row">
                                                <label class="col-sm-3 col-form-label" >Upload Seller Logo</label><br>
                                                <div class="col-sm-9">
                                                <input type="file"  class="form-control" name="SellerProfile" accept="image/*" >
                                                <img class="img-thumbnail my-2" src="{{ $seller_det->sellerprofile }}" alt="" width="100px">
                                                <small class="form-text text-muted">Upload Only JPG | JPEG. Max allowed size is 2MB</small>
                                                </div>
                                            </div>
                                            {{-- <div class="form-group row">
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
                                            </div> --}}
                        
                                            <div class="form-group row">
                                                <label class="col-sm-3 col-form-label">Upload Trade License</label>
                                                <div class="col-sm-9">
                                                <input type="file"  class="form-control" name="seller_trade_license" accept="application/pdf,application/vnd.ms-excel" >
                                                <small class="form-text text-muted">Upload Only in PDF format | Max allowed size is 2MB</small>
                                                <a href="{{ $seller_det->seller_trade_license }}" class="mt-2 text-italic" target="_blank" rel="noopener noreferrer">View Trade License</a>
                                                </div>
                                            </div>
                        
                                            <div class="form-group row">
                                                <label class="col-sm-3 col-form-label">Trade License Expiry Date</label>
                                                <div class="col-sm-9">
                                                <input type="date" class="form-control" value="{{ $seller_det->seller_trade_exp_dt }}"  name="seller_trade_exp_dt" id="bday">
                                                </div>
                                            </div>
                        
                                            <input type="hidden" name="commission" value="1">
                                            {{-- <div class="form-group row">
                                                <label class="col-sm-3 col-form-label">Commission</label>
                                                <input type="text" class="form-control" name="commission" value="{{ $seller_det->commission') }}" placeholder="Commission" readonly>
                                            </div> --}}
                        
                                            <div class="form-group row">
                                                <label class="col-sm-3 col-form-label" for="exampleFormControlInput2">Business Address</label>
                                                <div class="col-sm-9">
                                                    <input type="text" class="form-control" id="exampleFormControlInput2" name="sellerarea" value="{{ $seller_det->sellerarea }}" placeholder="Seller Area">
                                                </div>
                                            </div>
                        
                                            <div class="form-group row">
                                                <label class="col-sm-3 col-form-label" for="exampleFormControlemirates">City</label>
                                                <div class="col-sm-9">
                                                    <select class="form-control" name="seller_city" id="exampleFormControlemirates">
                                                        <option value="">Please Select</option>
                                                        @foreach ($uae_cities as $emirate)
                                                        <option value="{{ $emirate->id }}" @if($seller_det->seller_city == $emirate->id) selected @endif>{{ $emirate->city }}</option>
                                                        @endforeach
                                                        {{-- <option @if($seller_det->emirates == "Sheikhs of Abu Dhabi") selected @endif>Sheikhs of Abu Dhabi</option>
                                                        <option @if($seller_det->emirates == "Ajman") selected @endif>Ajman</option>
                                                        <option @if($seller_det->emirates == "Fujairah") selected @endif>Fujairah</option>
                                                        <option @if($seller_det->emirates == "Sharjah") selected @endif>Sharjah</option>
                                                        <option @if($seller_det->emirates == "Dubai") selected @endif>Dubai</option>
                                                        <option @if($seller_det->emirates == "Ras al-Khaimah") selected @endif>Ras al-Khaimah</option>
                                                        <option @if($seller_det->emirates == "Umm al-Quwain") selected @endif>Umm al-Quwain</option> --}}
                                                    </select>
                                                </div>
                                            </div>
                        
                        
                                            {{-- <div class="form-group row">
                                                <label class="col-sm-3 col-form-label" for="exampleFormControlInput2">Shop Latitude</label>
                                                <div class="col-sm-9">
                                                    <input type="text" class="form-control" id="exampleFormControlInput2" name="latitude" required value="{{ $seller_det->latitude }}" placeholder="Please Enter Shop Latitude">
                                                </div>
                                            </div>
                        
                                            <div class="form-group row">
                                                <label class="col-sm-3 col-form-label" for="exampleFormControlInput2">Shop Longitude</label>
                                                <div class="col-sm-9">
                                                    <input type="text" class="form-control" id="exampleFormControlInput2" name="longitude" required value="{{ $seller_det->longitude }}" placeholder="Please Enter Shop Longitude">
                                                </div>
                                            </div> --}}
                        
                                            <div class="form-group row">
                                                <label class="col-sm-3 col-form-label" for="exampleFormControlTextarea1">Short Description about Business</label>
                                                <div class="col-sm-9">
                                                <textarea class="form-control" name="sellerabout" required id="exampleFormControlTextarea1" rows="4">{{ $seller_det->sellerabout }}</textarea>
                                                </div>
                                            </div>
                        
                                            <div class="form-group row">
                                                <div class="col-sm-12">
                                                    <button type="submit" class="btn btn-gradient-primary float-right">Submit</button>
                                                </div>
                                            </div>
                                    </form>
                                </div>
                                @if (strpos($routeName, 'admin.') === 0)
                                    <div class="border rounded border-info bg-white my-4 py-4 px-3">
                                        <h4 class="card-title">Categories</h4>
                                        <form action=" {{ Route(''.$page_type.'.SellerCatUpdate') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="seller_id" value="{{ $seller_det->id }}">
                                            <div class="form-group row">
                                                <label class="col-sm-3 col-form-label" for="exampleFormControlSelect1">Select Category</label>
                                                <div class="col-sm-6">
                                                    <select class="form-control select_multiple" name="category_id[]" id="exampleFormControlSelect1" size="8" multiple>
                                                        @foreach ($categories as $category)
                                                        <option value="{{ $category->id }}"
                                                            @if ($category->seller_cat == true)
                                                            selected
                                                            @endif>{{ $category->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    <span class="text-danger">@error('category_id'){{ $message }}@enderror</span>
                                                    <small class="form-text text-muted">You can Select Multiple Categories</small>
                                                </div>
                                                <div class="col-sm-3">
                                                    <button type="submit" class="btn btn-primary">Update</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <hr size="2">
                                @endif
                                <div class="border rounded border-secondary bg-white mb-4 py-4 px-3">
                                    <h4 class="card-title">Banners</h4>
                                    <div class="row">
                                        @if (count($seller_banners) == 0)
                                            <div class="col-lg-12 mb-3 d-flex align-items-stretch">
                                                <form
                                                    @if (strpos($routeName, 'admin.') === 0)
                                                    action="{{ Route(''.$page_type.'.UpdateBannerImage') }}"
                                                    @else
                                                    action="{{ Route('seller.UpdateBannerImage') }}"
                                                    @endif
                                                    method="POST" enctype="multipart/form-data">
                                                    @csrf
                                                    <input type="hidden" name="form_type" value="new">
                                                    <input type="hidden" name="seller_id" value="{{  $seller_det->id }}">
                                                    <div class="row">
                                                        <div class="col-3">
                                                            <label>Shop Banners</label>
                                                        </div>
                                                        <div class="col-6">
                                                            <input type="file"  class="form-control" name="seller_banner_images[]" accept="image/png, image/gif, image/jpeg" multiple >
                                                            <small class="form-text text-muted">Select Upto 5 Images</small>
                                                        </div>
                                                        <div class="col-3">
                                                            <button class="btn btn-inverse-success">Upload</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        @else
                                        @foreach ($seller_banners as $bannerimg)
                                            <div class="col-lg-4 mb-3 d-flex align-items-stretch">
                                                <div class="card">
                                                    <form
                                                        @if (strpos($routeName, 'admin.') === 0)
                                                        action="{{ Route(''.$page_type.'.UpdateBannerImage') }}"
                                                        @else
                                                        action="{{ Route('seller.UpdateBannerImage') }}"
                                                        @endif
                                                        method="POST" enctype="multipart/form-data">
                                                        @csrf
                                                        <input type="hidden" name="banner_id" value="{{ $bannerimg->id }}">
                                                        <input type="hidden" name="form_type" value="existing">
                                                        <input type="hidden" name="seller_id" value="{{  $bannerimg->shop_id }}">
                                                        <img src="{{ $bannerimg->image_urls }}" height="250" class="card-img-top" alt="Banner Image">
                                                        <div class="card-body d-flex flex-column">
                                                            <input name="banner_image" required type="file" id="formFile">
                                                            <button class="btn mt-3 align-self-center btn-inverse-primary">Update</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('pagescript')
    <script src="{{ asset('assets/js/seller.js') }}"></script>
    <script src="{{ asset('assets/vendors/OwlCorousal/owl.carousel.min.js') }}"></script>
    <script>
        $(document).ready(function(){
            selectRefresh();

            function selectRefresh() {
                $(".select_multiple").select2({placeholder: "Select a Category",});
            }
        });
    </script>
    <script>
        var lat = {!! $latitude !!};
            var long = {!! $longitude !!};
            console.log(lat);
        function initialize() {


        $('form').on('keyup keypress', function (e) {
            var keyCode = e.keyCode || e.which;
            if (keyCode === 13) {
                e.preventDefault();
                return false;
            }
        });
        const locationInputs = document.getElementsByClassName("map-input");

        const autocompletes = [];
        const geocoder = new google.maps.Geocoder;
        for (let i = 0; i < locationInputs.length; i++) {

            const input = locationInputs[i];
            const fieldKey = input.id.replace("-input", "");
            const isEdit = document.getElementById(fieldKey + "-latitude").value != '' && document.getElementById(
                fieldKey + "-longitude").value != '';

            const latitude = parseFloat(document.getElementById(fieldKey + "-latitude").value) || lat;
            const longitude = parseFloat(document.getElementById(fieldKey + "-longitude").value) || long;


            const map = new google.maps.Map(document.getElementById(fieldKey + '-map'), {
                center: {
                    lat: latitude,
                    lng: longitude
                },
                zoom: 13
            });
            // console.log(long);

            const marker = new google.maps.Marker({
                map: map,
                position: {
                    lat: latitude,
                    lng: longitude
                },
            });

            marker.setVisible(isEdit);

            const autocomplete = new google.maps.places.Autocomplete(input);
            autocomplete.key = fieldKey;
            autocompletes.push({
                input: input,
                map: map,
                marker: marker,
                autocomplete: autocomplete
            });
        }

        for (let i = 0; i < autocompletes.length; i++) {
            const input = autocompletes[i].input;
            const autocomplete = autocompletes[i].autocomplete;
            const map = autocompletes[i].map;
            const marker = autocompletes[i].marker;

            google.maps.event.addListener(autocomplete, 'place_changed', function () {
                marker.setVisible(false);
                const place = autocomplete.getPlace();

                geocoder.geocode({
                    'placeId': place.place_id
                }, function (results, status) {
                    if (status === google.maps.GeocoderStatus.OK) {
                        const lat = results[0].geometry.location.lat();
                        const lng = results[0].geometry.location.lng();
                        setLocationCoordinates(autocomplete.key, lat, lng);
                    }
                });

                if (!place.geometry) {
                    window.alert("No details available for input: '" + place.name + "'");
                    input.value = "";
                    return;
                }

                if (place.geometry.viewport) {
                    map.fitBounds(place.geometry.viewport);
                } else {
                    map.setCenter(place.geometry.location);
                    map.setZoom(17);
                }
                marker.setPosition(place.geometry.location);
                marker.setVisible(true);

            });
        }
        }

        function setLocationCoordinates(key, lat, lng) {
        const latitudeField = document.getElementById(key + "-" + "latitude");
        const longitudeField = document.getElementById(key + "-" + "longitude");
        latitudeField.value = lat;
        longitudeField.value = lng;
        }

    </script>
    <script>
        $(document).ready(function(){
            var pickup = {!! $seller_det->pickup !!};
            var delivery = {!! $seller_det->delivery !!};
            console.log(pickup);
            console.log(delivery);
            if(pickup==1)
            {
                $("#chooseLocation").show();
            }
            else{
                $("#chooseLocation").hide();
            }

            $("#shippingCharge").hide();

            $("#membershipRadios1").click(function(){
                $("#chooseLocation").show();
            });
            $("#membershipRadios2").click(function(){
                $("#chooseLocation").hide();
                $("#shippingCharge").hide();

            });

            $("#membershipRadios11").click(function(){
                $("#chooseLocation").hide();
                $("#shippingCharge").show();

            });
            $("#membershipRadios2211").click(function(){
                $("#shippingCharge").hide();
                $("#chooseLocation").hide();
            });
        });
    </script>
@endsection