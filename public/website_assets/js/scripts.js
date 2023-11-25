$(window).load(function() {
  $('.preload-container').fadeOut(400);
});


$(document).ready(function () {

  var pageurl = window.location.href;
  $('#navbar-list a[href="' + pageurl + '"]').parent().addClass('current_page_item');

  // ===========================

  $('[data-toggle="tooltip"]').tooltip();




  $(".nav-btn").click(function () {
    $(this).toggleClass("open");
    $('.navbar-overlay').toggleClass("open");
    $('body').toggleClass("bodyhide");

  });

  $(".navbar-overlay ul li a").click(function () {
    $('.navbar-overlay').removeClass("open");
    $('.nav-btn').removeClass("open");

  });



  if (Modernizr.mq('(max-width: 991px)')) {
    $('.navbar-section').addClass('active fixed-top');
    $(window).scroll(function (e) {

      if ($(window).scrollTop() >= 0) {
        $('.navbar-section').addClass('active fixed-top');
      } else {
        $('.navbar-section').addClass('active fixed-top');

      }
      e.preventDefault();
    });

  }

  if (Modernizr.mq('(min-width: 992px)')) {

    $('.navbar-section').removeClass('active fixed-top');

    $(window).scroll(function (e) {

      if ($(window).scrollTop() >= 80) {
        $('.navbar-section').addClass('active fixed-top');
      } else {
        $('.navbar-section').removeClass('active fixed-top');


      }
      e.preventDefault();
    });

  }



  // ===================================================================




});
// =============================================
// var swiper = new Swiper(".swiper-features", {
//   slidesPerView: 1,
//   centeredSlides: false,
//   grabCursor: true,
//   // scrollbar: {
//   //   el: ".swiper-scrollbar",
//   // },
//   breakpoints: {
//     640: {
//       slidesPerView: 1,
  
//     },
//     768: {
//       slidesPerView: 1,
  
//     },
//     1024: {
//       slidesPerView: 2,
   
//     },
//     1440: {
//       slidesPerView: 4,
   
//     }
//   },

// });
// var swiper1 = new Swiper(".swiper-screens", {
//   slidesPerView: 1,
//   centeredSlides: false,
//   grabCursor: true,
//   // scrollbar: {
//   //   el: ".swiper-scrollbar",
//   // },
//   breakpoints: {
//     640: {
//       slidesPerView: 1,
  
//     },
//     768: {
//       slidesPerView: 2,
  
//     },
//     1024: {
//       slidesPerView: 2,
   
//     },
//     1440: {
//       slidesPerView: 4,
   
//     }
//   },
// });
$('.feature-slider').slick({
  dots: false,
  infinite: false,
  arrows: false,
  speed: 300,
  slidesToShow: 4,
  slidesToScroll: 1,
  autoplaySpeed: 1800,
  responsive: [
    {
      breakpoint: 1399,
      settings: {
        slidesToShow: 4,
        slidesToScroll: 1,
        infinite: true,
  
      }
    },
    {
      breakpoint: 1199,
      settings: {
        slidesToShow: 4,
        slidesToScroll: 1,
        infinite: true,
   
      }
    },
    {
      breakpoint: 991,
      settings: {
        slidesToShow: 2,
        slidesToScroll: 1
      }
    },
    {
      breakpoint: 767,
      settings: {
        slidesToShow: 2,
        slidesToScroll: 1,
        infinite: true,
        autoplay: true,
      }
    },
    {
      breakpoint: 575,
      settings: {
        slidesToShow: 1,
        slidesToScroll: 1,
        infinite: true,
        autoplay: true,
      }
    }
    // You can unslick at a given breakpoint now by adding:
    // settings: "unslick"
    // instead of a settings object
  ]
});
$('.screen-slider').slick({
  dots: false,
  infinite: false,
  arrows: false,
  speed: 300,
  slidesToShow: 4,
  slidesToScroll: 1,
  autoplaySpeed: 2000,
  responsive: [
    {
      breakpoint: 1399,
      settings: {
        slidesToShow: 4,
        slidesToScroll: 1,
        infinite: true,
    
      }
    },
    {
      breakpoint: 1199,
      settings: {
        slidesToShow: 3,
        slidesToScroll: 1,
        infinite: true,

      }
    },
    {
      breakpoint: 991,
      settings: {
        slidesToShow: 2,
        slidesToScroll: 1
      }
    },
    {
      breakpoint: 767,
      settings: {
        slidesToShow: 2,
        slidesToScroll: 1,
        infinite: true,
        autoplay: true,
      }
    },
    {
      breakpoint: 575,
      settings: {
        slidesToShow: 1,
        slidesToScroll: 1,
        infinite: true,
        autoplay: true,
      }
    }
  ]
});
// =============================================


// ===============================================







// $(function () {
//   $('a[href*=\\#]:not([href=\\#])').click(function () {
//     if (location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') && location.hostname == this.hostname) {
//       var target = $(this.hash);
//       target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
//       if (target.length) {
//         $('html,body').animate({
//           scrollTop: target.offset().top - 86
//         }, 1000);
//         return false;
//       }
//     }
//   });
// });

// $(document).ready(function() {
//   var target = $(location).attr("hash");
//   var offset = ($(this).attr('data-offset') ? $(this).attr('data-offset') : 0);
//   $('body,html').animate({
//       scrollTop: $(target).offset().top - offset
//   }, 700);
// });
// :: 4.0 SCROLL LINK ACTIVE CODE
var scrollLink = $('.scroll');

// :: 5.0 SMOOTH SCROLLING ACTIVE CODE
scrollLink.on('click', function (e) {
  e.preventDefault();
  $('body,html').animate({
    scrollTop: $(this.hash).offset().top - 100
  }, 1000);
});


// $("a[href^='#']").click(function(event) {
//     event.preventDefault();
//     $('ul li a').removeClass( 'active-menu' );
//     $(this).addClass( 'active-menu' );
//     var $this = $(this),
//       target = this.hash,
//       $target = $(target);

//     $(scrollElement).stop().animate({
//       'scrollTop': $target.offset().top
//     }, 1000, 'swing', function() {
//       window.location.hash = target;
//     }); 
// });

// ================================================
