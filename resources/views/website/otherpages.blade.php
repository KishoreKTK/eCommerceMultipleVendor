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
        <section>
            <div class="container">
                <div class="row justify-content-center text-lg-start text-center">
                    <div class="col-lg-12 col-sm-12 col-12 my-5 align-self-center pe-lg-8">
                        {!! $content !!}
                    </div>
                </div>
            </div>
        </section>
        @include('website_layouts.footer')
    </div>
</body>
</html>
