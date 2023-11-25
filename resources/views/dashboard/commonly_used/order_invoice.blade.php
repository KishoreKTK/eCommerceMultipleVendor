<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>{{ config('app.name') }} Admin</title>

        <!-- plugins:css -->
        <link rel="stylesheet" href=" {{ asset('assets/vendors/mdi/css/materialdesignicons.min.css') }}">
        <link rel="stylesheet" href=" {{ asset('assets/vendors/css/vendor.bundle.base.css') }}">

        <link rel="stylesheet" href=" {{ asset('assets/css/style.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">

        <!-- End layout styles -->
        <link rel="shortcut icon" href="{{ asset('assets/images/starling_logo.svg') }}" />
        <link href="{{ asset('assets/images/starling_logo.svg') }}" rel="icon" />
    </head>
    <body>
        <div class="container">
            <div class="card">

                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <img src="{{ asset('assets/images/starling_logo.svg') }}" width="150" alt="">
                        <table>
                            <tbody>
                                <tr>
                                    <td class="left">Order Id</td>
                                    <td class="right"><b>{{ $order_det['order']->order_id }}</b></td>
                                </tr>
                                <tr>
                                    <td class="left">Order Date</td>
                                    <td class="right"><b>{{ $order_det['order']->created_at }}</b></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="row mt-5 mb-4">
                        <div class="col-sm-6">
                            <h6 class="mb-3">From:</h6>
                            <div>
                            <strong>{{ $order_det['shop_address']->shopname }}</strong>
                            </div>
                            <div>{{ $order_det['shop_address']->sellerarea }}</div>
                            <div>{{ $order_det['shop_address']->city }}, UAE</div>
                            <div>Email: {{ $order_det['shop_address']->selleremail }}</div>
                            <div>Phone: {{ $order_det['shop_address']->mobile }}</div>
                        </div>
                        <div class="col-sm-6">
                            <h6 class="mb-3">To:</h6>
                            <div>
                            <strong>{{ $order_det['address']->first_name }}</strong>
                            </div>
                            <div>{{ $order_det['address']->address }}</div>
                            <div>{{ $order_det['address']->city }}, UAE</div>
                            <div>Email: {{ $order_det['user_det']->email }}</div>
                            <div>Phone: +{{ $order_det['address']->phone_num }}</div>
                        </div>
                    </div>
                    <div class="table-responsive-sm">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th class="center">#</th>
                                <th>Item</th>
                                <th>Options</th>
                                <th class="right">Unit Cost</th>
                                <th class="center">Qty</th>
                                <th class="right">Total</th>
                            </tr>
                            </thead>
                            <tbody>
                                @foreach ($order_det['qty_det'] as $e=>$qty)
                                <tr>
                                    <td class="center">{{ $e+1 }}</td>
                                    <td class="left strong">{{ $qty->prod_name }}</td>
                                    <td class="left">
                                        <p>
                                            @foreach ($qty->options as $opt)
                                            {{ $opt->option_name }} - <b>{{ $opt->attr_name }}</b>,
                                            @endforeach
                                        </p>
                                    </td>
                                    <td class="right">{{ $qty->price_per_unit }}</td>
                                    <td class="center">{{ $qty->prod_qty }}</td>
                                    <td class="right">{{ $qty->total_amount }} AED</td>
                                </tr>
                                @endforeach

                            </tbody>
                        </table>
                    </div>
                    <div class="row">
                        <div class="col-lg-4 col-sm-5">
                        </div>
                        <div class="col-lg-4 col-sm-5 ml-auto">
                            <table class="table table-clear">
                                <tbody>
                                    <tr>
                                        <td class="left">
                                            <strong>Shipping Fee</strong>
                                        </td>
                                        <td class="right">{{ $order_det['order']->shipping_charge  }} AED</td>
                                    </tr>
                                    <tr>
                                        <td class="left">
                                        <strong>Tax (5%)</strong>
                                        </td>
                                        <td class="right">{{ $order_det['order']->tax  }} AED</td>
                                    </tr>
                                    <tr>
                                        <td class="left">
                                            <strong>Procesing Fee</strong>
                                        </td>
                                        <td class="right">{{ ($order_det['order']->processing_fee)?$order_det['order']->processing_fee:0 }} AED</td>
                                    </tr>
                                    <tr>
                                        <td class="left">
                                        <strong>Subtotal</strong>
                                        </td>
                                        <td class="right">{{ ($order_det['order']->sub_total)?$order_det['order']->sub_total:0  }} AED</td>
                                    </tr>
                                    <tr>
                                        <td class="left">
                                            <strong>Transaction Fee</strong>
                                        </td>
                                        <td class="right">{{($order_det['order']->transaction_fee)?$order_det['order']->transaction_fee:0   }} AED</td>
                                    </tr>
                                    <tr>
                                        <td class="left">
                                            <strong>Total</strong>
                                        </td>
                                        <td class="right">
                                            <strong>{{ $order_det['order']->grand_total  }} AED</strong>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- plugins:js -->
        <script src="{{ asset('assets/vendors/js/vendor.bundle.base.js') }}"></script>
    </body>
</html>

