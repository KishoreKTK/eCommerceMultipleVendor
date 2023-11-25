<div class="preload-container">
    <div class="preload-wrapper d-flex justify-content-center">

        <div class="preload align-self-center">
            <span class="circle"></span>
            <span class="smcircle"></span>
        </div>
    </div>
</div>

<header class="navbar-section">
    <div class="container">
        <div class="row navbar">
            <div class="col-lg-2 col-6">
                <div class="logo">
                    <a href="{{ url('/') }}">
                        <img class="img-fluid" src="{{ asset('website_assets/img/starling-logo.svg') }}" alt="">
                    </a>
                </div>
            </div>

            <div class="col-lg-auto d-lg-block d-none ml-auto align-self-center">
                <div class="nav-list">
                    <ul class="d-flex" id="navbar-list">
                        <li class="align-self-center current_page_item"><a class="nav-link scroll" href="{{ url('/') }}#home">Home</a></li>
                        <li class="align-self-center"><a class="nav-link scroll" href="{{ url('/') }}#about">About</a></li>
                        <li class="align-self-center"><a class="nav-link scroll" href="{{ url('/') }}#features">Features</a></li>
                        <li class="align-self-center"><a class="nav-link scroll" href="{{ url('/') }}#screens">Screens</a></li>
                        <li class="align-self-center"><a class="nav-link scroll" data-bs-toggle="modal" data-bs-target="#contcatModal">Contact</a></li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-auto d-lg-block d-none ml-auto align-self-center">
                <div class="nav-list">
                    <ul class="d-flex" id="navbar-list">
                        <li class="align-self-center"><a href="{{ route('seller.register') }}" class="nav-link">Interested to Sell</a></li>
                        <li class="align-self-center"><a href="{{ route('checkloginuser') }}" class="btn btn-primary" >Login</a></li>
                    </ul>
                </div>
            </div>
            <div class="col-6 d-lg-none d-block align-self-center">
                <div class="nav-btn float-end me-2 mt-2">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
        </div>
    </div>
</header>


<div class="navbar-overlay d-lg-none text-center">

    <ul class="d-flex flex-column justify-content-center">
        <li class="mb-2"><a class="scroll" href="{{ url('/') }}#home">Home</a></li>
        <li class="mb-2"><a class="scroll" href="{{ url('/') }}#about">About</a></li>
        <li class="mb-2"><a class="scroll" href="{{ url('/') }}#features">Features</a></li>
        <li class="mb-2"><a class="scroll" href="{{ url('/') }}#screens">Screens</a></li>
        <li class="mb-2"><a class="nav-link" href="{{ route('seller.register') }}">Interested to Sell</a></li>
        <li class="mb-2"><a class="nav-link" href="{{ route('checkloginuser') }}">Login</a></li>
        <li class="mb-2"><a class="scroll" data-bs-toggle="modal" data-bs-target="#contcatModal">Contact</a></li>
    </ul>

</div>

