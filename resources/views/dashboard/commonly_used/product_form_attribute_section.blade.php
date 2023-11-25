@if(count($attributelist) > 0)
    @foreach ($attributelist as $key=>$attribute)
        <div class="col-md-6 col-sm-12">
            @if(count($attribute['custom_attr']) > 0)
                @foreach ($attribute['custom_attr'] as $e=>$item)
                <div class="form-group">
                    <label for="">{{ $attribute['name'] }}</label>
                    <input type="hidden" name="product_attr[{{ $key }}][sub_attr_id]" value="{{ $item->id }}">
                    <input type="hidden" name="product_attr[{{ $key }}][attribute_id]" value="{{ $item->attr_id }}">
                    <input type="text" name="product_attr[{{ $key }}][custom_values]" class="form-control form_attr_fields" value="" placeholder="" aria-describedby="helpId_{{ $item->id }}">
                    <small id="helpId_{{ $item->id }}" class="form-text text-muted">Hint:{{ $item->summary }} </small>
                </div>
                @endforeach
            @endif

            @if (count($attribute['sub_attr']) > 0)
                <div class="form-group">
                    <label for="">{{ $attribute['name'] }}</label>
                    {{-- <input type="hidden" name="product_attr[sub_attr][{{ $key }}][sub_attribute_id]" value="{{ $item->id }}"> --}}
                    <input type="hidden" name="product_attr[{{ $key }}][attribute_id]" value="{{  $attribute['id'] }}">
                    <select class="form-control my_select_box" name="product_attr[{{ $key }}][sub_attr_id][]">
                         {{-- multiple size="8"> --}}
                        @foreach ($attribute['sub_attr']  as $key=>$sub_attr)
                        <option value="{{ $sub_attr->id }}">{{ Str::upper($sub_attr->sub_attr_name) }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
        </div>
    @endforeach
@else
    <div class="col-md-12 col-sm-12">
        <p class="text-center">No Specifications Available. You Can Continue to Add Stocks</p>
        <input type="hidden" name="product_attr[]" value="">
    </div>
@endif




<div class="row" id="option_dynamic_values">
    @if(count($attributelist) > 0)
        @foreach ($attributelist as $key=>$attribute)
            <div class="col-md-6 col-sm-12">
                @if(count($attribute['custom_attr']) > 0)
                    @foreach ($attribute['custom_attr'] as $e=>$item)
                    <div class="form-group">
                        <label for="">{{ $attribute['name'] }}</label>
                        <input type="hidden" name="product_attr[{{ $key }}][sub_attr_id]" value="{{ $item->id }}">
                        <input type="hidden" name="product_attr[{{ $key }}][attribute_id]" value="{{ $item->attr_id }}">
                        <input type="text" name="product_attr[{{ $key }}][custom_values]" class="form-control form_attr_fields" value="" placeholder="" aria-describedby="helpId_{{ $item->id }}">
                        <small id="helpId_{{ $item->id }}" class="form-text text-muted">Hint:{{ $item->summary }} </small>
                    </div>
                    @endforeach
                @endif
                @if (count($attribute['sub_attr']) > 0)
                    <div class="form-group">
                        <label for="">{{ $attribute['name'] }}</label>
                        {{-- <input type="hidden" name="product_attr[sub_attr][{{ $key }}][sub_attribute_id]" value="{{ $item->id }}"> --}}
                        <input type="hidden" name="product_attr[{{ $key }}][attribute_id]" value="{{  $attribute['id'] }}">
                        <select class="form-control my_select_box" name="product_attr[{{ $key }}][sub_attr_id][]">
                            @foreach ($attribute['sub_attr']  as $key=>$sub_attr)
                            <option value="{{ $sub_attr->id }}">{{ Str::upper($sub_attr->sub_attr_name) }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
            </div>
        @endforeach
    @else
        <div class="col-md-12 col-sm-12">
            <p class="text-center">No Options Available. You Can Continue to Add Stocks</p>
        </div>
    @endif
</div>



