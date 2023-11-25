
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
                        @if ($page_type == 'admin')
                        <a class="nav-link text-primary" href="{{ Route(''.$page_type.'.EditSellerPage',[$seller_det->id]) }}">Seller Details</a>
                        @else    
                        <a class="nav-link text-primary" href="{{ Route(''.$page_type.'.SellerSettings',[$seller_det->id]) }}">Seller Details</a>
                        @endif
                        <a class="btn btn-outline-primary nav-link active " href="#" >Order Settings</a>
                    </div>
                    
                    <div class="row mt-3 justify-content-center">
                        {{-- Delivery Availability Form --}}
                        <div class="col-md-9 col-sm-12  grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <h4 class="card-title">Delivery Availability</h4>
                                        <p>Seller City : <span class="text-primary" data-toggle="tooltip"
                                            data-placement="top" title="Seller City">{{ $seller_det->city_name }}</span></p>
                                    </div>
                                    <hr>
                                    @if ($seller_det->delivery=="1" && count($seller_shipping_det) == 0)
                                    <div class="alert alert-danger" role="alert">
                                        <strong>Note:</strong>
                                        <small>If delivery availabile, Please add shipping fees by city. </small>
                                    </div>                                        
                                    @endif
                                    <div class="border rounded border-secondary bg-white p-3">
                                        <form action="{{ Route(''.$page_type.'.UpdateOrderSettings') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="seller_id" value="{{ $seller_det->id }}">
                                            <div class="form-group my-2 row">
                                                <label class="col-sm-3 col-form-label" for="exampleFormControlemirates">Starting City</label>
                                                <div class="col-sm-9">
                                                    <select class="form-control" name="seller_city" id="exampleFormControlemirates">
                                                        <option value="">Please Select</option>
                                                        @foreach ($uae_cities as $emirate)
                                                        <option value="{{ $emirate->id }}" @if($seller_det->seller_city == $emirate->id) selected @endif>{{ $emirate->city }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group my-2 row">
                                                <label class="col-sm-3 col-form-label" for="exampleFormControlTextarea1">Delivery Availability</label>
                                                <div class="col-sm-2">
                                                    <div class="form-check">
                                                    <label class="form-check-label">
                                                        <input type="radio" class="form-check-input"
                                                            name="delivery" id="membershipRadios11" value="1" {{ ($seller_det->delivery=="1")? "checked" : "" }}> Yes <i class="input-helper"></i><i class="input-helper"></i></label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-2">
                                                    <div class="form-check">
                                                    <label class="form-check-label">
                                                        <input type="radio" class="form-check-input "
                                                            name="delivery" id="membershipRadios2211" value="0" {{ ($seller_det->delivery=="0")? "checked" : "" }}> No <i class="input-helper"></i><i class="input-helper"></i></label>
                                                    </div>
                                                </div> 
                                            </div>  
                                            <div class="row mb-3">
                                                <div class="col-3"></div>
                                                <div class="col">
                                                    <button type="submit" class="btn btn-sm btn-inverse-primary">Update</button>
                                                </div>
                                            </div>
                                        </form>
                                       
                                        <hr size="3" class="tex-dark mt-5 mb-2">
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>To Designated City</th>
                                                        <th width="25%">Fees</th>
                                                        <th width="16%">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <form
                                                            @if (strpos($routeName, 'admin.') === 0)
                                                            action="{{ Route(''.$page_type.'.AddSellerShippingDetails') }}"
                                                            @else
                                                            action="{{ Route('seller.AddSellerShippingDetails') }}"
                                                            @endif
                                                            method="POST">
                                                            @csrf
                                                            <input type="hidden" name="seller_id" value="{{  $seller_det->id }}">
                                                            <input type="hidden" name="from_city" value="{{  $seller_det->seller_city }}">
                                                            <td>
                                                                <select class="form-control" name="to_city" id="exampleFormControlSelect321">
                                                                    <option value="">Select Shipping City</option>
                                                                    @foreach ($uae_cities as $emirate)
                                                                    <option value="{{ $emirate->id }}">{{ $emirate->city }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control mobile-nub" id="exampleFormControlInput1" name="fees" placeholder="Fee">
                                                            </td>
                                                            <td>
                                                                <button class="btn btn-inverse-success btn-sm" type="submit">
                                                                    <i class="mdi mdi-plus"></i>Add
                                                                </button>
                                                            </td>
                                                        </form>
                                                    </tr>
                                                    @if (count($seller_shipping_det)>0)
                                                        @foreach ($seller_shipping_det as $shippingdet)
                                                        <tr>
                                                            <td>{{ $shippingdet->to_city_name }}</td>
                                                            <td>{{ $shippingdet->fees }} AED</td>
                                                            <td>
                                                                <form
                                                                    @if (strpos($routeName, 'admin.') === 0)
                                                                    action="{{ Route(''.$page_type.'.DeleteSellerShippingDetails') }}"
                                                                    @else
                                                                    action="{{ Route('seller.DeleteSellerShippingDetails') }}"
                                                                    @endif
                                                                    method="POST">
                                                                    @csrf
                                                                    <input type="hidden" name="shipping_id" value="{{ $shippingdet->id }}">
                                                                    <button class="btn btn-inverse-danger btn-sm" type="submit">
                                                                        <i class="mdi mdi-delete"></i>Delete
                                                                    </button>
                                                                </form>
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                    @else
                                                        <tr>
                                                            <td class="text-center" colspan="3">No data found</td>
                                                        </tr>
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Pickup Availability form --}}
                        <div class="col-md-9 col-sm-12  grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <form
                                        action="{{ Route(''.$page_type.'.UpdateSellerLocation') }}"
                                        method="post" autocomplete="off" enctype="multipart/form-data">
                                        @csrf
                                        <div class="d-flex justify-content-between">
                                            <h4 class="card-title">Pickup Availability</h4>
                                        </div>
                                        <hr>
                                        <div class="border rounded border-secondary bg-white p-3">
                                            <div class="form-group row">
                                                <label class="col-sm-3 col-form-label" for="exampleFormControlTextarea1">Availability</label>
                                                <div class="col-sm-2">
                                                    <div class="form-check">
                                                    <label class="form-check-label">
                                                        <input type="radio" class="form-check-input "
                                                            name="pickup" id="membershipRadios1" value="1" {{ ($seller_det->pickup=="1")? "checked" : "" }}> Yes <i class="input-helper"></i><i class="input-helper"></i></label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-2">
                                                    <div class="form-check">
                                                    <label class="form-check-label">
                                                        <input type="radio" class="form-check-input "
                                                            name="pickup" id="membershipRadios2" value="0" {{ ($seller_det->pickup=="0")? "checked" : "" }}> No <i class="input-helper"></i><i class="input-helper"></i></label>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label class="col-sm-3 col-form-label" for="exampleFormControlTextarea1">Pickup Contact Number</label>
                                                <div class="col-sm-9">
                                                    <input type="text" class="form-control" id="exampleFormControlInput2" name="pickup_number" value="{{ $seller_det->pickup_number }}" placeholder="Pickup Number">
                                                </div>
                                            </div>
                            
                                            <input type="hidden" name="latitude" id="address-latitude" value="0" />
                                            <input type="hidden" name="longitude" id="address-longitude" value="0" />
                                            <input type="hidden" name="seller_id" value="{{ $seller_det->id }}">
                                            <div class="form-group row">
                                                <label class="col-sm-3 col-form-label" for="exampleFormControlTextarea1">Pickup Address</label>
                                                <div class="col-sm-9">
                                                    <input type="text" name="pickup_address" id="address-input" placeholder="Enter pickup address" value="{{ $seller_det->pickup_address }}" class="form-control map-input"/>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-sm-3 col-form-label" for="exampleFormControlTextarea1">Set Location</label>
                                                <div class="col-sm-9">        
                                                    <div id="address-map-container" style="width:100%;height:400px; ">
                                                        <div style="width: 100%; height: 100%" id="address-map"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <br>
                                            <div class="form-group row">
                                                <label class="col-sm-3 col-form-label" for="exampleFormControlTextarea1"></label>
                                                <div class="col-sm-9">  
                                                    <button class="btn btn-inverse-primary btn-sm" type="submit">Submit</button>
                                                </div>
                                            </div>
                                            {{-- <div class="d-flex justify-content-center">
                                            </div> --}}
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        {{-- Settings Form --}}
                        <div class="col-md-9 mt-2 col-sm-12  grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-start">
                                        <h4 class="card-title">Order Settings</h4>
                                    </div>
                                    <hr>
                                    <form
                                        @if (strpos($routeName, 'admin.') === 0)
                                        action="{{ Route(''.$page_type.'.UpdateOrderSettings') }}"
                                        @else
                                        action="{{ Route('seller.UpdateOrderSettings') }}"
                                        @endif
                                        method="POST">
                                        @csrf
                                        <input type="hidden" name="seller_id" value="{{ $seller_det->id }}">
                                        <div class="border rounded border-secondary bg-white p-3">
                                            <div class="form-group row">
                                                <label class="col-sm-3 col-form-label" for="exampleFormControlTextarea31">Cash on Delivery / Pickup</label>
                                                <div class="col-sm-2">
                                                    <div class="form-check">
                                                    <label class="form-check-label">
                                                        <input type="radio" class="form-check-input"
                                                            name="cash_on_delivery" id="membershipRadios131" value="1" {{ ($seller_det->cash_on_delivery=="1")? "checked" : "" }}> Yes <i class="input-helper"></i><i class="input-helper"></i></label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-2">
                                                    <div class="form-check">
                                                    <label class="form-check-label">
                                                        <input type="radio" class="form-check-input "
                                                            name="cash_on_delivery" id="membershipRadios22311" value="0" {{ ($seller_det->cash_on_delivery=="0")? "checked" : "" }}> No <i class="input-helper"></i><i class="input-helper"></i></label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-sm-3 col-form-label" for="exampleFormControlTextarea1">Tax Deduction</label>
                                                <div class="col-sm-2">
                                                    <div class="form-check">
                                                    <label class="form-check-label">
                                                        <input type="radio" class="form-check-input "
                                                            name="tax_handle" id="membershipRadios111" value="1" {{ ($seller_det->tax_handle=="1")? "checked" : "" }}> Yes <i class="input-helper"></i><i class="input-helper"></i></label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-2">
                                                    <div class="form-check">
                                                    <label class="form-check-label">
                                                        <input type="radio" class="form-check-input "
                                                            name="tax_handle" id="membershipRadios221" value="0" {{ ($seller_det->tax_handle=="0")? "checked" : "" }}> No <i class="input-helper"></i><i class="input-helper"></i></label>
                                                    </div>
                                                </div>
                                            </div>
                                            {{-- <div class="form-group row">
                                                <label class="col-sm-6 col-form-label" for="exampleFormControlTextarea1">Shipping Charges</label>
                                                <div class="col-sm-3">
                                                    <div class="form-check">
                                                    <label class="form-check-label">
                                                        <input type="radio" class="form-check-input "
                                                            name="shipping_charges" id="membershipRadios1111" value="1" {{ ($seller_det->shipping_charges=="1")? "checked" : "" }}> Yes <i class="input-helper"></i><i class="input-helper"></i></label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">
                                                    <div class="form-check">
                                                    <label class="form-check-label">
                                                        <input type="radio" class="form-check-input "
                                                            name="shipping_charges" id="membershipRadios21" value="0" {{ ($seller_det->shipping_charges=="0")? "checked" : "" }}> No <i class="input-helper"></i><i class="input-helper"></i></label>
                                                    </div>
                                                </div>
                                            </div> --}}
                                            <div class="form-group row">
                                                <label class="col-sm-3 col-form-label" for="exampleFormControlTextarea1"></label>
                                                <div class="col-sm-9">
                                                    <button class="btn btn-inverse-primary btn-sm" type="submit">Update</button>
                                                </div>
                                            </div>
                                            {{-- <div class="d-flex justify-content-center">
                                                <button class="btn btn-inverse-primary btn-sm" type="submit">Update</button>
                                            </div> --}}
                                        </div>
                                    </form>
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