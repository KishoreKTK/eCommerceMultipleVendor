<!-- partial:partials/_sidebar.html -->



<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        <li class="nav-item nav-profile">
            <a href="{{ Route('seller.SellerDetail') }}" class="nav-link">
              <div class="nav-profile-image">
                <img src="{{ asset(Auth::guard('seller')->user()->sellerprofile) }}" alt="profile">
                <span class="login-status online"></span>
                <!--change to offline or busy as needed-->
              </div>
              <div class="nav-profile-text d-flex flex-column">
                <span class="font-weight-bold mb-2">{{ Auth::guard('seller')->user()->seller_full_name_buss }}</span>
                <span class="text-secondary text-small">{{ Auth::guard('seller')->user()->sellerarea }}</span>
              </div>
              <i class="mdi mdi-store text-success nav-profile-badge"></i>
            </a>
        </li>
        <!-- Dashboard -->
        <li class="nav-item">
            <a class="nav-link" href="{{ Route('seller.home') }}">
                <span class="menu-title">Dashboard</span>
                <i class="mdi mdi-home menu-icon"></i>
            </a>
        </li>

        <!-- Categories -->
        <li class="nav-item">
            <a class="nav-link" href="{{ Route('seller.categories') }}">
                <span class="menu-title">Categories</span>
                <i class="mdi mdi mdi-apps menu-icon"></i>
            </a>
        </li>

        <!-- Products -->
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#ui-basic2" aria-expanded="false" aria-controls="ui-basic2">
                <span class="menu-title">Products</span>
                <i class="menu-arrow"></i>
                <i class="mdi mdi-shopping menu-icon"></i>
            </a>
            <div class="collapse" id="ui-basic2">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('seller.AddProduct') }}">Add Product</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('seller.ProductList') }}">Product List</a>
                    </li>
                </ul>
            </div>
        </li>
        <?php $unread_orders=Session::get('unread_orders'); ?>

        {{-- Orders --}}
        <li class="nav-item">
            <a class="nav-link" href="{{ Route('seller.OrderList') }}">
                @if(isset($unread_orders)&& $unread_orders>0)

               <span class="menu-title">Orders
               <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">{{$unread_orders }}</span></span>
                @else
                <span class="menu-title">Orders</span>

               @endif

                <i class="mdi mdi-cart-plus menu-icon"></i>
            </a>
        </li>

        {{-- Transaction --}}
        <li class="nav-item">
            <a class="nav-link" href="{{ route('seller.TransactionList') }}">
                <span class="menu-title">Transaction</span>
                <i class="mdi mdi-cash-multiple menu-icon"></i>
            </a>
        </li>
        {{--
        <li class="nav-item">
            <a class="nav-link" href="#">
                <span class="menu-title">Transaction</span>
                <i class="mdi mdi mdi-apps menu-icon"></i>
            </a>
        </li> --}}

        <li class="nav-item">
            <a class="nav-link" href="{{ Route('seller.SellerSettings') }}">
              <span class="menu-title">Settings</span>
              <i class="mdi mdi-settings menu-icon"></i>
            </a>
        </li>
    </ul>
</nav>
<!-- partial -->
