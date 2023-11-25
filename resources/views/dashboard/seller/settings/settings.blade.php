@extends('layouts.dashboard_layout')
@php
    $seller_id = Auth::guard('seller')->user()->id;
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
            </span> Shop Management
        </h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="{{ Route('seller.home') }}">Dashboard</a></li>
              <li class="breadcrumb-item active" aria-current="page">Settings</li>
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
    @if ($message = Session::get('fail'))
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
                    @include('dashboard.commonly_used.editSellerdetails')
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
@endsection
