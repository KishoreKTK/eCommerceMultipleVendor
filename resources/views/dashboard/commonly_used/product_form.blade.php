<input type="hidden" id="current_page_type" value="{{ session()->get('login_type') }}">
@php
    $page_type = session()->get('login_type');
@endphp
<section id="basic_product_details_section">
    <p class="card-description mt-3"> Basic Details</p>
    <hr size="3">

    <input type="hidden" name="is_featured" value="0">

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="exampleFormControlInput1">Name</label>
                <input type="text" name="name" required value="{{ old('name') }}" class="form-control"  id="exampleFormControlInput1"attributes>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label for="my-input">Product Display Image</label>
                <input id="my-input" required name="image" class="form-control" type="file" accept="image/*">
                <small class="form-text text-muted">Upload Only JPG | JPEG. Max allowed size is 2MB</small>
            </div>
        </div>

        @if ($page_type == "admin")
            <div class="col-md-6">
                <div class="form-group">
                    <label for="exampleFormControlSelect1">Select Category</label>
                    <select class="form-control" name="category_id" id="category_field_id" required>
                        <option value="">Please Select Category</option>
                        @foreach ($categorylist as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        @else
            <div class="col-md-6">
                <div class="form-group">
                    <label for="exampleFormControlSelect1">Select Category</label>
                    <select class="form-control" name="category_id" id="category_field_id" required>
                        <option value="">Please Select Category</option>
                        @foreach ($categorylist as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        @endif

        <div class="col-md-6">
            <div class="form-group">
                <label for="exampleFormControlTextarea1">Short Bio (in 30 Letters)</label>
                <input type="text" name="short_bio" value="{{ old('short_bio') }}" required class="form-control"  id="exampleFormControlInput12" attributes>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label for="exampleFormControlTextarea1">Description</label>
                <textarea class="form-control" id="exampleFormControlTextarea1" required name="description" required value="{{ old('description') }}" rows="3"></textarea>
            </div>
        </div>

        @if ($page_type == "admin")
            <div class="col-md-6" id="seller_category_id">
                <div class="form-group">
                    <label for="seller_based_on_cat">Select Seller</label>
                    <select class="form-control" name="seller_id" id="seller_based_on_cat" required>
                        <option value="">Please Select Seller</option>
                        @foreach ($sellerlist as $seller)
                        <option value="{{ $seller->id }}">{{ $seller->seller_full_name_buss }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        @else
            <input type="hidden" name="seller_id" value="{{ session()->get('seller_id') }}">
        @endif
    </div>


    <p class="card-description mt-3"> Additional Images</p>
    <hr size="3">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <input id="banner_images" class="form-control mt-2" type="file" name="banner[]" value="" multiple required accept="image/*">
                <small id="emailHelp" class="form-text text-muted">Select Multiple images. Max allowed upto 5 Images</small>
                <small class="form-text text-muted">Upload Only JPG | JPEG. Max allowed size is 2MB</small>
            </div>
        </div>
    </div>

    {{-- <p class="card-description mt-3"> Product Type</p>
    <hr size="3">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="exampleFormControlInput1">Price</label>
                <input type="number" name="starndard_price" value="{{ old('starndard_price') }}" class="form-control" required id="exampleFormControlInput1"attributes>
            </div>
        </div>
    </div> --}}



    <p class="card-description mt-3"> Product Stocks</p>
    <hr size="3">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="exampleFormControlInput1">Base Price</label>
                <input type="number" name="starndard_price" value="{{ old('starndard_price') }}" class="form-control" required id="exampleFormControlInput1"attributes>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label for="exampleFormControlInput1">Total Quantity</label>
                <input type="text" class="form-control" name="total_qty"  value="{{ old('total_qty') }}" required id="exampleFormControlInput1"attributes>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label for="exampleFormControlInput1">Minimum Order Quantity</label>
                <input type="text" class="form-control" name="min_order_qty"  value="{{ old('min_order_qty') }}" required id="exampleFormControlInput1"attributes>
            </div>
        </div>


        <div class="col-md-6">
            <div class="form-group">
                <label for="exampleFormControlInput531">Processing time (In Days)</label>
                <input type="number" class="form-control" name="processing_time" min="0" max="30" value="{{ old('processing_time') }}" required id="exampleFormControlInput531"attributes>
                <small class="form-text text-muted">If Out of Stock Please Enter the Time Limit Needed Restock this Product</small>
            </div>
        </div>
    </div>
    {{--
    <p class="card-description mt-3"> Product Specifications</p>
    <hr size="3">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="exampleFormControlInput1">Price</label>
                <input type="number" name="starndard_price" value="{{ old('starndard_price') }}" class="form-control" required id="exampleFormControlInput1"attributes>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label for="exampleFormControlInput1">Qunantities</label>
                <input type="text" class="form-control" name="total_qty"  value="{{ old('total_qty') }}" required id="exampleFormControlInput1"attributes>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label for="exampleFormControlInput1">Minimum Order Quantity</label>
                <input type="text" class="form-control" name="min_order_qty"  value="{{ old('min_order_qty') }}" required id="exampleFormControlInput1"attributes>
            </div>
        </div>


        <div class="col-md-6">
            <div class="form-group">
                <label for="exampleFormControlInput531">Processing time (In Days)</label>
                <input type="number" class="form-control" name="processing_time" min="0" max="30" value="{{ old('processing_time') }}" required id="exampleFormControlInput531"attributes>
                <small class="form-text text-muted">If Out of Stock Please Enter the Time Limit Needed Recreate this Product</small>
            </div>
        </div>
    </div>

    <p class="card-description mt-3"> Product Options</p>
    <hr size="3">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="exampleFormControlInput1">Price</label>
                <input type="number" name="starndard_price" value="{{ old('starndard_price') }}" class="form-control" required id="exampleFormControlInput1"attributes>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label for="exampleFormControlInput1">Qunantities</label>
                <input type="text" class="form-control" name="total_qty"  value="{{ old('total_qty') }}" required id="exampleFormControlInput1"attributes>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label for="exampleFormControlInput1">Minimum Order Quantity</label>
                <input type="text" class="form-control" name="min_order_qty"  value="{{ old('min_order_qty') }}" required id="exampleFormControlInput1"attributes>
            </div>
        </div>


        <div class="col-md-6">
            <div class="form-group">
                <label for="exampleFormControlInput531">Processing time (In Days)</label>
                <input type="number" class="form-control" name="processing_time" min="0" max="30" value="{{ old('processing_time') }}" required id="exampleFormControlInput531"attributes>
                <small class="form-text text-muted">If Out of Stock Please Enter the Time Limit Needed Recreate this Product</small>
            </div>
        </div>
    </div>

    <p class="card-description mt-3"> Product Stocks</p>
    <hr size="3">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="exampleFormControlInput1">Price</label>
                <input type="number" name="starndard_price" value="{{ old('starndard_price') }}" class="form-control" required id="exampleFormControlInput1"attributes>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label for="exampleFormControlInput1">Qunantities</label>
                <input type="text" class="form-control" name="total_qty"  value="{{ old('total_qty') }}" required id="exampleFormControlInput1"attributes>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label for="exampleFormControlInput1">Minimum Order Quantity</label>
                <input type="text" class="form-control" name="min_order_qty"  value="{{ old('min_order_qty') }}" required id="exampleFormControlInput1"attributes>
            </div>
        </div>


        <div class="col-md-6">
            <div class="form-group">
                <label for="exampleFormControlInput531">Processing time (In Days)</label>
                <input type="number" class="form-control" name="processing_time" min="0" max="30" value="{{ old('processing_time') }}" required id="exampleFormControlInput531"attributes>
                <small class="form-text text-muted">If Out of Stock Please Enter the Time Limit Needed Recreate this Product</small>
            </div>
        </div>
    </div> --}}


    <div class="row">
        <div class="col-md-12">
            <button type="submit" class="float-right btn btn-gradient-primary mr-2 submit_products_form">Submit</button>
        </div>
    </div>

</section>
