<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @include('website_layouts.header')
    <title>Starling</title>
</head>

<body>
    <div class="preload-container">
        <div class="preload-wrapper d-flex justify-content-center">

            <div class="preload align-self-center">
                <span class="circle"></span>
                <span class="smcircle"></span>
            </div>
        </div>
    </div>
    <div class="body-wrapper">
        @include('website_layouts.navbar')

        <section class="cover-section main-section pt-lg-0 pt-4" id="home">
            <div class="container">
                <div class="row justify-content-lg-between justify-content-center carousel-row g-0 gray-box">
                    <div class="col-lg-6 align-self-center text-lg-start text-center order-lg-1 order-2">
                        <div class="p-lg-6 pt-lg-6 pt-5">
                            <span class="mb-lg-3 d-block">App Should be like</span>
                            <h1>Life Should<br> Be Easy. <br>

                            </h1>
                            <p>Your one stop shop for all your needs. Order all your party, ocassions, store, café, restaurant requirements and get it delivered at your doorstep. Pay a mass volume price for whatever you buy.</p>


                            <div class="row g-0 justify-content-lg-start justify-content-center">
                                <div class="col-xxl-10 col-lg-12 col-sm-8 col-10">
                                    <div class="d-flex justify-content-center storeicons mt-lg-5 mt-4">
                                        <div class="pe-lg-3 pe-2"><a href="https://apps.apple.com/us/app/starling-app/id1613501923"> <img src="{{ asset('website_assets/img/appstore.svg') }}"
                                                    alt=""></a>
                                        </div>
                                        <div class="ps-lg-3 ps-2"><a href="https://play.google.com/store/apps/details?id=com.starling.starling"> <img src="{{ asset('website_assets/img/playstore.svg') }}"
                                                    alt=""></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 align-self-center order-lg-2 order-1">

                        <div class="grad-box p-lg-4">
                            <img src="{{ asset('website_assets/img/cover-screen.png') }}" class="img-fluid" alt="...">
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="about-section main-section pt-lg-8 pt-5" id="about">
            <div class="container">
                <div class="row justify-content-center text-lg-start text-center">
                    <div class="col-lg-6 col-sm-10 col-11 align-self-center pe-lg-8">
                        <div class="">
                            <div class="main-title">
                                <h2>Connecting all<br>
                                    Your e-commerce<br>
                                    Needs
                                </h2>
                            </div>
                            <p>Your B2B & B2C marketplace with all the great deals. Purchase your bulk essentials at your pace. </p>

                            <a class="btn btn-outline-secondary mt-4" href="">Get Started</a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-10 align-self-center d-lg-block d-none">
                        <img class="img-fluid" src="{{ asset('website_assets/img/screen-about.png') }}" alt="">

                    </div>
                </div>

            </div>
        </section>


        <section class="main-section features-section pt-lg-8 pt-5" id="features">
            <div class="container">
                <div class="row justify-content-lg-etween justify-content-center g-lg-3 g-2">
                    <div class="col-lg-12 col-11 align-self-center mb-lg-5">
                        <div class="main-title text-center">
                            <h2>App features
                            </h2>
                            {{-- <p>Contrary to popular belief, Lorem Ipsum is not simply random </p> --}}
                        </div>

                    </div>


                </div>
            </div>
            <div class="container-xxl feature-container">
                <div class="row g-0 justify-content-center">

                    <div class="col-lg-4 align-self-lg-center align-self-start">
                        <div class="featurebox text-center">
                            <div class="iconbox ps-lg-9 pe-lg-9 ps-7 pe-7 mb-3 mx-auto">
                                <img class="img-fluid" src="{{ asset('website_assets/img/click.svg') }}" alt="">
                            </div>
                            <h4 class="fw-500 mb-3">Click</h4>
                            <p class="mb-0 ">Select the items you require in bulk, add it to your cart and proceed to pay.
                            </p>
                        </div>
                    </div>

                    <div class="col-lg-4 align-self-lg-center align-self-start">
                        <div class="featurebox text-center">
                            <div class="iconbox ps-lg-9 pe-lg-9 ps-7 pe-7 mb-3 mx-auto">
                                <img class="img-fluid" src="{{ asset('website_assets/img/pay.svg') }}" alt="">
                            </div>
                            <h4 class="fw-500 mb-3">Pay</h4>
                            <p class="mb-0 ">
                                Make a hassle free payment throgh your debit/credit card and get your items delivered.
                            </p>
                        </div>
                    </div>

                    <div class="col-lg-4 align-self-lg-center align-self-start">
                        <div class="featurebox text-center">
                            <div class="iconbox ps-lg-9 pe-lg-9 ps-7 pe-7 mb-3 mx-auto">
                                <img class="img-fluid" src="{{ asset('website_assets/img/deliver.svg') }}" alt="">
                            </div>
                            <h4 class="fw-500 mb-3">Deliver</h4>
                            <p class="mb-0 ">Receive your order and relish.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>


        <section class="main-section video-section pt-lg-8 pt-5" id="video">
            <div class="container">
                <div class="row justify-content-lg-between justify-content-center">
                    <div class="col-lg-12 col-10 align-self-center">
                        <div class="videobox">
                            <img class="img-fluid" src="{{ asset('website_assets/img/video-img.jpg') }}" alt="...">
                            <a role="button" class="play-btn" href="">
                                <span></span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>


        <section class="main-section screens-section pt-lg-8 pt-5" id="screens">
            <div class="container-xxl">
                <div class="row justify-content-center">
                    <div class="col-lg-12 col-10">
                        <div class="screen-slider">
                            <div>
                                <img class="img-fluid d-block mx-auto" src="{{ asset('website_assets/img/screen-1.png') }}" alt="">
                            </div>
                            <div>
                                <img class="img-fluid d-block mx-auto" src="{{ asset('website_assets/img/screen-2.png') }}" alt="">
                            </div>
                            <div>
                                <img class="img-fluid d-block mx-auto" src="{{ asset('website_assets/img/screen-3.png') }}" alt="">
                            </div>
                            <div>
                                <img class="img-fluid d-block mx-auto" src="{{ asset('website_assets/img/screen-4.png') }}" alt="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>


        <section class="main-section downloads-section pt-lg-8 pt-5" id="downloads">
            <div class="container">
                <div class="row justify-content-center text-lg-start text-center">

                    <div class="col-lg-3 col-10 align-self-center d-lg-block d-none">
                        <img class="img-fluid" src="{{ asset('website_assets/img/screen-about.png') }}" alt="">

                    </div>
                    <div class="col-lg-6 col-sm-10 col-11 align-self-center pe-lg-8 pb-lg-0 pb-5">
                        <div class="ps-lg-6">
                            <div class="main-title">
                                <h2>Download
                                </h2>
                            </div>
                            <p>Need not visit the market and haggle for purchasing your bulk essentials. We are here to keep you unruffle.</p>

                            <div class="row g-0 justify-content-lg-start justify-content-center">
                                <div class="col-xxl-10 col-lg-12 col-sm-8 col-10">
                                    <div class="d-flex justify-content-center storeicons mt-lg-5 mt-4">
                                        <div class="pe-lg-3 pe-2"><a href="https://apps.apple.com/us/app/starling-app/id1613501923"> <img src="{{ asset('website_assets/img/appstore.svg') }}"
                                                    alt=""></a>
                                        </div>
                                        <div class="ps-lg-3 ps-2"><a href="https://play.google.com/store/apps/details?id=com.starling.starling"> <img src="{{ asset('website_assets/img/playstore.svg') }}"
                                                    alt=""></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="main-section newsletter-section  pt-lg-8 pb-lg-6 pt-5 pb-5" id="newsletter">
            <div class="container">
                <div class="row justify-content-center text-white text-center g-lg-3 g-2">
                    <div class="col-lg-10 align-self-center">
                        <div class="main-title mb-md-3 text-yellow">
                            <h2>Subscribe Newsletter
                            </h2>
                        </div>
                        <p class="text-purple-lite">
                            Subscribe to our mailing list and get interesting stuff and updates to your email inbox.
                        </p>
                    </div>
                </div>

                <div class="row justify-content-center mt-lg-5 mt-4">
                    <div class="col-xxl-4 col-lg-5 col-sm-8 col-12">
                        <div class="subscribebox position-relative">
                            <input type="text" class="form-control" id="exampleFormControlInput1" placeholder="Your Email">
                            <button type="button" class="btn btn-primary">Subscribe</button>
                        </div>
                    </div>
                </div>

            </div>
        </section>

        <!-- Modal -->
        <div class="modal fade" id="contcatModal" tabindex="-1" aria-labelledby="contcatModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title w-100 d-flex justify-content-center" id="contcatModalLabel"><span>Contact</span></h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-lg-5">
                        <form id="contactform" autocomplete="off">
                            <div class="row justify-content-center">
                                <div class="col-lg-7 col-sm-10 col-11">
                                    <div class="mb-3">
                                        <input type="text" class="form-control" required name="name" id="contactname" placeholder="Full Name">
                                    </div>
                                </div>

                                <div class="col-lg-5 col-sm-10 col-11">
                                    <div class="mb-3">
                                        <input type="number" class="form-control" required name="mobile" id="contactmobile" placeholder="Phone">
                                    </div>
                                </div>

                                <div class="clearfix"></div>
                                <div class="col-lg-12 col-sm-10 col-11">
                                    <div class="mb-3">
                                        <input type="email" class="form-control" required name="email" id="contactemail" placeholder="Email">
                                    </div>
                                </div>

                                <div class="clearfix"></div>
                                <div class="col-lg-12 col-sm-10 col-11">
                                    <div class="mb-3">
                                        <textarea class="form-control" required name="message" id="contactmessage" rows="3" placeholder="Message"></textarea>
                                    </div>
                                </div>

                                <div class="clearfix"></div>
                                <div class="col-lg-3 col-sm-10 col-11">
                                    <div class="mb-3 d-grid">
                                        <button type="submit" class="btn btn-primary btn-lg fw-600">Submit</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        @include('website_layouts.footer')
    </div>
</body>
</html>
