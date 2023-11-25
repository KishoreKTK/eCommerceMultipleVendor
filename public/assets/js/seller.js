$(document).ready(function() {
    $('.mobile-nub').keyup(function() {
        if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g, '')
    });
    $('#seller-detailed-list a').on('click', function(e) {
        e.preventDefault()
        $(this).tab('show')
    })

    $("body").on("click", ".view_trade_license", function() {

        var sellername = $(this).attr("data-seller_name");
        var sellerlicence = $(this).attr("data-trade_licenseurl");
        $("#ViewLicenceModelLabel").html(sellername + ' Trade Licence');
        $("#ViewLicencePdf").html('<iframe src="' + sellerlicence + '" frameborder="0" width="700" height="600"></iframe>');
        $("#ViewLicenceModel").modal('show');
    });

    // Owl Carousel
    $('.owl-carousel').owlCarousel({
        items: 1,
        loop: true,
        margin: 10,
        nav: true,
    });
});