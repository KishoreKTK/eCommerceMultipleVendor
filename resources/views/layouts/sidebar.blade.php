<!-- partial:partials/_sidebar.html -->
<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">

        <!-- Dashboard -->
        <li class="nav-item">
            <a class="nav-link" href="{{ Route('admin.home') }}">
                <span class="menu-title">Dashboard</span>
                <i class="mdi mdi-home menu-icon"></i>
            </a>
        </li>

        <!-- Categories -->
        <li class="nav-item">
            <a class="nav-link" href="{{ Route('admin.categories') }}">
                <span class="menu-title">Categories</span>
                <i class="mdi mdi mdi-apps menu-icon"></i>
            </a>
        </li>

        <!-- Approvals -->
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#ui-basic1" aria-expanded="false" aria-controls="ui-basic1">
                <span class="menu-title">Seller</span>
                <i class="menu-arrow"></i>
                <i class="mdi mdi-nature-people menu-icon"></i>
            </a>
            <div class="collapse" id="ui-basic1">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.sellerrequest') }}">Seller Requests</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.SellerCategoryRequestPage') }}">Seller Category Requests</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.SellerList') }}">Seller Lists</a>
                    </li>
                </ul>
            </div>
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
                        <a class="nav-link" href="{{ route('admin.ProductList') }}">Product Lists</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.ProductAttributes') }}">Product Attributes</a>
                    </li>
                </ul>
            </div>
        </li>

        <!-- Customers -->
        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.CustomerList') }}">
                <span class="menu-title">Customers</span>
                <i class="mdi mdi-human-male-female menu-icon"></i>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.OrderList') }}">
                <span class="menu-title">Orders</span>
                <i class="mdi mdi-cart-plus menu-icon"></i>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.TransactionList') }}">
                <span class="menu-title">Transaction</span>
                <i class="mdi mdi-cash-multiple menu-icon"></i>
            </a>
        </li>

        @if(Auth::guard('admin')->user()->is_super == '1')
                <!-- Settings -->
            <li class="nav-item">
                <a class="nav-link" data-toggle="collapse" href="#ui-basic4" aria-expanded="false" aria-controls="ui-basic4">
                    <span class="menu-title">Settings</span>
                    <i class="menu-arrow"></i>
                    <i class="mdi mdi-settings-box menu-icon"></i>
                </a>
                <div class="collapse" id="ui-basic4">
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.banners') }}">Banners</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.faq') }}">FAQ</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.contents') }}">Contents</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.contactus') }}">Contact Us</a>
                        </li>
                    </ul>
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.adminusers') }}">
                    <span class="menu-title">Admin Users</span>
                    <i class="mdi mdi-account-multiple-outline menu-icon"></i>
                </a>
            </li>
        @endif
    </ul>
</nav>
<!-- partial -->

