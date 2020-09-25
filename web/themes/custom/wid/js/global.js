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
    });
  }  

  $('#toolkitTestimonialSlide').find('.carousel-item').first().addClass('active');
  $('#toolkitTestimonialSlideIndicator').find('.item-indicator').first().addClass('active');

})(jQuery, Drupal);


var slideIndex = 1;
showSlides(slideIndex);
function plusSlides(n) {
  showSlides(slideIndex += n);
}
function currentSlide(n) {
  showSlides(slideIndex = n);
}
function showSlides(n) {
  var slideItem;
  var slides = document.getElementsByClassName("countryGallerySlides");
  var dots = document.getElementsByClassName("galleryImage");
  if (n > slides.length) {slideIndex = 1}
  if (n < 1) {slideIndex = slides.length}
  for (slideItem = 0; slideItem < slides.length; slideItem++) {
      slides[slideItem].style.display = "none";
  }
  for (slideItem = 0; slideItem < dots.length; slideItem++) {
      dots[slideItem].className = dots[slideItem].className.replace(" active", "");
  }
  slides[slideIndex-1].style.display = "block";
  dots[slideIndex-1].className += " active";
}
