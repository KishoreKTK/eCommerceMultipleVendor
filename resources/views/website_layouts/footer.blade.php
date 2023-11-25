<footer class="main-section footer-section pb-lg-6 pb-4" id="contact">

    <div class="container">
        <div class="row g-0">
            <div class="col-lg-12">
                <div class="gray-box p-lg-6 p-4">
                    <div class="row justify-content-lg-between justify-content-center" data-aos="fade-up"
                        data-aos-offset="0">
                        <div
                            class="col-lg-4 col-md-9 col-11 text-lg-start text-center align-self-center mb-md-6 mb-lg-0">
                            <div class="footerbox">
                                <div class="logo mb-4">
                                    <a href="{{ url('/') }}">
                                        <img class="img-fluid" src="{{ asset('website_assets/img/starling-logo.svg') }}" alt="">
                                    </a>
                                </div>
                                <p class="mb-4">No chaffering in the bazaars for the items you want.</p>
                                <div class="socialfooter mt-lg-4 mt-3">
                                    <ul class="d-flex justify-content-md-start justify-content-center">
                                        <li class="me-3"><a href="{{ url('/') }}" target="_blank"><i class="bi-facebook"></i></a></li>
                                        <li class="me-3"><a href="{{ url('/') }}" target="_blank"><i class="bi-instagram"></i></a>
                                        </li>
                                        <li class="me-3"><a href="{{ url('/') }}" target="_blank"><i class="bi-twitter"></i></a></li>
                                        <li class="me-3"><a href="{{ url('/') }}" target="_blank"><i class="bi-linkedin"></i></a></li>
                                    </ul>
                                </div>

                            </div>
                        </div>
                        <div class="col-lg-auto col-md-4 col-11 align-self-center">
                            <div class="footerbox text-md-start text-center mt-md-0 mt-4">
                                <h5 class="mb-3">Resources</h5>
                                <div class="f-menu">
                                    <ul class="">
                                        <li class="mb-md-2"><a href="{{ url('/') }}#features">Features</a></li>
                                        <li class="mb-md-2"><a href="{{ Route('seller.login') }}">Login</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-auto col-md-4 col-11 align-self-center">
                            <div class="footerbox text-md-start text-center mt-md-0 mt-4">
                                <h5 class="mb-3">Leagal</h5>
                                <div class="f-menu">
                                    <ul class="">
                                        <li class="mb-md-2"><a href="{{ Route('PrivacyPolicy')  }}">Privacy policy</a></li>
                                        <li class="mb-md-2"><a href="{{ Route('TermsnConditions') }}">Terms & conditions</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-auto col-md-4 col-11 align-self-center">
                            <div class="footerbox text-md-start text-center mt-md-0 mt-4">
                                <h5 class="mb-3">Contact</h5>
                                <div class="f-menu">
                                    <ul class="">
                                        <li class="mb-md-2"><a href="{{ url('/') }}">Feedback</a></li>
                                        <li class="mb-md-2"><a href="{{ url('/') }}#about">About Us</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row copyright mt-lg-6 mt-4">
                        <div class="col-lg-12">
                            <div class="copy-box text-lg-start text-center">
                                <p class="mb-0">&copy; <script>document.write(new Date().getFullYear())</script> Starling, All Rights Reserved.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</footer>



<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-migrate/3.3.2/jquery-migrate.min.js"></script>

<script src="{{ asset('website_assets/js/modernizr-custom.js') }}"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-kQtW33rZJAHjgefvhyyzcGF3C5TFyBQBA13V1RKPf4uH+bwyzQxZ6CmMZHmNBEfJ" crossorigin="anonymous">
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"
    integrity="sha512-XtmMtDEcNz2j7ekrtHvOVR4iwwaD6o/FUJe6+Zq+HgcCsk3kj4uSQQR8weQ2QVj1o0Pk6PwYLohm206ZzNfubg=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script src="{{ asset('website_assets/js/scripts.js') }}"></script>

{{-- <script src="{{ asset('assets/js/jquery.validate.js') }}"></script>
<script src="{{ asset('assets/js/additional-methods.js') }}"></script> --}}
<script src="{{ asset('assets/vendors/IziToast/iziToast.min.js') }}"></script>


<script>
    $(document).ready(function()
    {

        var base_url = window.location.origin;

        $("body").on('submit', '#contactform', function(e)
        {
            e.preventDefault();
            var formdata = {};

            formdata.first_name = $("#contactname").val();
            formdata.mobile     = $("#contactmobile").val();
            formdata.email      = $("#contactemail").val();
            formdata.message    = $("#contactmessage").val();
            // console.log(formdata);

            // return false;
            $.ajax({
                headers: { 'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content') },
                type: "POST",
                url: base_url + "/api/ContactUs",
                data: formdata,
                dataType: "JSON",
                // beforeSend: function() {
                //     $(".preload-container").show();
                // },
                success: function(msg) {
                    // $(".preload-container").hide();
                    if (msg['status'] == true)
                    {
                        $('#contactform')[0].reset();
                        $("#contcatModal").modal("hide");
                        iziToast.success({
                                timeout: 3000,
                                id: 'success',
                                title: 'Success',
                                message: msg['message'],
                                position: 'bottomLeft',
                                transitionIn: 'bounceInLeft',
                            });
                    } else {
                        iziToast.error({
                                timeout: 3000,
                                id: 'error',
                                title: 'Error',
                                message: msg['message'],
                                position: 'bottomLeft',
                                transitionIn: 'fadeInDown'
                            });
                            $("#contcatModal").modal("show");
                    }
                }
            });
        });

    });
</script>
