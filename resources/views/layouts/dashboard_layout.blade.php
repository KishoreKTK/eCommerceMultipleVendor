<!DOCTYPE html>
<?php
$routeName = \Request::route()->getName();
?>
<html lang="en">

    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name') }}</title>
        <!-- plugins:css -->
        <link rel="stylesheet" href="{{ asset('assets/vendors/mdi/css/materialdesignicons.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/vendors/css/vendor.bundle.base.css') }}">
        <!-- endinject -->

        <!-- Plugin css for this page -->
        <link rel="stylesheet" href="{{ asset('assets/vendors/IziToast/iziToast.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/vendors/select2/select2.min.css') }}">
        <!-- End plugin css for this page -->

        <!-- Layout styles -->
        <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">
        <!-- End layout styles -->
        <link rel="shortcut icon" href="{{ asset('assets/images/starling_logo.svg') }}" />
        <link href="{{ asset('assets/images/starling_logo.svg') }}" rel="icon" />
        <script src="{{ asset('assets/vendors/js/vendor.bundle.base.js') }}"></script>
        @yield('pagecss')

    </head>

    <body>
        <div id="cover-spin"></div>
        <div class="container-scroller">
            <!-- partial:partials/_navbar.html -->
            <nav class="navbar default-layout-navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
                <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
                    @if (strpos($routeName, 'admin.') === 0)
                    <a class="navbar-brand brand-logo" href="{{ route('admin.home') }}"><img src="{{ asset('assets/images/starling_logo.svg') }}" width="80" alt="logo" /></a>
                    <a class="navbar-brand brand-logo-mini" href="{{ route('admin.home') }}"><img src="{{ asset('assets/images/starling_logo.svg') }}" width="80" alt="logo" /></a>
                    @else
                    <a class="navbar-brand brand-logo" href="{{ route('seller.home') }}"><img src="{{ asset('assets/images/starling_logo.svg') }}" width="80" alt="logo" /></a>
                    <a class="navbar-brand brand-logo-mini" href="{{ route('seller.home') }}"><img src="{{ asset('assets/images/starling_logo.svg') }}" width="80" alt="logo" /></a>
                    @endif
                </div>
                <div class="navbar-menu-wrapper d-flex align-items-stretch">
                    <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
                        <span class="mdi mdi-menu"></span>
                    </button>
                    @if (strpos($routeName, 'admin.') === 0)
                        <ul class="navbar-nav navbar-nav-right">
                            <li class="nav-item nav-profile dropdown">
                                <a class="nav-link dropdown-toggle" id="profileDropdown" href="#" data-toggle="dropdown" aria-expanded="false">
                                    <div class="nav-profile-img">
                                        @if(!empty(Auth::guard('admin')->user()->profile))
                                            <img src="{{ Auth::guard('admin')->user()->profile }}" alt="image">
                                        @else
                                            <img src="{{ asset('assets/images/faces/blankuser.png') }}" alt="image">
                                        @endif
                                        <span class="availability-status online"></span>
                                    </div>

                                    <div class="nav-profile-text">
                                        <p class="mb-1 text-black">{{ Auth::guard('admin')->user()->name }}</p>
                                    </div>
                                </a>
                                <div class="dropdown-menu navbar-dropdown" aria-labelledby="profileDropdown">
                                    <a class="dropdown-item" href="{{ Route('admin.ProfilePage') }}">
                                        <i class="mdi mdi-account-settings mr-2 text-success"></i> Edit Profile </a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="{{ route('admin.ChangePassword') }}">
                                        <i class="mdi mdi-cached mr-2 text-dark"></i><span class="text-dark">Change Password</span> </a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="{{ route('admin.logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                                        <i class="mdi mdi-logout mr-2 text-primary"></i> Signout </a>
                                        <form action="{{ route('admin.logout') }}" id="logout-form" method="post">@csrf</form>
                                </div>
                            </li>
                        </ul>
                    @else
                        <ul class="navbar-nav navbar-nav-right">
                            <li class="nav-item nav-profile dropdown">
                                <a class="nav-link dropdown-toggle" id="profileDropdown" href="#" data-toggle="dropdown" aria-expanded="false">
                                    <div class="nav-profile-img">
                                        <img src="{{ asset(Auth::guard('seller')->user()->sellerprofile) }}" alt="image">
                                        <span class="availability-status online"></span>
                                    </div>
                                    <div class="nav-profile-text">
                                        <p class="mb-1 text-black">{{ Auth::guard('seller')->user()->seller_full_name_buss }}</p>
                                    </div>
                                </a>
                                <div class="dropdown-menu navbar-dropdown" aria-labelledby="profileDropdown">
                                    {{-- <a class="dropdown-item" href="#">
                                        <i class="mdi mdi-account-card-details mr-2 text-primary"></i><span class="text-dark">My Profile</span> </a>
                                    <div class="dropdown-divider"></div> --}}
                                    <a class="dropdown-item" href="{{ route('seller.ChangePassword') }}">
                                        <i class="mdi mdi-cached mr-2 text-dark"></i><span class="text-dark">Change Password</span> </a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="{{ route('seller.logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                                        <i class="mdi mdi-logout mr-2 text-danger"></i><span class="text-dark"> Signout </span> </a>
                                        <form action="{{ route('seller.logout') }}" id="logout-form" method="post">@csrf</form>
                                </div>
                            </li>
                        </ul>
                    @endif
                    <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
                        <span class="mdi mdi-menu"></span>
                    </button>
                </div>
            </nav>
            <!-- partial -->
            <div class="container-fluid page-body-wrapper">
                @if (strpos($routeName, 'admin.') === 0)
                    @include('layouts.sidebar')
                @else
                    @include('layouts.sellersidebar')
                @endif
                <div class="main-panel">
                    @if (strpos($routeName, 'seller.') === 0)
                        @php
                            $seller     =   Auth::guard('seller')->user();
                            $sellerid   =   Auth::guard('seller')->user()->id;
                            $messages   =   [];
                            // if Trade License Expired Not Shown
                            if(strtotime($seller->seller_trade_exp_dt) < strtotime('now') && $seller->seller_trade_exp_dt!=Null){
                                $messages[] =   'Your Trade license expired. please update trade license from Settings / Seller-Details';
                            }

                            // If no Latitude and Longitude Available
                            if($seller->latitdue == null && $seller->longitude == null){
                                $messages[] =   'Latitude and Longitude are required from Settings / Seller-Details';
                            }

                            // If no Delivery and Pickup Available Not Shown
                            if($seller->pickup == 0 && $seller->delivery == 0){
                                $messages[] =   'Enable atleast one option either delivery or pickup from Settings / Order Settings';
                            }

                            // if Delivery Yes and No Delivery Area Updated Not Shown
                            if($seller->delivery == 1){
                                $check_shipping_locations = DB::table('seller_shipping_details')->where('seller_id',$sellerid)->count();
                                if($check_shipping_locations == 0){
                                    $messages[] =   'Please add shipping fees for atleast one delivery area from Settings / Order Settings';
                                }
                            }

                            // if No Products Available don't Show
                            $check_avail_products = DB::table('products')->where('seller_id',$sellerid)
                                                    ->where('status','1')->count();
                            if($check_avail_products == 0){
                                $messages[] =   'Please add products to get listed';
                            }
                        @endphp

                        @if (count($messages) > 0)
                            <div class="content-wrapper-alert">
                                <div class="alert alert-warning" role="alert">
                                    <strong>Update:</strong>
                                    <small>Please update the following data from <a href="{{ route('seller.SellerSettings') }}">Settings</a> to get listed in Starling app</small>
                                    <ul class="mt-2">
                                        @foreach ($messages as $message)
                                        <li>{{ $message }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @endif
                    @endif
                    
                    @yield('content')
                    <!-- content-wrapper ends -->
                    <!-- partial:partials/_footer.html -->
                    <footer class="footer">
                        <div class="container-fluid clearfix">
                            <span class="text-muted d-block text-center text-sm-left d-sm-inline-block">Copyright © Starling {{ date('Y') }}</span>
                        </div>
                    </footer>
                    <!-- partial -->
                </div>
                <!-- main-panel ends -->
            </div>
            <!-- page-body-wrapper ends -->
        </div>
        <!-- container-scroller -->
        <!-- plugins:js -->

        <!-- endinject -->
        <!-- Plugin js for this page -->
        <script src="{{ asset('assets/vendors/chart.js/Chart.min.js') }}"></script>
        <!-- End plugin js for this page -->
        <!-- inject:js -->
        <script src="{{ asset('assets/js/off-canvas.js') }}"></script>
        <script src="{{ asset('assets/js/hoverable-collapse.js') }}"></script>
        <script src="{{ asset('assets/js/misc.js') }}"></script>
        <!-- endinject -->
        <!-- Custom js for this page -->
        <script src="{{ asset('assets/js/dashboard.js') }}"></script>
        <script src="{{ asset('assets/js/todolist.js') }}"></script>
        <!-- End custom js for this page -->
        <script src="{{ asset('assets/js/jquery.validate.js') }}"></script>
        <script src="{{ asset('assets/js/additional-methods.js') }}"></script>
        <script src="{{ asset('assets/vendors/IziToast/iziToast.min.js') }}"></script>
        <script src="{{ asset('assets/vendors/select2/select2.min.js') }}"></script>
        <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=places&callback=initialize" async defer></script>
        @yield('pagescript')
        <script>
            $(document).ready(function(){
                $('.mobile-nub').keyup(function() {
                    if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g, '')
                });
            });
        </script>
        <script>
            $( document ).ready(function() {
                $(".sidebar .nav .nav-item").removeClass('active');
                $(".sidebar .nav .nav-item .collapse").removeClass('show');

        });

        </script>

    </body>

</html>
