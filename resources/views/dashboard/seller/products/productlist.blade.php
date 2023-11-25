
<table class="table ">
    <thead>
        <tr>
            <th># </th>
            <th>Name</th>
            <th>Category</th>
            <th>View</th>
            <th class="col-sm-4">Action</th>
        </tr>
    </thead>
    <tbody>
    @if (count($products) > 0)
        @foreach ($products as $key=>$product)
        <tr>
            <td>{{ $key + $products->firstItem() }}</td>
            <td>
                <p>
                    <img src= "{{ asset($product->image) }}" alt=""> <b>{{ $product->name }}</b>
                </p>
            </td>
            <td>{{ $product->categoryname }}</td>
            <td>
                @if ($product->spec_count == 0 || $product->option_count == 0 || $product->product_combo == 0)
                    <button type="button" class="btn btn-inverse-danger btn-icon">
                        <i class="mdi mdi-eye-off"></i>
                    </button>
                @else
                    <a href="{{ Route('seller.ProductDetails',[$product->id]) }}"
                        class="btn btn-outline-primary btn-sm mr-1"  data-toggle="tooltip"
                        data-placement="top" title="View Product">
                        <i class="mdi mdi-eye" aria-hidden="true"></i>
                    </a>
                @endif
            </td>
            <td>
                <p>
                    <div class="d-flex flex-row justify-content-center">
                        {{-- <a href="{{ Route('seller.ProductSpecificationPage' ,[$product->id]) }}"
                            @if ($product->spec_count == 0)
                                class="btn btn-outline-secondary mx-1 btn-sm">
                            @else
                                class="btn btn-outline-info mx-1 btn-sm">
                            @endif
                            Specification</a>
                        <a href="{{ Route('seller.ProductOptionsPage' ,[$product->id]) }}"
                            @if ($product->option_count == 0)
                                class="btn btn-outline-secondary mx-1 btn-sm">
                            @else
                                class="btn btn-outline-info mx-1 btn-sm">
                            @endif
                            Options</a>
                        <a href="{{ Route('seller.ProductStocksPage', [$product->id]) }}"
                            @if ($product->product_combo == 0)
                                class="btn btn-outline-secondary mx-1 btn-sm">
                            @else
                                class="btn btn-outline-info mx-1 btn-sm">
                            @endif
                            Stocks</a> --}}
                            @if ($product->option_count != 0)
                            <a href="{{ Route('seller.EditProductPage',[$product->id]) }}" class="btn btn-outline-warning btn-sm mr-1"  data-toggle="tooltip" data-placement="top" title="Edit Product">
                                <i class="mdi mdi-pencil" aria-hidden="true"></i> Edit
                            </a>
                            @endif
                            <a href="{{ Route('seller.ProductCutomStock', [$product->id]) }}"
                                @if ($product->product_combo == 0)
                                    class="btn btn-outline-secondary mx-1 btn-sm">
                                @else
                                    class="btn btn-outline-info mx-1 btn-sm">
                                @endif
                            Custom Stock</a>
                    </div>
                </p>
                <p>
                    <div class="d-flex flex-row justify-content-center">
                       
                        @if ($product->status == '1')
                            <form action="{{ Route('seller.productstatus') }}" method="POST">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <input type="hidden" name="active_status" value="0">
                                <button class="btn btn-outline-success btn-sm ml-1"  data-toggle="tooltip" data-placement="top" title="Active Product">
                                    <i class="mdi mdi-lock-open" aria-hidden="true"></i> Active
                                </button>
                            </form>
                        @else
                            <form action="{{  Route('seller.productstatus') }}" method="POST">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <input type="hidden" name="active_status" value="1">
                                <button type="submit" class="btn btn-outline-secondary ml-1 btn-sm"  data-toggle="tooltip" data-placement="top" title="Inactive Product">
                                    <i class="mdi mdi-lock" aria-hidden="true"></i> Inactive
                                </button>
                            </form>
                        @endif
                        <form action="{{  Route('seller.productstatus') }}" method="POST">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <input type="hidden" name="active_status" value="2">
                            <button type="submit" class="btn btn-outline-danger ml-1 btn-sm"
                            data-toggle="tooltip" data-placement="top" title="Delete Product">
                                <i class="mdi mdi-delete" aria-hidden="true"></i> Delete
                            </button>
                        </form>
                    </div>
                </p>

            </td>
        </tr>
        @endforeach
        @else
        <tr>
            <td colspan="7"><center>No Products Found</center></td>
        </tr>
        @endif
    </tbody>
</table>

{{-- <table class="table 
    <thead>
        <tr>
            <th># </th>
            <th>Name</th>
            <th>Category</th>
            <th>View</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @if (count($products) >0)
        @foreach ($products as $key=>$product)
        <tr>
            <td>{{ $key + $products->firstItem() }}</td>
            <td>
                <img src= "{{ asset($product->image) }}" alt=""> {{ $product->name }}
            </td>
            <td>{{ $product->categoryname }}</td>

            <td>
                <a  class="btn btn-outline-info btn-sm"
                    href="{{ Route('seller.ProductDetails',[$product->id]) }}">
                    <i class="mdi mdi-eye" aria-hidden="true"></i>
                </a>
            </td>

            <td>
                @if ($product->status == '1')
                <span class="badge badge-success">Active</span>
                @else
                <span class="badge badge-danger">Inactive</span>
                @endif
            </td>
            <td>
                <div class="d-flex flex-row justify-content-end">
                    <a href="#" class="btn btn-outline-warning btn-sm mr-1"  data-toggle="tooltip" data-placement="top" title="Edit Product">
                        <i class="mdi mdi-pencil" aria-hidden="true"></i>
                    </a>
                    <a href="{{ Route('seller.ProductStocksPage', [$product->id]) }}" target="_blank" class="btn btn-inverse-primary mx-1 btn-sm">Stocks</a>
                    <a href="{{ Route('seller.ProductOptionsPage' ,[$product->id]) }}" target="_blank" class="btn btn-inverse-info mx-1 btn-sm">Options</a>
                    @if ($product->status == '1')
                        <form action="{{ Route('seller.productstatus') }}" method="POST">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <input type="hidden" name="active_status" value="0">
                            <button class="btn btn-outline-success btn-sm ml-1"  data-toggle="tooltip" data-placement="top" title="Active Product">
                                <i class="mdi mdi-lock-open" aria-hidden="true"></i>
                            </button>
                        </form>
                    @else
                        <form action="{{  Route('seller.productstatus') }}" method="POST">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <input type="hidden" name="active_status" value="1">
                            <button type="submit" class="btn btn-outline-secondary ml-1 btn-sm"  data-toggle="tooltip" data-placement="top" title="Inactive Product">
                                <i class="mdi mdi-lock" aria-hidden="true"></i>
                            </button>
                        </form>
                    @endif
                    <form action="{{  Route('seller.productstatus') }}" method="POST">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="hidden" name="active_status" value="1">
                        <button type="submit" class="btn btn-outline-danger ml-1 btn-sm"
                        data-toggle="tooltip" data-placement="top" title="Delete Product">
                            <i class="mdi mdi-delete" aria-hidden="true"></i>
                        </button>
                    </form>
                </div>
            </td>
        </tr>
        @endforeach
        @else
        <tr><td colspan="7"><center>No Products Found</center></td></tr>
        @endif
    </tbody>
</table> --}}

<div class="mt-2 float-right">
    @if(count($products) != 0)
    {!! $products->links() !!}
    @endif
</div>
