@php
$routeName = \Request::route()->getName();
@endphp

<div class="d-flex mb-4 justify-content-end">
    <ul class="nav nav-tabs card-header-tabs" id="seller-detailed-list" role="tablist">
        <li class="nav-item">
            <a class="btn btn-outline-primary nav-link active" href="#sellerdetails" role="tab" aria-controls="sellerdetails" aria-selected="true">Seller Details</a>
        </li>
        <li class="nav-item">
            <a class="btn btn-outline-primary nav-link" href="#sellerbanners" role="tab" aria-controls="sellerbanners" aria-selected="true">
                @if (strpos($routeName, 'admin.') === 0)
                    Categories & Banners
                @else
                    Banners
                @endif
            </a>
        </li>
        <li class="nav-item">
            <a class="btn btn-outline-primary nav-link" href="#ordersettings" role="tab" aria-controls="ordersettings" aria-selected="true">Order Settings</a>
        </li>
    </ul>
</div>
<div class="tab-content mt-3">
    <div class="tab-pane active" id="sellerdetails" role="tabpanel" aria-labelledby="sellerdetails-tab">
        <h4 class="card-title">Edit Seller Details</h4>
        <form class="pt-3"
            @if (strpos($routeName, 'admin.') === 0)
            action="{{ route('admin.UpdateSellerDetail') }}"
            @else
            action="{{ route('seller.UpdateSellerDetail') }}"
            @endif

            method="post" autocomplete="off" enctype="multipart/form-data">
            <div class="border rounded border-info bg-white mb-2 py-4 px-3">

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
                    <label class="col-sm-3 col-form-label" >Upload Seller Logo</label><br>
                    <div class="col-sm-9">
                    <input type="file"  class="form-control" name="SellerProfile" accept="image/*" >
                    <img class="img-thumbnail my-2" src="{{ $seller_det->sellerprofile }}" alt="" width="100px">
                    <small class="form-text text-muted">Upload Only JPG | JPEG. Max allowed size is 2MB</small>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-3 col-form-label" for="exampleFormControlInput2">Business Name</label>
                    <div class="col-sm-9">
                        <input type="name" class="form-control" id="exampleFormControlInput2" required name="seller_full_name_buss" value="{{ $seller_det->seller_full_name_buss }}" placeholder="Business Full Name">
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-3 col-form-label" for="exampleFormControlInput2">Contact Name</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" id="exampleFormControlInput2" required name="sellername" value="{{ $seller_det->sellername }}" placeholder="Contact Person Name">
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-3 col-form-label" for="exampleFormControlInput1">Email address</label>
                    <div class="col-sm-9">
                        <input type="email" class="form-control" id="exampleFormControlInput1" required name="selleremail" value="{{ $seller_det->selleremail }}" placeholder="Contact Email Address">
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-3 col-form-label" for="exampleFormControlInput2">Contact Number</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control mobile-nub" id="exampleFormControlInput2" required name="mobile" value="{{ $seller_det->mobile }}" placeholder="Contact Number">
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-3 col-form-label" for="exampleFormControlSelect1">Business Types</label>
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
                    <label class="col-sm-3 col-form-label" for="exampleFormControlInput2">Address</label>
                    <div class="col-sm-9">
                    <input type="text" class="form-control" id="exampleFormControlInput2" name="sellerarea" value="{{ $seller_det->sellerarea }}" placeholder="Seller Area">
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-3 col-form-label" for="exampleFormControlemirates">Select UAE City</label>
                    <div class="col-sm-9">
                        <select class="form-control" name="emirates" id="exampleFormControlemirates">
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
            </div>

            <div class="form-group float-right">
                <button type="submit" class="btn btn-gradient-primary">Submit</button>
            </div>
        </form>

    </div>

    <div class="tab-pane" id="sellerbanners" role="tabpanel" aria-labelledby="sellerbanners-tab">


        @if (strpos($routeName, 'admin.') === 0)
            <h4 class="card-title">Categories</h4>
            <div class="border rounded border-info bg-white mb-4 py-4 px-3">
                <form action=" {{ Route('admin.SellerCatUpdate') }}" method="POST">
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

        <h4 class="card-title">Banners</h4>
        <div class="border rounded border-secondary bg-white mb-4 py-4 px-3">
            <div class="row">
                @if (count($seller_banners) == 0)
                    <div class="col-lg-12 mb-3 d-flex align-items-stretch">
                        <form
                            @if (strpos($routeName, 'admin.') === 0)
                            action="{{ Route('admin.UpdateBannerImage') }}"
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
                                action="{{ Route('admin.UpdateBannerImage') }}"
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

    <div class="tab-pane" id="ordersettings" role="tabpanel" aria-labelledby="ordersettings-tab">
        <div class="row">
            <div class="col-md-6 col-sm-12  grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-start">
                        <h4 class="card-title">Order Settings</h4>
                        </div>
                        <hr>
                        <form
                            @if (strpos($routeName, 'admin.') === 0)
                            action="{{ Route('admin.UpdateOrderSettings') }}"
                            @else
                            action="{{ Route('seller.UpdateOrderSettings') }}"
                            @endif
                            method="POST">
                            @csrf
                            <input type="hidden" name="seller_id" value="{{ $seller_det->id }}">
                            <div class="border rounded border-info bg-white p-3">
                                <div class="form-group row">
                                    <label class="col-sm-6 col-form-label" for="exampleFormControlTextarea1">Pick Up Availability</label>
                                    <div class="col-sm-3">
                                        <div class="form-check">
                                        <label class="form-check-label">
                                            <input type="radio" class="form-check-input "
                                                name="pickup" id="membershipRadios1" value="1" {{ ($seller_det->pickup=="1")? "checked" : "" }}> Yes <i class="input-helper"></i><i class="input-helper"></i></label>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-check">
                                        <label class="form-check-label">
                                            <input type="radio" class="form-check-input "
                                                name="pickup" id="membershipRadios2" value="0" {{ ($seller_det->pickup=="0")? "checked" : "" }}> No <i class="input-helper"></i><i class="input-helper"></i></label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-6 col-form-label" for="exampleFormControlTextarea1">Delivery Availability</label>
                                    <div class="col-sm-3">
                                        <div class="form-check">
                                        <label class="form-check-label">
                                            <input type="radio" class="form-check-input"
                                                name="delivery" id="membershipRadios11" value="1" {{ ($seller_det->delivery=="1")? "checked" : "" }}> Yes <i class="input-helper"></i><i class="input-helper"></i></label>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-check">
                                        <label class="form-check-label">
                                            <input type="radio" class="form-check-input "
                                                name="delivery" id="membershipRadios2211" value="0" {{ ($seller_det->delivery=="0")? "checked" : "" }}> No <i class="input-helper"></i><i class="input-helper"></i></label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-6 col-form-label" for="exampleFormControlTextarea31">Cash on Delivery / Pickup</label>
                                    <div class="col-sm-3">
                                        <div class="form-check">
                                        <label class="form-check-label">
                                            <input type="radio" class="form-check-input"
                                                name="cash_on_delivery" id="membershipRadios131" value="1" {{ ($seller_det->cash_on_delivery=="1")? "checked" : "" }}> Yes <i class="input-helper"></i><i class="input-helper"></i></label>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-check">
                                        <label class="form-check-label">
                                            <input type="radio" class="form-check-input "
                                                name="cash_on_delivery" id="membershipRadios22311" value="0" {{ ($seller_det->cash_on_delivery=="0")? "checked" : "" }}> No <i class="input-helper"></i><i class="input-helper"></i></label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-6 col-form-label" for="exampleFormControlTextarea1">Tax Deduction</label>
                                    <div class="col-sm-3">
                                        <div class="form-check">
                                        <label class="form-check-label">
                                            <input type="radio" class="form-check-input "
                                                name="tax_handle" id="membershipRadios111" value="1" {{ ($seller_det->tax_handle=="1")? "checked" : "" }}> Yes <i class="input-helper"></i><i class="input-helper"></i></label>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
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
                                <div class="d-flex justify-content-end">
                                    <button class="btn btn-inverse-primary btn-sm" type="submit">Update</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-sm-12  grid-margin stretch-card">
                <div class="card">
                    <div class="card-body" id="shippingCharge">
                        <div class="d-flex justify-content-between">
                            <h4 class="card-title">Shipping Charges</h4>
                            <p>Seller City : <span class="text-primary" data-toggle="tooltip"
                                data-placement="top" title="Seller City">{{ $seller_det->city_name }}</span></p>
                        </div>
                        <hr>
                        <div class="border rounded border-info bg-white p-3">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>To City</th>
                                            <th width="25%">Fees</th>
                                            <th width="16%">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <form
                                                @if (strpos($routeName, 'admin.') === 0)
                                                action="{{ Route('admin.AddSellerShippingDetails') }}"
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
                                        @foreach ($seller_shipping_det as $shippingdet)
                                        <tr>
                                            <td>{{ $shippingdet->to_city_name }}</td>
                                            <td>{{ $shippingdet->fees }} AED</td>
                                            <td>
                                                <form
                                                    @if (strpos($routeName, 'admin.') === 0)
                                                    action="{{ Route('admin.DeleteSellerShippingDetails') }}"
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
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                        {{--  map  --}}

                    <div class="card-body"  id="chooseLocation">
                        <form class="pt-3"
                        @if (strpos($routeName, 'admin.') === 0)
                        action="{{ route('admin.UpdateSellerLocation') }}"
                        @else
                        action="{{ route('seller.UpdateSellerLocation') }}"
                        @endif

                        method="post" autocomplete="off" enctype="multipart/form-data">
                        @csrf

                        <div class="d-flex justify-content-between">
                            <h4 class="card-title">Choose Pickup Address</h4>
                            {{--  <p>Seller City : <span class="text-primary" data-toggle="tooltip"
                                data-placement="top" title="Seller City">{{ $seller_det->city_name }}</span></p>  --}}
                        </div>
                        <hr>

                        <div class="border rounded border-info bg-white p-3">
                            <div class="table-responsive">
                                <div class="form-group col-md-10">
                            <input type="text" name="address" id="address-input" value="" class="form-control map-input"/>

                        </div>

                        <input type="hidden" name="latitude" id="address-latitude" value="0" />
                        <input type="hidden" name="longitude" id="address-longitude" value="0" />
                        </div>

                                <div id="address-map-container" style="width:100%;height:400px; ">
                                    <div style="width: 100%; height: 100%" id="address-map"></div>
                                </div>
                                <br>
                                <div class="d-flex justify-content-end">
                            <input type="hidden" name="seller_id" value="{{ $seller_det->id }}">

                                    <button class="btn btn-inverse-primary btn-sm" type="submit">Submit</button>
                                </div>
                            </div>

                        </div>
                    </div>

                        {{--  map  --}}
            </div>
        </div>


    </div>
</div>

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
