/**
 * @file
 * Global utilities.
 *
 */
(function($, Drupal) {

  'use strict';

  Drupal.behaviors.bootstrap_barrio_subtheme = {
    attach: function(context, settings) {
      var position = $(window).scrollTop();
      $(window).scroll(function () {
        if ($(this).scrollTop() > 50) {
          $('body').addClass("scrolled");
        }
        else {
          $('body').removeClass("scrolled");
        }
        var scroll = $(window).scrollTop();
        if (scroll > position) {
          $('body').addClass("scrolldown");
          $('body').removeClass("scrollup");
        } else {
          $('body').addClass("scrollup");
          $('body').removeClass("scrolldown");
        }
        position = scroll;
      });

    }
  };

  function styleSearch() {
    $('.js-form-item-search-api-fulltext')
      .append("<span class='fa fa-search form-control-feedback' style=' position: absolute; top: 12px; left: 20px; color: #556cff; '></span>");
  }

  var accordion = document.getElementsByClassName("faq-accordion");
  var accordionItem;
  for (accordionItem = 0; accordionItem < accordion.length; accordionItem++) {
    accordion[accordionItem].addEventListener("click", function() {
      this.classList.toggle("active");
      var panel = this.nextElementSibling;
      if (panel.style.maxHeight) {
        panel.style.maxHeight = null;
      } else {
        panel.style.maxHeight = panel.scrollHeight + "px";
      }
    }); }

  $('#toolkitTestimonialSlide').find('.carousel-item').first().addClass('active');
  $('#toolkitTestimonialSlideIndicator').find('.item-indicator').first().addClass('active');

  styleSearch();

  // window.onscroll = function() {
  //   scrollFunction()
  // };
  // function scrollFunction() {
  //   if (document.body.scrollTop >= 20 || document.documentElement.scrollTop >= 20) {
  //     document.getElementById("navbar-main").style.padding = "5px 20px";
  //   } else {
  //     document.getElementById("navbar-main").style.padding = "25px 20px";
  //   }
  // }

  $(".owl-carousel").owlCarousel({
    margin: 20,
    mouseDrag: false,
    dots: false,
    nav: true,
    navText: [
      `
        <div class="carousel-card-button carousel-card-button-left">
          <i class="ion-android-arrow-back"></i>
        </div>
      `,
      `
        <div class="carousel-card-button carousel-card-button-right">
          <i class="ion-android-arrow-forward"></i>
        </div>
      `
    ],
    responsive:{
      0:{
        items:1,
      },
      480:{
        items:1,
      },
      768:{
        items:2,
      },
      1024: {
        items: 3,
      }
    }
  });

  $('.single-event-list').on('click', function() {
    $(this).toggleClass("active");
  });

  $('.view-taxonomy-term').addClass('container');
  $('.view-search').addClass('container');

  $("#login, #login-link").click(function(){
    $(".login-container").css("display", "block");
    $(".signup-container").css("display", "none");
    $("#login").addClass( "active" );
    $("#register").removeClass( "active" );
  });
  $("#register, #register-link").click(function(){
    $(".login-container").css("display", "none");
    $(".signup-container").css("display", "block");
    $("#register").addClass( "active" );
    $("#login").removeClass( "active" );
  });
})(jQuery, Drupal);
